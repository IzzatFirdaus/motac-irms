<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\User;
use App\Models\Grade; // For fetching grade name details
// Specific Notification Classes - System Design 5.1, 9.2
// Notifications are now dispatched via NotificationService
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config; // For accessing motac config
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EmailApplicationService
{
    private const LOG_AREA = 'EmailApplicationService: ';

    protected EmailProvisioningService $emailProvisioningService;
    protected ApprovalService $approvalService;
    private NotificationService $notificationService; // For centralized notification dispatch

    // Default relations to eager load for EmailApplication consistency
    private array $defaultEmailApplicationRelations = [
        'user:id,name,email,department_id,grade_id,service_status,appointment_type', // Added fields based on application context
        'user.department:id,name',
        'user.grade:id,name,level',
        'supportingOfficerUser:id,name,email,grade_id', // For system-selected supporting officer
        'supportingOfficerUser.grade:id,name,level',
        'approvals.officer:id,name', // For displaying approval history
    ];

    public function __construct(
        EmailProvisioningService $emailProvisioningService,
        ApprovalService $approvalService,
        NotificationService $notificationService // Injected
    ) {
        $this->emailProvisioningService = $emailProvisioningService;
        $this->approvalService = $approvalService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new email application.
     * System Design Reference: 5.1 Email/User ID Application Workflow.
     * @param  array<string, mixed>  $data Validated form data.
     * @param  User $applicant The user creating the application.
     * @throws RuntimeException If creation fails within transaction.
     */
    public function createApplication(array $data, User $applicant): EmailApplication
    {
        Log::info(self::LOG_AREA . "Attempting to create email application.", ['user_id' => $applicant->id, 'data_keys' => array_keys($data)]);

        return DB::transaction(function () use ($data, $applicant) {
            $applicationData = $data; // Start with validated data
            $applicationData['user_id'] = $applicant->id;
            // Status defaults to DRAFT in model, but can be overridden if 'status' is in $data
            $applicationData['status'] = $data['status'] ?? EmailApplication::STATUS_DRAFT;

            // Handle certification flags from MyMail form logic - System Design 5.1
            $this->setCertificationFields($applicationData, $data);

            /** @var EmailApplication $application */
            $application = EmailApplication::create($applicationData);
            Log::info(self::LOG_AREA . "Email application CREATED successfully.", ['application_id' => $application->id, 'status' => $application->status, 'user_id' => $applicant->id]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Update an existing email application.
     * @param EmailApplication $application The application to update.
     * @param array<string, mixed>  $data Validated data for update.
     * @param User $updater The user performing the update.
     * @throws RuntimeException If update fails within transaction.
     */
    public function updateApplication(EmailApplication $application, array $data, User $updater): EmailApplication
    {
        Log::info(self::LOG_AREA . "Attempting to update email application.", ['application_id' => $application->id, 'user_id' => $updater->id, 'data_keys' => array_keys($data)]);
        // Authorization check (e.g., $updater->can('update', $application)) should be handled in the controller or Livewire component.

        return DB::transaction(function () use ($application, $data) {
            // Prepare data, ensuring existing cert flags are used if not explicitly in $data to avoid accidental clearing
            $updateData = $data;
            $this->setCertificationFields($updateData, $data, $application);

            $application->fill($updateData);
            $application->save();

            Log::info(self::LOG_AREA . "Email application UPDATED successfully.", ['application_id' => $application->id]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Helper to set certification fields based on input, preserving existing state if not overridden.
     * @param array &$applicationData Reference to the data array to be modified for create/update.
     * @param array $inputData The raw input data from the form.
     * @param EmailApplication|null $existingApplication Optional existing application for context during updates.
     */
    private function setCertificationFields(array &$applicationData, array $inputData, ?EmailApplication $existingApplication = null): void
    {
        // Determine the new state of individual certification flags
        $newCertInfoIsTrue = isset($inputData['cert_info_is_true']) ? (bool)$inputData['cert_info_is_true'] : ($existingApplication?->cert_info_is_true ?? false);
        $newCertDataUsageAgreed = isset($inputData['cert_data_usage_agreed']) ? (bool)$inputData['cert_data_usage_agreed'] : ($existingApplication?->cert_data_usage_agreed ?? false);
        $newCertEmailResponsibilityAgreed = isset($inputData['cert_email_responsibility_agreed']) ? (bool)$inputData['cert_email_responsibility_agreed'] : ($existingApplication?->cert_email_responsibility_agreed ?? false);

        // If a master 'certification_accepted' flag is passed and is true, it forces all individual flags to true.
        if (isset($inputData['certification_accepted']) && $inputData['certification_accepted'] === true) {
            $newCertInfoIsTrue = true;
            $newCertDataUsageAgreed = true;
            $newCertEmailResponsibilityAgreed = true;
        }
        // If 'certification_accepted' is explicitly false, it doesn't necessarily mean unchecking all; individual flags dictate.

        $applicationData['cert_info_is_true'] = $newCertInfoIsTrue;
        $applicationData['cert_data_usage_agreed'] = $newCertDataUsageAgreed;
        $applicationData['cert_email_responsibility_agreed'] = $newCertEmailResponsibilityAgreed;

        // Update certification_timestamp
        if ($newCertInfoIsTrue && $newCertDataUsageAgreed && $newCertEmailResponsibilityAgreed) {
            // If all are true, set/keep timestamp. If previously uncertified and now certified, set new timestamp.
            $applicationData['certification_timestamp'] = $existingApplication?->certification_timestamp ?? now();
            if (isset($inputData['certification_accepted']) && $inputData['certification_accepted'] === true && !$existingApplication?->areAllCertificationsComplete()) {
                 $applicationData['certification_timestamp'] = now(); // Fresh timestamp if master accept flag made it complete
            }
        } else {
            // If any certification is false, clear the timestamp.
            $applicationData['certification_timestamp'] = null;
        }
        unset($applicationData['certification_accepted']); // Remove temporary key
    }


    /**
     * Submit an email application for the approval workflow.
     * System Design Reference: 5.1 Email/User ID Application Workflow.
     * @param EmailApplication $application The application to submit.
     * @param User $submitter The user submitting the application.
     * @throws RuntimeException | InvalidArgumentException | ModelNotFoundException If submission criteria not met.
     */
    public function submitApplication(EmailApplication $application, User $submitter): EmailApplication
    {
        if (!((int) $application->user_id === (int) $submitter->id && $application->canBeSubmitted($submitter))) {
            // Using __() for translatable messages
            throw new RuntimeException(__('Permohonan ini tidak boleh dihantar oleh anda atau statusnya tidak membenarkan penghantaran.'));
        }
        if (!$application->areAllCertificationsComplete()) {
            throw new RuntimeException(__('Semua tiga perakuan mesti ditandakan sebelum permohonan boleh dihantar.'));
        }

        $supportingOfficer = null;
        // Min grade for supporting officer from config - System Design 3.3, 7.2
        $minSupportGradeLevel = (int) Config::get('motac.approval.min_email_supporting_officer_grade_level', 9);

        if ($application->supporting_officer_id) { // If supporting officer selected from system users
            $supportingOfficer = User::with('grade:id,name,level')->find($application->supporting_officer_id);
            if (!$supportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong (dari sistem) yang dipilih tidak ditemui.'));
            }
            if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) {
                $minGradeName = Grade::where('level', $minSupportGradeLevel)->value('name') ?? "Gred $minSupportGradeLevel";
                throw new InvalidArgumentException(
                    __("Pegawai Penyokong (:name) tidak memenuhi syarat gred minima (:minGrade). Gred semasa: :currentGrade", [
                        'name' => $supportingOfficer->name,
                        'minGrade' => $minGradeName,
                        'currentGrade' => $supportingOfficer->grade?->name ?? __('Tidak Ditetapkan')
                    ])
                );
            }
        } elseif (empty($application->supporting_officer_name) || empty($application->supporting_officer_email) || empty($application->supporting_officer_grade)) {
            // If supporting officer details are entered manually
            throw new RuntimeException(__('Maklumat Pegawai Penyokong (Nama, E-mel, dan Gred) mesti diisi dengan lengkap jika tidak dipilih dari senarai pengguna sistem.'));
        }
        // Additional validation for manually entered grade text could be added here if needed.

        Log::info(self::LOG_AREA . "Submitting email application for approval.", ['application_id' => $application->id, 'submitter_id' => $submitter->id]);

        return DB::transaction(function () use ($application, $submitter, $supportingOfficer) {
            // Transition to PENDING_SUPPORT status
            $application->transitionToStatus(EmailApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar untuk semakan dan sokongan.'), $submitter->id);

            if ($supportingOfficer instanceof User) { // If system user is selected as supporting officer
                $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_EMAIL_SUPPORT_REVIEW, $supportingOfficer);
            } else { // Manual supporting officer details provided
                Log::info(self::LOG_AREA."Manual supporting officer details provided. An admin may need to create an approval task or the system should notify the external email.", ['application_id' => $application->id]);
                // TODO: Implement notification to manually entered supporting officer's email if required.
                // Or, create a pending approval task assigned to a generic "BPM Admin" role to handle these.
            }

            // Notify applicant of submission - System Design 5.1
            $this->notificationService->notifyApplicantApplicationSubmitted($application);

            Log::info(self::LOG_AREA . "Email application SUBMITTED successfully.", ['application_id' => $application->id, 'new_status' => $application->status]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Process provisioning for an approved email application by IT Admin.
     * System Design Reference: 5.1 IT Processing & Credential Delivery.
     * @param EmailApplication $application The application to provision.
     * @param array<string, mixed> $provisioningDetails Data for provisioning, typically ['final_assigned_email' => ?, 'final_assigned_user_id' => ?].
     * @param User $actingAdmin The IT Admin performing the action.
     * @throws RuntimeException | InvalidArgumentException If provisioning criteria not met or process fails.
     */
    public function processProvisioning(EmailApplication $application, array $provisioningDetails, User $actingAdmin): EmailApplication
    {
        // Authorization check (e.g., $actingAdmin->can('processByIT', $application)) should be in the controller.
        if (!in_array($application->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN])) {
            throw new RuntimeException(
                __("Permohonan mesti berstatus ':statusApproved' atau ':statusPendingAdmin' untuk tindakan penyediaan. Status semasa: :currentStatus", [
                    'statusApproved' => __(EmailApplication::$STATUSES_LABELS[EmailApplication::STATUS_APPROVED]),
                    'statusPendingAdmin' => __(EmailApplication::$STATUSES_LABELS[EmailApplication::STATUS_PENDING_ADMIN]),
                    'currentStatus' => $application->statusTranslated
                ])
            );
        }

        Log::info(self::LOG_AREA . "Starting provisioning process for EmailApplication.", ['application_id' => $application->id, 'admin_id' => $actingAdmin->id]);

        return DB::transaction(function () use ($application, $provisioningDetails, $actingAdmin) {
            $application->transitionToStatus(EmailApplication::STATUS_PROCESSING, __('Proses penyediaan akaun dimulakan oleh Pentadbir IT.'), $actingAdmin->id);

            // Use details from form if provided, otherwise from application's proposed/final fields.
            $targetEmail = $provisioningDetails['final_assigned_email'] ?? $application->final_assigned_email ?? $application->proposed_email;
            $targetUserId = $provisioningDetails['final_assigned_user_id'] ?? $application->final_assigned_user_id; // Can be optional

            if (empty($targetEmail)) {
                 throw new InvalidArgumentException(__('Alamat e-mel yang akan disediakan adalah mandatori.'));
            }

            // Update application with these target values before calling provisioning service
            $application->final_assigned_email = $targetEmail;
            $application->final_assigned_user_id = $targetUserId;
            $application->saveQuietly(); // Save without triggering observers again for this minor update if any

            // Call the EmailProvisioningService to handle the actual provisioning logic
            $provisionResult = $this->emailProvisioningService->provisionEmailAccount($application, $targetEmail, $targetUserId);

            if ($provisionResult['success']) {
                $application->transitionToStatus(EmailApplication::STATUS_COMPLETED, $provisionResult['message'] ?? __('Akaun e-mel/ID pengguna telah berjaya disediakan.'), $actingAdmin->id);
                // Update application with confirmed details if provisioning service returns them explicitly
                $application->final_assigned_email = $provisionResult['assigned_email'] ?? $application->final_assigned_email;
                $application->final_assigned_user_id = $provisionResult['assigned_user_id'] ?? $application->final_assigned_user_id;
                $application->save();

                Log::info(self::LOG_AREA."Provisioning COMPLETED successfully.", ['application_id' => $application->id, 'assigned_email' => $application->final_assigned_email]);
                // Notify the applicant - System Design 5.1
                if ($application->user) { // Check if user relationship is loaded/exists
                    $this->notificationService->notifyApplicantEmailProvisioned($application);
                }
            } else {
                $failureReason = $provisionResult['message'] ?? __('Proses penyediaan akaun e-mel/ID pengguna gagal tanpa mesej spesifik.');
                $application->transitionToStatus(EmailApplication::STATUS_PROVISION_FAILED, $failureReason, $actingAdmin->id);
                // Store failure reason, perhaps append to admin_notes or rejection_reason
                $application->admin_notes = ($application->admin_notes ? $application->admin_notes . "\n" : '') . __('Kegagalan Penyediaan IT: ') . $failureReason;
                $application->save();
                Log::error(self::LOG_AREA."Provisioning FAILED.", ['application_id' => $application->id, 'reason' => $failureReason]);
                // Notify acting admin and potentially other IT Admins of the failure
                $this->notificationService->notifyAdminProvisioningFailed($application, $failureReason, $actingAdmin);
            }
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    // Other methods like deleteApplication, findApplicationById would follow similar logging and transaction patterns.
}
