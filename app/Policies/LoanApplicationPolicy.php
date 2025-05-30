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
     * Admins can do anything. [cite: 56, 193]
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) { // Standardized role 'Admin' [cite: 8]
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any loan applications (e.g., a full list).
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins handled by before().
        // BPM Staff or users with specific permission can view all. [cite: 137, 192]
        return $user->hasRole('BPM Staff') ||
               $user->can('view_all_loan_applications') // Assumes a permission like 'view_all_loan_applications'
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai permohonan pinjaman ini.'));
    }

    /**
     * Determine whether the user can view the specified loan application.
     */
    public function view(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Owner, BPM staff, or users with general view permission.
        // Also consider if supporting officer or current approver should view.
        return $user->id === $loanApplication->user_id ||
               $user->id === $loanApplication->responsible_officer_id ||
               $user->id === $loanApplication->supporting_officer_id ||
               $user->id === $loanApplication->current_approval_officer_id ||
               $user->hasRole('BPM Staff') || // [cite: 192]
               $user->can('view_loan_applications') // Assumes a general permission
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat permohonan pinjaman ini.'));
    }

    /**
     * Determine whether the user can create loan applications.
     */
    public function create(User $user): Response|bool
    {
        // Any authenticated user can attempt to create. [cite: 176] (Eligibility rules apply)
        return $user->id !== null
            ? Response::allow()
            : Response::deny(__('Anda mesti log masuk untuk membuat permohonan pinjaman.'));
    }

    /**
     * Determine whether the user can update the specified loan application.
     * Only the applicant can update their own draft application. [cite: 85]
     */
    public function update(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->id === $loanApplication->user_id && $loanApplication->is_draft // Using accessor
            ? Response::allow()
            : Response::deny(__('Anda hanya boleh mengemaskini draf permohonan anda sahaja.'));
    }

    /**
     * Determine whether the user can delete the specified loan application.
     * Only the applicant can delete their own draft application. [cite: 85]
     */
    public function delete(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->id === $loanApplication->user_id && $loanApplication->is_draft // Using accessor
            ? Response::allow()
            : Response::deny(__('Anda hanya boleh memadam draf permohonan anda sahaja.'));
    }

    public function restore(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->can('restore_loan_applications') // Assumes permission
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan permohonan pinjaman ini.'));
    }

    public function forceDelete(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->can('force_delete_loan_applications') // Assumes permission
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam permohonan pinjaman ini secara kekal.'));
    }

    /**
     * Determine whether the user can submit a draft or rejected loan application.
     */
    public function submit(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Applicant submits their own draft or previously rejected application.
        $canSubmit = ($user->id === $loanApplication->user_id &&
                       ($loanApplication->is_draft || $loanApplication->status === LoanApplication::STATUS_REJECTED)); // [cite: 87]

        return $canSubmit
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk menghantar permohonan ini atau statusnya tidak membenarkan penghantaran.'));
    }

    /**
     * Determine whether the user has the general ability to *be assigned* an approval task for loan applications.
     * Actual processing of an approval task is governed by ApprovalPolicy.
     * This checks if the application is in a state to be approved by *someone*.
     */
    public function approve(User $user, LoanApplication $loanApplication): Response|bool
    {
        // This policy method might be more about *assigning* an approval rather than *performing* it.
        // The actual 'process' action on an Approval model instance would be in ApprovalPolicy.
        // For general capability (e.g., for a button visibility that leads to an approval list):
        $isPending = in_array($loanApplication->status, [ // [cite: 87]
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_HOD_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
        ]);

        // User must have a role/permission that allows them to be an approver in general.
        // This doesn't mean they can approve THIS specific application, but that they *could* be an approver.
        return $user->can('be_loan_approver') && $isPending // Example permission
            ? Response::allow()
            : Response::deny(__('Permohonan ini tidak dalam status menunggu kelulusan atau anda tiada kebenaran umum untuk meluluskan.'));
    }

    /**
     * Determine whether the user can process issuance for the loan application.
     * Typically performed by BPM staff after approval. [cite: 137]
     */
    public function processIssuance(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') && // [cite: 192]
                      in_array($loanApplication->status, [
                          LoanApplication::STATUS_APPROVED, // [cite: 87]
                          LoanApplication::STATUS_PARTIALLY_ISSUED, // [cite: 87]
                      ]);

        return $canProcess
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan ini atau statusnya tidak membenarkan (mesti Diluluskan atau Sebahagian Dikeluarkan).'));
    }

    /**
     * Determine whether the user can process return for the loan application.
     * Typically performed by BPM staff. [cite: 141]
     */
    public function processReturn(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') && // [cite: 192]
                      in_array($loanApplication->status, [
                          LoanApplication::STATUS_ISSUED, // [cite: 87]
                          LoanApplication::STATUS_PARTIALLY_ISSUED, // [cite: 87]
                          LoanApplication::STATUS_OVERDUE, // [cite: 87]
                      ]);

        return $canProcess
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memproses pemulangan bagi permohonan ini atau statusnya tidak membenarkan (mesti Dikeluarkan, Sebahagian Dikeluarkan, atau Tertunggak).'));
    }
}
