<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\Response;

class LoanTransactionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): Response|bool
    {
        return $user->hasAnyRole(['Admin', 'BPM Staff'])
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pinjaman.'));
    }

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

    public function createIssue(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Design Ref (Rev. 3.5): Section 4.3 (loan_applications.status: 'approved', 'partially_issued')
        // Assumes LoanApplication.php model has a canBeIssued() method.
        $isReadyForIssuance = method_exists($loanApplication, 'canBeIssued') && $loanApplication->canBeIssued();

        if (!method_exists($loanApplication, 'canBeIssued')) {
             Log::warning("LoanTransactionPolicy: LoanApplication model ID {$loanApplication->id} is missing canBeIssued() method.");
             // Fallback to direct status check if method doesn't exist for some reason
             $isReadyForIssuance = in_array($loanApplication->status, [
                 LoanApplication::STATUS_APPROVED,
                 LoanApplication::STATUS_PARTIALLY_ISSUED
             ]);
        }

        if (!$user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::deny(__('Hanya Admin atau Staf BPM yang boleh memulakan proses pengeluaran.'));
        }

        return $isReadyForIssuance
            ? Response::allow()
            : Response::deny(__('Permohonan pinjaman ini tidak dalam status yang membenarkan pengeluaran peralatan.'));
    }

    public function createReturn(User $user, LoanTransaction $issueLoanTransaction): Response|bool
    {
        // Design Ref (Rev. 3.5): Section 4.3 (loan_transactions.type: 'issue'; loan_transactions.status)
        if (!method_exists($issueLoanTransaction, 'isIssue') || !method_exists($issueLoanTransaction, 'isFullyClosedOrReturned')) {
            Log::error("LoanTransactionPolicy: LoanTransaction model is missing isIssue() or isFullyClosedOrReturned() methods for Tx ID {$issueLoanTransaction->id}. Denying return creation.");
            return Response::deny(__('Konfigurasi sistem tidak lengkap untuk memproses pemulangan.'));
        }

        if (!$user->hasAnyRole(['Admin', 'BPM Staff'])) {
            return Response::deny(__('Hanya Admin atau Staf BPM yang boleh memproses pemulangan.'));
        }

        return $issueLoanTransaction->isIssue() && !$issueLoanTransaction->isFullyClosedOrReturned()
            ? Response::allow()
            : Response::deny(__('Transaksi pengeluaran ini tidak sah atau telahpun selesai dipulangkan sepenuhnya.'));
    }

    public function update(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengemaskini transaksi pinjaman.'));
    }

    public function delete(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam transaksi pinjaman.'));
    }

    public function restore(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('restore_loan_transactions')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan transaksi pinjaman.'));
    }

    public function forceDelete(User $user, LoanTransaction $loanTransaction): Response|bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('force_delete_loan_transactions')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam transaksi pinjaman ini secara kekal.'));
    }

    public function viewAnyIssued(User $user): Response|bool
    {
        return $user->hasAnyRole(['Admin', 'BPM Staff'])
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pengeluaran.'));
    }
}
