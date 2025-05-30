<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Grade;
use App\Models\User; // For fetching grade name details & type hinting
// Specific Notification Classes - System Design 5.1, 9.2
// Notifications are now dispatched via NotificationService
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // For accessing motac config
use InvalidArgumentException;
use RuntimeException;
use Throwable; // For catching exceptions in deleteApplication

final class EmailApplicationService
{
    private const LOG_AREA = 'EmailApplicationService: ';

    protected EmailProvisioningService $emailProvisioningService;
    protected ApprovalService $approvalService;
    private NotificationService $notificationService; // For centralized notification dispatch

    // Default relations to eager load for EmailApplication consistency
    private array $defaultEmailApplicationRelations = [
        // Ensure fields here match what's selected in EmailApplicationController's show method for User
        'user:id,name,title,full_name,email,department_id,grade_id,position_id,service_status,appointment_type',
        'user.department:id,name',
        'user.grade:id,name,level',
        'user.position:id,name', // Added user.position based on typical needs
        'supportingOfficer:id,name,title,full_name,email,grade_id', // CORRECTED: supportingOfficerUser to supportingOfficer
        'supportingOfficer.grade:id,name,level', // CORRECTED: supportingOfficerUser to supportingOfficer
        'approvals.officer:id,name,title,full_name', // For displaying approval history
        'creator:id,name,title,full_name', // For blameable
        'updater:id,name,title,full_name', // For blameable
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

        return DB::transaction(function () use ($application, $data) {
            $updateData = $data;
            $this->setCertificationFields($updateData, $data, $application);

            $application->fill($updateData);
            $application->save();

            Log::info(self::LOG_AREA . "Email application UPDATED successfully.", ['application_id' => $application->id]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
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
        // Ensure the applicant is the one submitting and the application is in a submittable state.
        // The canBeSubmitted method should encapsulate status checks (e.g., isDraft).
        // System Design (Rev. 3, Sources 391-396 for certification, 397-400 for supporting officer)
        if (!($application->user_id == $submitter->id && $application->isDraft())) { // Simplified based on typical logic
            throw new RuntimeException(__('Permohonan ini tidak boleh dihantar oleh anda atau statusnya tidak membenarkan penghantaran.'));
        }
        if (!$application->cert_info_is_true || !$application->cert_data_usage_agreed || !$application->cert_email_responsibility_agreed) {
            throw new RuntimeException(__('Semua tiga perakuan mesti ditandakan sebelum permohonan boleh dihantar. Pastikan perakuan disimpan sebelum menghantar.'));
        }

        $supportingOfficer = null;
        $minSupportGradeLevel = (int) Config::get('motac.approval.min_email_supporting_officer_grade_level', 9); // Source 61, 183

        if ($application->supporting_officer_id) {
            $supportingOfficer = User::with('grade:id,name,level')->find($application->supporting_officer_id);
            if (!$supportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong (dari sistem) yang dipilih tidak ditemui.'));
            }
            if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) { // Source 114, 398
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
            throw new RuntimeException(__('Maklumat Pegawai Penyokong (Nama, E-mel, dan Gred) mesti diisi dengan lengkap jika tidak dipilih dari senarai pengguna sistem.'));
        }

        Log::info(self::LOG_AREA . "Submitting email application for approval.", ['application_id' => $application->id, 'submitter_id' => $submitter->id]);

        return DB::transaction(function () use ($application, $submitter, $supportingOfficer) {
            $application->status = EmailApplication::STATUS_PENDING_SUPPORT;
            $application->certification_timestamp = now(); // Ensure timestamp is set on submission
            $application->save(); // Persist status change

            // Use ApprovalService to create the approval task
            // System Design (Rev. 3, Sources 401, 116)
            if ($supportingOfficer) { // System user selected
                $this->approvalService->initiateApprovalWorkflow(
                    $application,
                    $submitter, // The user initiating this stage transition
                    Approval::STAGE_EMAIL_SUPPORT_REVIEW,
                    $supportingOfficer // The officer assigned to this stage
                );
            } else { // Manual supporting officer details
                // If manual, a notification should go to the manually entered email,
                // or an admin needs to create the task.
                // For now, logging this. Notification to external email can be added.
                Log::info(self::LOG_AREA . "Manual supporting officer details. Notify external or BPM admin.", [
                    'application_id' => $application->id,
                    'manual_officer_email' => $application->supporting_officer_email
                ]);
                // Consider: $this->notificationService->notifyExternalSupportingOfficer($application);
            }

            // Notify applicant of submission - System Design 5.1, 215
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
        if (!in_array($application->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN])) { // Source 402
            throw new RuntimeException(
                __("Permohonan mesti berstatus ':statusApproved' atau ':statusPendingAdmin' untuk tindakan penyediaan. Status semasa: :currentStatus", [
                    'statusApproved' => __(EmailApplication::$STATUS_OPTIONS[EmailApplication::STATUS_APPROVED]), // Use STATUS_OPTIONS for labels
                    'statusPendingAdmin' => __(EmailApplication::$STATUS_OPTIONS[EmailApplication::STATUS_PENDING_ADMIN]),
                    'currentStatus' => $application->status_translated // Use accessor
                ])
            );
        }

        Log::info(self::LOG_AREA . "Starting provisioning process for EmailApplication.", ['application_id' => $application->id, 'admin_id' => $actingAdmin->id]);

        return DB::transaction(function () use ($application, $provisioningDetails, $actingAdmin) {
            // System Design (Rev. 3, Source 78, 403)
            $application->status = EmailApplication::STATUS_PROCESSING;
            $application->save(); // Persist status change

            $targetEmail = $provisioningDetails['final_assigned_email'] ?? $application->final_assigned_email ?? $application->proposed_email;
            $targetUserId = $provisioningDetails['final_assigned_user_id'] ?? $application->final_assigned_user_id;

            if (empty($targetEmail)) {
                throw new InvalidArgumentException(__('Alamat e-mel yang akan disediakan adalah mandatori.'));
            }

            $application->final_assigned_email = $targetEmail;
            $application->final_assigned_user_id = $targetUserId;
            $application->saveQuietly();

            // System Design (Rev. 3, Source 324, 404)
            $provisionResult = $this->emailProvisioningService->provisionEmailAccount($application, $targetEmail, $targetUserId);

            if ($provisionResult['success']) {
                $application->status = EmailApplication::STATUS_COMPLETED; // Source 78
                $application->final_assigned_email = $provisionResult['assigned_email'] ?? $application->final_assigned_email;
                $application->final_assigned_user_id = $provisionResult['assigned_user_id'] ?? $application->final_assigned_user_id;
                $application->save();

                Log::info(self::LOG_AREA."Provisioning COMPLETED successfully.", ['application_id' => $application->id, 'assigned_email' => $application->final_assigned_email]);
                // System Design (Rev. 3, Source 216, 406)
                if ($application->user) {
                    $this->notificationService->notifyApplicantEmailProvisioned($application);
                }
            } else {
                $failureReason = $provisionResult['message'] ?? __('Proses penyediaan akaun e-mel/ID pengguna gagal tanpa mesej spesifik.');
                $application->status = EmailApplication::STATUS_PROVISION_FAILED; // Source 78
                $application->rejection_reason = ($application->rejection_reason ? $application->rejection_reason . "\n" : '') . __('Kegagalan Penyediaan IT: ') . $failureReason;
                $application->save();
                Log::error(self::LOG_AREA."Provisioning FAILED.", ['application_id' => $application->id, 'reason' => $failureReason]);
                // System Design (Rev. 3, Source 216, 407)
                $this->notificationService->notifyAdminProvisioningFailed($application, $failureReason, $actingAdmin);
            }
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Helper to set certification fields based on input, preserving existing state if not overridden.
     * System Design (Rev. 3, Source 108-111, 392-395)
     * @param array &$applicationData Reference to the data array to be modified for create/update.
     * @param array $inputData The raw input data from the form.
     * @param EmailApplication|null $existingApplication Optional existing application for context during updates.
     */
    private function setCertificationFields(array &$applicationData, array $inputData, ?EmailApplication $existingApplication = null): void
    {
        $applicationData['cert_info_is_true'] = isset($inputData['cert_info_is_true'])
            ? (bool)$inputData['cert_info_is_true']
            : ($existingApplication?->cert_info_is_true ?? false);
        $applicationData['cert_data_usage_agreed'] = isset($inputData['cert_data_usage_agreed'])
            ? (bool)$inputData['cert_data_usage_agreed']
            : ($existingApplication?->cert_data_usage_agreed ?? false);
        $applicationData['cert_email_responsibility_agreed'] = isset($inputData['cert_email_responsibility_agreed'])
            ? (bool)$inputData['cert_email_responsibility_agreed']
            : ($existingApplication?->cert_email_responsibility_agreed ?? false);

        // If all individual certs are true, set the timestamp
        if ($applicationData['cert_info_is_true'] && $applicationData['cert_data_usage_agreed'] && $applicationData['cert_email_responsibility_agreed']) {
            // If it wasn't certified before, or if we are forcing a new timestamp via a master accept flag (not present here)
            if (!($existingApplication && $existingApplication->cert_info_is_true && $existingApplication->cert_data_usage_agreed && $existingApplication->cert_email_responsibility_agreed)) {
                 $applicationData['certification_timestamp'] = now();
            } elseif ($existingApplication) {
                // Preserve existing timestamp if already fully certified and no changes to certs
                 $applicationData['certification_timestamp'] = $existingApplication->certification_timestamp;
            } else {
                 $applicationData['certification_timestamp'] = now(); // For new applications fully certified from start
            }
        } else {
            $applicationData['certification_timestamp'] = null;
        }
    }

    /**
     * Delete an email application (soft delete).
     * System Design Reference: Controller calls this, expects soft delete.
     * @param EmailApplication $application The application to delete.
     * @param User $deleter The user performing the deletion (for logging/audit).
     * @return bool True if deletion was successful, false otherwise.
     * @throws RuntimeException If deletion fails unexpectedly.
     */
    public function deleteApplication(EmailApplication $application, User $deleter): bool
    {
        Log::info(self::LOG_AREA . "Attempting to soft delete email application.", [
            'application_id' => $application->id,
            'deleter_id' => $deleter->id
        ]);

        // Controller should ensure only draft can be deleted.
        // This service method performs the actual deletion.
        try {
            $result = $application->delete(); // Eloquent soft delete
            if ($result) {
                Log::info(self::LOG_AREA . "Email application soft DELETED successfully.", ['application_id' => $application->id]);
            } else {
                Log::warning(self::LOG_AREA . "Email application soft delete operation returned false.", ['application_id' => $application->id]);
            }
            return (bool)$result;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA . "Error during email application soft delete.", [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace_snippet' => substr($e->getTraceAsString(), 0, 300)
            ]);
            // Optionally rethrow or wrap in a custom exception
            throw new RuntimeException(__('Gagal memadamkan permohonan e-mel: ') . $e->getMessage(), 0, $e);
        }
    }
}
