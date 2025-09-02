<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        // Allow Admin via before(); also allow users with Approver role (used in tests)
        if ($user->hasRole('Approver')) {
            return true;
        }

        // Otherwise, check for explicit permission if assigned in production
        return $user->can('view_any_approvals');
    }

    public function view(User $user, Approval $approval): bool
    {
        // Allows the assigned officer OR anyone with broader task-viewing permissions.
        return $approval->officer_id === $user->id || $user->can('view_approval_tasks');
    }

    public function create(User $user): bool
    {
        return false; // Only Admin via before()
    }

    public function update(User $user, Approval $approval): bool
    {
        // ADJUSTMENT: Added a permission check alongside the ownership and status check.
        return $user->can('act_on_approval_tasks') && $approval->officer_id === $user->id && $approval->status === Approval::STATUS_PENDING;
    }

    /**
     * Determine whether the user can delete a specific approval record.
     * Only Admin via before().
     */
    public function delete(User $user, Approval $approval): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore a specific approval record.
     * Only Admin via before().
     */
    public function restore(User $user, Approval $approval): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete a specific approval record.
     * Only Admin via before().
     */
    public function forceDelete(User $user, Approval $approval): bool
    {
        return false;
    }
}
