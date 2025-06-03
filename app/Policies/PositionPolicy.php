<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization; // Often included for convenience in policies

class PositionPolicy
{
    use HandlesAuthorization; // This trait provides the deny() and allow() methods

    /**
     * Determine whether the user can view any models.
     * System Design Reference: Policies define authorization logic for actions on specific models.
     */
    public function viewAny(User $user): bool
    {
        // Assumes 'view-any-position' permission is granted via Spatie
        return $user->can('view-any-position');
    }

    /**
     * Determine whether the user can view the model.
     * System Design Reference: Policies define authorization logic for actions on specific models.
     */
    public function view(User $user, Position $position): bool
    {
        // Assumes 'view-position' permission is granted via Spatie
        // You might add additional logic here, e.g., if the user is in the same department
        return $user->can('view-position');
    }

    /**
     * Determine whether the user can create models.
     * System Design Reference: Policies define authorization logic for actions on specific models.
     */
    public function create(User $user): bool
    {
        // Assumes 'create-position' permission is granted via Spatie
        return $user->can('create-position');
    }

    /**
     * Determine whether the user can update the model.
     * System Design Reference: Policies define authorization logic for actions on specific models.
     */
    public function update(User $user, Position $position): bool
    {
        // Assumes 'update-position' permission is granted via Spatie
        return $user->can('update-position');
    }

    /**
     * Determine whether the user can delete the model.
     * System Design Reference: Policies define authorization logic for actions on specific models.
     */
    public function delete(User $user, Position $position): bool
    {
        // Assumes 'delete-position' permission is granted via Spatie
        // Additional business logic for deletion (e.g., cannot delete if assigned to users)
        // is handled in the Livewire component's confirmPositionDeletion method.
        return $user->can('delete-position');
    }

    /**
     * Determine whether the user can restore the model (for soft deletes).
     * System Design Reference: Not explicitly detailed for restore, but common for full CRUD policy.
     */
    public function restore(User $user, Position $position): bool
    {
        // Assumes 'restore-position' permission is granted via Spatie
        return $user->can('restore-position');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * System Design Reference: Not explicitly detailed for forceDelete, but common for full CRUD policy.
     */
    public function forceDelete(User $user, Position $position): bool
    {
        // Assumes 'force-delete-position' permission is granted via Spatie
        return $user->can('force-delete-position');
    }
}
