<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ApprovalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any approval records.
     * Allows Admins, or users whose grade indicates they are an approver
     * or meets a minimum configured grade level for viewing approvals.
     */
    public function viewAny(User $user): Response|bool
    {
        if ($user->hasRole('Admin')) { // Standardized to 'Admin'
            return Response::allow();
        }

        // Check if the user's grade is marked as an approver grade
        // OR if their grade level meets a configured minimum.
        // Design Ref: Sections 4.1 (grades.is_approver_grade), 7.2 (motac.approval.min_approver_grade_level)
        $minApprovalGradeLevel = config('motac.approval.min_approver_grade_level');
        if (
            ($user->grade && $user->grade->is_approver_grade) ||
            ($user->grade && is_numeric($minApprovalGradeLevel) && $user->grade->level >= $minApprovalGradeLevel)
        ) {
            return Response::allow();
        }

        return Response::deny('Anda tidak mempunyai kebenaran untuk melihat senarai kelulusan.');
    }

    /**
     * Determine whether the user can view a specific approval record.
     * Allows Admins or the officer assigned to this specific approval task.
     *
     * @param  \App\Models\Approval  $approval  The specific approval model instance.
     */
    public function view(User $user, Approval $approval): Response|bool
    {
        if ($user->hasRole('Admin')) { // Standardized to 'Admin'
            return Response::allow();
        }

        // Design Ref: Section 4.4 (approvals.officer_id)
        if ($approval->officer_id === $user->id) { // User is the assigned officer
            return Response::allow();
        }

        // Potentially allow the creator of the approvable item to view its approvals too
        // if ($approval->approvable && property_exists($approval->approvable, 'user_id') && $approval->approvable->user_id === $user->id) {
        //     return Response::allow();
        // }

        return Response::deny('Anda tidak mempunyai kebenaran untuk melihat kelulusan ini.');
    }

    /**
     * Determine whether the user can create approval records.
     * Typically, approvals are system-generated. Manual creation is restricted.
     */
    public function create(User $user): Response|bool
    {
        // Only Admins can manually create approval records, if ever needed.
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mencipta rekod kelulusan.');
    }

    /**
     * Determine whether the user can update a specific approval record (i.e., approve/reject).
     * Allows Admins or the assigned officer, only if the approval is pending.
     *
     * @param  \App\Models\Approval  $approval  The specific approval model instance.
     */
    public function update(User $user, Approval $approval): Response|bool
    {
        if ($user->hasRole('Admin')) { // Standardized to 'Admin'
            return Response::allow(); // Admins can always update (e.g., override, correct)
        }

        // Assigned officer can update only if it's their task and it's pending
        // Design Ref: Section 4.4 (approvals.status: 'pending')
        return $approval->officer_id === $user->id &&
          $approval->status === Approval::STATUS_PENDING // Assuming STATUS_PENDING constant exists in Approval model and maps to 'pending'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mengambil tindakan ke atas kelulusan ini atau ia tidak lagi menunggu tindakan.');
    }

    /**
     * Determine whether the user can delete a specific approval record.
     * Deletion is highly restricted.
     *
     * @param  \App\Models\Approval  $_approval  (Marked as unused if no specific attributes are checked)
     */
    public function delete(User $user, Approval $_approval): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam rekod kelulusan.');
    }

    /**
     * Determine whether the user can restore a specific approval record (if using soft deletes).
     *
     * @param  \App\Models\Approval  $_approval  (Marked as unused)
     */
    public function restore(User $user, Approval $_approval): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memulihkan rekod kelulusan.');
    }

    /**
     * Determine whether the user can permanently delete a specific approval record.
     *
     * @param  \App\Models\Approval  $_approval  (Marked as unused)
     */
    public function forceDelete(User $user, Approval $_approval): Response|bool
    {
        return $user->hasRole('Admin') // Standardized to 'Admin'
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam rekod kelulusan secara kekal.');
    }
}
