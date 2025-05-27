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
use App\Notifications\ApplicationSubmitted;
use App\Notifications\ApplicationStatusUpdatedNotification;
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
 */
final class NotificationService
{
    private const LOG_AREA = 'NotificationService:';

    /**
     * Notifies an applicant about a status update on their application
     * using the dedicated ApplicationStatusUpdatedNotification.
     */
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
                'application_type' => $applicationClass, 'application_id' => $applicationId,
            ]);
            return;
        }

        $applicantId = $applicant->id;
        Log::info(self::LOG_AREA." Preparing ApplicationStatusUpdatedNotification to Applicant ID {$applicantId} for {$applicationClass} ID {$applicationId}. Status: {$oldStatus} -> {$newStatus}.", [
            'applicant_id' => $applicantId, 'application_id' => $applicationId
        ]);

        try {
            $notification = new ApplicationStatusUpdatedNotification($application, $oldStatus, $newStatus);
            $applicant->notify($notification);
            Log::info(self::LOG_AREA." ApplicationStatusUpdatedNotification sent to Applicant ID {$applicantId}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA." Failed to send ApplicationStatusUpdatedNotification for {$applicationClass} ID {$applicationId}: ".$e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    /**
     * Notifies a user with a generic DefaultUserNotification.
     */
    public function notifyUserWithDefaultNotification(
        User $userToNotify,
        string $taskTitle,
        string $taskMessage,
        ?Model $relatedItem = null,
        ?string $actionUrl = null,
        string $actionText = 'Lihat Butiran' // Default localized
    ): void {
        $userId = $userToNotify->id ?? 'N/A_USER_ID';
        $itemInfo = $relatedItem ? " related to ".($relatedItem::class)." ID ".($relatedItem->id ?? 'N/A') : '';

        Log::info(self::LOG_AREA." Preparing DefaultUserNotification to User ID {$userId} for task '{$taskTitle}'{$itemInfo}.", [
            'user_id' => $userId, 'task_title' => $taskTitle
        ]);

        try {
            $greeting = __('Salam Sejahtera, :name,', ['name' => $userToNotify->name ?? __('Pengguna')]);
            $notification = new DefaultUserNotification(
                $taskTitle, $greeting, $taskMessage, $actionUrl, $actionText
            );
            $userToNotify->notify($notification);
            Log::info(self::LOG_AREA." DefaultUserNotification sent to User ID {$userId}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA." Failed to send DefaultUserNotification to User ID {$userId}: ".$e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    /**
     * Dispatches a given Notification object to a group of users.
     */
    public function notifyGroup(
        iterable|User $users,
        Notification $notificationInstance,
        ?Model $relatedModel = null
    ): void {
        $context = $relatedModel ? ($relatedModel::class)." ID ".($relatedModel->id ?? 'N/A') : 'general context';
        Log::info(self::LOG_AREA." Preparing to send group notification ".($notificationInstance::class)." for {$context}.");

        try {
            NotificationFacade::send($users, $notificationInstance);
            $userCount = $users instanceof User ? 1 : count($users instanceof SupportCollection ? $users->all() : $users);
            Log::info(self::LOG_AREA." Group notification ".($notificationInstance::class)." dispatched to {$userCount} users for {$context}.");
        } catch (Exception $e) {
            Log::error(self::LOG_AREA." Failed group notification dispatch of ".($notificationInstance::class)." for {$context}: ".$e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    // --- Specific Notification Dispatch Methods ---

    public function notifyApplicantApplicationSubmitted(EmailApplication|LoanApplication $application): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for ApplicationSubmitted: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new ApplicationSubmitted($application), $application);
    }

    public function notifyApproverApplicationNeedsAction(Approval $approvalTask, Model $approvableItem, User $approver): void
    {
        $this->notifyGroup($approver, new ApplicationNeedsAction($approvalTask, $approvableItem), $approvableItem);
    }

    public function notifyApplicantApplicationApproved(EmailApplication|LoanApplication $application): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for ApplicationApproved: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new ApplicationApproved($application), $application);
    }

    public function notifyApplicantApplicationRejected(EmailApplication|LoanApplication $application, User $rejecter, ?string $reason): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for ApplicationRejected: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new ApplicationRejected($application, $rejecter, $reason), $application);
    }

    /** @param User|iterable<User> $admins */
    public function notifyAdminEmailReadyForProcessing(EmailApplication $application, User|iterable $admins): void
    {
        $this->notifyGroup($admins, new EmailApplicationReadyForProcessingNotification($application), $application);
    }

    public function notifyApplicantEmailProvisioned(EmailApplication $application): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for EmailProvisioned: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new EmailProvisionedNotification($application), $application);
    }

    /** @param User|iterable<User> $admins */
    public function notifyAdminProvisioningFailed(EmailApplication $application, string $failureReason, User|iterable $admins, ?User $triggeringAdmin = null, ?array $errorDetails = null): void
    {
        // Pass errorDetails to the constructor if ProvisioningFailedNotification is updated to accept it
        $this->notifyGroup($admins, new ProvisioningFailedNotification($application, $failureReason, $triggeringAdmin /*, $errorDetails */), $application);
    }

    /** @param User|iterable<User> $bpmStaff */
    public function notifyBpmLoanReadyForIssuance(LoanApplication $application, User|iterable $bpmStaff): void
    {
        $this->notifyGroup($bpmStaff, new LoanApplicationReadyForIssuanceNotification($application), $application);
    }

    public function notifyApplicantEquipmentIssued(LoanApplication $application, LoanTransaction $issueTransaction, User $issuedByOfficer): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for EquipmentIssued: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new EquipmentIssuedNotification($application, $issueTransaction, $issuedByOfficer), $application);
    }

    public function notifyApplicantEquipmentReturned(LoanApplication $application, LoanTransaction $returnTransaction, User $returnAcceptingOfficer): void
    {
        if (!$application->user) {
            Log::warning(self::LOG_AREA."Applicant missing for EquipmentReturned: ".($application->id ?? 'N/A'));
            return;
        }
        $this->notifyGroup($application->user, new EquipmentReturnedNotification($application, $returnTransaction, $returnAcceptingOfficer), $application);
    }

    public function notifyUserEquipmentReturnReminder(LoanApplication $application, int $daysUntilReturn, User $userToNotify): void
    {
        $this->notifyGroup($userToNotify, new EquipmentReturnReminderNotification($application, $daysUntilReturn), $application);
    }

    /**
     * @param User|iterable<User> $partiesToNotify
     * @param EloquentCollection<int, \App\Models\LoanTransactionItem> $incidentItems
     */
    public function notifyRelevantPartiesEquipmentIncident(LoanApplication $application, EloquentCollection $incidentItems, string $incidentType, User|iterable $partiesToNotify): void
    {
        $this->notifyGroup($partiesToNotify, new EquipmentIncidentNotification($application, $incidentItems, $incidentType), $application);
    }
}
