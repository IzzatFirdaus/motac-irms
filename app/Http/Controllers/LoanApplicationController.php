<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanApplicationService;
use Barryvdh\DomPDF\Facade\Pdf; // ++ ADD THIS USE STATEMENT ++
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // ++ ADD THIS USE STATEMENT ++
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

        $this->authorizeResource(LoanApplication::class, 'loan_application', [
            'except' => ['index', 'create', 'edit', 'createTraditionalForm'],
        ]);
    }

    public function createTraditionalForm(): View
    {
        $this->authorize('create', LoanApplication::class);

        $responsibleOfficers = User::where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->with(['position:id,name', 'grade:id,name', 'department:id,name'])
            ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']);

        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
        $supportingOfficers = User::where('status', User::STATUS_ACTIVE)
            ->whereHas('grade', function ($query) use ($minSupportGradeLevel) {
                $query->where('level', '>=', $minSupportGradeLevel);
            })
            ->with(['position:id,name', 'grade:id,name', 'department:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']);

        $equipmentAssetTypeOptions = Equipment::getAssetTypeOptions() ?? [];

        return view('loan-applications.create', compact(
            'responsibleOfficers',
            'supportingOfficers',
            'equipmentAssetTypeOptions'
        ));
    }

    public function store(StoreLoanApplicationRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("LoanApplicationController@store: User ID {$user->id} attempting to create and submit new loan application via traditional form.", ['data_keys' => array_keys($validatedData)]);

        try {
            $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
                $validatedData,
                $user,
                false
            );

            Log::info("Loan application ID: {$loanApplication->id} created and submitted successfully by User ID: {$user->id} via traditional form.");

            return redirect()
                ->route('loan-applications.show', $loanApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (IlluminateValidationException $e) {
            Log::warning("LoanApplicationController@store: Validation error for User ID {$user->id} (traditional form).", ['errors' => $e->errors()]);

            return redirect()->back()->withInput()->withErrors($e->errors())
                ->with('error', __('Sila semak semula borang permohonan. Terdapat maklumat yang tidak sah.'));
        } catch (Throwable $e) {
            Log::error("Error creating and submitting loan application for User ID: {$user->id} (traditional form).", [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException || $e instanceof ModelNotFoundException)
              ? $e->getMessage()
              : __('Satu ralat berlaku semasa menghantar permohonan pinjaman.');

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    public function show(LoanApplication $loanApplication): View
    {
        $this->authorize('view', $loanApplication);
        Log::info('LoanApplicationController@show: User ID '.Auth::id()." viewing LoanApplication ID {$loanApplication->id}.");

        $loanApplication->loadMissing([
            'user' => fn ($q) => $q->with(['position:id,name', 'grade:id,name', 'department:id,name']),
            'responsibleOfficer' => fn ($q) => $q->with(['position:id,name', 'grade:id,name', 'department:id,name']),
            'supportingOfficer' => fn ($q) => $q->with(['position:id,name', 'grade:id,name', 'department:id,name']),
            'approvedBy:id,name,title',
            'rejectedBy:id,name,title',
            'cancelledBy:id,name,title',
            'currentApprovalOfficer:id,name,title',
            'loanApplicationItems.equipment:id,brand,model,asset_type,tag_id,serial_number',
            'loanTransactions' => fn ($q) => $q->with([
                'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
                'loanTransactionItems.loanApplicationItem',
                'issuingOfficer:id,name,title',
                'receivingOfficer:id,name,title',
                'returningOfficer:id,name,title',
                'returnAcceptingOfficer:id,name,title',
            ])->orderByDesc('transaction_date'),
            'approvals' => fn ($q) => $q->with('officer:id,name,title')->orderBy('created_at'),
            'creator:id,name,title',
            'updater:id,name,title',
        ]);

        return view('loan-applications.show', compact('loanApplication'));
    }

    /**
     * ++ ADD THIS NEW METHOD TO GENERATE THE PDF ++
     *
     * Generate a PDF printout for the specified loan application.
     * @param \App\Models\LoanApplication $loanApplication
     * @return \Illuminate\Http\Response
     */
    public function printPdf(LoanApplication $loanApplication): Response
    {
        // Authorize the user can view the application
        $this->authorize('view', $loanApplication);
        Log::info('LoanApplicationController@printPdf: User ID '.Auth::id()." generating PDF for LoanApplication ID {$loanApplication->id}.");

        // Eager load all data needed for the PDF view to prevent lazy-loading errors.
        $loanApplication->loadMissing([
            'user' => fn ($q) => $q->with(['position:id,name', 'grade:id,name', 'department:id,name']),
            'responsibleOfficer' => fn ($q) => $q->with(['position:id,name', 'grade:id,name']),
            'approvals.officer',
            'loanApplicationItems',
            'loanTransactions' => fn ($q) => $q->with([
                'issuingOfficer',
                'receivingOfficer',
                'returningOfficer',
                'returnAcceptingOfficer',
                'loanTransactionItems.equipment',
                'loanTransactionItems.loanApplicationItem'
            ])
        ]);

        // Load the dedicated Blade view for the PDF
        $pdf = Pdf::loadView('loan-applications.pdf.print-form', [
            'loanApplication' => $loanApplication
        ]);

        // Set paper size to A4 portrait to match the official form
        $pdf->setPaper('A4', 'portrait');

        // Stream the PDF to the browser
        return $pdf->stream('borang-pinjaman-ict-'.$loanApplication->id.'.pdf');
    }

    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('update', $loanApplication);
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("LoanApplicationController@update: User ID {$user->id} attempting to update LoanApplication ID {$loanApplication->id} via traditional form.");

        try {
            $updatedApplication = $this->loanApplicationService->updateApplication(
                $loanApplication,
                $validatedData,
                $user
            );
            Log::info("LoanApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id} (traditional form).");

            return redirect()
                ->route('loan-applications.show', $updatedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dikemaskini.'));
        } catch (Throwable $e) {
            Log::error("Error updating LoanApplication ID {$loanApplication->id} by User ID {$user->id} (traditional form).", [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException || $e instanceof ModelNotFoundException)
              ? $e->getMessage()
              : __('Satu ralat berlaku semasa mengemaskini permohonan pinjaman.');

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    public function submitApplication(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $user = $request->user();
        Log::info("LoanApplicationController@submitApplication: User ID {$user->id} attempting to submit LoanApplication ID {$loanApplication->id} (traditional flow).");

        try {
            $submittedApplication = $this->loanApplicationService->submitApplicationForApproval(
                $loanApplication,
                $user
            );
            Log::info("LoanApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status} (traditional flow).");

            return redirect()
                ->route('loan-applications.show', $submittedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting LoanApplication ID {$loanApplication->id} by User ID {$user->id} (traditional flow).", [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $userMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
              ? $e->getMessage()
              : __('Gagal menghantar permohonan pinjaman disebabkan ralat sistem.');

            return redirect()->route('loan-applications.show', $loanApplication)->with('error', $userMessage);
        }
    }

    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        $user = Auth::user();
        $this->authorize('delete', $loanApplication);

        Log::info("LoanApplicationController@destroy: User ID {$user->id} attempting to soft delete LoanApplication ID {$loanApplication->id} (traditional flow).");

        try {
            $this->loanApplicationService->deleteApplication($loanApplication, $user);
            Log::info("LoanApplication ID {$loanApplication->id} (now soft deleted) operation by User ID {$user->id} was successful (traditional flow).");

            return redirect()->route('loan-applications.index')
                ->with('success', __('Permohonan pinjaman berjaya dibuang.'));
        } catch (Throwable $e) {
            Log::error("Error soft deleting LoanApplication ID {$loanApplication->id} by User ID {$user->id} (traditional flow).", [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $userMessage = ($e instanceof \RuntimeException) ? $e->getMessage() : __('Gagal membuang permohonan pinjaman.');

            return redirect()->back()->with('error', $userMessage);
        }
    }
}
