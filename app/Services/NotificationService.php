<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\Equipment;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Notifications\ApplicationApproved;
use App\Notifications\ApplicationNeedsAction;
use App\Notifications\ApplicationRejected;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\EquipmentIncidentNotification;
use App\Notifications\EquipmentIssuedNotification;
use App\Notifications\EquipmentOverdueNotification;
use App\Notifications\EquipmentReturnedNotification;
use App\Notifications\EquipmentReturnReminderNotification;
use App\Notifications\LoanApplicationReadyForIssuanceNotification;
use App\Notifications\SupportPendingApprovalNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentAddedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification instance to a user.
     *
    * @param User $user
    * @param Notification $notification
     */
    public function notifyUser(User $user, Notification $notification): void
    {
        try {
            $user->notify($notification);
            Log::info("Notification sent to user {$user->id}.");
        } catch (Exception $e) {
            Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Notify an approver that a new approval request is pending their action.
     */
    public function notifyApproverOfPendingApproval(User $approver, Approval $approval): void
    {
        $this->notifyUser($approver, new ApplicationNeedsAction($approval));
    }

    /**
     * Notify support officers that a new loan application is pending their review.
     */
    public function notifySupportOfPendingApproval(LoanApplication $loanApplication): void
    {
        // Use the 'Supporting Officer' role name which exists in the system
        $supportUsers = User::role('Supporting Officer')->get();
        foreach ($supportUsers as $supportUser) {
            $this->notifyUser($supportUser, new SupportPendingApprovalNotification($loanApplication));
        }
    }

    /**
     * Notify the applicant that their loan application has been approved.
     */
    public function notifyApplicationApproved(User $recipient, LoanApplication $loanApplication): void
    {
        $this->notifyUser($recipient, new ApplicationApproved($loanApplication));
    }

    /**
     * Notify the applicant that their loan application has been rejected.
     *
     * @param User            $recipient       The user to notify (applicant)
     * @param LoanApplication $loanApplication The application that was rejected
     * @param string          $rejectionReason The reason for rejection
     * @param User|null       $rejecter        The user who rejected the application (optional)
     */
    public function notifyApplicationRejected(User $recipient, LoanApplication $loanApplication, string $rejectionReason, ?User $rejecter = null): void
    {
        // If rejecter is not provided, use the currently authenticated user
        if ($rejecter === null) {
            // Safely handle the case when there might not be an authenticated user
            if (Auth::check()) {
                $rejecter = Auth::user();
            } else {
                // Use system user or the first admin as fallback
                $rejecter = User::role('Admin')->first() ?? User::find(1);
            }
        }

        // If we still don't have a User, bail out safely (shouldn't happen in normal setups)
        if (! $rejecter instanceof User) {
            Log::warning('No rejecter available when notifying application rejection; skipping notification.', ['loan_application_id' => $loanApplication->id ?? null]);
            return;
        }

        // Create the notification (now that $rejecter is guaranteed to be User)
        $notification = new ApplicationRejected(
            $loanApplication,
            $rejecter,
            $rejectionReason
        );

        // Send notification to recipient
        $this->notifyUser($recipient, $notification);
    }

    /**
     * Notify the applicant that their loan application status has been updated.
     *
     * @param string|null $reason (optional, not in constructor)
     */
    public function notifyApplicationStatusUpdated(User $recipient, LoanApplication $loanApplication, string $newStatus, ?string $reason = null): void
    {
        // The ApplicationStatusUpdatedNotification constructor does not take $reason.
        $notification = new ApplicationStatusUpdatedNotification(
            $recipient,      // User
            $loanApplication, // LoanApplication
            $newStatus        // string
            // $reason is not in the constructor, so do not pass it
        );

        $this->notifyUser($recipient, $notification);
    }

    /**
     * Notify the applicant that their loan application is ready for issuance.
     */
    public function notifyLoanApplicationReadyForIssuance(User $recipient, LoanApplication $loanApplication): void
    {
        $this->notifyUser($recipient, new LoanApplicationReadyForIssuanceNotification($loanApplication));
    }

    /**
     * Notify a user that equipment has been issued.
     */
    public function notifyLoanIssued(User $recipient, LoanApplication $loanApplication, LoanTransaction $loanTransaction): void
    {
        $this->notifyUser($recipient, new EquipmentIssuedNotification($loanApplication, $loanTransaction));
    }

    /**
     * Notify a user that equipment has been returned.
     */
    public function notifyLoanReturned(User $recipient, LoanApplication $loanApplication, LoanTransaction $loanTransaction): void
    {
        $this->notifyUser($recipient, new EquipmentReturnedNotification($loanApplication, $loanTransaction));
    }

    /**
     * Notify about an incident related to equipment.
     */
    public function notifyEquipmentIncident(User $recipient, LoanApplication $loanApplication, Equipment $equipment, string $incidentType, ?string $notes = null): void
    {
        // Ensure we pass an Eloquent Collection as required by the notification constructor
        $collection = Equipment::whereIn('id', [$equipment->id])->get();
        $this->notifyUser($recipient, new EquipmentIncidentNotification($loanApplication, $collection, $incidentType));
    }

    /**
     * Notify a user about an upcoming equipment return date.
     */
    public function notifyEquipmentReturnReminder(User $recipient, LoanApplication $loanApplication, int $daysUntilReturn): void
    {
        $this->notifyUser($recipient, new EquipmentReturnReminderNotification($loanApplication, $daysUntilReturn));
    }

    /**
     * Notify a user about overdue equipment.
     */
    public function notifyEquipmentOverdue(User $recipient, LoanTransaction $loanTransaction, int $overdueDays): void
    {
        $loanApplication = $loanTransaction->loanApplication;

        if (! $loanApplication instanceof LoanApplication) {
            Log::warning('LoanTransaction missing loanApplication when sending overdue notification', ['loan_transaction_id' => $loanTransaction->id ?? null]);
            return;
        }

        $this->notifyUser($recipient, new EquipmentOverdueNotification(
            $loanApplication,
            $overdueDays
        ));
    }

    public function notifyTicketCreated(User $recipient, HelpdeskTicket $ticket, string $recipientType): void
    {
        $this->notifyUser($recipient, new TicketCreatedNotification($ticket, $recipientType));
    }

    public function notifyTicketAssigned(User $assignedTo, HelpdeskTicket $ticket, User $assigner): void
    {
        $this->notifyUser($assignedTo, new TicketAssignedNotification($ticket, $assigner));
    }

    public function notifyTicketStatusUpdated(User $recipient, HelpdeskTicket $ticket, User $updater, string $recipientType): void
    {
        $this->notifyUser($recipient, new TicketStatusUpdatedNotification($ticket, $updater, $recipientType));
    }

    public function notifyTicketCommentAdded(User $recipient, HelpdeskComment $comment, User $commenter, string $recipientType): void
    {
        $this->notifyUser($recipient, new TicketCommentAddedNotification($comment, $commenter, $recipientType));
    }
}
