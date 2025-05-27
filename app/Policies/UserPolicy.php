<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user models (e.g., on a user list page).
     * Only administrators or BPM staff can view the list of all users.
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasRole('BPM Staff') // Standardized to 'Admin' and 'BPM Staff'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat senarai pengguna.');
    }

    /**
     * Determine whether the user can view a specific user model.
     * Users can view their own profile. Administrators and BPM staff can view any user's profile.
     *
     * @param  \App\Models\User  $user  The authenticated user performing the action.
     * @param  \App\Models\User  $model  The user model being viewed.
     */
    public function view(User $user, User $model): Response|bool
    {
        return $user->id === $model->id || // User can view their own profile
          $user->hasRole('Admin') ||      // Standardized to 'Admin'
          $user->hasRole('BPM Staff')      // Standardized to 'BPM Staff'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat profil pengguna ini.');
    }

    /**
     * Determine whether the user can create new user models.
     * Only administrators or BPM staff can create new users.
     */
    public function create(User $user): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasRole('BPM Staff') // Standardized to 'Admin' and 'BPM Staff'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mencipta pengguna baharu.');
    }

    /**
     * Determine whether the user can update a specific user model.
     * Users can update their own profile.
     * Administrators can update any user's profile.
     * BPM staff can update any user's profile, except for users who are administrators.
     *
     * @param  \App\Models\User  $user  The authenticated user performing the action.
     * @param  \App\Models\User  $model  The user model being updated.
     */
    public function update(User $user, User $model): Response|bool
    {
        if ($user->id === $model->id) { // User can update their own profile
            return Response::allow();
        }

        if ($user->hasRole('Admin')) { // Standardized to 'Admin'. Admins can update any user
            return Response::allow();
        }

        // BPM staff can update non-admin users
        // Standardized to 'BPM Staff' and 'Admin'
        if ($user->hasRole('BPM Staff') && ! $model->hasRole('Admin')) {
            return Response::allow();
        }

        return Response::deny('Anda tidak mempunyai kebenaran untuk mengemaskini profil pengguna ini.');
    }

    /**
     * Determine whether the user can delete a specific user model.
     * Only administrators can delete users, and they cannot delete their own account through this policy.
     *
     * @param  \App\Models\User  $user  The authenticated user performing the action.
     * @param  \App\Models\User  $model  The user model being deleted.
     */
    public function delete(User $user, User $model): Response|bool
    {
        // Only admins can delete, and not themselves
        return $user->hasRole('Admin') && $user->id !== $model->id // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam pengguna ini.');
    }

    /**
     * Determine whether the user can restore a specific soft-deleted user model.
     * Only administrators can restore users.
     */
    public function restore(User $user, User $model): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memulihkan pengguna.');
    }

    /**
     * Determine whether the user can permanently delete a specific user model.
     * Only administrators can force delete users.
     */
    public function forceDelete(User $user, User $model): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam pengguna ini secara kekal.');
    }

    /**
     * Determine whether the user can manage roles and permissions for other users.
     * This is a custom policy method, typically restricted to administrators.
     */
    public function manageRoles(User $user): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mengurus peranan dan kebenaran pengguna.');
    }

    /**
     * Determine whether the user can view sensitive user data.
     * Users can view their own; Admins and BPM staff can view for others.
     */
    public function viewSensitiveData(User $user, User $model): Response|bool
    {
        return $user->id === $model->id || // User can view their own sensitive data
          $user->hasRole('Admin') ||      // Standardized to 'Admin'
          $user->hasRole('BPM Staff')      // Standardized to 'BPM Staff'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat data sensitif pengguna ini.');
    }
}
