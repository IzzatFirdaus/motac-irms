<?php

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy class for HelpdeskTicket model.
 *
 * Controls access to HelpdeskTicket actions (view, update, delete, etc.) based on user roles and relationships.
 */
class HelpdeskTicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     * Allows Admin, IT Admin, or users with specific permission.
     */
    public function viewAny(User $user): bool
    {
        // Grant access to ticket listing for admins and users with explicit permission
        return $user->hasRole('Admin') ||
               $user->hasRole('IT Admin') ||
               $user->hasPermissionTo('view helpdesk tickets');
    }

    /**
     * Determine whether the user can view a specific ticket.
     * Allows Admin, IT Admin, the applicant, or the assigned agent.
     */
    public function view(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        // Allow viewing if user is admin, IT admin, the ticket's applicant, or assigned agent
        return $user->hasRole('Admin') ||
               $user->hasRole('IT Admin') ||
               $user->id === $helpdeskTicket->user_id ||
               ($helpdeskTicket->assigned_to_user_id && $user->id === $helpdeskTicket->assigned_to_user_id);
    }

    /**
     * Determine whether the user can create tickets.
     * Any authenticated user can create a helpdesk ticket.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update a ticket.
     * Only Admin, IT Admin, or the assigned agent (if ticket not closed) can update.
     * The applicant cannot update, but can comment.
     */
    public function update(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        // Permit update if user is Admin or IT Admin, or the assigned agent and ticket is not closed
        return ($user->hasRole('Admin') ||
                $user->hasRole('IT Admin') ||
                ($helpdeskTicket->assigned_to_user_id && $user->id === $helpdeskTicket->assigned_to_user_id && $helpdeskTicket->status !== HelpdeskTicket::STATUS_CLOSED)
        );
    }

    /**
     * Determine whether the user can delete a ticket.
     * Only Admin can delete a ticket (usually tickets are closed, not deleted).
     */
    public function delete(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore a ticket.
     * Only Admin can restore a deleted ticket.
     */
    public function restore(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete a ticket.
     * Only Admin can force-delete.
     */
    public function forceDelete(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can add a comment to the ticket.
     * Applicant, assigned agent, IT Admin, or Admin can comment.
     */
    public function addComment(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->id === $helpdeskTicket->user_id ||
               ($helpdeskTicket->assigned_to_user_id && $user->id === $helpdeskTicket->assigned_to_user_id) ||
               $user->hasRole('IT Admin') ||
               $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can close the ticket (set status to "closed").
     * Only Admin, IT Admin, or assigned agent (if not already closed) can close.
     */
    public function close(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return ($user->hasRole('Admin') ||
                $user->hasRole('IT Admin') ||
                ($helpdeskTicket->assigned_to_user_id && $user->id === $helpdeskTicket->assigned_to_user_id && $helpdeskTicket->status !== HelpdeskTicket::STATUS_CLOSED)
        );
    }
}
