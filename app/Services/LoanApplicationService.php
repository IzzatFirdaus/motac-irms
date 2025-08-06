<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem as AppLoanTransactionItemModel;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * Create or update a loan application, and optionally submit it for approval.
     * This method combines create/update logic and handles both drafts and submissions.
     *
     * @param array $data The validated data from the form.
     * @param User $actingUser The user performing the action.
     * @param bool $isDraft True if saving as a draft, false for submission.
     * @param LoanApplication|null $existingApplication The application to update, if any.
     * @return LoanApplication
     */
    public function createAndSubmitApplication(array $data, User $actingUser, bool $isDraft, ?LoanApplication $existingApplication = null): LoanApplication
    {
        return DB::transaction(function () use ($data, $actingUser, $isDraft, $existingApplication) {
            $applicationData = $this->prepareApplicationData($data, $actingUser, $isDraft);
            $itemsData = $data['items'];

            if ($existingApplication) {
                $existingApplication->update($applicationData);
                $application = $existingApplication;
                Log::info(self::LOG_AREA . "Updating application ID: {$application->id}", ['is_draft' => $isDraft]);
            } else {
                $application = LoanApplication::create($applicationData);
                Log::info(self::LOG_AREA . "Creating new application.", ['user_id' => $actingUser->id, 'is_draft' => $isDraft]);
            }

            $this->syncLoanApplicationItems($application, $itemsData);

            if (!$isDraft) {
                $this->submitApplicationForApproval($application, $actingUser);
            }

            return $application;
        });
    }

    /**
     * Updates an existing loan application.
     * This is a more specific method used for updating drafts.
     *
     * @param LoanApplication $application The application instance.
     * @param array $data The validated form data.
     * @param User $actingUser The user performing the update.
     * @return LoanApplication
     */
    public function updateApplication(LoanApplication $application, array $data, User $actingUser): LoanApplication
    {
        return $this->createAndSubmitApplication($data, $actingUser, true, $application);
    }

    /**
     * Submits a loan application for the approval process.
     *
     * @param LoanApplication $application The application to submit.
     * @param User $actingUser The user submitting the application.
     * @return LoanApplication
     */
    public function submitApplicationForApproval(LoanApplication $application, User $actingUser): LoanApplication
    {
        // Ensure the application status is correctly set for submission
        $application->status = LoanApplication::STATUS_PENDING_SUPPORT;
        $application->submitted_at = now();
        $application->save();

        // Use the dedicated method to handle approval logic
        $this->processInitialApproval($application);

        return $application;
    }

    /**
     * Prepares the main application data array for creation or update.
     *
     * @param array $data Raw validated data.
     * @param User $user The acting user.
     * @param bool $isDraft If the application is a draft.
     * @return array
     */
    private function prepareApplicationData(array $data, User $user, bool $isDraft): array
    {
        $applicationData = Arr::except($data, ['loan_application_items', 'applicant_confirmation', 'applicant_is_responsible_officer']);
        $applicationData['user_id'] = $user->id;
        $applicationData['status'] = $isDraft ? LoanApplication::STATUS_DRAFT : LoanApplication::STATUS_PENDING_SUPPORT;

        // Set the responsible officer ID based on the checkbox
        $applicationData['responsible_officer_id'] = $data['applicant_is_responsible_officer']
            ? $user->id
            : $data['responsible_officer_id'];

        // Format dates correctly
        $applicationData['loan_start_date'] = Carbon::parse($data['loan_start_date']);
        $applicationData['loan_end_date'] = Carbon::parse($data['loan_end_date']);

        if (!$isDraft) {
            $applicationData['applicant_confirmation_timestamp'] = now();
        }

        return $applicationData;
    }

    /**
     * Find a loan application by its ID with specified relations.
     *
     * @param int $id The ID of the loan application.
     * @param array $relations Optional relations to load.
     * @return LoanApplication
     * @throws ModelNotFoundException
     */
    public function findLoanApplicationById(int $id, array $relations = []): LoanApplication
    {
        $relationsToLoad = array_merge($this->defaultLoanApplicationRelations, $relations);
        $loanApplication = LoanApplication::with($relationsToLoad)->find($id);

        if (! $loanApplication) {
            Log::warning(self::LOG_AREA.sprintf('Loan application with ID %d not found.', $id));
            throw new ModelNotFoundException(sprintf('Loan application with ID %d not found.', $id));
        }

        return $loanApplication;
    }

    /**
     * Get a paginated list of loan applications with filters.
     *
     * @param array $filters Filters for the query (e.g., 'status', 'user_id').
     * @param int $perPage Number of items per page.
     * @return LengthAwarePaginator
     */
    public function getLoanApplications(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = LoanApplication::query()->with($this->defaultLoanApplicationRelations);

        // Apply filters
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Syncs loan application items. Creates new, updates existing, deletes removed.
     *
     * @param LoanApplication $application The loan application.
     * @param array $itemsData The items data from the form.
     */
    private function syncLoanApplicationItems(LoanApplication $application, array $itemsData): void
    {
        $existingItemIds = $application->loanApplicationItems->pluck('id')->toArray();
        $processedItemIds = [];
        $itemPayloadsToCreate = [];

        foreach ($itemsData as $itemData) {
            $quantity = (int) ($itemData['quantity_requested'] ?? 0);
            $itemId = Arr::get($itemData, 'id');

            if ($itemId && in_array($itemId, $existingItemIds, true)) {
                if ($quantity > 0) {
                    LoanApplicationItem::find($itemId)?->update([
                        'equipment_type' => $itemData['equipment_type'],
                        'quantity_requested' => $quantity,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    $processedItemIds[] = $itemId;
                }
            } elseif ($quantity > 0) {
                $itemPayloadsToCreate[] = [
                    'equipment_type' => $itemData['equipment_type'],
                    'quantity_requested' => $quantity,
                    'notes' => $itemData['notes'] ?? null,
                ];
            }
        }

        if (! empty($itemPayloadsToCreate)) {
            $createdItems = $application->loanApplicationItems()->createMany($itemPayloadsToCreate);
            foreach ($createdItems as $createdItem) {
                $processedItemIds[] = $createdItem->id;
            }
        }

        $idsToDelete = array_diff($existingItemIds, $processedItemIds);
        if (! empty($idsToDelete)) {
            $application->loanApplicationItems()->whereIn('id', $idsToDelete)->delete();
            Log::info(self::LOG_AREA.'Removed items no longer in submission.', ['deleted_ids' => $idsToDelete, 'application_id' => $application->id]);
        }
    }

    /**
     * Handles the initial approval process for a loan application.
     *
     * @param LoanApplication $loanApplication
     * @throws RuntimeException
     */
    private function processInitialApproval(LoanApplication $loanApplication): void
    {
        Log::info(self::LOG_AREA.sprintf('Initiating approval process for Loan Application ID %d.', $loanApplication->id));

        // This is now called from submitApplicationForApproval to ensure status is set correctly.
        // Notify relevant parties (e.g., support officers)
        $this->notificationService->notifySupportOfPendingApproval($loanApplication);

        Log::info(self::LOG_AREA.sprintf('Loan Application ID %d status set to %s and support notified.', $loanApplication->id, $loanApplication->status));
    }
}
