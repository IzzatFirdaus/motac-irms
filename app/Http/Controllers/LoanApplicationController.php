<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use App\Services\LoanApplicationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Controller for managing Loan Applications.
 * Handles creation, update, submission, PDF generation, display, and deletion of loan applications.
 */
class LoanApplicationController extends Controller
{
    protected LoanApplicationService $loanApplicationService;

    public function __construct(LoanApplicationService $loanApplicationService)
    {
        $this->loanApplicationService = $loanApplicationService;
        $this->middleware('auth');

        // Authorization handled for all except index, create, edit, createTraditionalForm
        $this->authorizeResource(LoanApplication::class, 'loan_application', [
            'except' => ['index', 'create', 'edit', 'createTraditionalForm'],
        ]);
    }

    /**
     * Show the loan application creation form (traditional).
     */
    public function createTraditionalForm(): View
    {
        $this->authorize('create', LoanApplication::class);

        // Get responsible officers (active users)
        $responsibleOfficers = User::where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->with(['position:id,name', 'grade:id,name', 'department:id,name'])
            ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']);

        // Get supporting officers (filtered by grade level)
        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
        $supportingOfficers = User::where('status', User::STATUS_ACTIVE)
            ->whereHas('grade', function ($query) use ($minSupportGradeLevel): void {
                $query->where('level', '>=', $minSupportGradeLevel);
            })
            ->with(['position:id,name', 'grade:id,name', 'department:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']);

        // Equipment asset type options for dropdowns
        $equipmentAssetTypeOptions = Equipment::getAssetTypeOptions() ?? [];

        return view('loan-applications.create', [
            'responsibleOfficers' => $responsibleOfficers,
            'supportingOfficers' => $supportingOfficers,
            'equipmentAssetTypeOptions' => $equipmentAssetTypeOptions
        ]);
    }

    /**
     * Store a new loan application from the traditional form.
     */
    public function store(StoreLoanApplicationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info(sprintf('LoanApplicationController@store: User ID %d attempting to create and submit new loan application via traditional form.', $user->id), ['data_keys' => array_keys($validatedData)]);

        try {
            $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
                $validatedData,
                $user,
                false // not API
            );

            Log::info(sprintf('Loan application ID: %d created and submitted successfully by User ID: %d via traditional form.', $loanApplication->id, $user->id));

            return redirect()
                ->route('loan-applications.show', $loanApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (IlluminateValidationException $e) {
            Log::warning(sprintf('LoanApplicationController@store: Validation error for User ID %d (traditional form).', $user->id), ['errors' => $e->errors()]);

            return redirect()->back()->withInput()->withErrors($e->errors())
                ->with('error', __('Sila semak semula borang permohonan. Terdapat maklumat yang tidak sah.'));
        } catch (Throwable $e) {
            Log::error(sprintf('Error creating and submitting loan application for User ID: %d (traditional form).', $user->id), [
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

    /**
     * Show the details of a loan application.
     */
    public function show(LoanApplication $loanApplication): View
    {
        $this->authorize('view', $loanApplication);
        Log::info('LoanApplicationController@show: User ID '.Auth::id().sprintf(' viewing LoanApplication ID %d.', $loanApplication->id));

        // Eager load all relationships needed for display
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

        // Attach status label and color class for UI
        $statusLabel = $loanApplication->status_label;
        $statusColorClass = $loanApplication->status_color_class;

        // Load item status labels and equipment type labels for each item
        foreach ($loanApplication->loanApplicationItems as $item) {
            // Accessors from LoanApplicationItem will be called automatically when accessed in the view
            // No assignment needed for equipment_type_label accessor
        }

        return view('loan-applications.show', [
            'loanApplication' => $loanApplication,
            'statusLabel' => $statusLabel,
            'statusColorClass' => $statusColorClass,
        ]);
    }

    /**
     * Generate a PDF printout for the specified loan application.
     */
    public function printPdf(LoanApplication $loanApplication): Response
    {
        $this->authorize('view', $loanApplication);
        Log::info('LoanApplicationController@printPdf: User ID '.Auth::id().sprintf(' generating PDF for LoanApplication ID %d.', $loanApplication->id));

        // Eager load all necessary relations for PDF generation
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
                'loanTransactionItems.loanApplicationItem',
            ]),
        ]);

        $pdf = Pdf::loadView('loan-applications.pdf.print-form', [
            'loanApplication' => $loanApplication,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('borang-pinjaman-ict-'.$loanApplication->id.'.pdf');
    }

    /**
     * Update an existing loan application.
     */
    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('update', $loanApplication);
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info(sprintf('LoanApplicationController@update: User ID %s attempting to update LoanApplication ID %d via traditional form.', $user->id, $loanApplication->id));

        try {
            $updatedApplication = $this->loanApplicationService->updateApplication(
                $loanApplication,
                $validatedData,
                $user
            );
            Log::info(sprintf('LoanApplication ID %d updated successfully by User ID %s (traditional form).', $updatedApplication->id, $user->id));

            return redirect()
                ->route('loan-applications.show', $updatedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dikemaskini.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error updating LoanApplication ID %d by User ID %s (traditional form).', $loanApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_class' => get_class($throwable),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $userMessage = ($throwable instanceof \RuntimeException || $throwable instanceof \InvalidArgumentException || $throwable instanceof ModelNotFoundException)
                ? $throwable->getMessage()
                : __('Satu ralat berlaku semasa mengemaskini permohonan pinjaman.');

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Submit a loan application for approval.
     */
    public function submitApplication(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $user = $request->user();
        Log::info(sprintf('LoanApplicationController@submitApplication: User ID %s attempting to submit LoanApplication ID %d (traditional flow).', $user->id, $loanApplication->id));

        try {
            $submittedApplication = $this->loanApplicationService->submitApplicationForApproval(
                $loanApplication,
                $user
            );
            Log::info(sprintf('LoanApplication ID %d submitted successfully by User ID %s. Status: %s (traditional flow).', $submittedApplication->id, $user->id, $submittedApplication->status));

            return redirect()
                ->route('loan-applications.show', $submittedApplication)
                ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error submitting LoanApplication ID %d by User ID %s (traditional flow).', $loanApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_class' => get_class($throwable),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
            ]);
            $userMessage = ($throwable instanceof \RuntimeException || $throwable instanceof \InvalidArgumentException)
                ? $throwable->getMessage()
                : __('Gagal menghantar permohonan pinjaman disebabkan ralat sistem.');

            return redirect()->route('loan-applications.show', $loanApplication)->with('error', $userMessage);
        }
    }

    /**
     * Soft delete a loan application.
     */
    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        $user = Auth::user();
        $this->authorize('delete', $loanApplication);

        Log::info(sprintf('LoanApplicationController@destroy: User ID %s attempting to soft delete LoanApplication ID %d (traditional flow).', $user->id, $loanApplication->id));

        try {
            $this->loanApplicationService->deleteApplication($loanApplication, $user);
            Log::info(sprintf('LoanApplication ID %d (now soft deleted) operation by User ID %s was successful (traditional flow).', $loanApplication->id, $user->id));

            return redirect()->route('loan-applications.index')
                ->with('success', __('Permohonan pinjaman berjaya dibuang.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error soft deleting LoanApplication ID %d by User ID %s (traditional flow).', $loanApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_class' => get_class($throwable),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
            ]);
            $userMessage = ($throwable instanceof \RuntimeException) ? $throwable->getMessage() : __('Gagal membuang permohonan pinjaman.');

            return redirect()->back()->with('error', $userMessage);
        }
    }
}
