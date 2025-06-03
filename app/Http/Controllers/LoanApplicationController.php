<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\User;
use App\Models\Equipment; // Added for fetching equipment types
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

    // Adjusted authorizeResource to correctly handle parameters for specific methods
    // Index, create, edit are typically handled by Livewire components if they exist for those actions
    // or might need separate policy checks if these are traditional controller methods.
    // Assuming 'loan_application' is the parameter name used in routes for show, update, destroy.
    $this->authorizeResource(LoanApplication::class, 'loan_application', [
      'except' => ['index', 'create', 'edit', 'createTraditionalForm'], // Exclude methods handled by Livewire or custom logic
    ]);
  }

  /**
   * Show the form for creating a new loan application using a traditional Blade view.
   * This method handles the route: /resource-management/application-forms/loan/create
   *
   * @return \Illuminate\View\View
   */
  public function createTraditionalForm(): View
  {
    $this->authorize('create', LoanApplication::class); // Authorize using policy

    // Fetch users who can be responsible officers
    $responsibleOfficers = User::where('status', User::STATUS_ACTIVE)
      ->orderBy('name')
      ->with(['position:id,name', 'grade:id,name']) // Eager load for display
      ->get(['id', 'name', 'position_id', 'grade_id']);

    // Fetch users who can be supporting officers (e.g., Gred 41 and above)
    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
    $supportingOfficers = User::where('status', User::STATUS_ACTIVE)
      ->whereHas('grade', function ($query) use ($minSupportGradeLevel) {
        $query->where('level', '>=', $minSupportGradeLevel); // Assuming 'level' is numeric
      })
      ->with(['position:id,name', 'grade:id,name']) // Eager load for display
      ->orderBy('name')
      ->get(['id', 'name', 'position_id', 'grade_id']);

    // Fetch equipment asset type options
    $equipmentAssetTypeOptions = Equipment::getAssetTypeOptions() ?? [];

    // Log::info('LoanApplicationController@createTraditionalForm: Displaying traditional create form.');
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
      // The createAndSubmitApplication service method is designed to take $isDraft as the 3rd param.
      // For a direct store from traditional form, it's not a draft by default unless you add a "save draft" button.
      // If this 'store' always means "submit", then $isDraftOnly would be false.
      // Your service method createAndSubmitApplication creates as draft then relies on submitApplicationForApproval.
      // Let's align with how the service seems to work: create as draft first if not explicitly submitting.
      // However, StoreLoanApplicationRequest implies intent to submit.
      // The service method's `createAndSubmitApplication` has $saveAsDraftOnly parameter.
      // If the traditional form doesn't have a "save as draft" option, we pass false.
      $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
        $validatedData,
        $user,
        false // false for $saveAsDraftOnly, meaning it will attempt full submission process via service
      );

      // If createAndSubmitApplication only makes it a draft "ready for submission" and requires explicit call
      // to submitApplicationForApproval, that needs to happen here or be handled by the service.
      // The service `createAndSubmitApplication` creates as 'draft' then expects submission.
      // The controller's `store` method implies immediate submission.
      // Let's assume for now the service's `createAndSubmitApplication(data, user, false)` handles making it 'pending_support'.
      // Or, if it just makes a confirmed draft, then:
      // if ($loanApplication->status === LoanApplication::STATUS_DRAFT && ($validatedData['applicant_confirmation'] ?? false)) {
      //    $loanApplication = $this->loanApplicationService->submitApplicationForApproval($loanApplication, $user);
      // }
      // Given the service method name 'createAndSubmitApplication', it should ideally handle the submission part when $saveAsDraftOnly is false.

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
    $this->authorize('view', $loanApplication); // Authorize using policy
    Log::info("LoanApplicationController@show: User ID " . Auth::id() . " viewing LoanApplication ID {$loanApplication->id}.");

    $loanApplication->loadMissing([
      'user.department:id,name',
      'user.position:id,name',
      'user.grade:id,name,level',
      'responsibleOfficer:id,name,email,position_id,grade_id,department_id',
      'responsibleOfficer.position:id,name',
      'responsibleOfficer.grade:id,name,level',
      'responsibleOfficer.department:id,name',
      'supportingOfficer:id,name,email,position_id,grade_id,department_id',
      'supportingOfficer.position:id,name',
      'supportingOfficer.grade:id,name,level',
      'supportingOfficer.department:id,name',
      'approvals.officer:id,name,title',
      'applicationItems', // Removed equipmentCategory for simplicity, add if direct relation exists
      'loanTransactions.issuingOfficer:id,name',
      'loanTransactions.receivingOfficer:id,name',
      'loanTransactions.returningOfficer:id,name',
      'loanTransactions.returnAcceptingOfficer:id,name',
      'loanTransactions.loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
      'creator:id,name',
      'updater:id,name',
    ]);

    return view('loan-applications.show', compact('loanApplication'));
  }

  public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication): RedirectResponse
  {
    $this->authorize('update', $loanApplication); // Authorize using policy
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
      // The service method `updateApplication` in `LoanApplicationService.php` (from your provided file)
      // has signature: updateApplication(LoanApplication $application, array $validatedData, User $user)
      // It needs to handle items and draft status from within $validatedData.
      $updatedApplication = $this->loanApplicationService->updateApplication(
        $loanApplication,
        $validatedData, // This array should contain items and any flags for draft status for the service to interpret.
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
      // Re-validate applicant_confirmation if it's a separate step
      // For simplicity, assuming it's handled by the service or already set.
      // If applicant_confirmation must be true here:
      // if (!$loanApplication->applicant_confirmation_timestamp) {
      //      $request->validate(['applicant_confirmation' => 'accepted'], ['applicant_confirmation.accepted' => __('Anda mesti mengesahkan perakuan sebelum menghantar.')]);
      //      $loanApplication->applicant_confirmation_timestamp = now();
      //      $loanApplication->save();
      // }

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
