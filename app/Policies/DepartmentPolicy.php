<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Grant all abilities to the Admin role, consistent with other policies.
        // The isAdmin() method is a helper in the User model.
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Let other policy methods decide.
    }

    /**
     * Determine whether the user can view the list of departments.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response
    {
        // Allow users with 'BPM Staff' role to view the department list. Admin is handled by before().
        // The isBpmStaff() method is a helper in the User model.
        return $user->isBpmStaff()
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai jabatan.'));
    }

    /**
     * Determine whether the user can view a specific department.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Department $department): Response
    {
        // Allow if user is BPM Staff, or if the user is the Head of this specific Department.
        // This uses the isBpmStaff() helper from the User model and the head_of_department_id from the Department model.
        return $user->isBpmStaff() || $user->id === $department->head_of_department_id
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat butiran jabatan ini.'));
    }

    /**
     * Determine whether the user can create new departments.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response
    {
        // Creating departments is a high-level task restricted to Admin (handled by before() method).
        // This will deny all other users.
        return Response::deny(__('Anda tidak mempunyai kebenaran untuk mencipta jabatan baharu.'));
    }

    /**
     * Determine whether the user can update a department.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Department $department): Response
    {
        // Allow the Head of Department to update their own department's details. Admin is handled by before().
        // This uses the head_of_department_id from the Department model.
        return $user->id === $department->head_of_department_id
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini jabatan ini.'));
    }

    /**
     * Determine whether the user can delete a department.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Department $department): Response
    {
        // Deleting departments is restricted to Admin (handled by before() method).
        return Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam jabatan ini.'));
    }

    /**
     * Determine whether the user can restore a soft-deleted department.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Department $department): Response
    {
        // Restoring is restricted to Admin (handled by before() method).
        return Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan jabatan ini.'));
    }

    /**
     * Determine whether the user can permanently delete a department.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Department $department): Response
    {
        // Force deleting is restricted to Admin (handled by before() method).
        return Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam jabatan ini secara kekal.'));
    }
}
