<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use App\Notifications\LoanApplicationSubmitted; // Added for submit notification
use Illuminate\Auth\Access\AuthorizationException as IlluminateAuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade; // For sending notifications
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class LoanApplicationService
{
    private const LOG_AREA = 'LoanApplicationService: ';
    private ApprovalService $approvalService;
    private LoanTransactionService $loanTransactionService;

    public function __construct(ApprovalService $approvalService, LoanTransactionService $loanTransactionService)
    {
        $this->approvalService = $approvalService;
        $this->loanTransactionService = $loanTransactionService;
    }

    public function getApplicationsForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA . 'Fetching loan applications for user.', ['user_id' => $user->id, 'filters' => $filters]);
        $query = LoanApplication::query()->withDefaultRelations(); // Assuming a withDefaultRelations scope in Model

        $isPrivilegedUser = $user->hasAnyRole(['Admin', 'BPMStaff']); // 'Approver' role is too broad for general list

        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            if (!$isPrivilegedUser && (int) $filters['user_id'] !== $user->id) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('user_id', (int) $filters['user_id']);
            }
        } elseif (!$isPrivilegedUser) {
            $query->where('user_id', $user->id);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        if ($isPrivilegedUser && !empty($filters['supporting_officer_id'])) {
            $query->where('supporting_officer_id', (int) $filters['supporting_officer_id']);
        }
        if (!empty($filters['search_term'])) {
            $term = '%' . $filters['search_term'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', $term)
                  ->orWhere('purpose', 'like', $term)
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', $term));
            });
        }

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->latest('updated_at')->paginate($perPage); // Sort by latest update
    }

    /**
     * @param array{responsible_officer_id?: int|null, supporting_officer_id: int, purpose: string, location: string, return_location?: string|null, loan_start_date: string, loan_end_date: string, items: list<array{equipment_type: string, quantity_requested: int, notes?: string|null}>, applicant_confirmation?: bool} $validatedData
     */
    public function createAndSubmitApplication(array $validatedData, User $applicant): LoanApplication
    {
        $applicantId = $applicant->id;
        Log::info(self::LOG_AREA . "Creating and submitting loan application for User ID: {$applicantId}");

        if (empty($validatedData['items'])) {
            throw new InvalidArgumentException(__('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));
        }
        if (empty($validatedData['supporting_officer_id'])) {
            Log::error(self::LOG_AREA . "supporting_officer_id is missing for User ID: {$applicantId}.");
            throw new InvalidArgumentException(__('Pegawai Penyokong mesti dipilih untuk menghantar permohonan.'));
        }
        if (empty($validatedData['applicant_confirmation'])) {
             throw new InvalidArgumentException(__('Perakuan pemohon mesti diterima sebelum penghantaran.'));
        }


        /** @var User|null $supportingOfficer */
        $supportingOfficer = User::find($validatedData['supporting_officer_id']);
        if (!$supportingOfficer) {
            Log::error(self::LOG_AREA . "Supporting Officer ID {$validatedData['supporting_officer_id']} not found for User ID: {$applicantId}.");
            throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih tidak sah.'));
        }
        // TODO: Validate if supportingOfficer is eligible (e.g., grade >= 41 as per ICT form)
        // if ($supportingOfficer->grade?->level < config('motac.approval.min_loan_support_grade_level', 41)) { ... }

        DB::beginTransaction();
        try {
            $applicationData = Arr::except($validatedData, ['items', 'applicant_confirmation']);
            $applicationData['user_id'] = $applicantId;
            $applicationData['responsible_officer_id'] = $validatedData['responsible_officer_id'] ?? $applicantId;
            // supporting_officer_id from $validatedData
            $applicationData['status'] = LoanApplication::STATUS_DRAFT; // Create as draft first

            /** @var LoanApplication $application */
            $application = LoanApplication::create($applicationData);

            foreach ($validatedData['items'] as $item) {
                // No specific item status on LoanApplicationItem as per core design. Progress tracked by quantities.
                $application->applicationItems()->create([
                    'equipment_type' => $item['equipment_type'],
                    'quantity_requested' => (int) $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $application->applicant_confirmation_timestamp = now(); // Set confirmation timestamp
            $application->save();

            // Now transition to pending support and initiate workflow
            $application->transitionToStatus(LoanApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar oleh pengguna untuk kelulusan pegawai penyokong.'), $applicantId);

            $this->approvalService->initiateApprovalWorkflow($application, $applicant, Approval::STAGE_LOAN_SUPPORT_REVIEW, $supportingOfficer);

            DB::commit();
            Log::info(self::LOG_AREA . 'Loan application created and submitted successfully.', ['application_id' => $application->id]);
            NotificationFacade::send($applicant, new LoanApplicationSubmitted($application));
            return $application->fresh(['applicationItems', 'user', 'responsibleOfficer', 'supportingOfficer', 'approvals']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . 'Failed to create and submit loan application for User ID: ' . $applicantId, ['error_message' => $e->getMessage(), 'data' => $validatedData]);
            if ($e instanceof InvalidArgumentException || $e instanceof ModelNotFoundException) throw $e;
            throw new RuntimeException(__('Gagal mencipta dan menghantar permohonan pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

     /**
     * Submits a previously drafted or rejected loan application for approval.
     */
    public function submitApplicationForApproval(LoanApplication $application, User $submitter): LoanApplication
    {
        if ((int) $application->user_id !== (int) $submitter->id && !$submitter->can('manage', $application)) { // Assuming 'manage' for admin submit
            throw new IlluminateAuthorizationException(__('Anda hanya boleh menghantar permohonan anda sendiri.'));
        }
        if (!$application->isDraft() && $application->status !== LoanApplication::STATUS_REJECTED) {
            throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dihantar semula.'));
        }
        if (empty($application->supporting_officer_id)) {
            throw new RuntimeException(__('Pegawai Penyokong mesti ditetapkan sebelum permohonan boleh dihantar.'));
        }
        /** @var User $supportingOfficer */
        $supportingOfficer = User::findOrFail($application->supporting_officer_id); // Throws ModelNotFound if not found

        // Ensure applicant_confirmation is set
        if (empty($application->applicant_confirmation_timestamp)) {
             throw new RuntimeException(__('Perakuan pemohon mesti diterima sebelum penghantaran. Sila kemaskini draf.'));
        }


        Log::info(self::LOG_AREA . "Submitting LoanApplication ID: {$application->id} for approval by User ID: {$submitter->id}.");
        DB::beginTransaction();
        try {
            $application->transitionToStatus(LoanApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar semula untuk kelulusan pegawai penyokong.'), $submitter->id);

            $existingPendingSupportApproval = $application->approvals()
                ->where('stage', Approval::STAGE_LOAN_SUPPORT_REVIEW)
                ->where('status', Approval::STATUS_PENDING)
                ->where('officer_id', $supportingOfficer->id) // Ensure it's for the correct officer
                ->first();

            if (!$existingPendingSupportApproval) {
                $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_LOAN_SUPPORT_REVIEW, $supportingOfficer);
            } else {
                // Optionally, re-notify the existing officer
                if (class_exists(\App\Notifications\ApplicationNeedsAction::class)) {
                    $supportingOfficer->notify(new \App\Notifications\ApplicationNeedsAction($existingPendingSupportApproval, $application));
                }
            }

            DB::commit();
            Log::info(self::LOG_AREA . "LoanApplication ID: {$application->id} resubmitted. Status: {$application->status}.");
            NotificationFacade::send($submitter, new LoanApplicationSubmitted($application));
            return $application->fresh(['user', 'supportingOfficer', 'approvals']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to resubmit LoanApplication ID: {$application->id}.", ['error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal menghantar semula permohonan pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param array{responsible_officer_id?: int|null, supporting_officer_id?: int|null, purpose?: string, location?: string, return_location?: string|null, loan_start_date?: string, loan_end_date?: string, items?: list<array{id?: int|null, equipment_type: string, quantity_requested: int, notes?: string|null, _delete?: bool}>, applicant_confirmation?:bool} $validatedData
     */
    public function updateApplication(LoanApplication $application, array $validatedData, User $user): LoanApplication
    {
        // Authorization to update is handled by controller/policy ($user->can('update', $application))
        Log::info(self::LOG_AREA . "Updating loan application ID: {$application->id} by user ID: {$user->id}.");

        // If supporting_officer_id is being changed, validate the new one
        if (isset($validatedData['supporting_officer_id']) && $validatedData['supporting_officer_id'] !== $application->supporting_officer_id) {
            $newSupportingOfficer = User::find($validatedData['supporting_officer_id']);
            if (!$newSupportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih untuk kemaskini tidak sah.'));
            }
            // TODO: Additional eligibility checks for the new supporting officer if needed
        }

        DB::beginTransaction();
        try {
            $applicationData = Arr::except($validatedData, ['items', 'applicant_confirmation']);
            $application->fill($applicationData);

            // Handle applicant_confirmation if provided
            if (isset($validatedData['applicant_confirmation']) && $validatedData['applicant_confirmation'] === true && !$application->applicant_confirmation_timestamp) {
                $application->applicant_confirmation_timestamp = now();
            } elseif (isset($validatedData['applicant_confirmation']) && $validatedData['applicant_confirmation'] === false) {
                $application->applicant_confirmation_timestamp = null; // Clear if unchecked
            }

            $application->save();

            if (isset($validatedData['items']) && is_array($validatedData['items'])) {
                $this->syncApplicationItems($application, $validatedData['items']); // Removed $user, not needed by current sync logic
            }
            DB::commit();
            Log::info(self::LOG_AREA . "Loan application ID {$application->id} updated successfully.");
            return $application->fresh(['applicationItems', 'user', 'responsibleOfficer', 'supportingOfficer']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to update loan application ID: {$application->id}.", ['error_message' => $e->getMessage(), 'data' => $validatedData]);
            if ($e instanceof IlluminateAuthorizationException || $e instanceof ModelNotFoundException) throw $e;
            throw new RuntimeException(__('Gagal mengemaskini permohonan pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function deleteApplication(LoanApplication $application, User $user): bool
    {
        // Authorization to delete handled by controller/policy ($user->can('delete', $application))
        Log::info(self::LOG_AREA . "Deleting loan application ID: {$application->id} by user ID: {$user->id}");
        DB::beginTransaction();
        try {
            // Delete related items first.
            $application->applicationItems()->delete(); // Soft delete items
            // Soft delete approvals associated with this application
            $application->approvals()->delete();
            $deleted = $application->delete(); // Soft delete application itself
            DB::commit();
            Log::info(self::LOG_AREA . "Loan application ID {$application->id} and its items/approvals soft deleted successfully.");
            return (bool) $deleted;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to delete loan application ID: {$application->id}.", ['error_message' => $e->getMessage()]);
            if ($e instanceof IlluminateAuthorizationException) throw $e;
            throw new RuntimeException(__('Gagal memadam permohonan pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param array<array{loan_application_item_id: int, equipment_id: int, quantity_issued: int, issue_item_notes?: string|null, accessories_checklist_item?: array|null}> $itemsDetails
     * @param array{receiving_officer_id: int, transaction_date: string, issue_notes?: string|null} $transactionDetails
     */
    public function createIssueTransaction(LoanApplication $loanApplication, array $itemsDetails, User $issuingOfficer, array $transactionDetails): LoanTransaction
    {
        $appIdLog = $loanApplication->id;
        Log::info(self::LOG_AREA . "Creating issue transaction for LoanApplication ID: {$appIdLog} by Issuing Officer ID: {$issuingOfficer->id}");

        if (!in_array($loanApplication->status, [LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_PARTIALLY_ISSUED])) {
            throw new RuntimeException(__("Peralatan hanya boleh dikeluarkan untuk permohonan yang telah diluluskan atau separa dikeluarkan. Status semasa: :status", ['status' => $loanApplication->statusTranslated]));
        }
        if (empty($itemsDetails)) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dikeluarkan dalam transaksi.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            $serviceItemData[] = [
                'equipment_id' => $item['equipment_id'],
                'loan_application_item_id' => $item['loan_application_item_id'],
                'quantity' => $item['quantity_issued'], // This maps to quantity_transacted in LoanTransactionItem
                'notes' => $item['issue_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? null,
            ];
        }

        $extraServiceDetails = [
            'receiving_officer_id' => $transactionDetails['receiving_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now(),
            'issue_notes' => $transactionDetails['issue_notes'] ?? null,
            'status' => LoanTransaction::STATUS_ISSUED, // Default status for a new issue transaction
        ];

        return $this->loanTransactionService->createTransaction($loanApplication, LoanTransaction::TYPE_ISSUE, $issuingOfficer, $serviceItemData, $extraServiceDetails);
    }

    /**
     * @param array<array{loan_transaction_item_id: int, equipment_id: int, quantity_returned: int, condition_on_return: string, item_status_on_return: string, return_item_notes?: string|null, accessories_checklist_item?: array|null}> $itemsDetails
     * @param array{returning_officer_id: int, transaction_date: string, return_notes?: string|null} $transactionDetails
     */
    public function createReturnTransaction(LoanTransaction $issueTransaction, array $itemsDetails, User $returnAcceptingOfficer, array $transactionDetails): LoanTransaction
    {
        $loanApplication = $issueTransaction->loanApplication()->firstOrFail(); // Get parent LA
        $appIdLog = $loanApplication->id;
        Log::info(self::LOG_AREA . "Creating return transaction for LA ID: {$appIdLog} (against Issue Tx ID: {$issueTransaction->id}) by Officer ID: {$returnAcceptingOfficer->id}");

        if (empty($itemsDetails)) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dipulangkan dalam transaksi.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            $originalIssuedItem = LoanTransactionItem::findOrFail($item['loan_transaction_item_id']);
            // Ensure this item belongs to the provided issueTransaction and has the correct equipment_id
            if ($originalIssuedItem->loan_transaction_id !== $issueTransaction->id || $originalIssuedItem->equipment_id !== $item['equipment_id']) {
                 throw new InvalidArgumentException(__("Item rujukan pengeluaran tidak sepadan atau tidak sah: ID #:id", ['id' => $item['loan_transaction_item_id']]));
            }

            $serviceItemData[] = [
                'equipment_id' => $item['equipment_id'], // Equipment being returned
                'original_loan_transaction_item_id' => $item['loan_transaction_item_id'], // Link to the item line in the issue TX
                'loan_application_item_id' => $originalIssuedItem->loan_application_item_id, // Link to the overall application item
                'quantity' => $item['quantity_returned'],
                'condition_on_return' => $item['condition_on_return'],
                'item_status_on_return' => $item['item_status_on_return'], // Status of the item in this return tx (e.g., returned_good)
                'notes' => $item['return_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? null,
            ];
        }

        $extraServiceDetails = [
            'returning_officer_id' => $transactionDetails['returning_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now(),
            'return_notes' => $transactionDetails['return_notes'] ?? null,
            'related_transaction_id' => $issueTransaction->id, // Link this return TX to the issue TX
            'status' => LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION, // Initial status for a return
        ];

        return $this->loanTransactionService->createTransaction($loanApplication, LoanTransaction::TYPE_RETURN, $returnAcceptingOfficer, $serviceItemData, $extraServiceDetails);
    }

    // This method seems more appropriate for a ReportService or a specialized query scope/repository
    public function getIssuedLoanTransactionsSummary(array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA . 'Fetching summary of issued loan applications (active loans).', ['filters' => $filters]);
        $query = LoanApplication::query()
            ->whereIn('status', [LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED, LoanApplication::STATUS_OVERDUE])
            ->withDefaultRelations(); // Ensure default relations include items for summary

        // Apply filters as in getApplicationsForUser or more specific ones for active loans
        if (!empty($filters['search_term'])) {
            // ... search logic ...
        }
        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->latest('updated_at')->paginate($perPage);
    }

    public function findLoanApplication(int $id, array $with = []): ?LoanApplication
    {
        $query = LoanApplication::query();
        $defaultWith = LoanApplication::getDefaultRelations(); // Assuming scope on model
        $finalWith = array_unique(array_merge($defaultWith, $with));

        if (!empty($finalWith)) {
            $query->with($finalWith);
        }
        return $query->find($id);
    }

    /**
     * @param array<array{id?:int|null, equipment_type:string, quantity_requested:int, notes?:string|null, _delete?:bool}> $itemsData
     */
    protected function syncApplicationItems(LoanApplication $application, array $itemsData): void // Removed User $user parameter
    {
        $existingItemIds = $application->applicationItems()->pluck('id')->all();
        $processedItemIds = [];

        foreach ($itemsData as $itemData) {
            if (empty($itemData['equipment_type']) || !isset($itemData['quantity_requested']) || (int)$itemData['quantity_requested'] <= 0) {
                Log::warning(self::LOG_AREA . "Skipping invalid item data during sync for LA ID: {$application->id}", ['item_data' => $itemData]);
                continue;
            }

            $itemId = isset($itemData['id']) && is_numeric($itemData['id']) ? (int)$itemData['id'] : null;
            $itemPayload = [
                'equipment_type' => $itemData['equipment_type'],
                'quantity_requested' => (int)$itemData['quantity_requested'],
                'notes' => $itemData['notes'] ?? null,
                // Quantities (approved, issued, returned) are updated via other processes, not direct sync here.
                // This method syncs the *requested* items list.
            ];

            if ($itemId && in_array($itemId, $existingItemIds, true)) {
                if (!empty($itemData['_delete'])) {
                    LoanApplicationItem::find($itemId)?->delete(); // Soft delete
                } else {
                    LoanApplicationItem::find($itemId)?->update($itemPayload);
                    $processedItemIds[] = $itemId;
                }
            } elseif (empty($itemData['_delete'])) { // New item
                $createdItem = $application->applicationItems()->create($itemPayload);
                $processedItemIds[] = $createdItem->id;
            }
        }
        $idsToDelete = array_diff($existingItemIds, $processedItemIds);
        if (!empty($idsToDelete)) {
            $application->applicationItems()->whereIn('id', $idsToDelete)->delete(); // Soft delete
        }
    }
}
