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
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) { // Standardized to 'Admin'
            return true;
        }

        return null;
    }

    public function viewAny(User $user): Response|bool
    {
        // Admins (by before), IT Admins, or users with specific permission can view all.
        // Design Ref: Sections 5.1 (IT Admin role)
        if ($user->hasAnyRole(['IT Admin']) || $user->hasPermissionTo('view_any_email_applications')) { // Standardized to 'IT Admin'
            return Response::allow();
        }

        return Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai permohonan e-mel.'));
    }

    public function view(User $user, EmailApplication $emailApplication): Response|bool
    {
        // Admins (by before), owner, IT Admins, assigned supporting officer,
        // or users with general view permission.
        // Design Ref: Section 4.2 (email_applications.user_id, email_applications.supporting_officer_id)
        return $user->id === $emailApplication->user_id ||
               $user->id === $emailApplication->supporting_officer_id ||
               $user->hasAnyRole(['IT Admin']) || // Standardized to 'IT Admin'
               $user->hasPermissionTo('view_email_applications')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat permohonan e-mel ini.'));
    }

    public function create(User $user): Response|bool
    {
        // Any authenticated user can generally create an application.
        return $user->exists
            ? Response::allow()
            : Response::deny(__('Anda mesti log masuk untuk membuat permohonan e-mel.'));
    }

    public function update(User $user, EmailApplication $emailApplication): Response|bool
    {
        // Owner can update if it's a draft.
        // Assumes EmailApplication model has an isDraft() method or accessor.
        if ($user->id === $emailApplication->user_id && $emailApplication->isDraft()) {
            return Response::allow();
        }
        // IT Admin can update certain fields at certain stages (e.g., final_assigned_email, status).
        // Design Ref: Section 5.1 (IT Admin provisioning), Section 4.2 (email_applications.status)
        if ($user->hasRole('IT Admin') && in_array($emailApplication->status, [ // Standardized to 'IT Admin'
            EmailApplication::STATUS_PENDING_ADMIN, //
            EmailApplication::STATUS_APPROVED, //
            EmailApplication::STATUS_PROCESSING, //
            EmailApplication::STATUS_COMPLETED, //
            EmailApplication::STATUS_PROVISION_FAILED, //
        ])) {
            return Response::allow();
        }

        return Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini permohonan e-mel ini.'));
    }

    public function delete(User $user, EmailApplication $emailApplication): Response|bool
    {
        // Owner can delete if it's a draft. Admins can delete (via before).
        // Assumes EmailApplication model has an isDraft() method or accessor.
        return $user->id === $emailApplication->user_id && $emailApplication->isDraft()
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam permohonan e-mel ini.'));
    }

    public function submit(User $user, EmailApplication $emailApplication): Response|bool
    {
        // Owner can submit if it's a draft.
        // Assumes EmailApplication model has an isDraft() method or accessor.
        return $user->id === $emailApplication->user_id && $emailApplication->isDraft()
            ? Response::allow()
            : Response::deny(__('Hanya draf permohonan boleh dihantar.'));
    }

    public function actAsSupportingOfficer(User $user, EmailApplication $emailApplication): Response|bool
    {
        // Design Ref: Sections 5.1, 7.2 (Grade 9+ for supporting officer)
        // Design Ref: Section 4.2 (email_applications.status: 'pending_support')
        // Uses specific config key from motac.php
        $minGradeLevel = config('motac.approval.min_email_supporting_officer_grade_level', 9); //

        return $user->hasPermissionTo('act_as_email_support_officer') &&
               ($user->grade && $user->grade->level >= $minGradeLevel) &&
               $emailApplication->status === EmailApplication::STATUS_PENDING_SUPPORT //
            ? Response::allow()
            : Response::deny(__('Anda tidak layak untuk menyokong permohonan ini atau ia tidak menunggu sokongan.'));
    }

    public function processByIT(User $user, EmailApplication $emailApplication): Response|bool
    {
        // IT Admin can process applications that are 'approved' or 'pending_admin'
        // Design Ref: Section 5.1 (IT Admin processing), Section 4.2 (email_applications.status: 'approved', 'pending_admin')
        return $user->hasRole('IT Admin') && // Standardized to 'IT Admin'
               in_array($emailApplication->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN]) //
            ? Response::allow()
            : Response::deny(__('Hanya Pentadbir IT boleh memproses permohonan ini pada peringkat ini.'));
    }

}
