<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\User; // Ensure User model is imported
use App\Services\LoanApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\View\View;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoanApplicationController extends Controller
{
  protected LoanApplicationService $loanApplicationService;

  public function __construct(LoanApplicationService $loanApplicationService)
  {
    $this->loanApplicationService = $loanApplicationService;
    $this->middleware('auth');

    $this->authorizeResource(LoanApplication::class, 'loan_application', [
      'except' => ['index', 'create', 'edit'],
    ]);
  }

  public function store(StoreLoanApplicationRequest $request): RedirectResponse
  {
    /** @var \App\Models\User $user */
    $user = $request->user();
    $validatedData = $request->validated();

    Log::info("LoanApplicationController@store: User ID {$user->id} attempting to create and submit new loan application.");

    try {
      $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
        $validatedData,
        $user
      );

      Log::info("Loan application ID: {$loanApplication->id} created and submitted successfully by User ID: {$user->id}.");

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
    Log::info("LoanApplicationController@show: User ID " . Auth::id() . " viewing LoanApplication ID {$loanApplication->id}.");

    // UNCOMMENT ONE OF THESE FOR DEBUGGING:
    // Option 1: Log the attributes (check your laravel.log file)
    // Log::debug('LoanApplication attributes before loadMissing: ', $loanApplication->getAttributes());

    // Option 2: Dump and die to see attributes in the browser (stops further execution)
    // dd($loanApplication->getAttributes());
    // You can also try: dd($loanApplication->toArray()); to see how it serializes

    $loanApplication->loadMissing([ // This is line 78 where the error occurs
      'user.department:id,name',
      'user.position:id,name',
      'user.grade:id,name',
      'responsibleOfficer:id,name,email,position_id,grade_id,department_id',
      'responsibleOfficer.position:id,name',
      'responsibleOfficer.grade:id,name',
      'responsibleOfficer.department:id,name',
      'supportingOfficer:id,name,email,position_id,grade_id,department_id',
      'supportingOfficer.position:id,name',
      'supportingOfficer.grade:id,name',
      'supportingOfficer.department:id,name',
      'approvals.officer:id,name',
      'applicationItems',
      'loanTransactions.issuingOfficer:id,name',
      'loanTransactions.receivingOfficer:id,name',
      'loanTransactions.returningOfficer:id,name',
      'loanTransactions.returnAcceptingOfficer:id,name',
      'loanTransactions.loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
    ]);

    return view('loan-applications.show', compact('loanApplication'));
  }

  public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
  {
    /** @var \App\Models\User $user */
    $user = $request->user();
    $validatedData = $request->validated();

    if (!$loanApplication->is_draft) {
      Log::warning("User ID {$user->id} attempt to update non-draft LoanApplication ID {$loanApplication->id}.", [
        'application_status' => $loanApplication->status,
      ]);
      return redirect()
        ->route('loan-applications.show', $loanApplication)
        ->with('error', __('Hanya draf permohonan yang boleh dikemaskini.'));
    }

    Log::info("LoanApplicationController@update: User ID {$user->id} attempting to update LoanApplication ID {$loanApplication->id}.");

    try {
      $updatedApplication = $this->loanApplicationService->updateApplication(
        $loanApplication,
        $validatedData,
        $user
      );
      Log::info("LoanApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id}.");

      return redirect()
        ->route('loan-applications.show', $updatedApplication)
        ->with('success', __('Permohonan pinjaman berjaya dikemaskini.'));
    } catch (Throwable $e) {
      Log::error("Error updating LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
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

  public function submitApplication(LoanApplication $loanApplication): RedirectResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $this->authorize('submit', $loanApplication);

    Log::info("LoanApplicationController@submitApplication: User ID {$user->id} attempting to submit LoanApplication ID {$loanApplication->id}.");

    try {
      $submittedApplication = $this->loanApplicationService->submitApplicationForApproval(
        $loanApplication,
        $user
      );
      Log::info("LoanApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

      return redirect()
        ->route('loan-applications.show', $submittedApplication)
        ->with('success', __('Permohonan pinjaman berjaya dihantar untuk kelulusan.'));
    } catch (Throwable $e) {
      Log::error("Error submitting LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
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

    if (!$loanApplication->is_draft) {
      Log::warning("User ID {$user->id} attempt to delete non-draft LoanApplication ID {$loanApplication->id}.", [
        'application_status' => $loanApplication->status,
      ]);
      return redirect()
        ->route('loan-applications.show', $loanApplication)
        ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
    }

    Log::info("LoanApplicationController@destroy: User ID {$user->id} attempting to soft delete LoanApplication ID {$loanApplication->id}.");

    try {
      $this->loanApplicationService->deleteApplication($loanApplication, $user);
      Log::info("LoanApplication ID {$loanApplication->id} (now soft deleted) operation by User ID {$user->id} was successful.");

      return redirect()->route('loan-applications.index')
        ->with('success', __('Permohonan pinjaman berjaya dibuang.'));
    } catch (Throwable $e) {
      Log::error("Error soft deleting LoanApplication ID {$loanApplication->id} by User ID {$user->id}.", [
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
