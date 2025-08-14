<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem as AppLoanTransactionItemModel;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use RuntimeException;

final class LoanApplicationService
{
    private const LOG_AREA = 'LoanApplicationService: ';

    private ApprovalService $approvalService;

    private LoanTransactionService $loanTransactionService;

    private NotificationService $notificationService;

    private array $defaultLoanApplicationRelations = [
        'user:id,name,email,department_id,position_id,grade_id',
        'user.department:id,name',
        'user.position:id,name',
        'user.grade:id,name,level',
        'responsibleOfficer:id,name,email',
        'supportingOfficer:id,name,email,grade_id',
        'supportingOfficer.grade:id,name,level',
        'loanApplicationItems',
        'approvals.officer:id,name',
        'loanTransactions.issuingOfficer:id,name',
        'loanTransactions.receivingOfficer:id,name',
        'loanTransactions.returningOfficer:id,name',
        'loanTransactions.returnAcceptingOfficer:id,name',
    ];

    public function __construct(
        ApprovalService $approvalService,
        LoanTransactionService $loanTransactionService,
        NotificationService $notificationService
    ) {
        $this->approvalService = $approvalService;
        $this->loanTransactionService = $loanTransactionService;
        $this->notificationService = $notificationService;
    }

    public function getApplicationsForUser(User $requestingUser, array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA.'Fetching loan applications.', ['requesting_user_id' => $requestingUser->id, 'filters' => $filters]);
        $query = LoanApplication::query()->with($this->defaultLoanApplicationRelations);

        $isPrivilegedUser = $requestingUser->hasAnyRole(['Admin', 'BPM Staff']);

        if (isset($filters['user_id']) && ! empty($filters['user_id'])) {
            if (! $isPrivilegedUser && (int) $filters['user_id'] !== $requestingUser->id) {
                $query->where('user_id', $requestingUser->id);
                Log::warning(self::LOG_AREA.'Unauthorized attempt to filter by user_id, restricted to own.', ['requesting_user_id' => $requestingUser->id, 'target_user_id' => $filters['user_id']]);
            } else {
                $query->where('user_id', (int) $filters['user_id']);
            }
        } elseif (! $isPrivilegedUser) {
            $query->where('user_id', $requestingUser->id);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            if (in_array($filters['status'], LoanApplication::getStatusKeys(), true)) {
                $query->where('status', $filters['status']);
            } else {
                Log::warning(self::LOG_AREA.'Invalid status filter ignored.', ['status_filter' => $filters['status']]);
            }
        }

        if ($isPrivilegedUser && ! empty($filters['supporting_officer_id'])) {
            $query->where('supporting_officer_id', (int) $filters['supporting_officer_id']);
        }

