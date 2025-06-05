<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem as AppLoanTransactionItemModel; // Explicit import
use App\Models\User;
// No new notification class is imported for this option
use Illuminate\Auth\Access\AuthorizationException as IlluminateAuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // Ensure Schema is imported
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class LoanApplicationService
{
  private const LOG_AREA = 'LoanApplicationService: ';
  private ApprovalService $approvalService;
  private LoanTransactionService $loanTransactionService;
  private NotificationService $notificationService;

  private array $defaultLoanApplicationRelations = [
    'user:id,name,email,department_id,position_id,grade_id', // [cite: 73]
    'user.department:id,name', // [cite: 73]
    'user.position:id,name', // [cite: 73]
    'user.grade:id,name', // [cite: 73]
    'responsibleOfficer:id,name,email', // [cite: 73]
    'supportingOfficer:id,name,email,grade_id', // [cite: 73]
    'supportingOfficer.grade:id,name,level', // [cite: 73]
    'loanApplicationItems', // [cite: 73]
    'approvals.officer:id,name', // [cite: 73]
    'loanTransactions.issuingOfficer:id,name', // [cite: 73]
    'loanTransactions.receivingOfficer:id,name', // [cite: 73]
    'loanTransactions.returningOfficer:id,name', // [cite: 73]
    'loanTransactions.returnAcceptingOfficer:id,name', // [cite: 73]
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
    Log::debug(self::LOG_AREA . 'Fetching loan applications.', ['requesting_user_id' => $requestingUser->id, 'filters' => $filters]);
    $query = LoanApplication::query()->with($this->defaultLoanApplicationRelations);

    $isPrivilegedUser = $requestingUser->hasAnyRole(['Admin', 'BPM Staff']); // [cite: 8]

    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
      if (!$isPrivilegedUser && (int) $filters['user_id'] !== $requestingUser->id) {
        $query->where('user_id', $requestingUser->id);
        Log::warning(self::LOG_AREA . "Unauthorized attempt to filter by user_id, restricted to own.", ['requesting_user_id' => $requestingUser->id, 'target_user_id' => $filters['user_id']]);
      } else {
        $query->where('user_id', (int) $filters['user_id']);
      }
    } elseif (!$isPrivilegedUser) {
      $query->where('user_id', $requestingUser->id);
    }

    if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
      if (in_array($filters['status'], LoanApplication::getStatusKeys(), true)) { // [cite: 110, 111]
        $query->where('status', $filters['status']);
      } else {
        Log::warning(self::LOG_AREA . "Invalid status filter ignored.", ['status_filter' => $filters['status']]);
      }
    }
    if ($isPrivilegedUser && !empty($filters['supporting_officer_id'])) { // [cite: 109]
      $query->where('supporting_officer_id', (int) $filters['supporting_officer_id']);
    }
    if (!empty($filters['search_term'])) {
      $term = '%' . $filters['search_term'] . '%';
      $query->where(function ($q) use ($term) {
        $q->where('id', 'like', $term)
          ->orWhere('purpose', 'like', $term) // [cite: 109]
          ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', $term));
      });
    }

    $orderBy = $filters['order_by'] ?? 'updated_at';
    $orderDirection = $filters['order_direction'] ?? 'desc';
    $validOrderBy = ['id', 'created_at', 'updated_at', 'loan_start_date', 'status', 'purpose']; // [cite: 109]
    if (!in_array($orderBy, $validOrderBy)) {
      $orderBy = 'updated_at';
    }
    if (!in_array($orderDirection, ['asc', 'desc'])) {
      $orderDirection = 'desc';
    }

    $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
    return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
  }

  public function createAndSubmitApplication(array $validatedData, User $applicant, bool $saveAsDraftOnly = false): LoanApplication
  {
    $applicantId = $applicant->id;
    Log::info(self::LOG_AREA . "Processing create application request.", [
      'user_id' => $applicantId,
      'data_keys' => array_keys($validatedData),
      'save_as_draft_only' => $saveAsDraftOnly
    ]);

    if (empty($validatedData['items'])) { // [cite: 39]
      throw new InvalidArgumentException(__('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));
    }

    if (!$saveAsDraftOnly && empty($validatedData['applicant_confirmation'])) { // [cite: 109]
      throw new InvalidArgumentException(__('Perakuan pemohon mesti diterima sebelum penghantaran.'));
    }

    return DB::transaction(function () use ($validatedData, $applicant, $applicantId, $saveAsDraftOnly) {
      $applicationModelData = [
        'user_id' => $applicantId, // [cite: 109]
        'responsible_officer_id' => $validatedData['responsible_officer_id'] ?? $applicantId, // [cite: 109]
        'purpose' => $validatedData['purpose'], // [cite: 109]
        'location' => $validatedData['location'], // [cite: 109]
        'return_location' => $validatedData['return_location'], // [cite: 109]
        'loan_start_date' => $validatedData['loan_start_date'], // [cite: 109]
        'loan_end_date' => $validatedData['loan_end_date'], // [cite: 109]
        'status' => LoanApplication::STATUS_DRAFT, // [cite: 110]
        'supporting_officer_id' => $validatedData['supporting_officer_id'] ?? null, // [cite: 109]
      ];

      if (isset($validatedData['applicant_phone']) && Schema::hasColumn('loan_applications', 'applicant_phone')) {
        $applicationModelData['applicant_phone'] = $validatedData['applicant_phone'];
      }

      if (!$saveAsDraftOnly && ($validatedData['applicant_confirmation'] ?? false)) { // [cite: 109]
        $applicationModelData['applicant_confirmation_timestamp'] = now(); // [cite: 109]
      } else {
        $applicationModelData['applicant_confirmation_timestamp'] = null; // [cite: 109]
      }

      /** @var LoanApplication $application */
      $application = LoanApplication::create($applicationModelData);

      if (!empty($validatedData['items'])) {
        foreach ($validatedData['items'] as $item) {
          $application->loanApplicationItems()->create([ // [cite: 39]
            'equipment_type' => $item['equipment_type'], // [cite: 111]
            'quantity_requested' => (int) $item['quantity_requested'], // [cite: 111]
            'notes' => $item['notes'] ?? null, // [cite: 111]
          ]);
        }
      }

      if ($saveAsDraftOnly) {
        Log::info(self::LOG_AREA . 'Loan application created as draft.', ['application_id' => $application->id]);
      } else {
        Log::info(self::LOG_AREA . 'Loan application created (status draft), ready for submission.', ['application_id' => $application->id]);
      }

      return $application->fresh($this->defaultLoanApplicationRelations);
    });
  }

  /**
   * Submits or resubmits a loan application for the approval process.
   *
   * @param LoanApplication $application The loan application to submit.
   * @param User $submitter The user submitting the application.
   * @return LoanApplication The updated loan application.
   * @throws RuntimeException If the application is not in a submittable state or if prerequisites are not met.
   * @throws ModelNotFoundException If a required related model (e.g., officer) is not found.
   * @throws InvalidArgumentException If data validation for officers fails (e.g., grade level).
   */
  public function submitApplicationForApproval(LoanApplication $application, User $submitter): LoanApplication
  {
    // ##### START: MODIFIED FOR TESTING PURPOSES #####
    // This section identifies a specific "Approver" user (approver@motac.gov.my from AdminUserSeeder).
    // The intention is to guide the approval workflow towards this specific user for the
    // "Approver Review" stage, especially after the initial "Supporting Officer" approves.
    // This helps create a predictable approval chain for testing.

    /** @var User|null $designatedTestApprover */
    $designatedTestApprover = User::role('Approver') // Ensure 'Approver' role exists and is assigned
      ->where('email', 'approver@motac.gov.my') // Specific user from seeder
      ->where('status', User::STATUS_ACTIVE)    // Ensure user is active
      ->first();

    if (!$designatedTestApprover) {
      Log::critical(self::LOG_AREA . "DESIGNATED TEST APPROVER (approver@motac.gov.my with 'Approver' role and active status) NOT FOUND. This user is CRUCIAL for the current testing setup of the approval flow. Please ensure AdminUserSeeder has run correctly, and this user exists, is active, and has the 'Approver' role.");
      // In a real scenario, you might notify admins or prevent submission. For testing, throwing an exception is appropriate.
      throw new RuntimeException('Designated test approver (approver@motac.gov.my) not found. Critical for testing approval flow. Please contact an administrator.');
    }
    Log::info(self::LOG_AREA . "Designated Test Approver for STAGE_LOAN_APPROVER_REVIEW identified: User ID {$designatedTestApprover->id} ({$designatedTestApprover->email}).");
    // ##### END: MODIFIED FOR TESTING PURPOSES #####

    // Standard checks for application state
    if (!in_array($application->status, [LoanApplication::STATUS_DRAFT, LoanApplication::STATUS_REJECTED])) { // [cite: 110]
      throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dihantar semula. Status semasa: :status', ['status' => $application->status_label]));
    }

    if (empty($application->applicant_confirmation_timestamp)) { // [cite: 109]
      throw new RuntimeException(__('Perakuan pemohon mesti diterima sebelum penghantaran. Sila kemaskini draf dan sahkan perakuan.'));
    }

    // Validate selected Supporting Officer (if any)
    $supportingOfficer = null;
    if ($application->supporting_officer_id) { // [cite: 109]
      /** @var User|null $supportingOfficer */
      $supportingOfficer = User::with('grade:id,name,level')->find($application->supporting_officer_id);
      if (!$supportingOfficer) { // Added check if supporting officer is not found
        Log::error(self::LOG_AREA . "Selected Supporting Officer not found for ID: " . $application->supporting_officer_id . " on LoanApplication ID: " . $application->id);
        throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih untuk permohonan ini tidak ditemui dalam sistem.'));
      }
      if ($supportingOfficer->status !== User::STATUS_ACTIVE) { // Added check for active status [cite: 94]
        Log::error(self::LOG_AREA . "Selected Supporting Officer (ID: {$supportingOfficer->id}, Email: {$supportingOfficer->email}) for LoanApplication ID: {$application->id} is not active.");
        throw new InvalidArgumentException(__('Pegawai Penyokong yang dipilih tidak aktif. Sila pilih pegawai lain.'));
      }

      $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41); // [cite: 86]
      if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) { // [cite: 100]
        throw new InvalidArgumentException(__("Pegawai Penyokong yang ditetapkan (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade", [ // [cite: 153]
          'name' => $supportingOfficer->name,
          'minGrade' => $minSupportGradeLevel,
          'currentGrade' => $supportingOfficer->grade?->name ?? __('Tidak Dinyatakan')
        ]));
      }
      Log::info(self::LOG_AREA . "Validated Supporting Officer for LoanApplication ID {$application->id}: User ID {$supportingOfficer->id} ({$supportingOfficer->email}).");
    } else {
      Log::info(self::LOG_AREA . "LoanApplication ID {$application->id} is being submitted without an applicant-selected Supporting Officer. It will proceed directly to STAGE_LOAN_APPROVER_REVIEW with the Designated Test Approver.");
    }

    Log::info(self::LOG_AREA . "Attempting to submit/resubmit LoanApplication ID: {$application->id} for approval by User ID: {$submitter->id}.");

    return DB::transaction(function () use ($application, $submitter, $supportingOfficer, $designatedTestApprover) {
      // Determine the initial status based on whether a supporting officer was selected by the applicant
      $nextStatus = $supportingOfficer ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_PENDING_APPROVER_REVIEW; // [cite: 110]

      $application->status = $nextStatus; // [cite: 109]
      $application->submitted_at = now(); // [cite: 109]
      $application->rejection_reason = null; // [cite: 109]
      $application->rejected_by = null; // [cite: 109]
      $application->rejected_at = null; // [cite: 109]

      // ##### START: MODIFIED FOR TESTING PURPOSES #####
      // If a custom field 'approver_id' exists on the loan_applications table,
      // this attempts to set it to the ID of the $designatedTestApprover.
      // This is based on the assumption that the ApprovalService MIGHT look at such a field
      // to determine the officer for subsequent approval stages (e.g., STAGE_LOAN_APPROVER_REVIEW
      // after STAGE_LOAN_SUPPORT_REVIEW is completed).
      // If this field does not exist or is not used by ApprovalService for this purpose,
      // this specific assignment will not influence the problematic routing.
      // The primary fix for testing is that if no supporting officer is selected,
      // the flow goes directly to the $designatedTestApprover.
      // Note: The System Design document does not list 'approver_id' for 'loan_applications' table .
      // It lists 'approved_by', 'supporting_officer_id', 'current_approval_officer_id'.
      if (Schema::hasColumn($application->getTable(), 'approver_id')) {
        $application->approver_id = $designatedTestApprover->id;
        Log::info(self::LOG_AREA . "TESTING - Custom field 'approver_id' on LoanApplication ID: {$application->id} was set to designated test approver ID: {$designatedTestApprover->id}.");
      } else {
        Log::info(self::LOG_AREA . "TESTING - Custom field 'approver_id' does not exist on '{$application->getTable()}' table. Cannot prime designated approver via this field.");
      }
      // ##### END: MODIFIED FOR TESTING PURPOSES #####

      $application->save();

      $approvalTask = null;
      $officerForFirstApprovalTask = null;

      if ($supportingOfficer) {
        // Case 1: A supporting officer was selected by the applicant.
        // The first approval task is for STAGE_LOAN_SUPPORT_REVIEW, assigned to this supporting officer.
        $officerForFirstApprovalTask = $supportingOfficer;
        Log::info(self::LOG_AREA . "Initiating STAGE_LOAN_SUPPORT_REVIEW for LoanApplication ID: {$application->id} with Officer ID: {$officerForFirstApprovalTask->id}.");
        $approvalTask = $this->approvalService->initiateApprovalWorkflow( // [cite: 141]
          $application,
          $submitter,
          Approval::STAGE_LOAN_SUPPORT_REVIEW, // [cite: 120]
          $officerForFirstApprovalTask
        );
        // FOR TESTING: After this $supportingOfficer approves, the expectation is that
        // the approval workflow (managed by ApprovalService) should transition to the $designatedTestApprover
        // for the STAGE_LOAN_APPROVER_REVIEW. This relies on how ApprovalService determines the next approver.

      } else {
        // Case 2: No supporting officer was selected by the applicant.
        // The application should go directly to STAGE_LOAN_APPROVER_REVIEW,
        // assigned to the $designatedTestApprover for testing.
        $officerForFirstApprovalTask = $designatedTestApprover;
        Log::info(self::LOG_AREA . "No supporting officer selected. Initiating STAGE_LOAN_APPROVER_REVIEW directly for LoanApplication ID: {$application->id} with Designated Test Approver ID: {$officerForFirstApprovalTask->id}.");

        // We already confirmed $designatedTestApprover exists.
        $approvalTask = $this->approvalService->initiateApprovalWorkflow( // [cite: 141]
          $application,
          $submitter,
          Approval::STAGE_LOAN_APPROVER_REVIEW, // Assuming this stage exists, e.g., mapped from 'Kelulusan Pegawai Pelulus (Pinjaman)'
          $officerForFirstApprovalTask
        );
      }

      // Send notifications based on the first approval task created.
      $this->notificationService->notifyApplicantApplicationSubmitted($application); // [cite: 156]
      if ($approvalTask && $approvalTask->officer) {
        $this->notificationService->notifyApproverApplicationNeedsAction($approvalTask, $application, $approvalTask->officer); // [cite: 156]
      } elseif ($officerForFirstApprovalTask) {
        Log::warning(self::LOG_AREA . "Approval task was not created for LoanApplication ID: {$application->id}, although an officer (ID: {$officerForFirstApprovalTask->id}) was designated for the first task. Check ApprovalService logs.");
      } else {
        Log::warning(self::LOG_AREA . "No approval task was created and no officer was designated for the first task for LoanApplication ID: {$application->id}. This might indicate an issue in workflow logic or officer selection.");
        // Consider notifying admin about an application that couldn't enter workflow.
        // The original code had more detailed admin notification here for orphaned applications.
        // This can be re-added if the $this->approvalService->findOfficerForStage logic (which was here) is brought back for non-testing.
      }

      Log::info(self::LOG_AREA . "LoanApplication ID: {$application->id} submitted/resubmitted. Current Status: {$application->status}. Initial Approval Task Officer ID: " . ($approvalTask?->officer_id ?? 'N/A') . ", Stage: " . ($approvalTask?->stage ?? 'N/A'));
      return $application->fresh($this->defaultLoanApplicationRelations);
    });
  }

  public function updateApplication(LoanApplication $application, array $validatedData, User $user): LoanApplication
  {
    Log::info(self::LOG_AREA . "Updating loan application.", ['application_id' => $application->id, 'user_id' => $user->id, 'data_keys' => array_keys($validatedData)]);

    if (!$application->isDraft() && $application->status !== LoanApplication::STATUS_REJECTED) { // [cite: 110]
      throw new RuntimeException(__('Hanya draf permohonan atau permohonan yang ditolak boleh dikemaskini. Status semasa: :status', ['status' => $application->getStatusLabelAttribute()]));
    }

    if (isset($validatedData['supporting_officer_id']) && $validatedData['supporting_officer_id'] !== null && (int)$validatedData['supporting_officer_id'] !== (int)$application->supporting_officer_id) { // [cite: 109]
      /** @var User|null $newSupportingOfficer */
      $newSupportingOfficer = User::with('grade:id,name,level')->find((int)$validatedData['supporting_officer_id']);
      if (!$newSupportingOfficer) {
        throw new ModelNotFoundException(__('Pegawai Penyokong yang dipilih untuk kemaskini tidak sah.'));
      }
      $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41); // [cite: 86]
      if (!$newSupportingOfficer->grade || (int) $newSupportingOfficer->grade->level < $minSupportGradeLevel) { // [cite: 100]
        throw new InvalidArgumentException(__("Pegawai Penyokong baharu yang dipilih (:name) tidak memenuhi syarat minima gred (Gred :minGrade atau setara). Gred semasa: :currentGrade", [ // [cite: 153]
          'name' => $newSupportingOfficer->name,
          'minGrade' => $minSupportGradeLevel,
          'currentGrade' => $newSupportingOfficer->grade?->name ?? __('Tidak Dinyatakan')
        ]));
      }
    }

    return DB::transaction(function () use ($application, $validatedData, $user) {
      $applicationModelData = Arr::only($validatedData, $application->getFillable()); // [cite: 109]

      if (isset($validatedData['applicant_is_responsible_officer'])) {
        if ($validatedData['applicant_is_responsible_officer']) {
          $applicationModelData['responsible_officer_id'] = $user->id; // [cite: 109]
        } elseif (isset($validatedData['responsible_officer_id'])) {
          $applicationModelData['responsible_officer_id'] = $validatedData['responsible_officer_id']; // [cite: 109]
        }
      }

      $application->fill($applicationModelData);

      if (array_key_exists('applicant_confirmation', $validatedData)) { // [cite: 109]
        $isStillDraft = $validatedData['is_draft_submission'] ?? ($application->status === LoanApplication::STATUS_DRAFT); // [cite: 110]

        if (!$isStillDraft && ($validatedData['applicant_confirmation'] ?? false) === true) {
          $application->applicant_confirmation_timestamp = $application->applicant_confirmation_timestamp ?? now(); // [cite: 109]
        } elseif (($validatedData['applicant_confirmation'] ?? null) === false || $isStillDraft) {
          $application->applicant_confirmation_timestamp = null; // [cite: 109]
        }
      }

      $application->save();

      if (isset($validatedData['items']) && is_array($validatedData['items'])) { // [cite: 39]
        $this->syncApplicationItems($application, $validatedData['items']);
      }
      Log::info(self::LOG_AREA . "Loan application updated successfully.", ['application_id' => $application->id]);
      return $application->fresh($this->defaultLoanApplicationRelations);
    });
  }

  public function deleteApplication(LoanApplication $application, User $user): bool
  {
    Log::info(self::LOG_AREA . "Attempting to delete loan application.", ['application_id' => $application->id, 'user_id' => $user->id]);
    if (!$application->isDraft()) { // [cite: 110]
      Log::warning(self::LOG_AREA . "Attempt to delete non-draft application denied.", ['application_id' => $application->id, 'status' => $application->status]);
      throw new RuntimeException(__('Hanya draf permohonan yang boleh dibuang.'));
    }

    return DB::transaction(function () use ($application) {
      $application->loanApplicationItems()->delete(); // [cite: 39]
      $application->approvals()->delete(); // [cite: 41]
      $deleted = $application->delete();

      if ($deleted) {
        Log::info(self::LOG_AREA . "Loan application and related data soft deleted.", ['application_id' => $application->id]);
      } else {
        Log::warning(self::LOG_AREA . "Soft delete returned false for loan application.", ['application_id' => $application->id]);
        throw new RuntimeException(__('Gagal memadam permohonan.'));
      }
      return (bool) $deleted;
    });
  }

  public function createIssueTransaction(LoanApplication $loanApplication, array $itemsDetails, User $issuingOfficer, array $transactionDetails): LoanTransaction
  {
    $appIdLog = $loanApplication->id;
    Log::info(self::LOG_AREA . "Creating issue transaction.", ['application_id' => $appIdLog, 'issuing_officer_id' => $issuingOfficer->id]);

    if (!in_array($loanApplication->status, [LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_PARTIALLY_ISSUED])) { // [cite: 110]
      throw new RuntimeException(__("Peralatan hanya boleh dikeluarkan untuk permohonan yang telah diluluskan atau separa dikeluarkan. Status semasa: :status", ['status' => $loanApplication->status_label]));
    }
    if (empty($itemsDetails)) {
      throw new InvalidArgumentException(__('Tiada item peralatan untuk dikeluarkan dalam transaksi ini.'));
    }
    if (empty($transactionDetails['receiving_officer_id'])) { // [cite: 112]
      throw new InvalidArgumentException(__('Pegawai Penerima mesti dinyatakan.'));
    }

    $serviceItemData = [];
    foreach ($itemsDetails as $item) {
      if (empty($item['equipment_id']) || empty($item['loan_application_item_id']) || !isset($item['quantity_issued']) || (int)$item['quantity_issued'] <= 0) { // [cite: 111, 115]
        throw new InvalidArgumentException(__('Butiran item pengeluaran tidak lengkap atau kuantiti tidak sah.'));
      }
      $serviceItemData[] = [
        'equipment_id' => (int) $item['equipment_id'], // [cite: 115]
        'loan_application_item_id' => (int) $item['loan_application_item_id'], // [cite: 116]
        'quantity' => (int) $item['quantity_issued'], // This becomes quantity_transacted in LoanTransactionItem [cite: 116]
        'notes' => $item['issue_item_notes'] ?? null, // This becomes item_notes in LoanTransactionItem [cite: 116]
        'accessories_data' => $item['accessories_checklist_item'] ?? config('motac.loan_accessories_list_default_empty_json', '[]'), // [cite: 88, 112, 116]
      ];
    }

    $extraServiceDetails = [
      'receiving_officer_id' => (int) $transactionDetails['receiving_officer_id'], // [cite: 112]
      'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(), // [cite: 112]
      'issue_notes' => $transactionDetails['issue_notes'] ?? null, // [cite: 112]
      'status' => LoanTransaction::STATUS_ISSUED, // [cite: 113]
    ];

    $transaction = $this->loanTransactionService->createTransaction(
      $loanApplication,
      LoanTransaction::TYPE_ISSUE, // [cite: 113]
      $issuingOfficer, // This is the issuing_officer_id [cite: 112]
      $serviceItemData,
      $extraServiceDetails
    );
    $this->notificationService->notifyApplicantEquipmentIssued($loanApplication, $transaction, $issuingOfficer); // [cite: 157]
    return $transaction;
  }

  public function createReturnTransaction(LoanTransaction $issueTransaction, array $itemsDetails, User $returnAcceptingOfficer, array $transactionDetails): LoanTransaction
  {
    $loanApplication = $issueTransaction->loanApplication()->firstOrFail();
    Log::info(self::LOG_AREA . "Creating return transaction.", ['loan_application_id' => $loanApplication->id, 'issue_transaction_id' => $issueTransaction->id, 'accepting_officer_id' => $returnAcceptingOfficer->id]);

    if (empty($itemsDetails)) {
      throw new InvalidArgumentException(__('Tiada item peralatan untuk dipulangkan dalam transaksi ini.'));
    }
    if (empty($transactionDetails['returning_officer_id'])) { // [cite: 112]
      throw new InvalidArgumentException(__('Pegawai Yang Memulangkan mesti dinyatakan.'));
    }

    $serviceItemData = [];
    foreach ($itemsDetails as $item) {
      if (empty($item['equipment_id']) || empty($item['loan_transaction_item_id']) || !isset($item['quantity_returned']) || (int)$item['quantity_returned'] <= 0 || empty($item['condition_on_return']) || empty($item['item_status_on_return'])) { // [cite: 115, 116]
        throw new InvalidArgumentException(__('Butiran item pemulangan tidak lengkap, kuantiti tidak sah, atau status/keadaan tidak dinyatakan.'));
      }
      /** @var \App\Models\LoanTransactionItem $originalIssuedItem */
      $originalIssuedItem = AppLoanTransactionItemModel::findOrFail($item['loan_transaction_item_id']);
      if ($originalIssuedItem->loan_transaction_id !== $issueTransaction->id || (int)$originalIssuedItem->equipment_id !== (int)$item['equipment_id']) { // [cite: 115]
        throw new InvalidArgumentException(__("Item rujukan pengeluaran (ID: :itemRefId) tidak sepadan atau tidak sah untuk item peralatan (ID: :eqId).", ['itemRefId' => $item['loan_transaction_item_id'], 'eqId' => $item['equipment_id']]));
      }

      $serviceItemData[] = [
        'equipment_id' => (int) $item['equipment_id'], // [cite: 115]
        'original_loan_transaction_item_id' => (int) $item['loan_transaction_item_id'], // Custom field for service, not directly on LoanTransactionItem model
        'loan_application_item_id' => $originalIssuedItem->loan_application_item_id, // [cite: 116]
        'quantity' => (int) $item['quantity_returned'], // Becomes quantity_transacted [cite: 116]
        'condition_on_return' => $item['condition_on_return'], // [cite: 116]
        'item_status_on_return' => $item['item_status_on_return'], // Becomes status for LoanTransactionItem [cite: 116]
        'notes' => $item['return_item_notes'] ?? null, // Becomes item_notes [cite: 116]
        'accessories_data' => $item['accessories_checklist_item'] ?? config('motac.loan_accessories_list_default_empty_json', '[]'), // [cite: 88, 112, 116]
      ];
    }

    $extraServiceDetails = [
      'returning_officer_id' => (int) $transactionDetails['returning_officer_id'], // [cite: 112]
      'transaction_date' => $transactionDetails['transaction_date'] ?? now()->toDateTimeString(), // [cite: 112]
      'return_notes' => $transactionDetails['return_notes'] ?? null, // [cite: 112]
      'related_transaction_id' => $issueTransaction->id, // [cite: 112]
      'status' => LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION, // [cite: 113]
    ];

    $transaction =  $this->loanTransactionService->createTransaction(
      $loanApplication,
      LoanTransaction::TYPE_RETURN, // [cite: 113]
      $returnAcceptingOfficer, // This is the return_accepting_officer_id [cite: 112]
      $serviceItemData,
      $extraServiceDetails
    );
    $this->notificationService->notifyApplicantEquipmentReturned($loanApplication, $transaction, $returnAcceptingOfficer); // [cite: 158]
    return $transaction;
  }

  public function getActiveLoansSummary(array $filters = []): LengthAwarePaginator
  {
    Log::debug(self::LOG_AREA . 'Fetching summary of active loan applications.', ['filters' => $filters]);
    $query = LoanApplication::query()
      ->whereIn('status', [
        LoanApplication::STATUS_ISSUED, // [cite: 111]
        LoanApplication::STATUS_PARTIALLY_ISSUED, // [cite: 110]
        LoanApplication::STATUS_OVERDUE // [cite: 111]
      ])
      ->with($this->defaultLoanApplicationRelations);

    if (!empty($filters['search_term'])) {
      $term = '%' . $filters['search_term'] . '%';
      $query->where(function ($q) use ($term) {
        $q->where('id', 'like', $term)
          ->orWhere('purpose', 'like', $term) // [cite: 109]
          ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', $term))
          ->orWhereHas('responsibleOfficer', fn($roq) => $roq->where('name', 'like', $term)); // [cite: 109]
      });
    }
    $orderBy = $filters['order_by'] ?? 'loan_end_date'; // [cite: 109]
    $orderDirection = $filters['order_direction'] ?? 'asc';

    $perPage = isset($filters['per_page']) && is_numeric($filters['per_page']) ? (int) $filters['per_page'] : 15;
    return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
  }

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

  protected function syncApplicationItems(LoanApplication $application, array $itemsData): void
  {
    $existingItemIds = $application->loanApplicationItems()->pluck('id')->all(); // [cite: 39]
    $processedItemIds = [];
    $itemPayloadsToCreate = [];

    foreach ($itemsData as $itemData) {
      if (empty($itemData['equipment_type']) || !isset($itemData['quantity_requested'])) { // [cite: 111]
        Log::warning(self::LOG_AREA . "Skipping item with missing type or quantity during sync.", ['application_id' => $application->id, 'item_data' => $itemData]);
        continue;
      }
      $quantity = (int) $itemData['quantity_requested']; // [cite: 111]

      $itemId = isset($itemData['id']) && is_numeric($itemData['id']) ? (int)$itemData['id'] : null;
      $itemPayload = [
        'equipment_type' => $itemData['equipment_type'], // [cite: 111]
        'quantity_requested' => $quantity, // [cite: 111]
        'notes' => $itemData['notes'] ?? null, // [cite: 111]
      ];

      if ($itemId && in_array($itemId, $existingItemIds, true)) {
        if ($quantity > 0) {
          LoanApplicationItem::find($itemId)?->update($itemPayload);
          $processedItemIds[] = $itemId;
        } else {
          Log::info(self::LOG_AREA . "Existing item ID {$itemId} submitted with zero quantity, will be removed.", ['application_id' => $application->id]);
        }
      } elseif ($quantity > 0) {
        $itemPayloadsToCreate[] = $itemPayload;
      }
    }

    if (!empty($itemPayloadsToCreate)) {
      $createdItems = $application->loanApplicationItems()->createMany($itemPayloadsToCreate); // [cite: 39]
      foreach ($createdItems as $createdItem) {
        $processedItemIds[] = $createdItem->id;
      }
    }

    $idsToDelete = array_diff($existingItemIds, $processedItemIds);
    if (!empty($idsToDelete)) {
      $application->loanApplicationItems()->whereIn('id', $idsToDelete)->delete(); // [cite: 39]
      Log::info(self::LOG_AREA . "Removed items no longer in submission or marked for deletion.", ['deleted_ids' => $idsToDelete, 'application_id' => $application->id]);
    }
  }
}
