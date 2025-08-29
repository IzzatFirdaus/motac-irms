<?php

/**
 * TicketNotificationService.
 *
 * Service responsible for sending notifications related to helpdesk tickets.
 *
 * PHP version 8.2
 *
 * @category Services
 *
 * @author   MOTAC IRMS <dev@motac.example>
 * @license  https://opensource.org/licenses/MIT MIT
 *
 * @link     https://example.com/motac-irms
 */

namespace App\Services;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentAddedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Handles sending notifications for helpdesk tickets.
 *
 * @category Services
 *
 * @author   MOTAC IRMS <dev@motac.example>
 * @license  https://opensource.org/licenses/MIT MIT
 *
 * @link     https://example.com/motac-irms
 */
class TicketNotificationService
{
    /**
     * Notify when a new ticket is created.
     *
     * @param HelpdeskTicket $ticket The newly created ticket.
     */
    public function notifyTicketCreated(HelpdeskTicket $ticket): void
    {
        // Notify the applicant
        $ticket->applicant->notify(
            new TicketCreatedNotification($ticket, 'applicant')
        );

        // Notify IT Admins
        $itAdmins = User::role('IT Admin')
            ->get(); // Assuming 'IT Admin' is the role for support staff

        Notification::send(
            $itAdmins,
            new TicketCreatedNotification($ticket, 'admin')
        );

        Log::info(
            "Ticket Created Notification sent for Ticket ID: {$ticket->id}"
        );
    }

    /**
     * Notify when a ticket is assigned.
     *
     * @param HelpdeskTicket $ticket   The ticket being assigned.
     * @param User           $assigner The user who performed the assignment.
     */
    public function notifyTicketAssigned(HelpdeskTicket $ticket, User $assigner): void
    {
        if ($ticket->assignedTo) {
            $agent = $ticket->assignedTo;

            $agent->notify(
                new TicketAssignedNotification($ticket, $assigner)
            );

            $logMessage = sprintf(
                'Ticket Assigned Notification sent for Ticket ID: %s to Agent: %s',
                $ticket->id,
                $agent->email
            );

            Log::info($logMessage);
        }
    }

    /**
     * Notify when a ticket's status is updated.
     *
     * @param HelpdeskTicket $ticket  The updated ticket.
     * @param User           $updater The user who updated the ticket status.
     */
    public function notifyTicketStatusUpdated(HelpdeskTicket $ticket, User $updater): void
    {
        // Notify applicant
        $ticket->applicant->notify(
            new TicketStatusUpdatedNotification($ticket, $updater, 'applicant')
        );

        // Notify assigned agent if different from updater
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $updater->id) {
            $ticket->assignedTo->notify(
                new TicketStatusUpdatedNotification($ticket, $updater, 'agent')
            );
        }

        Log::info(
            "Ticket Status Updated Notification sent for Ticket ID: {$ticket->id}"
        );
    }

    /**
     * Notify when a comment is added to a ticket.
     *
     * @param HelpdeskComment $comment   The comment that was added.
     * @param User            $commenter The user who added the comment.
     */
    public function notifyTicketCommentAdded(HelpdeskComment $comment, User $commenter): void
    {
        $ticket = $comment->ticket;

        // Notify applicant if comment is not internal OR if internal and applicant needs to see (less common)
        if (! $comment->is_internal && $ticket->applicant->id !== $commenter->id) {
            $ticket->applicant->notify(
                new TicketCommentAddedNotification($comment, $commenter, 'applicant')
            );
        }

        // Notify assigned agent if different from commenter
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $commenter->id) {
            $ticket->assignedTo->notify(
                new TicketCommentAddedNotification($comment, $commenter, 'agent')
            );
        }

        // If an internal comment, notify other IT Admins
        if ($comment->is_internal) {
            $itAdmins = User::role('IT Admin')
                ->where('id', '!=', $commenter->id) // Don't notify the commenter themselves
                ->get();

            Notification::send(
                $itAdmins,
                new TicketCommentAddedNotification($comment, $commenter, 'internal_admin')
            );
        }

        $logMessage = sprintf(
            'Ticket Comment Added Notification sent for Ticket ID: %s by User: %s',
            $ticket->id,
            $commenter->email
        );

        Log::info($logMessage);
    }

    /**
     * Handle escalation notification for overdue tickets.
     * This method might be called by a scheduled command.
     *
     * @param HelpdeskTicket $ticket The ticket that was escalated.
     */
    public function notifyTicketEscalated(HelpdeskTicket $ticket): void
    {
        $itAdmins = User::role('IT Admin')->get();

        // Send escalation notification to IT Admins and keep a log entry
        Notification::send($itAdmins, new \App\Notifications\TicketEscalatedNotification($ticket));

        Log::warning("Ticket Escalated: Ticket ID {$ticket->id} is overdue.");
    }
}
