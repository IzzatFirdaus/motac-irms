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

  public function viewAny(User $user): Response|bool
  {
    // Adjusted to use Spatie's hasAnyRole and hasPermissionTo for clarity
    // Roles 'Admin', 'BPM Staff', 'IT Admin'
    return $user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin']) || $user->hasPermissionTo('view_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat inventori peralatan.'));
  }

  public function view(User $user, Equipment $equipment): Response|bool
  {
    // Allow if user has general view permission or specific logic (e.g., if it's assigned to their department)
    // Roles 'Admin', 'BPM Staff', 'IT Admin'
    return $user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin']) || $user->hasPermissionTo('view_equipment') // || ($equipment->department_id === $user->department_id) // Example additional logic
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat aset peralatan ini.'));
  }

  public function create(User $user): Response|bool
  {
    // Roles 'Admin', 'BPM Staff'
    return $user->hasAnyRole(['Admin', 'BPM Staff']) || $user->hasPermissionTo('create_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk mencipta aset peralatan.'));
  }

  public function update(User $user, Equipment $equipment): Response|bool
  {
    // Prevent update if equipment is in a non-editable operational state
    // Design Ref: Section 4.3 (equipment.status: 'disposed', 'lost')
    if (in_array($equipment->status, [Equipment::STATUS_DISPOSED, Equipment::STATUS_LOST])) { // Ensure constants exist
      return Response::deny(__('Tidak boleh mengemaskini peralatan yang telah dilupus atau hilang.'));
    }

    // Roles 'Admin', 'BPM Staff'
    return $user->hasAnyRole(['Admin', 'BPM Staff']) || $user->hasPermissionTo('update_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini aset peralatan ini.'));
  }

  public function delete(User $user, Equipment $equipment): Response|bool
  {
    // Design Ref: Section 4.3 (equipment.status: 'on_loan')
    if ($equipment->status === Equipment::STATUS_ON_LOAN) { // Ensure constant exists
      return Response::deny(__('Tidak boleh memadam peralatan yang sedang dalam pinjaman.'));
    }

    // Roles 'Admin', 'BPM Staff'
    return $user->hasAnyRole(['Admin', 'BPM Staff']) || $user->hasPermissionTo('delete_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam aset peralatan ini.'));
  }

  public function restore(User $user, Equipment $equipment): Response|bool
  {
    // Roles 'Admin', 'BPM Staff'
    return $user->hasAnyRole(['Admin', 'BPM Staff']) || $user->hasPermissionTo('restore_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan aset peralatan ini.'));
  }

  public function forceDelete(User $user, Equipment $equipment): Response|bool
  {
    // Design Ref: Section 4.3 (equipment.status: 'on_loan')
    if ($equipment->status === Equipment::STATUS_ON_LOAN) { // Ensure constant exists
      return Response::deny(__('Tidak boleh memadam secara kekal peralatan yang sedang dalam pinjaman.'));
    }

    // Force delete usually restricted to super admin or BPM Staff with specific permission
    // Role 'Admin', 'BPM Staff'
    return $user->hasRole('Admin') || ($user->hasAnyRole(['BPM Staff']) && $user->hasPermissionTo('force_delete_equipment'))
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam aset peralatan ini secara kekal.'));
  }

  /**
   * Determine whether the user can process the issuance of a specific equipment asset.
   * Policy check for *initiating* an issue process.
   */
  public function processIssue(User $user, Equipment $equipment): Response|bool
  {
    // Roles 'Admin', 'BPM Staff'
    if (! $user->hasAnyRole(['Admin', 'BPM Staff'])) {
      return Response::deny(__('Hanya Admin atau Staf BPM boleh mengeluarkan peralatan.'));
    }
    // Equipment must be operationally available AND in a suitable physical condition for loan
    // Design Ref: Section 4.3 (equipment.status: 'available'; equipment.condition_status: 'good', 'fair')
    // Note: CONDITION_NEW is used here and is reflected in design doc section 4.3 now.
    if ($equipment->status !== Equipment::STATUS_AVAILABLE) { // Ensure constant exists
      return Response::deny(__('Peralatan ini tidak tersedia untuk pengeluaran (Status Operasi: :status).', ['status' => $equipment->status]));
    }
    if (! in_array($equipment->condition_status, [Equipment::CONDITION_NEW, Equipment::CONDITION_GOOD, Equipment::CONDITION_FAIR])) { // Ensure constants exist
      return Response::deny(__('Peralatan ini tidak dalam keadaan fizikal yang sesuai untuk dipinjamkan (Keadaan: :condition).', ['condition' => $equipment->condition_status]));
    }

    return Response::allow();
  }

  /**
   * Determine whether the user can process the return of a specific equipment asset.
   * Policy check for *accepting* a return.
   */
  public function processReturn(User $user, Equipment $equipment): Response|bool
  {
    // Roles 'Admin', 'BPM Staff'
    if (! $user->hasAnyRole(['Admin', 'BPM Staff'])) {
      return Response::deny(__('Hanya Admin atau Staf BPM boleh memproses pemulangan peralatan.'));
    }
    // Design Ref: Section 4.3 (equipment.status: 'on_loan')
    if ($equipment->status !== Equipment::STATUS_ON_LOAN) { // Ensure constant exists
      return Response::deny(__('Peralatan ini tidak dalam status dipinjam.'));
    }

    return Response::allow();
  }
}
