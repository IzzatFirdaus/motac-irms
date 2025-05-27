<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log; // Retaining Log facade

class LoanTransactionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        // Ensure User model uses Spatie's HasRoles trait
        if ($user->hasRole('Admin')) { // Role 'Admin'
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        // Ensure 'BPM Staff' role exists and is assigned
        // Roles 'Admin', 'BPM Staff'
        return $user->hasAnyRole(['Admin', 'BPM Staff']); // Admins covered by before
    }

    public function view(User $user, LoanTransaction $loanTransaction): bool
    {
        // Roles 'Admin', 'BPM Staff'
        if ($user->hasAnyRole(['Admin', 'BPM Staff'])) { // Admins covered by before
            return true;
        }
        // Ensure loanApplication relationship exists and has user_id and responsible_officer_id
        // Design Ref: Section 4.3 (loan_applications.user_id, loan_applications.responsible_officer_id)
        if ($loanTransaction->loanApplication) {
            if ((int) $user->id === (int) $loanTransaction->loanApplication->user_id) {
                return true;
            }
            // Check if responsible_officer_id is set before accessing responsibleOfficer relationship or its id
            if ($loanTransaction->loanApplication->responsible_officer_id && (int) $user->id === (int) $loanTransaction->loanApplication->responsible_officer_id) {
                return true;
            }
        }

        return false;
    }

    public function createIssue(User $user, LoanApplication $loanApplication): bool
    {
        // CRUCIAL: LoanApplication model MUST have isApproved() and isPartiallyIssued() methods.
        // These methods should check $this->status against LoanApplication status constants.
        // Design Ref: Section 4.3 (loan_applications.status: 'approved', 'partially_issued')
        $isReadyForIssuance = false;
        if (method_exists($loanApplication, 'isApproved') && $loanApplication->isApproved()) {
            $isReadyForIssuance = true;
        }
        if (method_exists($loanApplication, 'isPartiallyIssued') && $loanApplication->isPartiallyIssued()) {
            $isReadyForIssuance = true;
        }
        if (! method_exists($loanApplication, 'isApproved') || ! method_exists($loanApplication, 'isPartiallyIssued')) {
            Log::warning("LoanTransactionPolicy: LoanApplication model ID {$loanApplication->id} is missing isApproved() or isPartiallyIssued() methods.");
        }

        // Roles 'Admin', 'BPM Staff'
        return $user->hasAnyRole(['Admin', 'BPM Staff']) && $isReadyForIssuance;
    }

    public function createReturn(User $user, LoanTransaction $issueLoanTransaction): bool
    {
        // CRUCIAL: LoanTransaction model MUST have isIssue() and isFullyClosedOrReturned() methods.
        // isIssue() checks $this->type; isFullyClosedOrReturned() checks its status or related items.
        // Design Ref: Section 4.3 (loan_transactions.type: 'issue'; loan_transactions.status)
        if (! method_exists($issueLoanTransaction, 'isIssue') || ! method_exists($issueLoanTransaction, 'isFullyClosedOrReturned')) {
            Log::error("LoanTransactionPolicy: LoanTransaction model is missing isIssue() or isFullyClosedOrReturned() methods for Tx ID {$issueLoanTransaction->id}. Denying return creation.");
            return false; // Fail safe
        }

        // Roles 'Admin', 'BPM Staff'
        return $user->hasAnyRole(['Admin', 'BPM Staff']) &&
          $issueLoanTransaction->isIssue() &&
          ! $issueLoanTransaction->isFullyClosedOrReturned();
    }

    public function update(User $user, LoanTransaction $loanTransaction): bool
    {
        // Transactions generally immutable by non-admins
        return $user->hasRole('Admin'); // Role 'Admin'. Admins implicitly allowed by before(), but explicit can be here too.
    }

    public function delete(User $user, LoanTransaction $loanTransaction): bool
    {
        // Deleting transactions is highly sensitive
        return $user->hasRole('Admin'); // Role 'Admin'
    }

    public function restore(User $user, LoanTransaction $loanTransaction): bool
    {
        // Role 'Admin'
        return $user->hasRole('Admin') || $user->hasPermissionTo('restore_loan_transactions');
    }

    public function forceDelete(User $user, LoanTransaction $loanTransaction): bool
    {
        // Role 'Admin'
        return $user->hasRole('Admin') || $user->hasPermissionTo('force_delete_loan_transactions');
    }

    public function viewAnyIssued(User $user): bool
    {
        // Roles 'Admin', 'BPM Staff'
        return $user->hasAnyRole(['Admin', 'BPM Staff']);
    }
}
