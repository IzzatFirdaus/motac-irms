<?php

namespace App\Services;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentAddedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use App\Notifications\TicketEscalatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TicketNotificationService
{
    /**
     * Notify when a new ticket is created.
     *
     * @param HelpdeskTicket $ticket
     * @return void
     */
    public function notifyTicketCreated(HelpdeskTicket $ticket): void
    {
        // Notify the applicant
        $ticket->applicant->notify(new TicketCreatedNotification($ticket, 'applicant'));

        // Notify IT Admins
        $itAdmins = User::role('IT Admin')->get(); // Assuming 'IT Admin' is the role for support staff
        Notification::send($itAdmins, new TicketCreatedNotification($ticket, 'admin'));

        Log::info("Ticket Created Notification sent for Ticket ID: {$ticket->id}");
    }

    /**
     * Notify when a ticket is assigned.
     *
     * @param HelpdeskTicket $ticket
     * @param User $assigner
     * @return void
     */
    public function notifyTicketAssigned(HelpdeskTicket $ticket, User $assigner): void
    {
        if ($ticket->assignedTo) {
            $ticket->assignedTo->notify(new TicketAssignedNotification($ticket, $assigner));
            Log::info("Ticket Assigned Notification sent for Ticket ID: {$ticket->id} to Agent: {$ticket->assignedTo->email}");
        }
    }

    /**
     * Notify when a ticket's status is updated.
     *
     * @param HelpdeskTicket $ticket
     * @param User $updater
     * @return void
     */
    public function notifyTicketStatusUpdated(HelpdeskTicket $ticket, User $updater): void
    {
        // Notify applicant
        $ticket->applicant->notify(new TicketStatusUpdatedNotification($ticket, $updater, 'applicant'));

        // Notify assigned agent if different from updater
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $updater->id) {
            $ticket->assignedTo->notify(new TicketStatusUpdatedNotification($ticket, $updater, 'agent'));
        }

        Log::info("Ticket Status Updated Notification sent for Ticket ID: {$ticket->id}");
    }

    /**
     * Notify when a comment is added to a ticket.
     *
     * @param HelpdeskComment $comment
     * @param User $commenter
     * @return void
     */
    public function notifyTicketCommentAdded(HelpdeskComment $comment, User $commenter): void
    {
        $ticket = $comment->ticket;

        // Notify applicant if comment is not internal OR if internal and applicant needs to see (less common)
        if (!$comment->is_internal && $ticket->applicant->id !== $commenter->id) {
            $ticket->applicant->notify(new TicketCommentAddedNotification($comment, $commenter, 'applicant'));
        }

        // Notify assigned agent if different from commenter
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $commenter->id) {
            $ticket->assignedTo->notify(new TicketCommentAddedNotification($comment, $commenter, 'agent'));
        }

        // If an internal comment, notify other IT Admins
        if ($comment->is_internal) {
            $itAdmins = User::role('IT Admin')
                            ->where('id', '!=', $commenter->id) // Don't notify the commenter themselves
                            ->get();
            Notification::send($itAdmins, new TicketCommentAddedNotification($comment, $commenter, 'internal_admin'));
        }

        Log::info("Ticket Comment Added Notification sent for Ticket ID: {$ticket->id} by User: {$commenter->email}");
    }

    /**
     * Handle escalation notification for overdue tickets.
     * This method might be called by a scheduled command.
     *
     * @param HelpdeskTicket $ticket
     * @return void
     */
    public function notifyTicketEscalated(HelpdeskTicket $ticket): void
    {
        $itAdmins = User::role('IT Admin')->get();
    Notification::send($itAdmins, new TicketEscalatedNotification($ticket));
    Log::warning("Ticket Escalated: Ticket ID {$ticket->id} is overdue.");
    }
}
