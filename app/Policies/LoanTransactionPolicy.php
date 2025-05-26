<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanTransactionPolicy
{
  use HandlesAuthorization;

  /**
   * Perform pre-authorization checks.
   */
  public function before(User $user, string $ability): ?bool
  {
    if ($user->hasRole('Admin')) { // Admins can manage transactions
      return true;
    }
    return null;
  }

  /**
   * Determine whether the user can view any loan transactions.
   */
  public function viewAny(User $user): Response|bool
  {
    // Admins (by before), BPM Staff can view all transactions.
    return $user->hasRole('BPMStaff') || $user->hasPermissionTo('view_loan_transactions')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat transaksi pinjaman.'));
  }

  /**
   * Determine whether the user can view the loan transaction.
   */
  public function view(User $user, LoanTransaction $loanTransaction): Response|bool
  {
    // Admins (by before), BPM Staff, or involved parties in the parent loan application.
    if ($user->hasRole('BPMStaff') || $user->hasPermissionTo('view_loan_transactions')) {
      return Response::allow();
    }
    // Check if user is applicant, responsible officer, or supporting officer of parent loan
    $loanApplication = $loanTransaction->loanApplication;
    if ($loanApplication && (
      $user->id === $loanApplication->user_id ||
      $user->id === $loanApplication->responsible_officer_id ||
      $user->id === $loanApplication->supporting_officer_id
    )) {
      return Response::allow();
    }
    return Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat transaksi pinjaman ini.'));
  }

  /**
   * Determine whether the user can create an issue loan transaction.
   * This is essentially checking if they can perform the 'issueEquipment' action on the LoanApplication.
   */
  public function createIssue(User $user, LoanApplication $loanApplication): Response|bool
  {
    // Admins handled by before().
    // BPM Staff can create issue transactions if the application is approved.
    return $user->hasRole('BPMStaff') &&
      ($loanApplication->status === LoanApplication::STATUS_APPROVED || $loanApplication->status === LoanApplication::STATUS_PARTIALLY_ISSUED) &&
      $user->hasPermissionTo('issue_equipment')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan ini atau status permohonan tidak betul.'));
  }

  /**
   * Determine whether the user can create a return loan transaction.
   * This uses the original issue transaction as context.
   */
  public function createReturn(User $user, LoanTransaction $issueTransaction): Response|bool
  {
    // Admins handled by before().
    // BPM Staff can create return transactions for items that were issued.
    if ($issueTransaction->type !== LoanTransaction::TYPE_ISSUE) {
      return Response::deny(__('Transaksi rujukan mestilah transaksi pengeluaran.'));
    }
    return $user->hasRole('BPMStaff') && $user->hasPermissionTo('process_equipment_return')
      ? Response::allow()
      : Response::deny(__('Anda tidak mempunyai kebenaran untuk memproses pemulangan bagi transaksi ini.'));
  }


  /**
   * Determine whether the user can update the loan transaction.
   * Generally, transactions are immutable once created. Updates might be for notes by admins.
   */
  public function update(User $user, LoanTransaction $loanTransaction): Response|bool
  {
    // Admins handled by before().
    // BPM Staff might be able to update notes or correct minor details if transaction is not finalized for reporting.
    if ($user->hasRole('BPMStaff') && $user->hasPermissionTo('edit_loan_transactions')) {
      // Add logic: e.g., only if transaction status is not 'completed' or 'cancelled'
      if (in_array($loanTransaction->status, [LoanTransaction::STATUS_COMPLETED, LoanTransaction::STATUS_CANCELLED])) {
        return Response::deny(__('Transaksi yang telah selesai atau dibatalkan tidak boleh dikemaskini.'));
      }
      return Response::allow();
    }
    return Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini transaksi ini.'));
  }

  /**
   * Determine whether the user can delete (cancel/void) the loan transaction.
   * Very restricted, as transactions record actual movements.
   */
  public function delete(User $user, LoanTransaction $loanTransaction): Response|bool
  {
    // Admins handled by before().
    // Perhaps only if no items are linked or if it's a very recent error.
    // Deleting transactions can cause data integrity issues. Prefer 'cancel' status.
    if ($user->hasRole('Admin') && $user->hasPermissionTo('delete_loan_transactions')) { // Requires specific high-level permission
      if ($loanTransaction->loanTransactionItems()->exists()) { //
        return Response::deny(__('Transaksi tidak boleh dipadam kerana mempunyai item berkaitan. Pertimbangkan untuk batalkan.'));
      }
      return Response::allow();
    }
    return Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam transaksi ini.'));
  }

  // restore and forceDelete are typically Admin-only, covered by before().
}
