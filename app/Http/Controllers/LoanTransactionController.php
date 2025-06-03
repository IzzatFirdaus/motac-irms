<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanTransaction;
// use App\Models\User; // Not strictly needed if only using Auth::id()
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class LoanTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display details of a specific loan transaction.
     * SDD Ref:
     * @param  \App\Models\LoanTransaction  $loanTransaction Route model bound instance
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        try {
            $this->authorize('view', $loanTransaction); // LoanTransactionPolicy@view
        } catch (AuthorizationException $e) {
            Log::warning("LoanTransactionController@show: Unauthorized attempt to view LoanTransaction ID {$loanTransaction->id}.", [
                'user_id' => Auth::id(), 'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
        }

        Log::info("LoanTransactionController@show: User ID ".Auth::id()." viewing LoanTransaction ID {$loanTransaction->id} (Type: {$loanTransaction->type}).");
        $loanTransaction->loadMissing([
            'loanApplication.user:id,name', 'loanApplication.responsibleOfficer:id,name',
            'issuingOfficer:id,name', 'receivingOfficer:id,name',
            'returningOfficer:id,name', 'returnAcceptingOfficer:id,name',
            'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
            'loanTransactionItems.loanApplicationItem:id,equipment_type,quantity_requested',
            'relatedIssueTransaction', // For return transactions
            'creator:id,name', // Blameable
        ]);
        // View path based on web.php 'resource-management.bpm.loan-transactions.show'
        return view('loan-transactions.show', compact('loanTransaction'));
    }
}
