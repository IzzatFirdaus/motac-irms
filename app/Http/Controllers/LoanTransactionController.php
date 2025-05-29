<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanTransaction;
use App\Models\User; // For type hinting if needed, though Auth::id() is used
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class LoanTransactionController extends Controller
{
    /**
     * Apply authentication middleware.
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
            $this->authorize('view', $loanTransaction);
        } catch (AuthorizationException $e) {
            Log::warning("LoanTransactionController@show: Unauthorized attempt to view LoanTransaction ID {$loanTransaction->id}.", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
        }

        Log::info("LoanTransactionController@show: User ID ".Auth::id()." viewing LoanTransaction ID {$loanTransaction->id} (Type: {$loanTransaction->type}).");

        $loanTransaction->loadMissing([
            'loanApplication.user:id,name',
            'loanApplication.responsibleOfficer:id,name',
            'issuingOfficer:id,name',
            'receivingOfficer:id,name',
            'returningOfficer:id,name',
            'returnAcceptingOfficer:id,name',
            'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
            'loanTransactionItems.loanApplicationItem:id,equipment_type,quantity_requested',
            'relatedIssueTransaction', // If it's a return transaction, link to the original issue transaction
            'creator:id,name',         // Blameable: who created this transaction record
        ]);

        // Ensure the view 'loan-transactions.show' exists.
        // The route in web.php is 'resource-management.bpm.loan-transactions.show'
        // So the view might be in 'resource-management.admin.bpm.transactions.show' or similar. Adjust path as needed.
        return view('loan-transactions.show', compact('loanTransaction'));
    }

    // Other methods removed as per system design indicating Livewire handles issue/return forms and lists.
}
