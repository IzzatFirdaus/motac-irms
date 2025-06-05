<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanTransaction;
use App\Models\LoanApplication; // Ensure this is imported
use App\Models\LoanTransactionItem;
use App\Models\Equipment;
use App\Models\User;
use App\Services\LoanTransactionService;
use App\Http\Requests\IssueEquipmentRequest;
use App\Http\Requests\ProcessReturnRequest; // This might be used by the Livewire component or a service
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Keep standard Request for controller methods
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
    try {
      $this->authorize('viewAny', LoanTransaction::class);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@index: Unauthorized attempt.", ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
      abort(403, __('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pinjaman.'));
    }

    $loanTransactions = $this->loanTransactionService->getTransactions(
      $request->all(),
      ['loanApplication.user:id,name', 'issuingOfficer:id,name', 'returnAcceptingOfficer:id,name'], // Adjusted default withs
      10
    );

    Log::info("LoanTransactionController@index: User ID " . Auth::id() . " viewing list of LoanTransactions.");
    // Ensure this view path is correct for your project structure
    return view('loan-transactions.index', compact('loanTransactions'));
  }

  /**
   * Display details of a specific loan transaction.
   */
  public function show(LoanTransaction $loanTransaction): View|RedirectResponse
  {
    try {
      $this->authorize('view', $loanTransaction);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@show: Unauthorized attempt for Tx ID {$loanTransaction->id}.", ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
      return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
    }

    Log::info("LoanTransactionController@show: User ID " . Auth::id() . " viewing LoanTransaction ID {$loanTransaction->id}.");
    $loanTransaction->loadMissing(LoanTransaction::getDefinedDefaultRelationsStatic());
    // Ensure this view path is correct
    return view('loan-transactions.show', compact('loanTransaction'));
  }

  /**
   * Show the form for recording equipment issuance.
   * This might be a Livewire component or a traditional Blade view.
   * The provided code has it as a Blade view.
   */
  public function showIssueForm(LoanApplication $loanApplication): View|RedirectResponse
  {
    try {
      // Ensure your policy is named 'createIssue' and accepts LoanApplication
      $this->authorize('createIssue', [LoanTransaction::class, $loanApplication]);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@showIssueForm: Unauthorized for LA ID {$loanApplication->id}.", ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
      return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pengeluaran untuk permohonan ini.'));
    }

    $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)->orderBy('brand')->orderBy('model')->get();
    $loanApplicantAndResponsibleOfficer = collect([$loanApplication->user, $loanApplication->responsibleOfficer])->filter()->unique('id');
    $allAccessoriesList = config('motac.loan_accessories_list', []); //

    Log::info("LoanTransactionController@showIssueForm: User ID " . Auth::id() . " accessing issue form for LA ID {$loanApplication->id}.");
    // Ensure this view path is correct
    return view('loan-transactions.issue', compact('loanApplication', 'availableEquipment', 'loanApplicantAndResponsibleOfficer', 'allAccessoriesList'));
  }

  /**
   * Store the recorded equipment issuance.
   */
  public function storeIssue(
    IssueEquipmentRequest $request,
    LoanApplication $loanApplication
  ): RedirectResponse {
    // Authorization is handled by IssueEquipmentRequest->authorize()

    /** @var User $issuingOfficer */
    $issuingOfficer = Auth::user();
    $validatedData = $request->validated();

    Log::info("LoanTransactionController@storeIssue: User ID {$issuingOfficer->id} storing issue for LA ID {$loanApplication->id}.");

    try {
      $this->loanTransactionService->processNewIssue(
        $loanApplication,
        $validatedData,
        $issuingOfficer
      );

      Log::info("LoanTransactionController@storeIssue: Issuance for LA ID {$loanApplication->id} processed successfully.");
      return redirect()->route('loan-applications.show', $loanApplication->id)
        ->with('success', __('Pengeluaran peralatan berjaya direkodkan.'));
    } catch (Throwable $e) {
      Log::error("LoanTransactionController@storeIssue: Error for LA ID {$loanApplication->id}: " . $e->getMessage(), [
        'user_id' => $issuingOfficer->id,
        'exception_class' => get_class($e),
        'request_data' => $request->except(['_token']),
      ]);
      $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException) ? $e->getMessage() : __('Gagal merekodkan pengeluaran peralatan.');
      return redirect()->back()->withInput()->with('error', $errorMessage);
    }
  }

  /**
   * Show the form for recording equipment return.
   * $loanTransaction here is the original ISSUE transaction.
   * This method will now load the wrapper view that hosts the Livewire component.
   */
  public function returnForm(LoanTransaction $loanTransaction): View|RedirectResponse
  {
    // $loanTransaction is the original "issue" transaction.
    // Authorize based on the loan application associated with this issue transaction.
    // The policy method 'processReturn' should expect a LoanApplication instance.
    try {
      $this->authorize('processReturn', $loanTransaction->loanApplication);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@returnForm: Unauthorized for Issue Tx ID {$loanTransaction->id}.", ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
      return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk memproses pulangan ini.'));
    }


    if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
      Log::warning("LoanTransactionController@returnForm: Attempted to process return for non-issue Tx ID {$loanTransaction->id}.", ['user_id' => Auth::id()]);
      return redirect()->back()->with('error', __('Transaksi ini bukan transaksi pengeluaran peralatan yang sah untuk dipulangkan.'));
    }

    $loanApplication = $loanTransaction->loanApplication; // Get the related LoanApplication

    if (!$loanApplication) {
      Log::error("LoanTransactionController@returnForm: LoanApplication not found for Tx ID {$loanTransaction->id}.");
      return redirect()->route('dashboard')->with('error', __('Permohonan pinjaman berkaitan tidak ditemui.'));
    }

    Log::info("LoanTransactionController@returnForm: User ID " . Auth::id() . " accessing return form (via Livewire wrapper) for Issue Tx ID {$loanTransaction->id}.");

    // This view ('loan-transactions.return-form-page') will host the Livewire component.
    // It passes the necessary IDs to the Livewire component's mount method.
    return view('loan-transactions.return-form-page', [
      'issueTransactionId' => $loanTransaction->id,
      'loanApplicationId' => $loanApplication->id,
    ]);
  }

  /**
   * Store the recorded equipment return.
   * This logic is now primarily handled by the ProcessReturn Livewire component's submitReturn() method.
   * This controller method might not be directly hit by the form if Livewire handles the submission.
   * If you need an endpoint for non-Livewire return submissions, it would go here.
   */
  public function storeReturn(
    ProcessReturnRequest $request, // Use ProcessReturnRequest for validation if a non-Livewire form posts here
    LoanTransaction $loanTransaction // This is the original ISSUE transaction
  ): RedirectResponse {
    // Authorization is (or should be) handled by ProcessReturnRequest->authorize()
    // or directly within the Livewire component.

    /** @var User $returnAcceptingOfficer (BPM Staff) */
    $returnAcceptingOfficer = Auth::user();
    $validatedData = $request->validated();

    Log::info("LoanTransactionController@storeReturn: User ID {$returnAcceptingOfficer->id} attempting to store return for Issue Tx ID {$loanTransaction->id} via controller method.");

    try {
      // This assumes your service can be called from here too, which is good for flexibility.
      $this->loanTransactionService->processExistingReturn(
        $loanTransaction,       // The original ISSUE transaction
        $validatedData,         // Validated data from ProcessReturnRequest
        $returnAcceptingOfficer
      );

      Log::info("LoanTransactionController@storeReturn: Controller-based return for Issue Tx ID {$loanTransaction->id} processed successfully.");
      return redirect()->route('loan-applications.show', $loanTransaction->loan_application_id)
        ->with('success', __('Peralatan telah berjaya direkodkan pemulangannya.'));
    } catch (Throwable $e) {
      Log::error("LoanTransactionController@storeReturn: Controller-based error for Issue Tx ID {$loanTransaction->id}: " . $e->getMessage(), [
        'user_id' => $returnAcceptingOfficer->id,
        'exception_class' => get_class($e),
        'request_data' => $request->except(['_token']),
      ]);
      $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
        ? $e->getMessage()
        : __('Gagal merekodkan pemulangan peralatan disebabkan ralat sistem.');
      return redirect()->back()->withInput()->with('error', $errorMessage);
    }
  }
}
