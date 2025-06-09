<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EmailApplicationService
{
    private const LOG_AREA = 'EmailApplicationService: ';

    protected EmailProvisioningService $emailProvisioningService;

    protected ApprovalService $approvalService;

    private NotificationService $notificationService;

    private array $defaultEmailApplicationRelations = [
        'user:id,name,title,personal_email,department_id,grade_id,position_id,service_status,appointment_type,mobile_number',
        'user.department:id,name',
        'user.grade:id,name,level',
        'user.position:id,name',
        'supportingOfficer:id,name,title,personal_email,grade_id',
        'supportingOfficer.grade:id,name,level',
        'approvals.officer:id,name,title',
        'creator:id,name',
        'updater:id,name',
        'processor:id,name',
    ];

    public function __construct(
        EmailProvisioningService $emailProvisioningService,
        ApprovalService $approvalService,
        NotificationService $notificationService
    ) {
        $this->emailProvisioningService = $emailProvisioningService;
        $this->approvalService = $approvalService;
        $this->notificationService = $notificationService;
    }

    private function prepareApplicationData(array $inputData, User $applicantOrUpdater, ?EmailApplication $existingApplication = null): array
    {
        $applicationData = $inputData;
        if (! $existingApplication) {
            $applicationData['user_id'] = $applicantOrUpdater->id;
            $snapshotFields = [
                'applicant_title', 'applicant_name', 'applicant_identification_number',
                'applicant_passport_number', 'applicant_jawatan_gred',
                'applicant_bahagian_unit', 'applicant_level_aras',
                'applicant_mobile_number', 'applicant_personal_email',
            ];
            foreach ($snapshotFields as $field) {
                if (array_key_exists($field, $inputData)) {
                    $applicationData[$field] = $inputData[$field];
                }
            }
        }

        // Align with new migration: purpose, group_admin_name, group_admin_email
        if (isset($inputData['application_reason_notes']) && ! isset($inputData['purpose'])) {
            $applicationData['purpose'] = $inputData['application_reason_notes'];
            unset($applicationData['application_reason_notes']);
        }
        if (isset($inputData['contact_person_name']) && ! isset($inputData['group_admin_name'])) {
            $applicationData['group_admin_name'] = $inputData['contact_person_name'];
            unset($applicationData['contact_person_name']);
        }
        if (isset($inputData['contact_person_email']) && ! isset($inputData['group_admin_email'])) {
            $applicationData['group_admin_email'] = $inputData['contact_person_email'];
            unset($applicationData['contact_person_email']);
        }

        $certInfo = isset($inputData['cert_info_is_true']) ? (bool) $inputData['cert_info_is_true'] : ($existingApplication?->cert_info_is_true ?? false);
        $certUsage = isset($inputData['cert_data_usage_agreed']) ? (bool) $inputData['cert_data_usage_agreed'] : ($existingApplication?->cert_data_usage_agreed ?? false);
        $certResp = isset($inputData['cert_email_responsibility_agreed']) ? (bool) $inputData['cert_email_responsibility_agreed'] : ($existingApplication?->cert_email_responsibility_agreed ?? false);

        $applicationData['cert_info_is_true'] = $certInfo;
        $applicationData['cert_data_usage_agreed'] = $certUsage;
        $applicationData['cert_email_responsibility_agreed'] = $certResp;

        if ($certInfo && $certUsage && $certResp) {
            if (! ($existingApplication && $existingApplication->certification_timestamp && $existingApplication->areAllCertificationsComplete())) {
                $applicationData['certification_timestamp'] = Carbon::now();
            } elseif ($existingApplication) {
                $applicationData['certification_timestamp'] = $existingApplication->certification_timestamp;
            }
        } else {
            $applicationData['certification_timestamp'] = null;
        }

        return $applicationData;
    }

    public function createDraftApplication(array $data, User $applicant): EmailApplication
    {
        Log::info(self::LOG_AREA.'Creating DRAFT email application.', ['user_id' => $applicant->id]);

        return DB::transaction(function () use ($data, $applicant) {
            $applicationData = $this->prepareApplicationData($data, $applicant);
            $applicationData['status'] = EmailApplication::STATUS_DRAFT;
            $applicationData['submitted_at'] = null;
            /** @var EmailApplication $application */
            $application = EmailApplication::create($applicationData);
            Log::info(self::LOG_AREA.'DRAFT Email application CREATED.', ['id' => $application->id]);

            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    public function updateDraftApplication(EmailApplication $application, array $data, User $updater): EmailApplication
    {
        if (! $application->isDraft()) {
            Log::warning(self::LOG_AREA.'Attempt to update non-draft application.', ['id' => $application->id, 'status' => $application->status]);
            throw new RuntimeException(__('Hanya draf permohonan yang boleh dikemaskini.'));
        }
        Log::info(self::LOG_AREA.'Updating DRAFT email application.', ['id' => $application->id, 'user_id' => $updater->id]);

        return DB::transaction(function () use ($application, $data, $updater) {
            $updateData = $this->prepareApplicationData($data, $updater, $application);
            $updateData['status'] = EmailApplication::STATUS_DRAFT;
            $application->fill($updateData);
            $application->save();
            Log::info(self::LOG_AREA.'DRAFT Email application UPDATED.', ['id' => $application->id]);

            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    private function submitApplicationLogic(EmailApplication $application, User $submitter): EmailApplication
    {
        if (! $application->areAllCertificationsComplete()) {
            throw new InvalidArgumentException(__('Semua tiga perakuan mesti ditandakan untuk menghantar permohonan.'));
        }

        $supportingOfficerModel = null;
        $minSupportGradeLevel = (int) Config::get('motac.approval.min_email_supporting_officer_grade_level', 9);

        if ($application->supporting_officer_id) {
            $supportingOfficerModel = User::with('grade:id,name,level')->find($application->supporting_officer_id);
            if (! $supportingOfficerModel) {
                throw new ModelNotFoundException(__('Pegawai Penyokong (dari sistem) yang dipilih tidak ditemui.'));
            }
            if (! $supportingOfficerModel->grade || (int) $supportingOfficerModel->grade->level < $minSupportGradeLevel) {
                $minGradeName = Grade::where('level', $minSupportGradeLevel)->value('name') ?? "Gred {$minSupportGradeLevel}";
                throw new InvalidArgumentException(__('Pegawai Penyokong (:name) tidak memenuhi syarat gred minima (:minGrade). Gred semasa: :currentGrade', ['name' => $supportingOfficerModel->name, 'minGrade' => $minGradeName, 'currentGrade' => $supportingOfficerModel->grade?->name ?? __('Tidak Ditetapkan')]));
            }
        } elseif (empty($application->supporting_officer_name) || empty($application->supporting_officer_email) || empty($application->supporting_officer_grade)) {
            throw new InvalidArgumentException(__('Maklumat Pegawai Penyokong (Nama, E-mel, dan Gred) mesti diisi dengan lengkap jika tiada pegawai dari sistem dipilih.'));
        }

        $application->status = EmailApplication::STATUS_PENDING_SUPPORT;
        $application->certification_timestamp = $application->certification_timestamp ?? Carbon::now();
        $application->submitted_at = Carbon::now();
        $application->save();

        $manualOfficerDetails = (! $supportingOfficerModel && ! empty($application->supporting_officer_name)) ? ['officer_name' => $application->supporting_officer_name, 'officer_email' => $application->supporting_officer_email, 'officer_grade' => $application->supporting_officer_grade] : null;

        $approvalTask = $this->approvalService->initiateApprovalWorkflow($application, $submitter, Approval::STAGE_EMAIL_SUPPORT_REVIEW, $supportingOfficerModel, $manualOfficerDetails);
        $this->notificationService->notifyApplicantApplicationSubmitted($application);
        if ($approvalTask && $supportingOfficerModel) {
            $this->notificationService->notifyApproverApplicationNeedsAction($approvalTask, $application, $supportingOfficerModel);
        } elseif ($manualOfficerDetails) {
            Log::info(self::LOG_AREA."Approval for Email App ID: {$application->id} relies on manual officer: {$manualOfficerDetails['officer_email']}. Manual notification may be required.");
        }

        Log::info(self::LOG_AREA.'Email application SUBMITTED.', ['id' => $application->id, 'status' => $application->status]);

        return $application->fresh($this->defaultEmailApplicationRelations);
    }

    public function createAndSubmitApplication(array $data, User $applicant): EmailApplication
    {
        Log::info(self::LOG_AREA.'Creating and Submitting email application.', ['user_id' => $applicant->id]);
        if (! (isset($data['cert_info_is_true']) && $data['cert_info_is_true'] && isset($data['cert_data_usage_agreed']) && $data['cert_data_usage_agreed'] && isset($data['cert_email_responsibility_agreed']) && $data['cert_email_responsibility_agreed'])) {
            throw new InvalidArgumentException(__('Semua tiga perakuan mesti ditandakan untuk menghantar permohonan.'));
        }

        return DB::transaction(function () use ($data, $applicant) {
            $applicationData = $this->prepareApplicationData($data, $applicant);
            $applicationData['status'] = EmailApplication::STATUS_DRAFT;
            /** @var EmailApplication $application */
            $application = EmailApplication::create($applicationData);
            Log::info(self::LOG_AREA.'Email application (for immediate submission) created as draft.', ['id' => $application->id]);

            return $this->submitApplicationLogic($application, $applicant);
        });
    }

    public function submitDraftApplication(EmailApplication $application, array $data, User $submitter): EmailApplication
    {
        if (! $application->isDraft()) {
            Log::warning(self::LOG_AREA.'Attempt to submit non-draft application.', ['id' => $application->id, 'status' => $application->status]);
            throw new RuntimeException(__('Hanya draf permohonan yang boleh dihantar.'));
        }
        Log::info(self::LOG_AREA.'Submitting DRAFT email application.', ['id' => $application->id, 'submitter_id' => $submitter->id]);

        return DB::transaction(function () use ($application, $data, $submitter) {
            $updateData = $this->prepareApplicationData($data, $submitter, $application);
            $application->fill($updateData)->save();

            return $this->submitApplicationLogic($application, $submitter);
        });
    }

    public function processProvisioning(EmailApplication $application, array $provisioningDetails, User $actingAdmin): EmailApplication
    {
        $actionableStatuses = [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN, EmailApplication::STATUS_PROVISION_FAILED];
        if (! in_array($application->status, $actionableStatuses)) {
            $currentStatusLabel = EmailApplication::getStatusOptions()[$application->status] ?? $application->status;
            throw new RuntimeException(__('Permohonan mesti berstatus diluluskan, menunggu tindakan IT, atau gagal sebelum ini untuk tindakan penyediaan. Status semasa: :currentStatus', ['currentStatus' => __($currentStatusLabel)]));
        }
        Log::info(self::LOG_AREA.'Processing provisioning for EmailApplication.', ['id' => $application->id, 'admin_id' => $actingAdmin->id]);

        return DB::transaction(function () use ($application, $provisioningDetails, $actingAdmin) {
            $application->status = EmailApplication::STATUS_PROCESSING;
            $application->processed_by = $actingAdmin->id;
            $application->processed_at = Carbon::now();
            $application->save();

            $targetEmail = $provisioningDetails['final_assigned_email'] ?? null;
            $targetUserId = $provisioningDetails['user_id_assigned'] ?? null;
            if (empty($targetEmail) && empty($targetUserId)) {
                throw new InvalidArgumentException(__('E-mel rasmi atau ID Pengguna yang akan disediakan adalah mandatori.'));
            }
            $application->final_assigned_email = $targetEmail;
            $application->final_assigned_user_id = $targetUserId;

            $provisionResult = $this->emailProvisioningService->provisionEmailAccount($application, (string) $targetEmail, $targetUserId);

            if ($provisionResult['success']) {
                $application->status = EmailApplication::STATUS_COMPLETED;
                $application->final_assigned_email = $provisionResult['assigned_email'] ?? $application->final_assigned_email;
                $application->final_assigned_user_id = $provisionResult['assigned_user_id'] ?? $application->final_assigned_user_id;
                $application->rejection_reason = null;
                if ($application->user) {
                    $userToUpdate = $application->user;
                    if ($application->final_assigned_email) {
                        $userToUpdate->motac_email = $application->final_assigned_email;
                    }
                    if ($application->final_assigned_user_id) {
                        $userToUpdate->user_id_assigned = $application->final_assigned_user_id;
                    }
                    $userToUpdate->save();
                }
                Log::info(self::LOG_AREA.'Provisioning COMPLETED.', ['id' => $application->id, 'assigned_email' => $application->final_assigned_email]);
                if ($application->user && method_exists($this->notificationService, 'notifyApplicantEmailProvisioned')) {
                    $this->notificationService->notifyApplicantEmailProvisioned($application);
                }
            } else {
                $failureReason = $provisionResult['message'] ?? __('Proses penyediaan akaun e-mel/ID pengguna gagal tanpa mesej spesifik.');
                $application->status = EmailApplication::STATUS_PROVISION_FAILED;
                $application->rejection_reason = ($application->rejection_reason ? $application->rejection_reason."\n" : '').__('Kegagalan Penyediaan IT: ').$failureReason;
                Log::error(self::LOG_AREA.'Provisioning FAILED.', ['id' => $application->id, 'reason' => $failureReason]);
                if (method_exists($this->notificationService, 'notifyAdminProvisioningFailed')) {
                    $itAdmins = User::role('IT Admin')->get();
                    if ($itAdmins->isNotEmpty()) {
                        $this->notificationService->notifyAdminProvisioningFailed($application, $failureReason, $itAdmins, $actingAdmin);
                    } else {
                        Log::warning(self::LOG_AREA.'No IT Admins found to notify for provisioning failure of App ID: '.$application->id);
                    }
                }
            }
            $application->save();

            return $application->fresh($this->defaultEmailApplicationRelations);
        });
    }

    public function deleteApplication(EmailApplication $application, User $deleter): bool
    {
        if (! $application->isDraft()) {
            Log::warning(self::LOG_AREA.'Attempt to delete non-draft application.', ['id' => $application->id, 'status' => $application->status, 'deleter_id' => $deleter->id]);
            throw new RuntimeException(__('Hanya draf permohonan yang boleh dibuang.'));
        }
        Log::info(self::LOG_AREA.'Attempting soft delete.', ['id' => $application->id, 'deleter_id' => $deleter->id]);
        try {
            return DB::transaction(function () use ($application) {
                $result = $application->delete();
                if ($result) {
                    Log::info(self::LOG_AREA.'Soft DELETED successfully.', ['id' => $application->id]);
                } else {
                    Log::warning(self::LOG_AREA.'Soft delete operation returned false.', ['id' => $application->id]);
                }

                return (bool) $result;
            });
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA.'Error during soft delete.', ['id' => $application->id, 'error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal memadamkan permohonan e-mel: ').$e->getMessage(), 0, $e);
        }
    }

    public function getDefaultEmailApplicationRelations(): array
    {
        return $this->defaultEmailApplicationRelations;
    }
}
