<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Notifications\ApplicationApproved;
use App\Notifications\ApplicationNeedsAction;
use App\Notifications\ApplicationRejected;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\ApplicationSubmitted;
use App\Notifications\DefaultUserNotification;
use App\Notifications\EmailApplicationReadyForProcessingNotification;
use App\Notifications\EmailProvisionedNotification;
use App\Notifications\EquipmentIncidentNotification;
use App\Notifications\EquipmentIssuedNotification;
use App\Notifications\EquipmentReturnedNotification;
use App\Notifications\EquipmentReturnReminderNotification;
use App\Notifications\LoanApplicationReadyForIssuanceNotification;
use App\Notifications\ProvisioningFailedNotification;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Service to encapsulate logic for dispatching various notifications.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 3.1, 9.5
 */
final class NotificationService
{
    private const LOG_AREA = 'NotificationService:';

    public function notifyApplicantStatusUpdate(
        EmailApplication|LoanApplication $application,
        string $oldStatus,
        string $newStatus
    ): void {
        $applicant = $application->user()->first();
        $applicationClass = $application::class;
        $applicationId = $application->id ?? 'N/A_APP_ID';

        if (! $applicant instanceof User) {
            Log::warning(self::LOG_AREA." Cannot notify applicant for {$applicationClass} ID {$applicationId}. Applicant is missing.", [
                'application_type' => $applicationClass,
                'application_id' => $applicationId,
            ]);

            return;
        }

        $applicantId = $applicant->id;
        Log::info(self::LOG_AREA." Preparing ApplicationStatusUpdatedNotification to Applicant ID {$applicantId} for {$applicationClass} ID {$applicationId}. Status: {$oldStatus} -> {$newStatus}.", [
            'applicant_id' => $applicantId,
            'application_id' => $applicationId,
        ]);

        try {
            $notification = new ApplicationStatusUpdatedNotification($application, $oldStatus, $newStatus);
            $applicant->notify($notification);
            Log::info(self::LOG_AREA." ApplicationStatusUpdatedNotification sent to Applicant ID {$applicantId}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA." Failed to send ApplicationStatusUpdatedNotification for {$applicationClass} ID {$applicationId}: ".$e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    public function notifyUserWithDefaultNotification(
        User $userToNotify,
        string $taskTitle,
        string $taskMessage,
        ?Model $relatedItem = null,
        ?string $actionUrl = null,
        string $actionText = 'Lihat Butiran'
    ): void {
        $userId = $userToNotify->id ?? 'N/A_USER_ID';
        $itemInfo = $relatedItem ? ' related to '.($relatedItem::class).' ID '.($relatedItem->id ?? 'N/A') : '';

        Log::info(self::LOG_AREA." Preparing DefaultUserNotification to User ID {$userId} for task '{$taskTitle}'{$itemInfo}.", [
            'user_id' => $userId,
            'task_title' => $taskTitle,
        ]);

        try {
            $greetingKey = __('Salam Sejahtera, :name,', ['name' => $userToNotify->name ?? __('Pengguna')]);
            $additionalData = [];
            if ($relatedItem) {
                $additionalData = [
                    'related_item_type' => $relatedItem->getMorphClass(),
                    'related_item_id' => $relatedItem->id,
                    'icon' => $this->getIconForModel($relatedItem),
                ];
            }

            $notification = new DefaultUserNotification(
                $taskTitle,
                $greetingKey,
                [$taskMessage],
                $actionUrl,
                $actionText,
                $additionalData
            );
            $userToNotify->notify($notification);
            Log::info(self::LOG_AREA." DefaultUserNotification sent to User ID {$userId}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA." Failed to send DefaultUserNotification to User ID {$userId}: ".$e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    private function getIconForModel(?Model $model): string
    {
        if ($model instanceof EmailApplication) {
            return 'ti ti-mail';
        }
        if ($model instanceof LoanApplication) {
            return 'ti ti-archive';
        }
        if ($model instanceof Approval) {
            return 'ti ti-clipboard-check';
        }

        return 'ti ti-info-circle';
    }

    public function notifyGroup(
        iterable|User $users,
        Notification $notificationInstance,
        ?Model $relatedModel = null
    ): void {
        $context = $relatedModel ? ($relatedModel::class).' ID '.($relatedModel->id ?? 'N/A') : 'general context';
        $notificationClass = $notificationInstance::class;

        $notifiables = ($users instanceof User) ? [$users] : $users;
        $userCount = 0;
        if (is_iterable($notifiables)) {
            if (is_array($notifiables) || $notifiables instanceof SupportCollection || $notifiables instanceof EloquentCollection) {
                $userCount = count($notifiables);
            } else {
                foreach ($notifiables as $_) {
                    $userCount++;
                }
            }
        }

        if ($userCount === 0) {
            Log::warning(self::LOG_AREA."Attempted to send group notification '{$notificationClass}' but no users provided for context: {$context}.");
            return;
        }

        Log::info(self::LOG_AREA.' Preparing to send group notification '.($notificationClass)." for {$context} to {$userCount} user(s).");

        try {
            NotificationFacade::send($notifiables, $notificationInstance);
            Log::info(self::LOG_AREA.' Group notification '.($notificationClass)." dispatched to {$userCount} users for {$context}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA.' Failed group notification dispatch of '.($notificationClass)." for {$context}: ".$e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    public function notifyApplicantApplicationSubmitted(EmailApplication|LoanApplication $application): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(ApplicationSubmitted::class)) {
            $this->notifyGroup($application->user, new ApplicationSubmitted($application), $application);
        } else {
            Log::error(self::LOG_AREA.'ApplicationSubmitted class not found.');
        }
    }

    public function notifyApproverApplicationNeedsAction(Approval $approvalTask): void
    {
        $officer = $approvalTask->officer;

        if (! $officer) {
            Log::warning(self::LOG_AREA."Cannot send 'ApplicationNeedsAction' because no officer is assigned to the approval task.", [
                'approval_id' => $approvalTask->id,
            ]);
            return;
        }

        try {
            $officer->notify(new ApplicationNeedsAction($approvalTask));

            Log::info(self::LOG_AREA."Successfully dispatched 'ApplicationNeedsAction' notification to officer.", [
                'approver_id' => $officer->id,
                'approval_id' => $approvalTask->id,
            ]);
        } catch (Exception $e) {
            Log::error(self::LOG_AREA."Failed to send 'ApplicationNeedsAction' notification.", [
                'error' => $e->getMessage(),
                'approver_id' => $officer->id,
                'approval_id' => $approvalTask->id,
            ]);
        }
    }

    public function notifyApplicantApplicationApproved(EmailApplication|LoanApplication $application): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(ApplicationApproved::class)) {
            $this->notifyGroup($application->user, new ApplicationApproved($application), $application);
        } else {
            Log::error(self::LOG_AREA.'ApplicationApproved class not found.');
        }
    }

    public function notifyApplicantApplicationRejected(EmailApplication|LoanApplication $application, User $rejecter, ?string $reason): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(ApplicationRejected::class)) {
            $this->notifyGroup($application->user, new ApplicationRejected($application, $rejecter, $reason), $application);
        } else {
            Log::error(self::LOG_AREA.'ApplicationRejected class not found.');
        }
    }

    public function notifyAdminEmailReadyForProcessing(EmailApplication $application, User|iterable $admins): void
    {
        if (class_exists(EmailApplicationReadyForProcessingNotification::class)) {
            $this->notifyGroup($admins, new EmailApplicationReadyForProcessingNotification($application), $application);
        } else {
            Log::error(self::LOG_AREA.'EmailApplicationReadyForProcessingNotification class not found.');
        }
    }

    public function notifyApplicantEmailProvisioned(EmailApplication $application): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(EmailProvisionedNotification::class)) {
            $this->notifyGroup($application->user, new EmailProvisionedNotification($application), $application);
        } else {
            Log::error(self::LOG_AREA.'EmailProvisionedNotification class not found.');
        }
    }

