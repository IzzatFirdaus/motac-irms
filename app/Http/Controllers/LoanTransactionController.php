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
     * Show the form for recording equipment return.
     * Route: GET /loan-transactions/{loanTransaction}/return (expects the original ISSUE transaction)
     */
    public function showReturnForm(LoanTransaction $loanTransaction): View|RedirectResponse
    {
        if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
            return redirect()->back()->with('error', __('Hanya transaksi pengeluaran boleh diproses untuk pemulangan.'));
        }

        $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);

        return view('loan-transactions.return', [
            'issueTransaction' => $loanTransaction,
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
