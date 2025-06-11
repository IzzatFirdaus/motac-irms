<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class EquipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * This method grants 'Admin' users access to all actions, so we don't
     * need to check for the 'Admin' role in the other policy methods.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the list of equipment in the admin section.
     */
    public function viewAny(User $user): bool
    {
        // This is the key fix: Explicitly check if the user has the required role.
        return $user->hasRole('BPM Staff');
    }

    /**
     * Determine whether the user can view a specific equipment's details.
     */
    public function view(User $user, Equipment $equipment): bool
    {
        // Allows BPM Staff to view details of any equipment.
        return $user->hasRole('BPM Staff');
    }

    /**
     * Determine whether the user can create new equipment.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('BPM Staff');
    }

    /**
     * Determine whether the user can update the equipment.
     */
    public function update(User $user, Equipment $equipment): bool
    {
        // Prevent updates if the equipment is already disposed of or lost.
        if (in_array($equipment->status, [Equipment::STATUS_DISPOSED, Equipment::STATUS_LOST])) {
            return false;
        }

        return $user->hasRole('BPM Staff');
    }

    /**
     * Determine whether the user can delete the equipment.
     */
    public function delete(User $user, Equipment $equipment): bool
    {
        // Prevent deletion if the equipment is currently on loan.
        if ($equipment->status === Equipment::STATUS_ON_LOAN) {
            return false;
        }

        return $user->hasRole('BPM Staff');
    }
}
