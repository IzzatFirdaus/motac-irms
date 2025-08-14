<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GradePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any grade models.
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins or users with specific permission can view any grade.
        return $user->hasRole('Admin') || $user->hasPermissionTo('view_grades')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat senarai gred.');
    }

    /**
     * Determine whether the user can view a specific grade model.
     */
    public function view(User $user, Grade $grade): Response|bool
    {
        // Admins or users with specific permission can view a grade.
        return $user->hasRole('Admin') || $user->hasPermissionTo('view_grades')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat gred ini.');
    }

    /**
     * Determine whether the user can create new grade models.
     */
    public function create(User $user): Response|bool
    {
        // Admins or users with specific permission can create grades.
        return $user->hasRole('Admin') || $user->hasPermissionTo('manage_grades')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mencipta gred.');
    }

    /**
     * Determine whether the user can update a specific grade model.
     */
    public function update(User $user, Grade $grade): Response|bool
    {
        // Admins or users with specific permission can update grades.
        return $user->hasRole('Admin') || $user->hasPermissionTo('manage_grades')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mengemaskini gred ini.');
    }

    /**
     * Determine whether the user can delete a specific grade model.
     * Prevents deletion if the grade is associated with any users or positions.
     */
    public function delete(User $user, Grade $grade): Response|bool
    {
        $canManage = $user->hasRole('Admin') || $user->hasPermissionTo('manage_grades');

        if ($canManage) {
            // Design Ref (Rev. 3.5): Section 4.1 (users.grade_id[cite: 70], positions.grade_id [cite: 73])
            if (method_exists($grade, 'users') && $grade->users()->exists()) {
                return Response::deny('Gred ini tidak boleh dipadam kerana ia sedang digunakan oleh pengguna.');
            }

            if (method_exists($grade, 'positions') && $grade->positions()->exists()) {
                return Response::deny('Gred ini tidak boleh dipadam kerana ia sedang digunakan oleh jawatan.');
            }

            return Response::allow();
        }

        return Response::deny('Anda tidak mempunyai kebenaran untuk memadam gred ini.');
    }

    /**
     * Determine whether the user can restore a specific grade model (if using soft deletes).
     */
    public function restore(User $user, Grade $grade): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('manage_grades')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memulihkan gred.');
    }

    /**
     * Determine whether the user can permanently delete a specific grade model.
     * This action is typically more restricted.
     */
    public function forceDelete(User $user, Grade $grade): Response|bool
    {
        if ($user->hasRole('Admin')) {
            // Design Ref (Rev. 3.5): Section 4.1 (users.grade_id[cite: 70], positions.grade_id [cite: 73])
            if (method_exists($grade, 'users') && $grade->users()->exists()) {
                return Response::deny('Gred ini tidak boleh dipadam secara kekal kerana ia sedang digunakan oleh pengguna.');
            }

            if (method_exists($grade, 'positions') && $grade->positions()->exists()) {
                return Response::deny('Gred ini tidak boleh dipadam secara kekal kerana ia sedang digunakan oleh jawatan.');
            }

            return Response::allow();
        }

        return Response::deny('Anda tidak mempunyai kebenaran untuk memadam gred ini secara kekal.');
    }
}