        if (! empty($filters['search_term'])) {
            $term = '%'.$filters['search_term'].'%';
            $query->where(function ($q) use ($term): void {
                $q->where('id', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $term));
            });
        }

        $orderBy = $filters['order_by'] ?? 'updated_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $validOrderBy = ['id', 'created_at', 'updated_at', 'loan_start_date', 'status', 'purpose'];
        if (! in_array($orderBy, $validOrderBy)) {
            $orderBy = 'updated_at';
        }

        if (! in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }

    /**
     * REFACTORED: Creates a new loan application and, if not a draft,
     * immediately processes it for submission in a single atomic transaction.
     */
    public function createAndSubmitApplication(array $validatedData, User $applicant, bool $saveAsDraftOnly = false): LoanApplication
    {
        $applicantId = $applicant->id;
        Log::info(self::LOG_AREA.'Processing create application request.', [
            'user_id' => $applicantId,
            'data_keys' => array_keys($validatedData),
            'save_as_draft_only' => $saveAsDraftOnly,
        ]);

        if (empty($validatedData['items'])) {
            throw new InvalidArgumentException(__('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));
        }

        if (! $saveAsDraftOnly && empty($validatedData['applicant_confirmation'])) {
            throw new InvalidArgumentException(__('Perakuan pemohon mesti diterima sebelum penghantaran.'));
        }

        return DB::transaction(function () use ($validatedData, $applicant, $saveAsDraftOnly) {
            $applicationModelData = [
                'user_id' => $applicant->id,
                'responsible_officer_id' => $validatedData['responsible_officer_id'] ?? $applicant->id,
                'purpose' => $validatedData['purpose'],
                'location' => $validatedData['location'],
                'return_location' => $validatedData['return_location'] ?? null,
                'loan_start_date' => $validatedData['loan_start_date'],
                'loan_end_date' => $validatedData['loan_end_date'],
                'status' => LoanApplication::STATUS_DRAFT,
                'supporting_officer_id' => $validatedData['supporting_officer_id'] ?? null,
            ];

            if (isset($validatedData['applicant_phone']) && Schema::hasColumn('loan_applications', 'applicant_phone')) {
                $applicationModelData['applicant_phone'] = $validatedData['applicant_phone'];
            }

            if (! $saveAsDraftOnly && ($validatedData['applicant_confirmation'] ?? false)) {
                $applicationModelData['applicant_confirmation_timestamp'] = now();
            } else {
                $applicationModelData['applicant_confirmation_timestamp'] = null;
            }

            /** @var LoanApplication $application */
            $application = LoanApplication::create($applicationModelData);

            if (! empty($validatedData['items'])) {
                $itemsToCreate = collect($validatedData['items'])->map(function ($item) {
                    return Arr::only($item, ['equipment_type', 'quantity_requested', 'notes']);
                })->all();

                $application->loanApplicationItems()->createMany($itemsToCreate);
            }

            // If it's a draft, we're done. If not, proceed with submission logic.
            if ($saveAsDraftOnly) {
                Log::info(self::LOG_AREA.'Loan application created as draft.', ['application_id' => $application->id]);
            } else {
                Log::info(self::LOG_AREA.'Loan application created, now processing for submission.', ['application_id' => $application->id]);

                // ##### SUBMISSION LOGIC MOVED HERE FOR ATOMIC OPERATION #####
                $this->processSubmission($application, $applicant);
            }

            return $application->fresh($this->defaultLoanApplicationRelations);
        });
    }

    /**
     * Submits an existing draft or rejected application for approval.
     * This is kept for use cases outside of the initial creation form (e.g., resubmitting a rejected application).
     */
    public function submitApplicationForApproval(LoanApplication $application, User $submitter): LoanApplication
    {
        if (! $application->applicant_confirmation_timestamp) {
            throw new RuntimeException(__('Perakuan pemohon mesti diterima sebelum penghantaran. Sila kemaskini draf dan sahkan perakuan.'));
        }

        if (! in_array($application->status, [LoanApplication::STATUS_DRAFT, LoanApplication::STATUS_REJECTED])) {
            throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dihantar semula. Status semasa: :status', ['status' => $application->status_label]));
        }

        Log::info(self::LOG_AREA.'Submitting existing LoanApplication for approval.', ['application_id' => $application->id, 'user_id' => $submitter->id]);

        return DB::transaction(fn (): \App\Models\LoanApplication => $this->processSubmission($application, $submitter));
    }

    /**
     * Central private method to handle the logic of submitting an application.
     * This is called by both createAndSubmitApplication and submitApplicationForApproval.
     */
    private function processSubmission(LoanApplication $application, User $submitter): LoanApplication
    {
        $supportingOfficer = null;
        if ($application->supporting_officer_id) {
            /** @var User $supportingOfficer */
            $supportingOfficer = User::with('grade:id,name,level')->findOrFail($application->supporting_officer_id);
            $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
            if (! $supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) {
                throw new InvalidArgumentException(__('Pegawai Penyokong yang ditetapkan (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade', [
                    'name' => $supportingOfficer->name,
                    'minGrade' => $minSupportGradeLevel,
                    'currentGrade' => $supportingOfficer->grade?->name ?? __('Tidak Dinyatakan'),
                ]));
            }
        }

        $nextStatus = $supportingOfficer ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_PENDING_APPROVER_REVIEW;
        $application->status = $nextStatus;
        $application->submitted_at = now();
        $application->save();

        $approvalTask = $this->approvalService->initiateApprovalWorkflow(
            $application,
            $submitter,
            Approval::STAGE_LOAN_SUPPORT_REVIEW,
            $supportingOfficer // Pass officer or null, service will handle it
        );

        $this->notificationService->notifyApplicantApplicationSubmitted($application);

        if ($approvalTask instanceof \App\Models\Approval && $approvalTask->officer) {
            $this->notificationService->notifyApproverApplicationNeedsAction($approvalTask);
        } else {
            Log::error(self::LOG_AREA.sprintf('No officer found for next approval stage. Application ID: %d may be orphaned.', $application->id));
            $this->notificationService->notifyAdminOfOrphanedApplication($application);
        }

        Log::info(self::LOG_AREA.'LoanApplication submitted/resubmitted successfully for approval.', ['application_id' => $application->id, 'status' => $application->status]);

        return $application;
    }

    public function updateApplication(LoanApplication $application, array $validatedData, User $user): LoanApplication
    {
        Log::info(self::LOG_AREA.'Updating loan application.', ['application_id' => $application->id, 'user_id' => $user->id, 'data_keys' => array_keys($validatedData)]);

        if (! $application->isDraft() && $application->status !== LoanApplication::STATUS_REJECTED) {
            throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dikemaskini. Status semasa: :status', ['status' => $application->getStatusLabelAttribute()]));
        }

        if (isset($validatedData['supporting_officer_id']) && $validatedData['supporting_officer_id'] !== null && (int) $validatedData['supporting_officer_id'] !== (int) $application->supporting_officer_id) {
            /** @var User|null $newSupportingOfficer */
            $newSupportingOfficer = User::with('grade:id,name,level')->find((int) $validatedData['supporting_officer_id']);
            if (! $newSupportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih untuk kemaskini tidak sah.'));
            }

            $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
            if (! $newSupportingOfficer->grade || (int) $newSupportingOfficer->grade->level < $minSupportGradeLevel) {
                throw new InvalidArgumentException(__('Pegawai Penyokong baharu yang dipilih (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade', [
                    'name' => $newSupportingOfficer->name,
                    'minGrade' => $minSupportGradeLevel,
                    'currentGrade' => $newSupportingOfficer->grade?->name ?? __('Tidak Dinyatakan'),
                ]));
            }
        }

        return DB::transaction(function () use ($application, $validatedData) {
            $applicationModelData = Arr::only($validatedData, $application->getFillable());

            // EDITED: Added logic to reset status for rejected applications
            if ($application->status === LoanApplication::STATUS_REJECTED) {
                $applicationModelData['status'] = LoanApplication::STATUS_DRAFT;
                $applicationModelData['rejection_reason'] = null;
            }

            $application->fill($applicationModelData);

            if (array_key_exists('applicant_confirmation', $validatedData)) {
                $isStillDraft = $validatedData['is_draft_submission'] ?? ($application->status === LoanApplication::STATUS_DRAFT);

                if (! $isStillDraft && ($validatedData['applicant_confirmation'] ?? false) === true) {
                    $application->applicant_confirmation_timestamp = $application->applicant_confirmation_timestamp ?? now();
                } elseif (($validatedData['applicant_confirmation'] ?? null) === false || $isStillDraft) {
                    $application->applicant_confirmation_timestamp = null;
                }
            }

            $application->save();

            if (isset($validatedData['items']) && is_array($validatedData['items'])) {
                $this->syncApplicationItems($application, $validatedData['items']);
            }

            Log::info(self::LOG_AREA.'Loan application updated successfully.', ['application_id' => $application->id]);

            return $application->fresh($this->defaultLoanApplicationRelations);
        });
    }

    public function deleteApplication(LoanApplication $application, User $user): bool
    {
        Log::info(self::LOG_AREA.'Attempting to delete loan application.', ['application_id' => $application->id, 'user_id' => $user->id]);
        if (! $application->isDraft()) {
            Log::warning(self::LOG_AREA.'Attempt to delete non-draft application denied.', ['application_id' => $application->id, 'status' => $application->status]);
            throw new RuntimeException(__('Hanya draf permohonan yang boleh dibuang.'));
        }

        return DB::transaction(function () use ($application): true {
            $application->loanApplicationItems()->delete();
            $application->approvals()->delete();
            $deleted = $application->delete();

            if ($deleted) {
                Log::info(self::LOG_AREA.'Loan application and related data soft deleted.', ['application_id' => $application->id]);
            } else {
                Log::warning(self::LOG_AREA.'Soft delete returned false for loan application.', ['application_id' => $application->id]);
                throw new RuntimeException(__('Gagal memadam permohonan.'));
            }

            return (bool) $deleted;
        });
    }

    public function createIssueTransaction(LoanApplication $loanApplication, array $itemsDetails, User $issuingOfficer, array $transactionDetails): LoanTransaction
    {
        $appIdLog = $loanApplication->id;
        Log::info(self::LOG_AREA.'Creating issue transaction.', ['application_id' => $appIdLog, 'issuing_officer_id' => $issuingOfficer->id]);

        if (! in_array($loanApplication->status, [LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_PARTIALLY_ISSUED])) {
            throw new RuntimeException(__('Peralatan hanya boleh dikeluarkan untuk permohonan yang telah diluluskan atau separa dikeluarkan. Status semasa: :status', ['status' => $loanApplication->status_label]));
        }

        if ($itemsDetails === []) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dikeluarkan dalam transaksi ini.'));
        }

        if (empty($transactionDetails['receiving_officer_id'])) {
            throw new InvalidArgumentException(__('Pegawai Penerima mesti dinyatakan.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            if (empty($item['equipment_id']) || empty($item['loan_application_item_id']) || ! isset($item['quantity_issued']) || (int) $item['quantity_issued'] <= 0) {
                throw new InvalidArgumentException(__('Butiran item pengeluaran tidak lengkap atau kuantiti tidak sah.'));
            }

            $serviceItemData[] = [
                'equipment_id' => (int) $item['equipment_id'],
                'loan_application_item_id' => (int) $item['loan_application_item_id'],
                'quantity' => (int) $item['quantity_issued'],
                'notes' => $item['issue_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? config('motac.loan_accessories_list_default_empty_json', '[]'),
            ];
        }

        $extraServiceDetails = [
            'receiving_officer_id' => (int) $transactionDetails['receiving_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(),
            'issue_notes' => $transactionDetails['issue_notes'] ?? null,
            'status' => LoanTransaction::STATUS_ISSUED,
        ];

        $transaction = $this->loanTransactionService->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_ISSUE,
            $issuingOfficer,
            $serviceItemData,
            $extraServiceDetails
        );
        $this->notificationService->notifyApplicantEquipmentIssued($loanApplication, $transaction, $issuingOfficer);

        return $transaction;
    }

    public function createReturnTransaction(LoanTransaction $issueTransaction, array $itemsDetails, User $returnAcceptingOfficer, array $transactionDetails): LoanTransaction
    {
        $loanApplication = $issueTransaction->loanApplication()->firstOrFail();
        Log::info(self::LOG_AREA.'Creating return transaction.', ['loan_application_id' => $loanApplication->id, 'issue_transaction_id' => $issueTransaction->id, 'accepting_officer_id' => $returnAcceptingOfficer->id]);

        if ($itemsDetails === []) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dipulangkan dalam transaksi ini.'));
        }

        if (empty($transactionDetails['returning_officer_id'])) {
            throw new InvalidArgumentException(__('Pegawai Yang Memulangkan mesti dinyatakan.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            if (empty($item['equipment_id']) || empty($item['loan_transaction_item_id']) || ! isset($item['quantity_returned']) || (int) $item['quantity_returned'] <= 0 || empty($item['condition_on_return']) || empty($item['item_status_on_return'])) {
                throw new InvalidArgumentException(__('Butiran item pemulangan tidak lengkap, kuantiti tidak sah, atau status/keadaan tidak dinyatakan.'));
            }

            /** @var \App\Models\LoanTransactionItem $originalIssuedItem */
            $originalIssuedItem = AppLoanTransactionItemModel::findOrFail($item['loan_transaction_item_id']);
            if ($originalIssuedItem->loan_transaction_id !== $issueTransaction->id || (int) $originalIssuedItem->equipment_id !== (int) $item['equipment_id']) {
                throw new InvalidArgumentException(__('Item rujukan pengeluaran (ID: :itemRefId) tidak sepadan atau tidak sah untuk item peralatan (ID: :eqId).', ['itemRefId' => $item['loan_transaction_item_id'], 'eqId' => $item['equipment_id']]));
            }

            $serviceItemData[] = [
                'equipment_id' => (int) $item['equipment_id'],
                'original_loan_transaction_item_id' => (int) $item['loan_transaction_item_id'],
                'loan_application_item_id' => $originalIssuedItem->loan_application_item_id,
                'quantity' => (int) $item['quantity_returned'],
                'condition_on_return' => $item['condition_on_return'],
                'item_status_on_return' => $item['item_status_on_return'],
                'notes' => $item['return_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? config('motac.loan_accessories_list_default_empty_json', '[]'),
            ];
        }

        $extraServiceDetails = [
            'returning_officer_id' => (int) $transactionDetails['returning_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(),
            'return_notes' => $transactionDetails['return_notes'] ?? null,
            'related_transaction_id' => $issueTransaction->id,
            'status' => LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION,
        ];

        $transaction = $this->loanTransactionService->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_RETURN,
            $returnAcceptingOfficer,
            $serviceItemData,
            $extraServiceDetails
        );
        $this->notificationService->notifyApplicantEquipmentReturned($loanApplication, $transaction, $returnAcceptingOfficer);

        return $transaction;
    }

    public function getActiveLoansSummary(array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA.'Fetching summary of active loan applications.', ['filters' => $filters]);
        $query = LoanApplication::query()
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
                LoanApplication::STATUS_OVERDUE,
            ])
            ->with($this->defaultLoanApplicationRelations);

        if (! empty($filters['search_term'])) {
            $term = '%'.$filters['search_term'].'%';
            $query->where(function ($q) use ($term): void {
                $q->where('id', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $term))
                    ->orWhereHas('responsibleOfficer', fn ($roq) => $roq->where('name', 'like', $term));
            });
        }

        $orderBy = $filters['order_by'] ?? 'loan_end_date';
        $orderDirection = $filters['order_direction'] ?? 'asc';

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }

    public function findLoanApplicationById(int $id, array $with = []): ?LoanApplication
    {
        Log::debug(self::LOG_AREA.'Finding loan application.', ['id' => $id, 'with_relations' => $with]);
        $relationsToLoad = $with === [] ? $this->defaultLoanApplicationRelations : array_unique(array_merge($this->defaultLoanApplicationRelations, $with));

        /** @var LoanApplication|null $application */
        $application = LoanApplication::with($relationsToLoad)->find($id);

        if (! $application) {
            Log::notice(self::LOG_AREA.'Loan application not found.', ['id' => $id]);
        }

        return $application;
    }

    private function syncApplicationItems(LoanApplication $application, array $itemsData): void
    {
        $existingItemIds = $application->loanApplicationItems()->pluck('id')->all();
        $processedItemIds = [];
        $itemPayloadsToCreate = [];

        foreach ($itemsData as $itemData) {
            if (empty($itemData['equipment_type']) || ! isset($itemData['quantity_requested'])) {
                Log::warning(self::LOG_AREA.'Skipping item with missing type or quantity during sync.', ['application_id' => $application->id, 'item_data' => $itemData]);

                continue;
            }

            $quantity = (int) $itemData['quantity_requested'];

            $itemId = isset($itemData['id']) && is_numeric($itemData['id']) ? (int) $itemData['id'] : null;
            $itemPayload = [
                'equipment_type' => $itemData['equipment_type'],
                'quantity_requested' => $quantity,
                'notes' => $itemData['notes'] ?? null,
            ];

            if ($itemId && in_array($itemId, $existingItemIds, true)) {
                if ($quantity > 0) {
                    LoanApplicationItem::find($itemId)?->update($itemPayload);
                    $processedItemIds[] = $itemId;
                } else {
                    Log::info(self::LOG_AREA.sprintf('Existing item ID %s submitted with zero quantity, will be removed.', $itemId), ['application_id' => $application->id]);
                }
            } elseif ($quantity > 0) {
                $itemPayloadsToCreate[] = $itemPayload;
            }
        }

        if ($itemPayloadsToCreate !== []) {
            $createdItems = $application->loanApplicationItems()->createMany($itemPayloadsToCreate);
            foreach ($createdItems as $createdItem) {
                $processedItemIds[] = $createdItem->id;
            }
        }

        $idsToDelete = array_diff($existingItemIds, $processedItemIds);
        if ($idsToDelete !== []) {
            $application->loanApplicationItems()->whereIn('id', $idsToDelete)->delete();
            Log::info(self::LOG_AREA.'Removed items no longer in submission or marked for deletion.', ['deleted_ids' => $idsToDelete, 'application_id' => $application->id]);
        }
    }
}
