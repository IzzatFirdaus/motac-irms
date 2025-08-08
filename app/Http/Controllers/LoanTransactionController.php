<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IssueEquipmentRequest;
use App\Http\Requests\ProcessReturnRequest;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Services\LoanTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Handles the issuance and return of equipment for loan applications,
 * as well as listing and displaying loan transactions.
 */
final class LoanTransactionController extends Controller
{
    private LoanTransactionService $loanTransactionService;

    public function __construct(LoanTransactionService $loanTransactionService)
    {
        $this->middleware('auth');
        $this->loanTransactionService = $loanTransactionService;
    }

    /**
     * Display a paginated listing of all loan transactions.
     * Route: GET /loan-transactions
     */
    public function index(): View
    {
        $this->authorize('viewAny', LoanTransaction::class);

        $transactions = LoanTransaction::with([
                'loanApplication.user',
                'issuingOfficer',
                'receivingOfficer',
                'returningOfficer',
                'returnAcceptingOfficer'
            ])
            ->latest('transaction_date')
            ->paginate(20);

        // Passes transactions with eager loaded relationships and accessors for status/type labels
        return view('loan-transactions.index', ['transactions' => $transactions]);
    }

    /**
     * Show the form for recording equipment issuance for a loan application.
     * Route: GET /loan-applications/{loanApplication}/issue
     */
    public function showIssueForm(LoanApplication $loanApplication): View
    {
        $this->authorize('processIssuance', $loanApplication);

        $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->orderBy('brand')->orderBy('model')->get();

        $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])
            ->filter()->unique('id');

        $allAccessoriesList = config('motac.loan_accessories_list', []);

        return view('loan-transactions.issue', [
            'loanApplication' => $loanApplication,
            'availableEquipment' => $availableEquipment,
            'loanApplicantAndResponsibleOfficer' => $loanApplicantAndResponsibleOfficer,
            'allAccessoriesList' => $allAccessoriesList,
        ]);
    }

    /**
     * Store the recorded equipment issuance for a loan application.
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
                $request->validated() // All validated data for notes, checklists, etc.
            );

            return redirect()->route('loan-applications.show', $loanApplication->id)
                ->with('success', __('Pengeluaran peralatan berjaya direkodkan.'));
        } catch (Throwable $throwable) {
            Log::error('Error in LoanTransactionController@storeIssue: '.$throwable->getMessage(), ['exception' => $throwable]);

            return redirect()->back()->withInput()->with('error', __('Gagal merekodkan pengeluaran peralatan: ').$throwable->getMessage());
        }
    }

    /**
     * Display the details of a specific loan transaction.
     * Route: GET /loan-transactions/{loanTransaction}
     */
    public function show(LoanTransaction $loanTransaction): View
    {
        $this->authorize('view', $loanTransaction);

        // Eager load relationships for display; includes user, officers, transaction items, and equipment
        $loanTransaction->load([
            'loanApplication.user',
            'loanApplication.responsibleOfficer',
            'loanTransactionItems.equipment',
            'loanTransactionItems.loanApplicationItem',
            'issuingOfficer',
            'receivingOfficer',
            'returningOfficer',
            'returnAcceptingOfficer',
            'relatedIssueTransaction'
        ]);

        // For each item, access status/condition for display (no assignment needed for accessors)
        // Accessors like condition_on_return_translated are available directly for use in the view.

        // Passes transaction details, ready for view rendering with all accessors available
        return view('loan-transactions.show', ['loanTransaction' => $loanTransaction]);
    }

    /**
     * Show the form for recording equipment return for an ISSUE transaction.
     * Route: GET /loan-transactions/{loanTransaction}/return
     */
    public function showReturnForm(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        // Only allow returns for transactions of type 'issue'
        if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
            return redirect()->back()->with('error', __('Hanya transaksi pengeluaran boleh diproses untuk pemulangan.'));
        }

        $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);

        // Eager load all necessary relationships for the return form
        $loanTransaction->load([
            'loanApplication.user',
            'loanApplication.responsibleOfficer',
            'loanTransactionItems.equipment',
            'loanTransactionItems.loanApplicationItem'
        ]);

        $loanApplication = $loanTransaction->loanApplication;
        $issuedItemsForThisTransaction = $loanTransaction->loanTransactionItems;
        $allAccessoriesList = config('motac.loan_accessories_list', []);
        $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])
            ->filter()->unique('id');

        // Pass all data required for the return form view
        return view('loan-transactions.return', [
            'loanTransaction' => $loanTransaction,
            'loanApplication' => $loanApplication,
            'issuedItemsForThisTransaction' => $issuedItemsForThisTransaction,
            'allAccessoriesList' => $allAccessoriesList,
            'loanApplicantAndResponsibleOfficer' => $loanApplicantAndResponsibleOfficer,
        ]);
    }

    /**
     * Store the recorded equipment return for an ISSUE transaction.
     * Route: POST /loan-transactions/{loanTransaction}/return
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
                $request->validated() // All validated data for notes, checklists, etc.
            );

            return redirect()->route('loan-applications.show', $loanTransaction->loan_application_id)
                ->with('success', __('Peralatan telah berjaya direkodkan pemulangannya.'));
        } catch (Throwable $throwable) {
            Log::error('Error in LoanTransactionController@storeReturn: '.$throwable->getMessage(), ['exception' => $throwable]);

            return redirect()->back()->withInput()->with('error', __('Gagal merekodkan pemulangan: ').$throwable->getMessage());
        }
    }
}
