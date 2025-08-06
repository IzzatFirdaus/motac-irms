<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

/**
 * Handles the deletion of a user account from the system.
 * This includes:
 *   - Deleting the user's profile photo (if any)
 *   - Revoking and deleting all API tokens associated with the user
 *   - Soft-deleting the user record itself (if soft deletes are enabled)
 *
 * System is up-to-date with v4.0: No references to legacy email provisioning or EmailApplication logic.
 */
class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user and related data.
     *
     * @param  User  $user  The user instance to be deleted
     */
    public function delete(User $user): void
    {
        // Remove the user's profile photo from storage, if present
        $user->deleteProfilePhoto();

        // Delete all API tokens belonging to this user
        $user->tokens->each->delete();

        // Soft delete (or hard delete if not using SoftDeletes) the user record
        $user->delete();

        // NOTE: If you wish to delete or anonymize related data (e.g., notifications, activity logs),
        // you can extend this method accordingly. As of v4.0, only the above actions are required.
    }
}
