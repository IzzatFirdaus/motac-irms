<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanTransaction; //
use App\Models\User;            // For type hinting Auth user if needed
// LoanApplicationService was removed as it's not used by the remaining 'show' method.
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse; // For redirecting on authorization failure
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class LoanTransactionController extends Controller
{
    /**
     * Apply authentication middleware.
     * LoanApplicationService is no longer injected as it's not directly used by the 'show' method.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display details of a specific loan transaction.
     * Authorization is handled by LoanTransactionPolicy@view.
     *
     * @param  \App\Models\LoanTransaction  $loanTransaction Route model bound instance
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        try {
            // Authorize viewing this specific transaction using LoanTransactionPolicy
            $this->authorize('view', $loanTransaction);
        } catch (AuthorizationException $e) {
            Log::warning("LoanTransactionController@show: Unauthorized attempt to view LoanTransaction ID {$loanTransaction->id}.", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            // Redirect to a general access-denied page or dashboard with an error.
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
        }

        Log::info("LoanTransactionController@show: User ID ".Auth::id()." viewing LoanTransaction ID {$loanTransaction->id} (Type: {$loanTransaction->type})."); //

        // Eager load necessary relationships for the 'loan-transactions.show' view
        $loanTransaction->loadMissing([
            'loanApplication.user:id,name', // Applicant
            'loanApplication.responsibleOfficer:id,name', // Responsible officer for the loan
            'issuingOfficer:id,name',         //
            'receivingOfficer:id,name',       //
            'returningOfficer:id,name',       //
            'returnAcceptingOfficer:id,name', //
            'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number', //
            'loanTransactionItems.loanApplicationItem:id,equipment_type,quantity_requested', // Original request item
            'relatedIssueTransaction', // If it's a return transaction, link to the issue
            'creator:id,name', // Blameable: who created this transaction record
        ]);

        // Ensure the view 'loan-transactions.show' exists and is designed to display these details.
        return view('loan-transactions.show', compact('loanTransaction'));
    }

    // Methods like issueEquipmentForm, issueEquipment, returnEquipmentForm, processReturn,
    // and issuedLoansList have been removed. Based on the review of 'web.php',
    // their functionalities are now primarily handled by Livewire components
    // (e.g., ProcessIssuance, ProcessReturn, IssuedLoans) or direct view routes
    // that embed these components. This keeps the PHP controller lean and focused
    // on the actions it is still directly responsible for via traditional HTTP requests.
}
