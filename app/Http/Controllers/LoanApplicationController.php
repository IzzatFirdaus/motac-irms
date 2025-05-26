<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanApplicationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanApplicationController extends Controller
{
    protected LoanApplicationService $loanApplicationService;

    public function __construct(LoanApplicationService $loanApplicationService)
    {
        $this->loanApplicationService = $loanApplicationService;
        $this->middleware('auth');
        $this->authorizeResource(LoanApplication::class, 'loan_application', [
            'except' => ['index', 'submit'], // submit is custom; index uses viewAny
        ]);
    }

    public function index(): View|RedirectResponse
    {
        $this->authorize('viewAny', LoanApplication::class);
        /** @var User $user */
        $user = Auth::user();
        Log::info('LoanApplicationController@index called for User ID: ' . $user->id . '. Typically handled by Livewire.');
        return redirect()->route('resource-management.my-applications.loan.index');
    }

    public function create(): View
    {
        $this->authorize('create', LoanApplication::class);
        Log::info('LoanApplicationController@create: Displaying create form (typically handled by Livewire).', ['user_id' => Auth::id()]);
        return view('resource-management.loan-applications.create'); // Embeds <livewire:loan-request-form />
    }

    public function store(StoreLoanApplicationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        Log::info('LoanApplicationController@store: Attempting to store new loan application.', ['user_id' => $user->id]);
        try {
            $validatedData = $request->validated();
            $loanApplication = $this->loanApplicationService->createAndSubmitApplication($validatedData, $user);
            Log::info('Loan application stored and submitted successfully.', ['user_id' => $user->id, 'application_id' => $loanApplication->id]);
            return redirect()
                ->route('resource-management.loan-applications.show', ['loan_application' => $loanApplication->id])
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (ValidationException $e) {
            Log::error('Validation error storing loan application: ' . $e->getMessage(), ['user_id' => $user->id, 'errors' => $e->errors()]);
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', __('Sila semak semula borang permohonan. Terdapat maklumat yang tidak sah.'));
        } catch (Exception $e) {
            Log::error('Error storing loan application: ' . $e->getMessage(), ['user_id' => $user->id, 'exception_trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', __('Satu ralat berlaku semasa menghantar permohonan pinjaman: ') . $e->getMessage());
        }
    }

    public function show(LoanApplication $loan_application): View
    {
        // authorizeResource handles 'view'
        Log::info('LoanApplicationController@show: Displaying loan application.', ['application_id' => $loan_application->id, 'user_id' => Auth::id()]);
        $loan_application->loadMissing([
            'user.department', 'user.position', 'user.grade',
            'responsibleOfficer.department', 'responsibleOfficer.position', 'responsibleOfficer.grade',
            'supportingOfficer.department', 'supportingOfficer.position', 'supportingOfficer.grade',
            'approvals.officer',
            'applicationItems',
            'loanTransactions.issuingOfficer', 'loanTransactions.receivingOfficer',
            'loanTransactions.returningOfficer', 'loanTransactions.returnAcceptingOfficer',
            'loanTransactions.loanTransactionItems.equipment',
            'creator', 'updater'
        ]);
        return view('resource-management.loan-applications.show', ['loanApplication' => $loan_application]);
    }

    public function edit(LoanApplication $loan_application): View|RedirectResponse
    {
        // authorizeResource handles 'update'
        if ($loan_application->isDraft()) {
            Log::info('LoanApplicationController@edit: Displaying edit form.', ['application_id' => $loan_application->id, 'user_id' => Auth::id()]);
            return view('resource-management.loan-applications.edit', ['loanApplication' => $loan_application]);
        }
        Log::warning('Attempt to edit non-draft loan application.', ['application_id' => $loan_application->id, 'user_id' => Auth::id(), 'status' => $loan_application->status]);
        return redirect()->route('resource-management.loan-applications.show', ['loan_application' => $loan_application->id])->with('error', __('Hanya permohonan draf yang boleh disunting.'));
    }

    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loan_application): RedirectResponse
    {
        // authorizeResource or UpdateLoanApplicationRequest handles 'update'
        /** @var User $user */
        $user = $request->user();
        if (!$loan_application->isDraft() && !$user->can('manageDirectly', $loan_application)) { // Assuming manageDirectly for admin edits
            Log::warning('Attempt to update non-draft loan application by non-authorized user.', ['application_id' => $loan_application->id, 'user_id' => $user->id]);
            return redirect()->route('resource-management.loan-applications.show', ['loan_application' => $loan_application->id])->with('error', __('Hanya permohonan draf yang boleh dikemaskini atau anda tiada kebenaran.'));
        }
        Log::info('LoanApplicationController@update: Attempting to update loan application.', ['user_id' => $user->id, 'application_id' => $loan_application->id]);
        try {
            $validatedData = $request->validated();
            $updatedApplication = $this->loanApplicationService->updateApplication($loan_application, $validatedData, $user);
            Log::info('Loan application updated successfully.', ['user_id' => $user->id, 'application_id' => $updatedApplication->id]);
            return redirect()->route('resource-management.loan-applications.show', ['loan_application' => $updatedApplication->id])->with('success', __('Permohonan pinjaman berjaya dikemaskini.'));
        } catch (ValidationException $e) {
            Log::error('Validation error updating loan application: ' . $e->getMessage(), ['user_id' => $user->id, 'application_id' => $loan_application->id, 'errors' => $e->errors()]);
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', __('Sila semak semula borang permohonan.'));
        } catch (Exception $e) {
            Log::error('Error updating loan application: ' . $e->getMessage(), ['user_id' => $user->id, 'application_id' => $loan_application->id]);
            return redirect()->back()->withInput()->with('error', __('Satu ralat berlaku semasa mengemaskini permohonan pinjaman: ') . $e->getMessage());
        }
    }

    public function submit(LoanApplication $loanApplication): RedirectResponse // Route model binding uses 'loanApplication'
    {
        $this->authorize('submit', $loanApplication);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("LoanApplicationController@submit: Attempting to submit loan application ID {$loanApplication->id} by User ID {$user->id}");
        try {
            $submittedApplication = $this->loanApplicationService->submitApplicationForApproval($loanApplication, $user);
            Log::info('Loan application submitted for approval successfully.', ['application_id' => $submittedApplication->id, 'user_id' => $user->id]);
            return redirect()->route('resource-management.loan-applications.show', ['loan_application' => $submittedApplication->id])->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (Exception $e) {
            Log::error('Error submitting loan application for approval.', ['application_id' => $loanApplication->id, 'user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', __('Gagal menghantar permohonan pinjaman untuk kelulusan: ') . $e->getMessage());
        }
    }

    public function destroy(LoanApplication $loan_application): RedirectResponse
    {
        // authorizeResource handles 'delete'
        /** @var User $user */
        $user = Auth::user();
        if (!$loan_application->isDraft() && !$user->can('forceDelete', $loan_application)) { // Assuming forceDelete for admin
            Log::warning('Attempt to delete non-draft/non-deletable loan application.', ['application_id' => $loan_application->id, 'user_id' => Auth::id()]);
            return redirect()->route('resource-management.loan-applications.show', ['loan_application' => $loan_application->id])->with('error', __('Hanya permohonan draf yang boleh dibuang atau anda tiada kebenaran.'));
        }
        Log::info('LoanApplicationController@destroy: Attempting to delete loan application.', ['user_id' => $user->id, 'application_id' => $loan_application->id]);
        try {
            $this->loanApplicationService->deleteApplication($loan_application, $user);
            Log::info('Loan application deleted successfully.', ['application_id' => $loan_application->id, 'user_id' => $user->id]);
            return redirect()->route('resource-management.my-applications.loan.index')->with('success', __('Permohonan pinjaman berjaya dibuang.'));
        } catch (Exception $e) {
            Log::error('Error deleting loan application: ' . $e->getMessage(), ['application_id' => $loan_application->id, 'user_id' => $user->id]);
            return redirect()->back()->with('error', __('Gagal membuang permohonan pinjaman: ') . $e->getMessage());
        }
    }
}
