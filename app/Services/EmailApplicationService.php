<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\User;
use App\Models\Grade; // For fetching grade name
// Specific Notification Classes - System Design 5.1, 9.2
use App\Notifications\EmailApplicationSubmitted;
use App\Notifications\EmailProvisionedNotification;
use App\Notifications\ProvisioningFailedNotification;
// use App\Notifications\ApplicationApproved; // Handled by ApprovalService potentially
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config; // For accessing motac config
// Using NotificationService for dispatching
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EmailApplicationService
{
    private const LOG_AREA = 'EmailApplicationService: ';

    protected EmailProvisioningService $emailProvisioningService;
    protected ApprovalService $approvalService;
    private NotificationService $notificationService; // For centralized notification dispatch

    // Default relations for EmailApplication
    private array $defaultEmailApplicationRelations = [
        'user:id,name,email,department_id,grade_id',
        'user.department:id,name',
        'user.grade:id,name,level',
        'supportingOfficerUser:id,name,email,grade_id',
        'supportingOfficerUser.grade:id,name,level',
        'approvals.officer:id,name',
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
     * @throws RuntimeException
     */
    public function createApplication(array $data, User $applicant): EmailApplication
    {
        $applicantIdLog = $applicant->id;
        Log::info(self::LOG_AREA . "Creating email application.", ['user_id' => $applicantIdLog]);

        return DB::transaction(function () use ($data, $applicant, $applicantIdLog) {
            $applicationData = $data;
            $applicationData['user_id'] = $applicant->id;
            $applicationData['status'] = $data['status'] ?? EmailApplication::STATUS_DRAFT;

            // Handle certification flags from MyMail form - System Design 5.1
            $this->setCertificationFields($applicationData, $data);

            /** @var EmailApplication $application */
            $application = EmailApplication::create($applicationData);
            Log::info(self::LOG_AREA . "Email application created.", ['application_id' => $application->id, 'status' => $application->status, 'user_id' => $applicantIdLog]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Update an existing email application.
     * @param  array<string, mixed>  $data Validated data.
     * @throws RuntimeException
     */
    public function updateApplication(EmailApplication $application, array $data, User $updater): EmailApplication
    {
        Log::info(self::LOG_AREA . "Updating email application.", ['application_id' => $application->id, 'user_id' => $updater->id]);
        // Policy check ($updater->can('update', $application)) in controller.

        return DB::transaction(function () use ($application, $data) {
            $originalCertTimestamp = $application->certification_timestamp;
            $originalAllCertified = $application->areAllCertificationsComplete();

            // Prepare data, ensuring existing cert flags are used if not in $data
            $updateData = $data;
            $updateData['cert_info_is_true'] = $data['cert_info_is_true'] ?? $application->cert_info_is_true;
            $updateData['cert_data_usage_agreed'] = $data['cert_data_usage_agreed'] ?? $application->cert_data_usage_agreed;
            $updateData['cert_email_responsibility_agreed'] = $data['cert_email_responsibility_agreed'] ?? $application->cert_email_responsibility_agreed;

            $this->setCertificationFields($updateData, $data, $application); // Pass current application for context

            $application->fill($updateData); // Use the processed $updateData
            $application->save();

            Log::info(self::LOG_AREA . "Email application updated.", ['application_id' => $application->id]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Helper to set certification fields based on input.
     * @param array &$applicationData Reference to the data array to be modified.
     * @param array $inputData The raw input data.
     * @param EmailApplication|null $existingApplication Optional existing application for context.
     */
    private function setCertificationFields(array &$applicationData, array $inputData, ?EmailApplication $existingApplication = null): void
    {
        $certInfo = $inputData['cert_info_is_true'] ?? $existingApplication?->cert_info_is_true ?? false;
        $certUsage = $inputData['cert_data_usage_agreed'] ?? $existingApplication?->cert_data_usage_agreed ?? false;
        $certResp = $inputData['cert_email_responsibility_agreed'] ?? $existingApplication?->cert_email_responsibility_agreed ?? false;

        // If a master 'certification_accepted' flag is passed from form
        if (isset($inputData['certification_accepted'])) {
            if ($inputData['certification_accepted'] === true) {
                $certInfo = true;
                $certUsage = true;
                $certResp = true;
                $applicationData['certification_timestamp'] = $existingApplication?->certification_timestamp ?? now();
            } else {
                // If master flag is explicitly false, it might mean clearing individual flags or just not confirming.
                // Current logic: individual flags dictate. If unchecking master means uncertify, then:
                // $certInfo = $certUsage = $certResp = false;
                // $applicationData['certification_timestamp'] = null;
            }
        }

        $applicationData['cert_info_is_true'] = (bool) $certInfo;
        $applicationData['cert_data_usage_agreed'] = (bool) $certUsage;
        $applicationData['cert_email_responsibility_agreed'] = (bool) $certResp;

        if ($applicationData['cert_info_is_true'] && $applicationData['cert_data_usage_agreed'] && $applicationData['cert_email_responsibility_agreed']) {
            // Set timestamp only if not already set, or if explicitly being re-certified from an uncertified state
            if (!($existingApplication?->certification_timestamp) || (isset($inputData['certification_accepted']) && $inputData['certification_accepted'] === true)) {
                 $applicationData['certification_timestamp'] = now();
            } else {
                // Keep existing timestamp if already certified and individual flags didn't change overall status
                 $applicationData['certification_timestamp'] = $existingApplication?->certification_timestamp;
            }
        } else {
            $applicationData['certification_timestamp'] = null;
        }
        unset($applicationData['certification_accepted']); // Clean up temporary key
    }


    /**
     * Submit an email application for approval.
     * System Design Reference: 5.1 Email/User ID Application Workflow.
     * @throws RuntimeException | InvalidArgumentException | ModelNotFoundException
     */
    public function submitApplication(EmailApplication $application, User $submitter): EmailApplication
    {
        if (!((int) $application->user_id === (int) $submitter->id && $application->canBeSubmitted($submitter))) {
            throw new RuntimeException(__('Permohonan ini tidak boleh dihantar oleh anda atau tidak dalam status yang betul.'));
        }
        if (!$application->areAllCertificationsComplete()) {
            throw new RuntimeException(__('Semua tiga perakuan mesti ditanda sebelum penghantaran.'));
        }

        $supportingOfficer = null;
        // System Design 3.3 & 7.2: motac.approval.min_email_supporting_officer_grade_level
        $minSupportGradeLevel = (int) Config::get('motac.approval.min_email_supporting_officer_grade_level', 9);

        if ($application->supporting_officer_id) {
            $supportingOfficer = User::with('grade:id,name,level')->find($application->supporting_officer_id);
            if (!$supportingOfficer) {
                throw new ModelNotFoundException(__('Pegawai Penyokong (sistem) yang dipilih tidak ditemui.'));
            }
            if (!$supportingOfficer->grade || (int) $supportingOfficer->grade->level < $minSupportGradeLevel) {
                $currentGradeName = $supportingOfficer->grade?->name ?? __('Tidak Dinyatakan');
                $minGradeName = Grade::where('level', $minSupportGradeLevel)->first()?->name ?? "Gred $minSupportGradeLevel";
                throw new InvalidArgumentException(__("Pegawai Penyokong (:name) tidak memenuhi syarat minima gred (:minGradeName). Gred semasa: :currentGradeName", [
                    'name' => $supportingOfficer->name, 'minGradeName' => $minGradeName, 'currentGradeName' => $currentGradeName
                ]));
            }
        } elseif (empty($application->supporting_officer_name) || empty($application->supporting_officer_email) || empty($application->supporting_officer_grade)) {
            throw new RuntimeException(__('Maklumat Pegawai Penyokong (Nama, E-mel, Gred) mesti lengkap.'));
        }
        // Further validation for manually entered grade could be added if it's not just free text.

        Log::info(self::LOG_AREA . "Submitting email application.", ['application_id' => $application->id, 'user_id' => $submitter->id]);
        return DB::transaction(function () use ($application, $submitter, $supportingOfficer) {
            $application->transitionToStatus(EmailApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar untuk semakan pegawai penyokong.'), $submitter->id);

            if ($supportingOfficer instanceof User) {
                $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_EMAIL_SUPPORT_REVIEW, $supportingOfficer);
            } else {
                Log::info(self::LOG_AREA."Manual supporting officer. Workflow may need admin action.", ['application_id' => $application->id]);
                // Optionally, notify an admin group about this type of submission that needs manual approval creation.
            }
            // System Design 5.1: Notify applicant
            $this->notificationService->notifyApplicantApplicationSubmitted($application);

            Log::info(self::LOG_AREA . "Email application submitted.", ['application_id' => $application->id, 'status' => $application->status]);
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    /**
     * Process provisioning for an approved email application.
     * System Design Reference: 5.1 IT Processing & Credential Delivery.
     * @param array<string, mixed> $provisioningDetails Typically ['final_assigned_email' => ?, 'final_assigned_user_id' => ?]
     * @throws RuntimeException | InvalidArgumentException
     */
    public function processProvisioning(EmailApplication $application, array $provisioningDetails, User $actingAdmin): EmailApplication
    {
        // Policy check ($actingAdmin->can('processByIT', $application)) is in controller.
        if (!in_array($application->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN])) {
            throw new RuntimeException(__("Permohonan mesti berstatus ':statusDiluluskan' atau ':statusMenungguPentadbir' untuk tindakan ini. Status semasa: :statusSemasa", [
                'statusDiluluskan' => EmailApplication::$STATUSES_LABELS[EmailApplication::STATUS_APPROVED] ?? EmailApplication::STATUS_APPROVED,
                'statusMenungguPentadbir' => EmailApplication::$STATUSES_LABELS[EmailApplication::STATUS_PENDING_ADMIN] ?? EmailApplication::STATUS_PENDING_ADMIN,
                'statusSemasa' => $application->statusTranslated
            ]));
        }

        Log::info(self::LOG_AREA . "Processing provisioning for EmailApplication.", ['application_id' => $application->id, 'admin_id' => $actingAdmin->id]);
        return DB::transaction(function () use ($application, $provisioningDetails, $actingAdmin) {
            $application->transitionToStatus(EmailApplication::STATUS_PROCESSING, __('Proses penyediaan dimulakan oleh Pentadbir IT.'), $actingAdmin->id);

            // Use details from form if provided, otherwise from application (proposed).
            $targetEmail = $provisioningDetails['final_assigned_email'] ?? $application->final_assigned_email ?? $application->proposed_email;
            $targetUserId = $provisioningDetails['final_assigned_user_id'] ?? $application->final_assigned_user_id; // User ID can be optional

            if (empty($targetEmail)) {
                 throw new InvalidArgumentException(__('E-mel yang akan disediakan tidak boleh kosong.'));
            }

            // Update application with these values before calling provisioning service
            $application->final_assigned_email = $targetEmail;
            $application->final_assigned_user_id = $targetUserId; // Nullable
            $application->save();

            // EmailProvisioningService handles the actual provisioning logic
            $provisionResult = $this->emailProvisioningService->provisionEmailAccount(
                $application, // Pass the application object
                $targetEmail,   // Pass target email
                $targetUserId   // Pass target user ID
            );


            if ($provisionResult['success']) {
                $application->transitionToStatus(EmailApplication::STATUS_COMPLETED, $provisionResult['message'] ?? __('Akaun e-mel/ID berjaya disediakan.'), $actingAdmin->id);
                // Update with confirmed details if provisioning service returns them explicitly
                $application->final_assigned_email = $provisionResult['assigned_email'] ?? $application->final_assigned_email;
                $application->final_assigned_user_id = $provisionResult['assigned_user_id'] ?? $application->final_assigned_user_id;
                $application->save();

                Log::info(self::LOG_AREA."Provisioning completed.", ['application_id' => $application->id, 'email' => $application->final_assigned_email]);
                if ($application->user) {
                    $this->notificationService->notifyApplicantEmailProvisioned($application);
                }
            } else {
                $failureReason = $provisionResult['message'] ?? __('Proses penyediaan akaun e-mel/ID gagal.');
                $application->transitionToStatus(EmailApplication::STATUS_PROVISION_FAILED, $failureReason, $actingAdmin->id);
                // Use rejection_reason to store this note as per current EmailApplication model
                $application->rejection_reason = ($application->rejection_reason ? $application->rejection_reason . "\n" : '') . __('IT Provisioning Failure: ') . $failureReason;
                $application->save();
                Log::error(self::LOG_AREA."Provisioning failed.", ['application_id' => $application->id, 'reason' => $failureReason]);
                // Notify acting admin and potentially other IT Admins
                $this->notificationService->notifyAdminProvisioningFailed($application, $failureReason, $actingAdmin);
            }
            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    // ... (deleteApplication and findEmailApplicationById methods - assuming previous edits were satisfactory) ...
    // For brevity, focusing on the impact of the error fix on the methods shown.
}