    public function notifyAdminProvisioningFailed(EmailApplication $application, string $failureReason, User|iterable $admins, ?User $triggeringAdmin = null): void
    {
        if (class_exists(ProvisioningFailedNotification::class)) {
            $this->notifyGroup($admins, new ProvisioningFailedNotification($application, $failureReason, $triggeringAdmin), $application);
        } else {
            Log::error(self::LOG_AREA.'ProvisioningFailedNotification class not found.');
        }
    }

    public function notifyBpmLoanReadyForIssuance(LoanApplication $application, User|iterable $bpmStaff): void
    {
        if (class_exists(LoanApplicationReadyForIssuanceNotification::class)) {
            $this->notifyGroup($bpmStaff, new LoanApplicationReadyForIssuanceNotification($application), $application);
        } else {
            Log::error(self::LOG_AREA.'LoanApplicationReadyForIssuanceNotification class not found.');
        }
    }

    public function notifyApplicantEquipmentIssued(LoanApplication $application, LoanTransaction $issueTransaction, User $issuedByOfficer): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(EquipmentIssuedNotification::class)) {
            $this->notifyGroup($application->user, new EquipmentIssuedNotification($application, $issueTransaction, $issuedByOfficer), $application);
        } else {
            Log::error(self::LOG_AREA.'EquipmentIssuedNotification class not found.');
        }
    }

    public function notifyApplicantEquipmentReturned(LoanApplication $application, LoanTransaction $returnTransaction, User $returnAcceptingOfficer): void
    {
        if (! $application->user) {
            return;
        }
        if (class_exists(EquipmentReturnedNotification::class)) {
            $this->notifyGroup($application->user, new EquipmentReturnedNotification($application, $returnTransaction, $returnAcceptingOfficer), $application);
        } else {
            Log::error(self::LOG_AREA.'EquipmentReturnedNotification class not found.');
        }
    }

    public function notifyUserEquipmentReturnReminder(LoanApplication $application, int $daysUntilReturn, User $userToNotify): void
    {
        if (class_exists(EquipmentReturnReminderNotification::class)) {
            $this->notifyGroup($userToNotify, new EquipmentReturnReminderNotification($application, $daysUntilReturn), $application);
        } else {
            Log::error(self::LOG_AREA.'EquipmentReturnReminderNotification class not found.');
        }
    }

    public function notifyRelevantPartiesEquipmentIncident(LoanApplication $application, EloquentCollection $incidentItems, string $incidentType, User|iterable $partiesToNotify): void
    {
        if (class_exists(EquipmentIncidentNotification::class)) {
            $this->notifyGroup($partiesToNotify, new EquipmentIncidentNotification($application, $incidentItems, $incidentType), $application);
        } else {
            Log::error(self::LOG_AREA.'EquipmentIncidentNotification class not found.');
        }
    }

    /**
     * Notifies administrators about an application that is stuck in the workflow
     * because no subsequent approver could be found.
     */
    public function notifyAdminOfOrphanedApplication(LoanApplication|EmailApplication $application): void
    {
        $admins = User::role('Admin')->where('status', User::STATUS_ACTIVE)->get();
        if ($admins->isEmpty()) {
            Log::error(self::LOG_AREA . "No active 'Admin' users found to notify about orphaned application.", [
                'application_id' => $application->id,
                'application_type' => $application->getMorphClass(),
            ]);
            return;
        }

        $appType = $application instanceof LoanApplication ? 'Pinjaman' : 'Emel';
        $title = "Amaran: Permohonan Terkandas";
        $message = "Sistem gagal mencari pegawai yang sesuai untuk peringkat kelulusan seterusnya bagi permohonan {$appType} #{$application->id}. Sila semak permohonan tersebut dan konfigurasikan aliran kerja kelulusan secara manual jika perlu.";

        $actionUrl = '#'; // Default URL
        if ($application instanceof LoanApplication) {
            $actionUrl = route('loan-applications.show', $application->id);
        } elseif ($application instanceof EmailApplication) {
            // Assuming a similar route exists for email applications
            // $actionUrl = route('email-applications.show', $application->id);
        }

        $this->notifyGroup(
            $admins,
            new DefaultUserNotification(
                $title,
                'Salam Sejahtera,',
                [$message],
                $actionUrl,
                'Lihat Permohonan'
            ),
            $application
        );
    }
}
