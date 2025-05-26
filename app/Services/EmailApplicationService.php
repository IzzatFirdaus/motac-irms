<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\User;
use App\Notifications\EmailApplicationSubmitted;
use App\Notifications\EmailProvisioningComplete;
use App\Notifications\EmailProvisioningFailed as EmailProvisioningFailedNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use RuntimeException;
use Throwable;

final class EmailApplicationService
{
    private const LOG_AREA = 'EmailApplicationService: ';

    protected EmailProvisioningService $emailProvisioningService;
    protected ApprovalService $approvalService;

    public function __construct(
        EmailProvisioningService $emailProvisioningService,
        ApprovalService $approvalService
    ) {
        $this->emailProvisioningService = $emailProvisioningService;
        $this->approvalService = $approvalService;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createApplication(array $data, User $applicant): EmailApplication
    {
        $applicantIdLog = $applicant->id;
        Log::info(self::LOG_AREA . "Attempting to create email application for User ID: {$applicantIdLog}.");
        DB::beginTransaction();
        try {
            $applicationData = $data;
            $applicationData['user_id'] = $applicant->id;
            $applicationData['status'] = $data['status'] ?? EmailApplication::STATUS_DRAFT;

            // Handle certification flags. Assume form sends individual booleans or a master 'certification_accepted'
            if (isset($data['certification_accepted']) && $data['certification_accepted'] === true) {
                $applicationData['cert_info_is_true'] = true;
                $applicationData['cert_data_usage_agreed'] = true;
                $applicationData['cert_email_responsibility_agreed'] = true;
                $applicationData['certification_timestamp'] = now();
            } else {
                $allCertified = ($data['cert_info_is_true'] ?? false) &&
                                ($data['cert_data_usage_agreed'] ?? false) &&
                                ($data['cert_email_responsibility_agreed'] ?? false);
                if ($allCertified) {
                    $applicationData['certification_timestamp'] = $data['certification_timestamp'] ?? now();
                } else {
                    $applicationData['certification_timestamp'] = null;
                }
            }
            unset($applicationData['certification_accepted']);

            /** @var EmailApplication $application */
            $application = EmailApplication::create($applicationData);
            DB::commit();
            Log::info(self::LOG_AREA . "Email application ID: {$application->id} (Status: {$application->status}) created by User ID: {$applicantIdLog}.");
            return $application;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to create email application for User ID: {$applicantIdLog}.", ['error' => $e->getMessage(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mencipta permohonan e-mel: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateApplication(EmailApplication $application, array $data, User $updater): EmailApplication
    {
        $appIdLog = $application->id;
        $updaterIdLog = $updater->id;
        Log::info(self::LOG_AREA . "Attempting to update email application ID: {$appIdLog} by User ID: {$updaterIdLog}.");
        // Authorization should be handled by policy in controller.

        DB::beginTransaction();
        try {
            // Handle certification updates
            if (isset($data['certification_accepted']) && $data['certification_accepted'] === true) {
                $data['cert_info_is_true'] = true;
                $data['cert_data_usage_agreed'] = true;
                $data['cert_email_responsibility_agreed'] = true;
                $data['certification_timestamp'] = now();
            } elseif (isset($data['cert_info_is_true']) || isset($data['cert_data_usage_agreed']) || isset($data['cert_email_responsibility_agreed'])) {
                 $allCertsNowTrue = ($data['cert_info_is_true'] ?? $application->cert_info_is_true) &&
                                   ($data['cert_data_usage_agreed'] ?? $application->cert_data_usage_agreed) &&
                                   ($data['cert_email_responsibility_agreed'] ?? $application->cert_email_responsibility_agreed);
                if ($allCertsNowTrue) {
                    $data['certification_timestamp'] = $data['certification_timestamp'] ?? ($application->certification_timestamp ?? now());
                } else {
                     $data['certification_timestamp'] = null;
                }
            }
            unset($data['certification_accepted']);

            $application->fill($data);
            $application->save();
            DB::commit();
            Log::info(self::LOG_AREA . "Email application ID: {$appIdLog} updated by User ID: {$updaterIdLog}.");
            return $application->fresh();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to update email application ID: {$appIdLog}.", ['error' => $e->getMessage(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mengemaskini permohonan e-mel: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function submitApplication(EmailApplication $application, User $submitter): EmailApplication
    {
        $appIdLog = $application->id;
        $submitterIdLog = $submitter->id;

        if (!$application->canBeSubmitted()) { // Assumes canBeSubmitted() checks status and ownership
            Log::warning(self::LOG_AREA."Attempt to submit EmailApplication ID: {$appIdLog} which is not in a submittable state (Status: {$application->status}) or by non-owner User ID: {$submitterIdLog}.");
            throw new RuntimeException(__('Permohonan ini tidak boleh dihantar atau anda tiada kebenaran. Semak status dan pemilikan.'));
        }
        if (!$application->areAllCertificationsComplete()) { // Assumes method checks all 3 certs and timestamp
            Log::warning(self::LOG_AREA."Attempt to submit uncertified EmailApplication ID: {$appIdLog}.");
            throw new RuntimeException(__('Permohonan mesti disahkan sepenuhnya sebelum penghantaran.'));
        }

        $supportingOfficer = null;
        if ($application->supporting_officer_id) {
            $supportingOfficer = User::find($application->supporting_officer_id);
            if (!$supportingOfficer) {
                Log::error(self::LOG_AREA . "System Supporting Officer ID {$application->supporting_officer_id} not found for EmailApplication ID: {$appIdLog}.");
                throw new ModelNotFoundException(__('Pegawai Penyokong (sistem) yang dipilih tidak ditemui. Sila kemaskini.'));
            }
            // TODO: Validate if $supportingOfficer is eligible (e.g., grade >= 9 as per MyMail form)
            if ($supportingOfficer->grade?->level < config('motac.approval.min_email_support_grade_level', 9)) { // Example config
                 // Log::warning(...); throw new InvalidArgumentException(...);
            }
        } elseif (empty($application->supporting_officer_name) || empty($application->supporting_officer_email)) {
            Log::error(self::LOG_AREA."EmailApplication ID: {$appIdLog} lacks any supporting officer details for submission.");
            throw new RuntimeException(__('Maklumat Pegawai Penyokong mesti lengkap (sama ada dari sistem atau diisi manual) sebelum permohonan boleh dihantar.'));
        }
        // If manual details are provided, $supportingOfficer remains null.

        Log::info(self::LOG_AREA . "Submitting EmailApplication ID: {$appIdLog} by User ID: {$submitterIdLog}. Supporting Officer: " . ($supportingOfficer?->name ?? $application->supporting_officer_name ?? 'Manual Details'));

        DB::beginTransaction();
        try {
            $application->transitionToStatus(EmailApplication::STATUS_PENDING_SUPPORT, __('Permohonan dihantar untuk semakan pegawai penyokong.'), $submitter->id);

            if ($supportingOfficer instanceof User) { // System user selected
                $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_EMAIL_SUPPORT_REVIEW, $supportingOfficer);
            } else { // Manual details provided
                Log::info(self::LOG_AREA . "EmailApplication ID: {$appIdLog} - Manual supporting officer details. Workflow initiation to support stage may require admin intervention or different handling.");
                // For manual officer, an admin might need to create an Approval task or it's handled offline.
                // Optionally, notify an admin group.
            }

            DB::commit();
            Log::info(self::LOG_AREA . "EmailApplication ID: {$appIdLog} submitted. Status: {$application->status}.");
            NotificationFacade::send($submitter, new EmailApplicationSubmitted($application));
            return $application->fresh(['user', 'supportingOfficer', 'approvals']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to submit EmailApplication ID: {$appIdLog}.", ['error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal menghantar permohonan e-mel: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param array<string, mixed> $provisioningDetails Usually ['final_assigned_email' => ?, 'final_assigned_user_id' => ?]
     */
    public function processProvisioning(EmailApplication $application, array $provisioningDetails, User $actingAdmin): EmailApplication
    {
        $appIdLog = $application->id;
        // Authorization handled by controller: $actingAdmin->can('processByIT', $application)

        if ($application->status !== EmailApplication::STATUS_APPROVED) {
            Log::warning(self::LOG_AREA . "Attempt to process non-approved EmailApplication ID: {$appIdLog}. Status: {$application->status}.");
            throw new RuntimeException(__("Permohonan mesti berstatus ':statusDiluluskan' untuk tindakan ini. Status semasa: :statusSemasa", ['statusDiluluskan' => EmailApplication::getStatusOptions()[EmailApplication::STATUS_APPROVED] ?? EmailApplication::STATUS_APPROVED, 'statusSemasa' => $application->statusTranslated]));
        }

        Log::info(self::LOG_AREA . "Processing provisioning for EmailApplication ID: {$appIdLog} by Admin User ID: {$actingAdmin->id}.");
        DB::beginTransaction();
        try {
            $application->transitionToStatus(EmailApplication::STATUS_PROCESSING, __('Proses penyediaan dimulakan oleh Pentadbir IT.'), $actingAdmin->id);

            // Update with details from provisioning form (admin might override proposed)
            $application->final_assigned_email = $provisioningDetails['final_assigned_email'] ?? $application->final_assigned_email ?? $application->proposed_email;
            $application->final_assigned_user_id = $provisioningDetails['final_assigned_user_id'] ?? $application->final_assigned_user_id;
            $application->save();

            // Call the actual provisioning service
            $provisionResult = $this->emailProvisioningService->provisionEmailAccount($application); // Service now takes only $application

            if ($provisionResult['success']) {
                $application->transitionToStatus(EmailApplication::STATUS_COMPLETED, $provisionResult['message'] ?? __('Akaun e-mel/ID berjaya disediakan.'), $actingAdmin->id);
                // Update with confirmed details if provisioning service returns them explicitly
                $application->final_assigned_email = $provisionResult['email'] ?? $application->final_assigned_email;
                $application->final_assigned_user_id = $provisionResult['user_id_assigned'] ?? $application->final_assigned_user_id;
                $application->save();
                Log::info(self::LOG_AREA . "Provisioning completed for EmailApplication ID: {$appIdLog}. Email: {$application->final_assigned_email}, UserID: {$application->final_assigned_user_id}.");
                if ($application->user) {
                    NotificationFacade::send($application->user, new EmailProvisioningComplete($application));
                }
            } else {
                $failureReason = $provisionResult['message'] ?? __('Proses penyediaan akaun e-mel/ID gagal tanpa sebab khusus.');
                $application->transitionToStatus(EmailApplication::STATUS_PROVISION_FAILED, $failureReason, $actingAdmin->id);
                $application->admin_notes = ($application->admin_notes ? $application->admin_notes . "\n" : '') . __('Kegagalan Penyediaan: ') . $failureReason; // Using admin_notes for failure reason
                $application->save();
                Log::error(self::LOG_AREA . "Provisioning failed for EmailApplication ID: {$appIdLog}. Reason: " . $failureReason);
                NotificationFacade::send($actingAdmin, new EmailProvisioningFailedNotification($application, $failureReason)); // Notify admin of failure
            }
            DB::commit();
            return $application->fresh();
        } catch (Throwable $e) {
            DB::rollBack();
            try {
                if ($application->status !== EmailApplication::STATUS_PROVISION_FAILED) {
                    $application->status = EmailApplication::STATUS_PROVISION_FAILED;
                    $application->admin_notes = ($application->admin_notes ? $application->admin_notes . "\n" : '') . __('Ralat kritikal semasa penyediaan: ') . $e->getMessage();
                    if ($application->isDirty()) $application->saveQuietly();
                }
            } catch (Throwable $saveErr) { /* Log and ignore */ }
            Log::error(self::LOG_AREA . "Critical error during provisioning for EmailApplication ID: {$appIdLog}.", ['error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal memproses penyediaan akaun e-mel: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function deleteApplication(EmailApplication $application, User $deleter): bool
    {
        $appIdLog = $application->id;
        // Authorization should be handled by policy in controller via $deleter->can('delete', $application)
        Log::info(self::LOG_AREA . "Attempting to soft delete EmailApplication ID: {$appIdLog} by User ID: {$deleter->id}.");

        DB::beginTransaction();
        try {
            // Also soft delete related approvals
            if (method_exists($application, 'approvals')) {
                $application->approvals()->delete(); // This should trigger BlameableObserver on Approval if set up
            }
            $deleted = $application->delete(); // Soft delete application itself

            if ($deleted) {
                DB::commit();
                Log::info(self::LOG_AREA . "EmailApplication ID: {$appIdLog} soft deleted successfully by User ID: {$deleter->id}.");
                return true;
            }
            DB::rollBack();
            Log::warning(self::LOG_AREA . "Soft delete returned false for EmailApplication ID: {$appIdLog}.");
            return false;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Failed to soft delete EmailApplication ID: {$appIdLog}.", ['error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal memadam permohonan e-mel: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
