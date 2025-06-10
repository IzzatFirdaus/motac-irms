<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IssueEquipmentRequest;
use App\Http\Requests\ProcessReturnRequest;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Display a listing of loan transactions.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LoanTransaction::class);

        $loanTransactions = $this->loanTransactionService->getTransactions(
            $request->all(),
            ['loanApplication.user:id,name', 'issuingOfficer:id,name', 'returnAcceptingOfficer:id,name'],
            10
        );

        return view('loan-transactions.index', compact('loanTransactions'));
    }

    /**
     * Display details of a specific loan transaction.
     */
    public function show(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        try {
            $this->authorize('view', $loanTransaction);
        } catch (AuthorizationException) {
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
        }

        $loanTransaction->loadMissing(LoanTransaction::getDefinedDefaultRelationsStatic());

        return view('loan-transactions.show', compact('loanTransaction'));
    }

    /**
     * Show the form for recording equipment issuance.
     */
    public function showIssueForm(LoanApplication $loanApplication): View|RedirectResponse
    {
        try {
            $this->authorize('createIssue', [LoanTransaction::class, $loanApplication]);
        } catch (AuthorizationException) {
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pengeluaran untuk permohonan ini.'));
        }

        $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)->orderBy('brand')->orderBy('model')->get();
        $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])->filter()->unique('id');
        $allAccessoriesList = config('motac.loan_accessories_list', []);

        return view('loan-transactions.issue', compact('loanApplication', 'availableEquipment', 'loanApplicantAndResponsibleOfficer', 'allAccessoriesList'));
    }

    /**
     * Store the recorded equipment issuance.
     */
    public function storeIssue(
        IssueEquipmentRequest $request,
        LoanApplication $loanApplication
    ): RedirectResponse {
        $issuingOfficer = Auth::user();
        $validatedData = $request->validated();

        // EDITED: Prepare transaction details and items payload separately for the service
        $itemsPayload = $validatedData['items'];
        $transactionDetails = [
            'transaction_date' => $validatedData['transaction_date'],
            'receiving_officer_id' => $validatedData['receiving_officer_id'],
            'issue_notes' => $validatedData['issue_notes'],
        ];

        try {
            // EDITED: Pass the fourth argument ($transactionDetails) to the service method
            $this->loanTransactionService->processNewIssue(
                $loanApplication,
                $itemsPayload,
                $issuingOfficer,
                $transactionDetails
            );

            return redirect()->route('loan-applications.show', $loanApplication->id)
                ->with('success', __('Pengeluaran peralatan berjaya direkodkan.'));
        } catch (Throwable $e) {
            Log::error("Error in LoanTransactionController@storeIssue: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', __('Gagal merekodkan pengeluaran peralatan.'));
        }
    }

    /**
     * Show the form for recording equipment return.
     */
    public function returnForm(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        try {
            $this->authorize('processReturn', $loanTransaction->loanApplication);
        } catch (AuthorizationException) {
            return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk memproses pulangan ini.'));
        }

        if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
            return redirect()->back()->with('error', __('Transaksi ini bukan transaksi pengeluaran peralatan yang sah untuk dipulangkan.'));
        }

        return view('loan-transactions.return-form-page', [
            'issueTransactionId' => $loanTransaction->id,
            'loanApplicationId' => $loanTransaction->loanApplication->id,
        ]);
    }

    /**
     * Store the recorded equipment return.
     */
    public function storeReturn(
        ProcessReturnRequest $request,
        LoanTransaction $loanTransaction
    ): RedirectResponse {
        $returnAcceptingOfficer = Auth::user();
        $validatedData = $request->validated();

        // EDITED: Prepare transaction details and items payload separately for the service
        $itemsPayload = $validatedData['returnItems'];
        $transactionDetails = [
            'returning_officer_id' => $validatedData['returning_officer_id'],
            'transaction_date' => $validatedData['transaction_date'],
            'return_notes' => $validatedData['return_notes'],
        ];

        try {
            // EDITED: Pass the fourth argument ($transactionDetails) to the service method
            $this->loanTransactionService->processExistingReturn(
                $loanTransaction,
                $itemsPayload,
                $returnAcceptingOfficer,
                $transactionDetails
            );

            return redirect()->route('loan-applications.show', $loanTransaction->loan_application_id)
                ->with('success', __('Peralatan telah berjaya direkodkan pemulangannya.'));
        } catch (Throwable $e) {
            Log::error("Error in LoanTransactionController@storeReturn: " . $e->getMessage(), ['exception' => $e]);
            $errorMessage = ($e instanceof \RuntimeException) ? $e->getMessage() : __('Gagal merekodkan pemulangan peralatan disebabkan ralat sistem.');
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }
}
