<?php

namespace App\Policies;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailApplicationPolicy
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
        // ADJUSTMENT: Changed from role check to a permission check.
        return $user->can('view_all_email_applications');
    }

    public function view(User $user, EmailApplication $emailApplication): bool
    {
        // ADJUSTMENT: Simplified to check for general permission or ownership.
        return $user->id === $emailApplication->user_id ||
               $user->id === $emailApplication->supporting_officer_id ||
               $user->can('view_email_applications');
    }

    public function create(User $user): bool
    {
        return $user->can('create_email_applications');
    }

    public function update(User $user, EmailApplication $emailApplication): bool
    {
        if ($user->id === $emailApplication->user_id && $emailApplication->isDraft()) {
            return true;
        }

        // ADJUSTMENT: Changed from role check to permission check for IT Admin edits.
        return $user->can('process_email_provisioning') && in_array($emailApplication->status, [
            EmailApplication::STATUS_PENDING_ADMIN,
            EmailApplication::STATUS_APPROVED,
            EmailApplication::STATUS_PROCESSING,
        ]);
    }

    public function delete(User $user, EmailApplication $emailApplication): bool
    {
        return $user->id === $emailApplication->user_id && $emailApplication->isDraft();
    }

    public function submit(User $user, EmailApplication $emailApplication): bool
    {
        return $user->id === $emailApplication->user_id && $emailApplication->isDraft();
    }

    public function processByIT(User $user, EmailApplication $emailApplication): bool
    {
        // ADJUSTMENT: Changed from role check to a permission check.
        return $user->can('process_email_provisioning') &&
               in_array($emailApplication->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN]);
    }

    public function viewAnyAdmin(User $user): bool
    {
        return $user->can('view_any_admin_email_applications');
    }
}
