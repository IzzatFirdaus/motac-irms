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
     */
    public function before(User $user, string $ability): ?bool
    {
        // Standardized to use Spatie's hasRole for consistency if User model uses HasRoles trait
        if ($user->hasRole('Admin')) { // Role 'Admin'
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_equipment');
    }

    public function view(User $user, Equipment $equipment): bool
    {
        return $user->can('view_equipment_details');
    }

    public function create(User $user): bool
    {
        return $user->can('create_equipment');
    }

    public function update(User $user, Equipment $equipment): Response|bool
    {
        // Prevent update if equipment is in a non-editable operational state
        // Design Ref: Section 4.3 (equipment.status: 'disposed', 'lost')
        if (in_array($equipment->status, [Equipment::STATUS_DISPOSED, Equipment::STATUS_LOST])) { // Ensure constants exist
            return Response::deny(__('Tidak boleh mengemaskini peralatan yang telah dilupus atau hilang.'));
        }

        return $user->can('edit_equipment');
    }

    public function delete(User $user, Equipment $equipment): Response|bool
    {
        // Design Ref: Section 4.3 (equipment.status: 'on_loan')
        if ($equipment->status === Equipment::STATUS_ON_LOAN) { // Ensure constant exists
            return Response::deny(__('Tidak boleh memadam peralatan yang sedang dalam pinjaman.'));
        }

        return $user->can('delete_equipment');
    }

    /**
     * Determine whether the user can process the issuance of a specific equipment asset.
     * Policy check for *initiating* an issue process.
     */
    public function processIssue(User $user, Equipment $equipment): bool
    {
        return $user->can('process_loan_issuance');
    }

    /**
     * Determine whether the user can process the return of a specific equipment asset.
     * Policy check for *accepting* a return.
     */
    public function processReturn(User $user, Equipment $equipment): bool
    {
        return $user->can('process_loan_return');
    }
}
