<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class LoanTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Allow Admins to perform any action
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null; // Continue to specific policy methods
    }

    /**
     * Determine whether the user can view any loan transactions.
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->hasAnyRole(['Admin', 'BPM Staff'])
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pinjaman.'));
    }

    /**
     * Determine whether the user can view the specific loan transaction.
     */
    public function view(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        if ($user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::allow();
        }
        // Design Ref (Rev. 3.5): Section 4.3 (loan_applications.user_id, loan_applications.responsible_officer_id)
        if ($loanTransaction->loanApplication) {
            if ((int) $user->id === (int) $loanTransaction->loanApplication->user_id) {
                return Response::allow();
            }
            if ($loanTransaction->loanApplication->responsible_officer_id && (int) $user->id === (int) $loanTransaction->loanApplication->responsible_officer_id) {
                return Response::allow();
            }
        }

        return Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat transaksi pinjaman ini.'));
    }

    /**
     * Determine whether the user can create an issue transaction.
     */
    public function createIssue(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Design Ref (Rev. 3.5): Section 4.3 (loan_applications.status: 'approved', 'partially_issued')
        // Assumes LoanApplication.php model has a canBeIssued() method.
        $isReadyForIssuance = method_exists($loanApplication, 'canBeIssued') && $loanApplication->canBeIssued();

        if (! method_exists($loanApplication, 'canBeIssued')) {
            Log::warning("LoanTransactionPolicy: LoanApplication model ID {$loanApplication->id} is missing canBeIssued() method. Falling back to direct status check.");
            // Fallback to direct status check if method doesn't exist for some reason
            $isReadyForIssuance = in_array($loanApplication->status, [
                LoanApplication::STATUS_APPROVED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
            ]);
        }

        if (! $user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::deny(__('Hanya Admin atau Staf BPM yang boleh memulakan proses pengeluaran.'));
        }

        return $isReadyForIssuance
            ? Response::allow()
            : Response::deny(__('Permohonan pinjaman ini tidak dalam status yang membenarkan pengeluaran peralatan.'));
    }

    /**
     * Determine whether the user can create a return transaction (legacy method, consider processReturn).
     */
    public function createReturn(User $user, LoanTransaction $issueLoanTransaction): Response|bool
    {
        // Design Ref (Rev. 3.5): Section 4.3 (loan_transactions.type: 'issue'; loan_transactions.status)
        if (! method_exists($issueLoanTransaction, 'isIssue') || ! method_exists($issueLoanTransaction, 'isFullyClosedOrReturned')) {
            Log::error("LoanTransactionPolicy: LoanTransaction model is missing isIssue() or isFullyClosedOrReturned() methods for Tx ID {$issueLoanTransaction->id}. Denying return creation.");

            return Response::deny(__('Konfigurasi sistem tidak lengkap untuk memproses pemulangan.'));
        }

        if (! $user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::deny(__('Hanya Admin atau Staf BPM yang boleh memproses pemulangan.'));
        }

        // Allow if it's an issue transaction and not fully returned/closed
        return $issueLoanTransaction->isIssue() && ! $issueLoanTransaction->isFullyClosedOrReturned()
            ? Response::allow()
            : Response::deny(__('Transaksi pengeluaran ini tidak sah atau telahpun selesai dipulangkan sepenuhnya.'));
    }

    /**
     * Determine whether the user can process a return (view form and store).
     * This method is used by both the returnForm and storeReturn methods in the controller.
     *
     * @param  \App\Models\LoanTransaction  $issueLoanTransaction  The original ISSUE transaction being returned.
     * @param  \App\Models\LoanApplication  $loanApplication  The related loan application.
     */
    public function processReturn(User $user, LoanTransaction $issueLoanTransaction, LoanApplication $loanApplication): Response|bool
    {
        // Only BPM Staff can process returns
        if (! $user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::deny(__('Anda tidak mempunyai kebenaran untuk merekodkan pulangan peralatan.'));
        }

        // The transaction must be an 'issue' type
        if (! $issueLoanTransaction->isIssue()) {
            return Response::deny(__('Transaksi yang dipilih bukan transaksi pengeluaran peralatan.'));
        }

        // The issue transaction must not be fully closed or returned yet
        // If it's already fully returned/cancelled, no further returns can be recorded for it.
        if ($issueLoanTransaction->isFullyClosedOrReturned()) {
            return Response::deny(__('Transaksi pengeluaran ini telahpun selesai dipulangkan sepenuhnya atau dibatalkan.'));
        }

        // Also ensure the loan application is not in a terminal state that prevents returns
        // (e.g., cancelled, fully completed without return tracking, etc.)
        // Assuming LoanApplication has a method like canAcceptReturns() or status checks.
        // For example, if it's 'approved' or 'partially_issued' or 'issued'
        if (! in_array($loanApplication->status, [
            LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_PARTIALLY_ISSUED,
            LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION, // If tracking partial returns at application level
        ])) {
            return Response::deny(__('Status permohonan pinjaman tidak membenarkan proses pemulangan.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the specific loan transaction.
     */
    public function update(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        // Generally, update on transactions might be restricted, e.g., only Admin for corrections
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini transaksi pinjaman.'));
    }

    /**
     * Determine whether the user can delete the specific loan transaction (soft delete).
     */
    public function delete(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        // Only Admin can soft delete
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam transaksi pinjaman.'));
    }

    /**
     * Determine whether the user can restore a soft-deleted loan transaction.
     */
    public function restore(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('restore_loan_transactions')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan transaksi pinjaman.'));
    }

    /**
     * Determine whether the user can permanently delete the specific loan transaction.
     */
    public function forceDelete(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('force_delete_loan_transactions')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam transaksi pinjaman ini secara kekal.'));
    }

    /**
     * Determine whether the user can view any issued loan transactions.
     */
    public function viewAnyIssued(User $user): Response|bool
    {
        return $user->hasAnyRole(['Admin', 'BPM Staff'])
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pengeluaran.'));
    }
}
