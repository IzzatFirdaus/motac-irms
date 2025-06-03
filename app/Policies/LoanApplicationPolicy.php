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
    if ($user->hasRole('Admin')) { // Standardized role 'Admin'
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
    // BPM Staff or users with specific permission can view all.
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
    // Also consider if responsible officer, supporting officer or current approver should view.
    // Design Ref (Rev. 3.5): LoanApplication model fields
    return $user->id === $loanApplication->user_id ||
      $user->id === $loanApplication->responsible_officer_id ||
      $user->id === $loanApplication->supporting_officer_id ||
      ($loanApplication->current_approval_officer_id && $user->id === $loanApplication->current_approval_officer_id) ||
      $user->hasRole('BPM Staff') ||
      $user->can('view_loan_applications') // Assumes a general permission
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat permohonan pinjaman ini.'));
  }

  /**
   * Determine whether the user can create loan applications.
   */
  public function create(User $user): Response|bool
  {
    // Any authenticated user can attempt to create. (Eligibility rules apply separately)
    // Design Ref (Rev. 3.5): Section 7.1
    return $user->id !== null
      ? Response::allow()
      : Response::deny(__('Anda mesti log masuk untuk membuat permohonan pinjaman.'));
  }

  /**
   * Determine whether the user can update the specified loan application.
   * Only the applicant can update their own draft application.
   * Design Ref (Rev. 3.5): LoanApplication model status
   */
  public function update(User $user, LoanApplication $loanApplication): Response|bool
  {
    // Using isDraft() method from LoanApplication model or direct status check if is_draft is an accessor
    // The provided LoanApplication model in a previous turn had isDraft() method.
    return $user->id === $loanApplication->user_id && $loanApplication->isDraft()
      ? Response::allow()
      : Response::deny(__('Anda hanya boleh mengemaskini draf permohonan anda sahaja.'));
  }

  /**
   * Determine whether the user can delete the specified loan application.
   * Only the applicant can delete their own draft application.
   * Design Ref (Rev. 3.5): LoanApplication model status
   */
  public function delete(User $user, LoanApplication $loanApplication): Response|bool
  {
    // Using isDraft() method from LoanApplication model or direct status check
    return $user->id === $loanApplication->user_id && $loanApplication->isDraft()
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
    // Design Ref (Rev. 3.5): LoanApplication model status
    $canSubmit = ($user->id === $loanApplication->user_id &&
      ($loanApplication->isDraft() || $loanApplication->status === LoanApplication::STATUS_REJECTED));

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
    // Design Ref (Rev. 3.5): LoanApplication model status constants
    $isPending = in_array($loanApplication->status, [
      LoanApplication::STATUS_PENDING_SUPPORT,
      LoanApplication::STATUS_PENDING_HOD_REVIEW,
      LoanApplication::STATUS_PENDING_BPM_REVIEW,
    ]);

    // User must have a role/permission that allows them to be an approver in general.
    return $user->can('be_loan_approver') && $isPending // Example permission
      ? Response::allow()
      : Response::deny(__('Permohonan ini tidak dalam status menunggu kelulusan atau anda tiada kebenaran umum untuk meluluskan.'));
  }

  /**
   * Determine whether the user can process issuance for the loan application.
   * Typically performed by BPM staff after approval.
   * Design Ref (Rev. 3.5): Section 5.2, Role names, LoanApplication model status
   */
  public function processIssuance(User $user, LoanApplication $loanApplication): Response|bool
  {
    $canProcess = $user->hasRole('BPM Staff') &&
      in_array($loanApplication->status, [
        LoanApplication::STATUS_APPROVED,
        LoanApplication::STATUS_PARTIALLY_ISSUED,
      ]);

    return $canProcess
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan ini atau statusnya tidak membenarkan (mesti Diluluskan atau Sebahagian Dikeluarkan).'));
  }

  /**
   * Determine whether the user can process return for the loan application.
   * Typically performed by BPM staff.
   * Design Ref (Rev. 3.5): Section 5.2, Role names, LoanApplication model status
   */
  public function processReturn(User $user, LoanApplication $loanApplication): Response|bool
  {
    $canProcess = $user->hasRole('BPM Staff') &&
      in_array($loanApplication->status, [
        LoanApplication::STATUS_ISSUED,
        LoanApplication::STATUS_PARTIALLY_ISSUED,
        LoanApplication::STATUS_OVERDUE,
      ]);

    return $canProcess
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk memproses pemulangan bagi permohonan ini atau statusnya tidak membenarkan (mesti Dikeluarkan, Sebahagian Dikeluarkan, atau Tertunggak).'));
  }
}
