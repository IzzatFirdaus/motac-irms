<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\View\View;
use Throwable;

class LoanApplicationController extends Controller
{
    protected LoanApplicationService $loanApplicationService;

    public function __construct(LoanApplicationService $loanApplicationService)
    {
        $this->loanApplicationService = $loanApplicationService;
        $this->middleware('auth');

        // Authorize resource methods. 'index', 'create', 'edit' are primarily handled by Livewire.
        $this->authorizeResource(LoanApplication::class, 'loan_application', [ // Use snake_case for parameter name
            'except' => ['index', 'create', 'edit'], // Assuming Livewire handles these form/listing views
        ]);
    }

    /**
     * Store a newly created loan application and submit it for approval.
     *
     * @param  \App\Http\Requests\StoreLoanApplicationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLoanApplicationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("LoanApplicationController@store: User ID {$user->id} attempting to create and submit new loan application.");

        try {
            $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
                $validatedData,
                $user
            );

            Log::info("Loan application ID: {$loanApplication->id} created and submitted successfully by User ID: {$user->id}.");

            // Corrected route name
            return redirect()
                ->route('loan-applications.show', $loanApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (IlluminateValidationException $e) {
            Log::warning("LoanApplicationController@store: Validation error for User ID {$user->id}.", ['errors' => $e->errors()]);
            return redirect()->back()->withInput()->withErrors($e->errors())
                ->with('error', __('Sila semak semula borang permohonan. Terdapat maklumat yang tidak sah.'));
        } catch (Throwable $e) {
            Log::error("Error creating and submitting loan application for User ID: {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Satu ralat berlaku semasa menghantar permohonan pinjaman.');
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Display the specified loan application.
     *
     * @param  \App\Models\LoanApplication  $loanApplication
     * @return \Illuminate\View\View
     */
    public function show(LoanApplication $loanApplication): View
    {
        // Authorization handled by authorizeResource for 'loan_application'
        // $this->authorize('view', $loanApplication);

        Log::info("LoanApplicationController@show: User ID ".Auth::id()." viewing LoanApplication ID {$loanApplication->id}.");

        $loanApplication->loadMissing([
            'user.department:id,name',
            'user.position:id,name',
            'user.grade:id,name',
            'responsibleOfficer:id,name',
            'supportingOfficer:id,name',
            'approvals.officer:id,name',
            'applicationItems', // Removed .equipmentTypeLabel, assuming it's an accessor used in Blade
            'loanTransactions.issuingOfficer:id,name',
            'loanTransactions.receivingOfficer:id,name',
            'loanTransactions.returningOfficer:id,name',
            'loanTransactions.returnAcceptingOfficer:id,name',
            'loanTransactions.loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
        ]);

        return view('loan-applications.show', compact('loanApplication'));
    }

    /**
     * Update the specified loan application in storage.
     *
     * @param  \App\Http\Requests\UpdateLoanApplicationRequest  $request
     * @param  \App\Models\LoanApplication  $loanApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
    {
        // Authorization handled by authorizeResource for 'loan_application'
        // $this->authorize('update', $loanApplication);

        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        if (!$loanApplication->isDraft() || (int)$loanApplication->user_id !== (int)$user->id) {
            Log::warning("User ID {$user->id} attempt to update non-draft or unauthorized LoanApplication ID {$loanApplication->id}.", [
                'application_status' => $loanApplication->status,
                'owner_id' => $loanApplication->user_id,
            ]);
            // Corrected route name
            return redirect()
                ->route('loan-applications.show', $loanApplication)
                ->with('error', __('Hanya draf permohonan anda yang boleh dikemaskini.'));
        }

        Log::info("LoanApplicationController@update: User ID {$user->id} attempting to update LoanApplication ID {$loanApplication->id}.");

        try {
            $updatedApplication = $this->loanApplicationService->updateApplication(
                $loanApplication,
                $validatedData,
                $user
            );
            Log::info("LoanApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id}.");

            // Corrected route name
            return redirect()
                ->route('loan-applications.show', $updatedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dikemaskini.'));
        } catch (Throwable $e) {
            Log::error("Error updating LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Satu ralat berlaku semasa mengemaskini permohonan pinjaman.');
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Submit a previously saved draft or rejected loan application for approval.
     *
     * @param  \App\Models\LoanApplication  $loanApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitApplication(LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Manually authorize the 'submit' ability from LoanApplicationPolicy
        $this->authorize('submit', $loanApplication);

        Log::info("LoanApplicationController@submitApplication: User ID {$user->id} attempting to submit LoanApplication ID {$loanApplication->id}.");

        try {
            $submittedApplication = $this->loanApplicationService->submitApplicationForApproval(
                $loanApplication,
                $user
            );
            Log::info("LoanApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

            // Corrected route name
            return redirect()
                ->route('loan-applications.show', $submittedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal menghantar permohonan pinjaman disebabkan ralat sistem.');
            // Corrected route name for redirect back
            return redirect()->route('loan-applications.show', $loanApplication)->with('error', $userMessage);
        }
    }

    /**
     * Remove the specified loan application (soft delete).
     *
     * @param  \App\Models\LoanApplication  $loanApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        // Authorization handled by authorizeResource for 'loan_application'
        // $this->authorize('delete', $loanApplication);

        /** @var User $user */
        $user = Auth::user();

        if (!$loanApplication->isDraft() || (int)$loanApplication->user_id !== (int)$user->id) {
            Log::warning("User ID {$user->id} attempt to delete non-draft or unauthorized LoanApplication ID {$loanApplication->id}.", [
                'application_status' => $loanApplication->status,
                'owner_id' => $loanApplication->user_id,
            ]);
            // Corrected route name
            return redirect()
                ->route('loan-applications.show', $loanApplication)
                ->with('error', __('Hanya draf permohonan anda yang boleh dibuang.'));
        }

        Log::info("LoanApplicationController@destroy: User ID {$user->id} attempting to soft delete LoanApplication ID {$loanApplication->id}.");

        try {
            $this->loanApplicationService->deleteApplication($loanApplication, $user);
            Log::info("LoanApplication ID {$loanApplication->id} soft deleted successfully by User ID {$user->id}.");

            // Corrected route name - this should point to the Livewire index page for loan applications
            return redirect()->route('loan-applications.index')
                ->with('success', __('Permohonan pinjaman berjaya dibuang.'));
        } catch (Throwable $e) {
            Log::error("Error soft deleting LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return redirect()->back()->with('error', __('Gagal membuang permohonan pinjaman: ') . $e->getMessage());
        }
    }
}
