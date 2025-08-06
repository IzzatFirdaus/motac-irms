<?php

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HelpdeskTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('IT Admin') || $user->hasPermissionTo('view helpdesk tickets');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('IT Admin') || $user->id === $helpdeskTicket->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any logged-in user can create a ticket
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        // Only IT Admin or the assigned agent can update the ticket status/assignment
        // The applicant can't update, but they can add comments.
        return ($user->hasRole('IT Admin') || $user->hasRole('Admin') || ($user->id === $helpdeskTicket->assigned_to_user_id && $helpdeskTicket->status !== 'closed'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        // Typically, tickets are not deleted but closed. Only admins might delete.
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        return $user->hasRole('Admin');
    }

    // Policy for adding comments
    public function addComment(User $user, HelpdeskTicket $helpdeskTicket): bool
    {
        // Applicant can comment, assigned agent can comment, IT Admin can comment
        return $user->id === $helpdeskTicket->user_id ||
               ($helpdeskTicket->assigned_to_user_id && $user->id === $helpdeskTicket->assigned_to_user_id) ||
               $user->hasRole('IT Admin') ||
               $user->hasRole('Admin'); // Explicitly allow Admin to add comments
    }
}
