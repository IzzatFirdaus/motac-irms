<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IssueEquipmentRequest;
use App\Http\Requests\ProcessReturnRequest;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Services\LoanTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

final class LoanTransactionController extends Controller
{
    private LoanTransactionService $loanTransactionService;

    public function __construct(LoanTransactionService $loanTransactionService)
    {
        $this->middleware('auth');
        $this->loanTransactionService = $loanTransactionService;
    }

    /**
     * ++ ADDED THIS METHOD TO LIST ALL TRANSACTIONS ++
     * Display a listing of the resource.
     * Route: GET /loan-transactions
     */
    public function index(): View
    {
        $this->authorize('viewAny', LoanTransaction::class);

        $transactions = LoanTransaction::with('loanApplication.user')
            ->latest('transaction_date')
            ->paginate(20);

        return view('loan-transactions.index', compact('transactions'));
    }

    /**
     * Show the form for recording equipment issuance.
     * Route: GET /loan-applications/{loanApplication}/issue
     */
    public function showIssueForm(LoanApplication $loanApplication): View
    {
        $this->authorize('processIssuance', $loanApplication);

        $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)->orderBy('brand')->orderBy('model')->get();
        $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])->filter()->unique('id');
        $allAccessoriesList = config('motac.loan_accessories_list', []);

        return view('loan-transactions.issue', compact('loanApplication', 'availableEquipment', 'loanApplicantAndResponsibleOfficer', 'allAccessoriesList'));
    }

    /**
     * Store the recorded equipment issuance.
     * Route: POST /loan-applications/{loanApplication}/issue
     */
    public function storeIssue(IssueEquipmentRequest $request, LoanApplication $loanApplication): RedirectResponse
    {
        $issuingOfficer = Auth::user();
        if (!$issuingOfficer) {
            return redirect()->back()->with('error', 'Sila log masuk semula.');
        }

        try {
            $this->loanTransactionService->processNewIssue(
                $loanApplication,
                $request->validated('items'),
                $issuingOfficer,
                $request->validated() // Pass all validated data for details
            );

            return redirect()->route('loan-applications.show', $loanApplication->id)
                ->with('success', __('Pengeluaran peralatan berjaya direkodkan.'));
        } catch (Throwable $e) {
            Log::error("Error in LoanTransactionController@storeIssue: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', __('Gagal merekodkan pengeluaran peralatan: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified loan transaction details.
     * Route: GET /loan-transactions/{loanTransaction}
     */
    public function show(LoanTransaction $loanTransaction): View
    {
        // Assuming you have a policy to view a transaction
        $this->authorize('view', $loanTransaction);

        // Eager load all necessary relationships for the detailed view
        $loanTransaction->load([
            'loanApplication.user',
            'loanTransactionItems.equipment',
            'issuingOfficer',   // The officer who issued the items
            'receivingOfficer' // The officer who accepted the return (will be null on issue transactions)
        ]);

        // You will need to create this view file
        return view('loan-transactions.show', compact('loanTransaction'));
    }

    /**
     * Show the form for recording equipment return.
     * Route: GET /loan-transactions/{loanTransaction}/return (expects the original ISSUE transaction)
     */
    public function showReturnForm(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        // THE FIX IS APPLIED IN THIS METHOD
        if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
            return redirect()->back()->with('error', __('Hanya transaksi pengeluaran boleh diproses untuk pemulangan.'));
        }

        $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);

        // 1. Eager load all necessary relationships from the start.
        $loanTransaction->load([
            'loanApplication.user',
            'loanApplication.responsibleOfficer',
            'loanTransactionItems.equipment'
        ]);

        // 2. Prepare all variables that the view expects.
        $loanApplication = $loanTransaction->loanApplication;
        $issuedItemsForThisTransaction = $loanTransaction->loanTransactionItems;
        $allAccessoriesList = config('motac.loan_accessories_list', []);

        // This logic is copied from your view to ensure the variable is available.
        $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])->filter()->unique('id');

        // 3. Pass all variables to the view with the correct keys.
        return view('loan-transactions.return', [
            'loanTransaction' => $loanTransaction, // Changed from 'issueTransaction' to match the view
            'loanApplication' => $loanApplication,
            'issuedItemsForThisTransaction' => $issuedItemsForThisTransaction,
            'allAccessoriesList' => $allAccessoriesList,
            'loanApplicantAndResponsibleOfficer' => $loanApplicantAndResponsibleOfficer,
        ]);
    }

    /**
     * Store the recorded equipment return.
     * Route: POST /loan-transactions/{loanTransaction}/return (expects the original ISSUE transaction)
     */
    public function storeReturn(ProcessReturnRequest $request, LoanTransaction $loanTransaction): RedirectResponse
    {
        $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);

        $returnAcceptingOfficer = Auth::user();
        if (!$returnAcceptingOfficer) {
            return redirect()->back()->with('error', 'Sila log masuk semula.');
        }

        try {
            $this->loanTransactionService->processExistingReturn(
                $loanTransaction,
                $request->validated()['items'],
                $returnAcceptingOfficer,
                $request->validated() // Pass all validated data for details
            );

            return redirect()->route('loan-applications.show', $loanTransaction->loan_application_id)
                ->with('success', __('Peralatan telah berjaya direkodkan pemulangannya.'));
        } catch (Throwable $e) {
            Log::error("Error in LoanTransactionController@storeReturn: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', __('Gagal merekodkan pemulangan: ') . $e->getMessage());
        }
    }
}
