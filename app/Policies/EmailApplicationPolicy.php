<?php

namespace App\Policies;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class EmailApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Admins can bypass all other checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any email applications.
     * (This is for admin-level views of all applications).
     */
    public function viewAny(User $user): Response
    {
        return $user->can('view_all_email_applications')
            ? Response::allow()
            : Response::deny(__('You do not have permission to view all email applications.'));
    }

    /**
     * Determine whether the user can view a specific email application.
     */
    public function view(User $user, EmailApplication $emailApplication): Response
    {
        $isOwner = $user->id === $emailApplication->user_id;
        $isSupportingOfficer = $user->id === $emailApplication->supporting_officer_id;
        $canViewAll = $user->hasRole('IT Admin') || $user->can('view_email_applications');

        return $isOwner || $isSupportingOfficer || $canViewAll
            ? Response::allow()
            : Response::deny(__('You do not have permission to view this email application.'));
    }

    /**
     * Determine whether the user can create a new email application.
     */
    public function create(User $user): Response
    {
        // ADJUSTED: This logic is now more flexible.
        // Instead of limiting creation to a specific 'Applicant' role, we now allow any
        // authenticated user to create an application. This is a common practice, as the
        // act of filling out a form is generally open to all users.
        return Response::allow();
    }

    /**
     * Determine whether the user can update the application.
     */
    public function update(User $user, EmailApplication $emailApplication): Response
    {
        // The owner can update their own application only if it's a draft.
        if ($user->id === $emailApplication->user_id && $emailApplication->isDraft()) {
            return Response::allow();
        }

        // An authorized user (e.g., IT Admin) can update it during processing stages.
        if ($user->can('process_email_provisioning')) {
            $isProcessableStatus = in_array($emailApplication->status, [
                EmailApplication::STATUS_PENDING_ADMIN,
                EmailApplication::STATUS_APPROVED,
                EmailApplication::STATUS_PROCESSING,
            ]);

            return $isProcessableStatus
                ? Response::allow()
                : Response::deny(__('This application is not in a state that can be updated by an administrator.'));
        }

        return Response::deny(__('You cannot update this email application.'));
    }

    /**
     * Determine whether the user can delete the application.
     */
    public function delete(User $user, EmailApplication $emailApplication): Response
    {
        return ($user->id === $emailApplication->user_id && $emailApplication->isDraft())
            ? Response::allow()
            : Response::deny(__('You can only delete your own draft applications.'));
    }

    /**
     * Determine whether the user can submit the application for approval.
     */
    public function submit(User $user, EmailApplication $emailApplication): Response
    {
        return ($user->id === $emailApplication->user_id && $emailApplication->isDraft())
            ? Response::allow()
            : Response::deny(__('You can only submit your own draft applications.'));
    }

    /**
     * Determine whether the user can process the application as an IT Admin.
     */
    public function processByIT(User $user, EmailApplication $emailApplication): Response
    {
        $canProcess = $user->can('process_email_provisioning');
        $isProcessableStatus = in_array($emailApplication->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN]);

        return $canProcess && $isProcessableStatus
            ? Response::allow()
            : Response::deny(__('You do not have permission to process this application or it is not in a processable state.'));
    }

    /**
     * Determine whether the user can view the admin-specific list of applications.
     */
    public function viewAnyAdmin(User $user): Response
    {
        return $user->can('view_any_admin_email_applications')
            ? Response::allow()
            : Response::deny(__('You do not have permission to view the admin list of email applications.'));
    }
}
