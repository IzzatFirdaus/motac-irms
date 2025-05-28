<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\User;
// System Design 5.2
use Illuminate\Auth\Access\AuthorizationException as IlluminateAuthorizationException; // Laravel's standard auth exception
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; // For default acting user ID
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// For sending notifications
use InvalidArgumentException; // Standard PHP exception
use RuntimeException;         // Standard PHP exception

// Catch all throwables

final class LoanApplicationService
{
    private const LOG_AREA = 'LoanApplicationService: ';
    private ApprovalService $approvalService;
    private LoanTransactionService $loanTransactionService;
    private NotificationService $notificationService; // Added for centralized notifications

    // Default relations to eager load for LoanApplication
    private array $defaultLoanApplicationRelations = [
        'user:id,name,email,department_id', // Added email for notifications
        'user.department:id,name',
        'responsibleOfficer:id,name,email',
        'supportingOfficer:id,name,email,grade_id', // Eager load grade for validation
        'supportingOfficer.grade:id,name,level',
        'applicationItems',
        'approvals.officer:id,name',
        'loanTransactions', // Useful for status updates and history
    ];

    public function __construct(
        ApprovalService $approvalService,
        LoanTransactionService $loanTransactionService,
        NotificationService $notificationService // Injected
    ) {
        $this->approvalService = $approvalService;
        $this->loanTransactionService = $loanTransactionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get paginated loan applications for a user or all users (if privileged).
     * System Design Reference: 6.2 User Dashboard (My Applications), Admin views
     */
    public function getApplicationsForUser(User $requestingUser, array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA . 'Fetching loan applications.', ['requesting_user_id' => $requestingUser->id, 'filters' => $filters]);
        $query = LoanApplication::query()->with($this->defaultLoanApplicationRelations);

        // Standardized role names from System Design 8.1
        $isPrivilegedUser = $requestingUser->hasAnyRole(['Admin', 'BPM Staff']); // Ensure 'BPM Staff' is exact role name

        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            if (!$isPrivilegedUser && (int) $filters['user_id'] !== $requestingUser->id) {
                // Non-privileged user trying to access other user's applications - restrict to their own
                $query->where('user_id', $requestingUser->id);
                Log::warning(self::LOG_AREA."Unauthorized attempt to filter by user_id.", ['requesting_user_id' => $requestingUser->id, 'target_user_id' => $filters['user_id']]);
            } else {
                $query->where('user_id', (int) $filters['user_id']);
            }
        } elseif (!$isPrivilegedUser) {
            // Default for non-privileged users: only their own applications
            $query->where('user_id', $requestingUser->id);
        }
        // Else (privileged user and no user_id filter): show all applications (or based on other filters)

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            if (in_array($filters['status'], LoanApplication::getStatusKeys(), true)) {
                $query->where('status', $filters['status']);
            } else {
                Log::warning(self::LOG_AREA."Invalid status filter ignored.", ['status_filter' => $filters['status']]);
            }
        }
        if ($isPrivilegedUser && !empty($filters['supporting_officer_id'])) {
            $query->where('supporting_officer_id', (int) $filters['supporting_officer_id']);
        }
        if (!empty($filters['search_term'])) {
            $term = '%' . $filters['search_term'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', $term) // Search by ID
                  ->orWhere('purpose', 'like', $term)
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $term));
            });
        }

        $orderBy = $filters['order_by'] ?? 'updated_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        if (!in_array($orderBy, ['created_at', 'updated_at', 'loan_start_date', 'status'])) {
            $orderBy = 'updated_at';
        }
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }

    /**
     * Create and submit a new loan application.
     * System Design Reference: 5.2 ICT Equipment Loan Workflow.
     * @param array{responsible_officer_id?: int|null, supporting_officer_id: int, purpose: string, location: string, return_location?: string|null, loan_start_date: string, loan_end_date: string, items: list<array{equipment_type: string, quantity_requested: int, notes?: string|null}>, applicant_confirmation: bool} $validatedData
     * @throws InvalidArgumentException | ModelNotFoundException | RuntimeException
     */
    public function createAndSubmitApplication(array $validatedData, User $applicant): LoanApplication
    {
        $applicantId = $applicant->id;
        Log::info(self::LOG_AREA . "Creating and submitting loan application.", ['user_id' => $applicantId]);

        if (empty($validatedData['items'])) {
            throw new InvalidArgumentException(__('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));
        }
        if (empty($validatedData['supporting_officer_id'])) {
            throw new InvalidArgumentException(__('Pegawai Penyokong mesti dipilih untuk menghantar permohonan.'));
        }
        if (empty($validatedData['applicant_confirmation']) || $validatedData['applicant_confirmation'] !== true) { // Stricter check
            throw new InvalidArgumentException(__('Perakuan pemohon mesti diterima sebelum penghantaran.'));
        }

        /** @var User $supportingOfficer */
        $supportingOfficer = User::with('grade:id,name,level')->find($validatedData['supporting_officer_id']);
        if (!$supportingOfficer) {
            throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih tidak sah.'));
        }

        // Validate Supporting Officer's Grade - System Design 3.3, 7.2
        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
        if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) {
            Log::warning(self::LOG_AREA."Supporting Officer Grade Check Failed.", ['officer_id' => $supportingOfficer->id, 'officer_grade_level' => $supportingOfficer->grade?->level, 'required_level' => $minSupportGradeLevel, 'application_user_id' => $applicantId]);
            throw new InvalidArgumentException(__("Pegawai Penyokong yang dipilih (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade", [
                'name' => $supportingOfficer->name,
                'minGrade' => $minSupportGradeLevel, // Consider fetching grade name for $minSupportGradeLevel for better message
                'currentGrade' => $supportingOfficer->grade?->name ?? __('Tidak Dinyatakan')
               ]));
        }

        return DB::transaction(function () use ($validatedData, $applicant, $supportingOfficer, $applicantId) {
            $applicationData = Arr::except($validatedData, ['items', 'applicant_confirmation']);
            $applicationData['user_id'] = $applicantId;
            $applicationData['responsible_officer_id'] = $validatedData['responsible_officer_id'] ?? $applicantId;
            $applicationData['status'] = LoanApplication::STATUS_DRAFT; // Initially draft, then transitioned
            $applicationData['applicant_confirmation_timestamp'] = now(); // Set confirmation now

            /** @var LoanApplication $application */
            $application = LoanApplication::create($applicationData);

            foreach ($validatedData['items'] as $item) {
                $application->applicationItems()->create([
                    'equipment_type' => $item['equipment_type'],
                    'quantity_requested' => (int) $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $application->transitionToStatus(LoanApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar untuk semakan pegawai penyokong.'), $applicantId);
            $this->approvalService->initiateApprovalWorkflow($application, $applicant, Approval::STAGE_LOAN_SUPPORT_REVIEW, $supportingOfficer);

            // Send Notification - System Design 5.2
            $this->notificationService->notifyApplicantApplicationSubmitted($application);

            Log::info(self::LOG_AREA . 'Loan application created and submitted successfully.', ['application_id' => $application->id]);
            return $application->fresh($this->defaultLoanApplicationRelations);
        });
    }

    /**
     * Submits an existing draft or rejected loan application for approval.
     * System Design Reference: 5.2 ICT Equipment Loan Workflow (resubmission implicitly part of workflow).
     * @throws IlluminateAuthorizationException | RuntimeException | InvalidArgumentException
     */
    public function submitApplicationForApproval(LoanApplication $application, User $submitter): LoanApplication
    {
        if ((int) $application->user_id !== (int) $submitter->id && !$submitter->can('submit', $application)) { // Policy check for submission
            throw new IlluminateAuthorizationException(__('Anda tidak mempunyai kebenaran untuk menghantar permohonan ini.'));
        }
        if (!in_array($application->status, [LoanApplication::STATUS_DRAFT, LoanApplication::STATUS_REJECTED])) {
            throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dihantar semula. Status semasa: :status', ['status' => $application->statusTranslated]));
        }
        if (empty($application->supporting_officer_id)) {
            throw new RuntimeException(__('Pegawai Penyokong mesti ditetapkan sebelum permohonan boleh dihantar. Sila kemaskini draf.'));
        }
        if (empty($application->applicant_confirmation_timestamp)) {
            throw new RuntimeException(__('Perakuan pemohon mesti diterima sebelum penghantaran. Sila kemaskini draf dan sahkan perakuan.'));
        }

        /** @var User $supportingOfficer */
        $supportingOfficer = User::with('grade:id,name,level')->findOrFail($application->supporting_officer_id);
        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
        if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) {
            throw new InvalidArgumentException(__("Pegawai Penyokong yang ditetapkan (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade", [
                 'name' => $supportingOfficer->name,
                 'minGrade' => $minSupportGradeLevel,
                 'currentGrade' => $supportingOfficer->grade?->name ?? __('Tidak Dinyatakan')
                ]));
        }

        Log::info(self::LOG_AREA . "Resubmitting LoanApplication.", ['application_id' => $application->id, 'user_id' => $submitter->id]);
        return DB::transaction(function () use ($application, $submitter, $supportingOfficer) {
            $application->transitionToStatus(LoanApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar semula untuk semakan pegawai penyokong.'), $submitter->id);

            // Check for existing PENDING approval task for this stage and officer to avoid duplicates
            $existingPendingSupportApproval = $application->approvals()
                ->where('stage', Approval::STAGE_LOAN_SUPPORT_REVIEW)
                ->where('status', Approval::STATUS_PENDING)
                ->where('officer_id', $supportingOfficer->id)
                ->first();

            if (!$existingPendingSupportApproval) {
                $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_LOAN_SUPPORT_REVIEW, $supportingOfficer);
            } else {
                // Re-notify if task already exists but application is resubmitted
                Log::info(self::LOG_AREA . "Existing pending approval task found for resubmission.", ['approval_id' => $existingPendingSupportApproval->id]);
                $this->notificationService->notifyApproverApplicationNeedsAction($existingPendingSupportApproval, $application, $supportingOfficer);
            }

            $this->notificationService->notifyApplicantApplicationSubmitted($application); // Notify applicant of resubmission

            Log::info(self::LOG_AREA . "LoanApplication resubmitted successfully.", ['application_id' => $application->id, 'status' => $application->status]);
            return $application->fresh($this->defaultLoanApplicationRelations);
        });
    }

    /**
     * Update an existing loan application, typically in draft state.
     * System Design Reference: 3.1 Controllers (LoanApplicationController handles updates).
     * @param array{responsible_officer_id?: int|null, supporting_officer_id?: int|null, purpose?: string, location?: string, return_location?: string|null, loan_start_date?: string, loan_end_date?: string, items?: list<array{id?: int|null, equipment_type: string, quantity_requested: int, notes?: string|null, _delete?: bool}>, applicant_confirmation?:bool} $validatedData
     * @throws ModelNotFoundException | InvalidArgumentException | RuntimeException
     */
    public function updateApplication(LoanApplication $application, array $validatedData, User $user): LoanApplication
    {
        Log::info(self::LOG_AREA . "Updating loan application.", ['application_id' => $application->id, 'user_id' => $user->id]);

        // Policy should handle if user can update (e.g., only owner of draft)

        if (isset($validatedData['supporting_officer_id']) && (int)$validatedData['supporting_officer_id'] !== (int)$application->supporting_officer_id) {
            /** @var User|null $newSupportingOfficer */
            $newSupportingOfficer = User::with('grade:id,name,level')->find($validatedData['supporting_officer_id']);
            if (!$newSupportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih untuk kemaskini tidak sah.'));
            }
            $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
            if (!$newSupportingOfficer->grade || (int) $newSupportingOfficer->grade->level < $minSupportGradeLevel) {
                throw new InvalidArgumentException(__("Pegawai Penyokong baharu yang dipilih (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade", [
                    'name' => $newSupportingOfficer->name,
                    'minGrade' => $minSupportGradeLevel,
                    'currentGrade' => $newSupportingOfficer->grade?->name ?? __('Tidak Dinyatakan')
                ]));
            }
        }

        return DB::transaction(function () use ($application, $validatedData, $user) {
            $applicationDataToFill = Arr::except($validatedData, ['items', 'applicant_confirmation']);
            $application->fill($applicationDataToFill);

            if (array_key_exists('applicant_confirmation', $validatedData)) { // Check if key exists to allow unchecking
                if ($validatedData['applicant_confirmation'] === true && !$application->applicant_confirmation_timestamp) {
                    $application->applicant_confirmation_timestamp = now();
                } elseif ($validatedData['applicant_confirmation'] === false) {
                    $application->applicant_confirmation_timestamp = null;
                }
            }
            $application->save();

            if (isset($validatedData['items']) && is_array($validatedData['items'])) {
                $this->syncApplicationItems($application, $validatedData['items']);
            }
            Log::info(self::LOG_AREA . "Loan application updated successfully.", ['application_id' => $application->id]);
            return $application->fresh($this->defaultLoanApplicationRelations);
        });
    }

    /**
     * Soft delete a loan application and its related items/approvals.
     * System Design Reference: 3.1 Controllers (LoanApplicationController handles deletions).
     * @throws IlluminateAuthorizationException | RuntimeException
     */
    public function deleteApplication(LoanApplication $application, User $user): bool
    {
        // Policy check ($user->can('delete', $application)) should be done in controller
        Log::info(self::LOG_AREA . "Attempting to delete loan application.", ['application_id' => $application->id, 'user_id' => $user->id]);
        return DB::transaction(function () use ($application) {
            // Soft delete related items first to trigger their observers if any, or handle cascades
            $application->applicationItems()->delete(); // Assumes LoanApplicationItem uses SoftDeletes
            $application->approvals()->delete();       // Assumes Approval uses SoftDeletes
            $deleted = $application->delete();         // Soft delete the application itself

            if ($deleted) {
                Log::info(self::LOG_AREA . "Loan application and related data soft deleted.", ['application_id' => $application->id]);
            } else {
                Log::warning(self::LOG_AREA . "Soft delete returned false for loan application.", ['application_id' => $application->id]);
            }
            return (bool) $deleted;
        });
    }

    /**
     * Create an issue transaction for a loan application.
     * Delegates to LoanTransactionService.
     * System Design Reference: 5.2 Equipment Issuance.
     * @param array<array{loan_application_item_id: int, equipment_id: int, quantity_issued: int, issue_item_notes?: string|null, accessories_checklist_item?: array|null}> $itemsDetails
     * @param array{receiving_officer_id: int, transaction_date?: string, issue_notes?: string|null} $transactionDetails
     * @throws RuntimeException | InvalidArgumentException
     */
    public function createIssueTransaction(LoanApplication $loanApplication, array $itemsDetails, User $issuingOfficer, array $transactionDetails): LoanTransaction
    {
        $appIdLog = $loanApplication->id;
        Log::info(self::LOG_AREA . "Creating issue transaction.", ['application_id' => $appIdLog, 'issuing_officer_id' => $issuingOfficer->id]);

        if (!in_array($loanApplication->status, [LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_PARTIALLY_ISSUED])) {
            throw new RuntimeException(__("Peralatan hanya boleh dikeluarkan untuk permohonan yang telah diluluskan atau separa dikeluarkan. Status semasa: :status", ['status' => $loanApplication->statusTranslated]));
        }
        if (empty($itemsDetails)) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dikeluarkan dalam transaksi ini.'));
        }
        if (empty($transactionDetails['receiving_officer_id'])) {
            throw new InvalidArgumentException(__('Pegawai Penerima mesti dinyatakan.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            if (empty($item['equipment_id']) || empty($item['loan_application_item_id']) || !isset($item['quantity_issued']) || (int)$item['quantity_issued'] <= 0) {
                throw new InvalidArgumentException(__('Butiran item pengeluaran tidak lengkap atau kuantiti tidak sah.'));
            }
            $serviceItemData[] = [
                'equipment_id' => (int) $item['equipment_id'],
                'loan_application_item_id' => (int) $item['loan_application_item_id'],
                'quantity' => (int) $item['quantity_issued'],
                'notes' => $item['issue_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? [], // Default to empty array
            ];
        }

        $extraServiceDetails = [
            'receiving_officer_id' => (int) $transactionDetails['receiving_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(),
            'issue_notes' => $transactionDetails['issue_notes'] ?? null,
            'status' => LoanTransaction::STATUS_ISSUED, // Default status for a new issue
        ];

        return $this->loanTransactionService->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_ISSUE,
            $issuingOfficer,
            $serviceItemData,
            $extraServiceDetails
        );
    }

    /**
     * Create a return transaction, typically related to a previous issue transaction.
     * Delegates to LoanTransactionService.
     * System Design Reference: 5.2 Equipment Return Process.
     * @param array<array{loan_transaction_item_id: int, equipment_id: int, quantity_returned: int, condition_on_return: string, item_status_on_return: string, return_item_notes?: string|null, accessories_checklist_item?: array|null}> $itemsDetails
     * @param array{returning_officer_id: int, transaction_date?: string, return_notes?: string|null} $transactionDetails
     * @throws RuntimeException | InvalidArgumentException | ModelNotFoundException
     */
    public function createReturnTransaction(LoanTransaction $issueTransaction, array $itemsDetails, User $returnAcceptingOfficer, array $transactionDetails): LoanTransaction
    {
        $loanApplication = $issueTransaction->loanApplication()->firstOrFail(); // Ensure LA exists
        Log::info(self::LOG_AREA . "Creating return transaction.", ['loan_application_id' => $loanApplication->id, 'issue_transaction_id' => $issueTransaction->id, 'accepting_officer_id' => $returnAcceptingOfficer->id]);

        if (empty($itemsDetails)) {
            throw new InvalidArgumentException(__('Tiada item peralatan untuk dipulangkan dalam transaksi ini.'));
        }
        if (empty($transactionDetails['returning_officer_id'])) {
            throw new InvalidArgumentException(__('Pegawai Yang Memulangkan mesti dinyatakan.'));
        }

        $serviceItemData = [];
        foreach ($itemsDetails as $item) {
            if (empty($item['equipment_id']) || empty($item['loan_transaction_item_id']) || !isset($item['quantity_returned']) || (int)$item['quantity_returned'] <= 0 || empty($item['condition_on_return']) || empty($item['item_status_on_return'])) {
                throw new InvalidArgumentException(__('Butiran item pemulangan tidak lengkap, kuantiti tidak sah, atau status/keadaan tidak dinyatakan.'));
            }
            /** @var \App\Models\LoanTransactionItem $originalIssuedItem */
            //$originalIssuedItem = AppLoanTransactionItemModel::findOrFail($item['loan_transaction_item_id']);
            if ($originalIssuedItem->loan_transaction_id !== $issueTransaction->id || (int)$originalIssuedItem->equipment_id !== (int)$item['equipment_id']) {
                throw new InvalidArgumentException(__("Item rujukan pengeluaran (ID: :itemRefId) tidak sepadan atau tidak sah untuk item peralatan (ID: :eqId).", ['itemRefId' => $item['loan_transaction_item_id'], 'eqId' => $item['equipment_id']]));
            }

            $serviceItemData[] = [
                'equipment_id' => (int) $item['equipment_id'],
                'original_loan_transaction_item_id' => (int) $item['loan_transaction_item_id'],
                'loan_application_item_id' => $originalIssuedItem->loan_application_item_id, // Get from original item
                'quantity' => (int) $item['quantity_returned'],
                'condition_on_return' => $item['condition_on_return'],
                'item_status_on_return' => $item['item_status_on_return'], // From LoanTransactionItem statuses
                'notes' => $item['return_item_notes'] ?? null,
                'accessories_data' => $item['accessories_checklist_item'] ?? [],
            ];
        }

        $extraServiceDetails = [
            'returning_officer_id' => (int) $transactionDetails['returning_officer_id'],
            'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(),
            'return_notes' => $transactionDetails['return_notes'] ?? null,
            'related_transaction_id' => $issueTransaction->id, // Link to the issue transaction
            'status' => LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION, // Default status for new return
        ];

        return $this->loanTransactionService->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_RETURN,
            $returnAcceptingOfficer, // The BPM staff accepting the return
            $serviceItemData,
            $extraServiceDetails
        );
    }

    /**
     * Get summary of loan applications that are currently 'issued', 'partially_issued', or 'overdue'.
     * System Design Reference: Admin/BPM dashboards might need this (e.g., OutstandingLoans LW component).
     */
    public function getActiveLoansSummary(array $filters = []): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA . 'Fetching summary of active loan applications.', ['filters' => $filters]);
        $query = LoanApplication::query()
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
                LoanApplication::STATUS_OVERDUE
            ])
            ->with($this->defaultLoanApplicationRelations); // Eager load details

        if (!empty($filters['search_term'])) {
            $term = '%' . $filters['search_term'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', $term)
                  ->orWhere('purpose', 'like', $term)
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $term))
                  ->orWhereHas('responsibleOfficer', fn ($roq) => $roq->where('name', 'like', $term));
            });
        }
        // Add other filters as needed (e.g., by department, by due date range for overdue)
        $orderBy = $filters['order_by'] ?? 'loan_end_date'; // Default sort by loan_end_date for active loans
        $orderDirection = $filters['order_direction'] ?? 'asc'; // Ascending to see soonest due/overdue

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }

    /**
     * Find a specific loan application by ID with specified relations.
     */
    public function findLoanApplicationById(int $id, array $with = []): ?LoanApplication
    {
        Log::debug(self::LOG_AREA . "Finding loan application.", ['id' => $id, 'with_relations' => $with]);
        $relationsToLoad = !empty($with) ? array_unique(array_merge($this->defaultLoanApplicationRelations, $with)) : $this->defaultLoanApplicationRelations;

        /** @var LoanApplication|null $application */
        $application = LoanApplication::with($relationsToLoad)->find($id);

        if (!$application) {
            Log::notice(self::LOG_AREA . "Loan application not found.", ['id' => $id]);
        }
        return $application;
    }

    /**
     * Syncs application items for a loan application.
     * @param LoanApplication $application The parent loan application.
     * @param list<array{id?:int|null, equipment_type:string, quantity_requested:int, notes?:string|null, _delete?:bool}> $itemsData Array of item data.
     */
    protected function syncApplicationItems(LoanApplication $application, array $itemsData): void
    {
        $existingItemIds = $application->applicationItems()->pluck('id')->all();
        $processedItemIds = [];

        foreach ($itemsData as $itemData) {
            if (empty($itemData['equipment_type']) || !isset($itemData['quantity_requested'])) {
                Log::warning(self::LOG_AREA . "Skipping item with missing type or quantity during sync.", ['application_id' => $application->id, 'item_data' => $itemData]);
                continue;
            }
            $quantity = (int) $itemData['quantity_requested'];
            if ($quantity <= 0 && empty($itemData['id'])) { // Don't add new items with zero quantity
                Log::warning(self::LOG_AREA . "Skipping new item with zero/negative quantity.", ['application_id' => $application->id, 'item_data' => $itemData]);
                continue;
            }


            $itemId = isset($itemData['id']) && is_numeric($itemData['id']) ? (int)$itemData['id'] : null;
            $itemPayload = [
                'equipment_type' => $itemData['equipment_type'],
                'quantity_requested' => $quantity,
                'notes' => $itemData['notes'] ?? null,
                // 'quantity_approved' might be set later by an approver, not typically during draft update by user
            ];

            if ($itemId && in_array($itemId, $existingItemIds, true)) { // Existing item
                if (!empty($itemData['_delete']) || $quantity <= 0) { // Mark for deletion or quantity is zero
                    LoanApplicationItem::find($itemId)?->delete(); // Soft delete
                    Log::info(self::LOG_AREA . "Item marked for deletion or quantity zeroed.", ['item_id' => $itemId, 'application_id' => $application->id]);
                } else {
                    LoanApplicationItem::find($itemId)?->update($itemPayload);
                    $processedItemIds[] = $itemId;
                }
            } elseif (empty($itemData['_delete']) && $quantity > 0) { // New item to add (and not marked for deletion)
                $createdItem = $application->applicationItems()->create($itemPayload);
                $processedItemIds[] = $createdItem->id;
            }
        }
        // Delete items that were present before but not in the new $itemsData (and not processed for update)
        $idsToDelete = array_diff($existingItemIds, $processedItemIds);
        if (!empty($idsToDelete)) {
            $application->applicationItems()->whereIn('id', $idsToDelete)->delete(); // Soft delete
            Log::info(self::LOG_AREA . "Removed items no longer in submission.", ['deleted_ids' => $idsToDelete, 'application_id' => $application->id]);
        }
    }
}
