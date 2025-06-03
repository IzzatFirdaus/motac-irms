<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\User;
use App\Models\Equipment;
use App\Services\LoanApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\View\View;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


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
    $this->authorize('create', LoanApplication::class); //

    $responsibleOfficers = User::where('status', User::STATUS_ACTIVE)
      ->orderBy('name')
      ->with(['position:id,name', 'grade:id,name', 'department:id,name']) //
      ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']); //

    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
    $supportingOfficers = User::where('status', User::STATUS_ACTIVE)
      ->whereHas('grade', function ($query) use ($minSupportGradeLevel) {
        $query->where('level', '>=', $minSupportGradeLevel); //
      })
      ->with(['position:id,name', 'grade:id,name', 'department:id,name']) //
      ->orderBy('name')
      ->get(['id', 'name', 'title', 'position_id', 'grade_id', 'department_id']); //

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
    $this->authorize('view', $loanApplication); //
    Log::info("LoanApplicationController@show: User ID " . Auth::id() . " viewing LoanApplication ID {$loanApplication->id}.");

    $loanApplication->loadMissing([
      // Applicant User details: explicitly select 'title' and eager load nested relations
      'user' => function ($query) {
          $query->select('id', 'name', 'title', 'identification_number', 'mobile_number', 'email', 'motac_email', 'position_id', 'grade_id', 'department_id')
                ->with(['position:id,name', 'grade:id,name', 'department:id,name']);
      },
      // Responsible Officer details: explicitly select 'title' and eager load nested relations
      'responsibleOfficer' => function ($query) {
          $query->select('id', 'name', 'title', 'identification_number', 'mobile_number', 'email', 'motac_email', 'position_id', 'grade_id', 'department_id')
                ->with(['position:id,name', 'grade:id,name', 'department:id,name']);
      },
      // Supporting Officer details: explicitly select 'title' and eager load nested relations
      'supportingOfficer' => function ($query) {
          $query->select('id', 'name', 'title', 'identification_number', 'mobile_number', 'email', 'motac_email', 'position_id', 'grade_id', 'department_id')
                ->with(['position:id,name', 'grade:id,name', 'department:id,name']);
      },
      // Approval officers: explicitly select 'title'
      'approvedBy:id,name,title',
      'rejectedBy:id,name,title',
      'cancelledBy:id,name,title',
      'currentApprovalOfficer:id,name,title',

      // THIS IS THE CRUCIAL PART FOR `applicationItems`
      // Ensure 'loanApplicationItems' (the relationship method name in the model) is directly eager loaded
      'loanApplicationItems' => function ($query) {
          $query->with('equipment:id,brand,model,asset_type,tag_id,serial_number'); // Nested eager load for equipment details
      },

      // Loan Transactions and related officers: explicitly select 'title' for officers
      'loanTransactions' => function ($query) {
          $query->with([
              'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number', //
              'issuingOfficer:id,name,title',
              'receivingOfficer:id,name,title',
              'returningOfficer:id,name,title',
              'returnAcceptingOfficer:id,name,title',
          ])->orderByDesc('transaction_date');
      },
      // Approvals and the officer who made the decision: explicitly select 'title'
      'approvals' => function ($query) {
          $query->with('officer:id,name,title')
                ->orderBy('created_at');
      },
      // Audit trail users: explicitly select 'title'
      'creator:id,name,title',
      'updater:id,name,title',
    ]);

    return view('loan-applications.show', compact('loanApplication'));
  }

  public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
  {
    $this->authorize('update', $loanApplication); //
    /** @var \App\Models\User $user */
    $user = $request->user();
    $validatedData = $request->validated();

    if (!$loanApplication->isDraft() && $loanApplication->status !== LoanApplication::STATUS_REJECTED) {
      Log::warning("User ID {$user->id} attempt to update non-draft/non-rejected LoanApplication ID {$loanApplication->id}.", [
        'application_status' => $loanApplication->status,
      ]);
      return redirect()
        ->route('loan-applications.show', $loanApplication)
        ->with('error', __('Hanya draf permohonan atau permohonan yang ditolak boleh dikemaskini.'));
    }

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
    /** @var \App\Models\User $user */
    $user = $request->user();
    $this->authorize('submit', $loanApplication);

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
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $this->authorize('delete', $loanApplication);

    if (!$loanApplication->isDraft()) {
      Log::warning("User ID {$user->id} attempt to delete non-draft LoanApplication ID {$loanApplication->id} (traditional flow).", [
        'application_status' => $loanApplication->status,
      ]);
      return redirect()
        ->route('loan-applications.show', $loanApplication)
        ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
    }

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
