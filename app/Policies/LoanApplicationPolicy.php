<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Admins can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) { // Standardized to 'Admin'
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any loan applications (e.g., a full list).
     * Restricted to specific roles or permissions. Regular users view their own via controller logic.
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins handled by before().
        // Allow BPM Staff or users with a specific permission to view all loan applications.
        // Design Ref: Section 5.2 (BPM Staff involvement)
        return $user->hasRole('BPM Staff') || // Standardized to 'BPM Staff'
          $user->hasPermissionTo('view_all_loan_applications')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat senarai permohonan pinjaman.');
    }

    /**
     * Determine whether the user can view the specified loan application.
     */
    public function view(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admins handled by before().
        // Applicant, BPM staff, or users with general view permission can view.
        // Design Ref: Section 4.3 (loan_applications.user_id)
        return $user->id === $loanApplication->user_id || // Owner
          $user->hasRole('BPM Staff') || // Standardized to 'BPM Staff'
          $user->hasPermissionTo('view_loan_applications')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk melihat permohonan pinjaman ini.');
    }

    /**
     * Determine whether the user can create loan applications.
     */
    public function create(User $user): Response|bool
    {
        // Any authenticated user can create.
        return $user->id !== null
          ? Response::allow()
          : Response::deny('Anda mesti log masuk untuk membuat permohonan pinjaman.');
    }

    /**
     * Determine whether the user can update the specified loan application.
     * Typically only the applicant can update a draft application. Admins handled by before().
     */
    public function update(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Assumes LoanApplication model has an isDraft() method.
        // Design Ref: Section 4.3 (loan_applications.status: 'draft')
        return $user->id === $loanApplication->user_id && // Owner
          $loanApplication->isDraft()
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mengemaskini permohonan pinjaman ini atau ia bukan draf.');
    }

    /**
     * Determine whether the user can delete the specified loan application.
     * Typically only the applicant can delete a draft application. Admins handled by before().
     */
    public function delete(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Assumes LoanApplication model has an isDraft() method.
        // Design Ref: Section 4.3 (loan_applications.status: 'draft')
        return $user->id === $loanApplication->user_id && // Owner
          $loanApplication->isDraft()
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam permohonan pinjaman ini atau ia bukan draf.');
    }

    /**
     * Determine whether the user can restore the specified loan application.
     */
    public function restore(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admins handled by before().
        return $user->hasPermissionTo('restore_loan_applications')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memulihkan permohonan pinjaman ini.');
    }

    /**
     * Determine whether the user can permanently delete the specified loan application.
     */
    public function forceDelete(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admins handled by before().
        return $user->hasPermissionTo('force_delete_loan_applications')
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memadam permohonan pinjaman ini secara kekal.');
    }

    /**
     * Determine whether the user can submit a draft loan application.
     */
    public function submit(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admins likely won't submit for users (handled by before if they could). Applicant submits their own draft.
        // Assumes LoanApplication model has an isDraft() method.
        // Design Ref: Section 4.3 (loan_applications.status: 'draft')
        return $user->id === $loanApplication->user_id && // Owner
          $loanApplication->isDraft()
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk menghantar permohonan pinjaman ini atau ia bukan draf.');
    }

    /**
     * Determine whether the user has the general ability to *approve* loan applications.
     * Actual approval of an assigned task is governed by ApprovalPolicy.
     * This checks general capability and if the application is in a state to be approved.
     */
    public function approve(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admins handled by before().
        // Design Ref: Section 4.3 (loan_applications.status: 'pending_support', 'pending_hod_review', 'pending_bpm_review')
        $isPending = in_array($loanApplication->status, [
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_HOD_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
            // Ensure these constants exist in LoanApplication model and match design doc statuses
        ]);

        return $user->hasPermissionTo('approve_loan_applications') && $isPending
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk meluluskan permohonan pinjaman ini atau ia tidak dalam status menunggu kelulusan.');
    }

    /**
     * Determine whether the user can process issuance for the loan application.
     * This action is typically performed by BPM staff after approval. Admins handled by before().
     */
    public function processIssuance(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Design Ref: Section 5.2 (BPM Staff Involvement), Section 4.3 (loan_applications.status: 'approved', 'partially_issued')
        $canProcess = $user->hasRole('BPM Staff') && // Standardized to 'BPM Staff'
          in_array($loanApplication->status, [
              LoanApplication::STATUS_APPROVED,
              LoanApplication::STATUS_PARTIALLY_ISSUED,
              // Ensure these constants exist in LoanApplication model and match design doc statuses
          ]);

        return $canProcess
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan pinjaman ini atau statusnya tidak membenarkan.');
    }

    /**
     * Determine whether the user can process return for the loan application.
     * This action is typically performed by BPM staff when equipment is returned. Admins handled by before().
     */
    public function processReturn(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Design Ref: Section 5.2 (BPM Staff Involvement), Section 4.3 (loan_applications.status: 'issued', 'partially_issued', 'overdue')
        $canProcess = $user->hasRole('BPM Staff') && // Standardized to 'BPM Staff'
          in_array($loanApplication->status, [
              LoanApplication::STATUS_ISSUED,
              LoanApplication::STATUS_PARTIALLY_ISSUED,
              LoanApplication::STATUS_OVERDUE,
              // Ensure these constants exist in LoanApplication model and match design doc statuses
          ]);

        return $canProcess
          ? Response::allow()
          : Response::deny('Anda tidak mempunyai kebenaran untuk memproses pemulangan bagi permohonan pinjaman ini atau statusnya tidak membenarkan.');
    }
}
