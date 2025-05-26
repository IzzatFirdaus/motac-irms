<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Http\Requests\IssueEquipmentRequest; // From user's upload
use App\Http\Requests\ProcessReturnRequest;  // From user's upload
use App\Services\LoanTransactionService;
use App\Services\LoanApplicationService; // To fetch loan application details
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception; // General exception

class LoanTransactionController extends Controller
{
  protected LoanTransactionService $loanTransactionService;
  protected LoanApplicationService $loanApplicationService;

  public function __construct(
    LoanTransactionService $loanTransactionService,
    LoanApplicationService $loanApplicationService
  ) {
    $this->loanTransactionService = $loanTransactionService;
    $this->loanApplicationService = $loanApplicationService;
    $this->middleware('auth');
    // Policies will be checked within methods or via FormRequests
  }

  /**
   * Show the form for issuing equipment for a specific loan application.
   */
  public function showIssueForm(LoanApplication $loanApplication): View|RedirectResponse // Route model binding
  {
    /** @var User $user */
    $user = Auth::user();
    // Policy check: can the current user issue equipment for this loan application?
    // This might be LoanApplicationPolicy@issueEquipment or a new LoanTransactionPolicy@createIssue
    if ($user->cannot('issueEquipment', $loanApplication)) { // Assumes 'issueEquipment' ability on LoanApplicationPolicy
      Log::warning('User unauthorized to show issue form.', ['user_id' => $user->id, 'loan_app_id' => $loanApplication->id]);
      return redirect()->route('dashboard')->with('error', 'Anda tidak dibenarkan untuk mengeluarkan peralatan bagi permohonan ini.');
    }

    // Ensure application is in a state where issuance is allowed
    if (!in_array($loanApplication->status, [LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_PARTIALLY_ISSUED])) {
      return redirect()->route('admin.loan_applications.show', $loanApplication) // Or appropriate admin route
        ->with('error', "Peralatan tidak boleh dikeluarkan. Status permohonan: {$loanApplication->status_translated}.");
    }

    $loanApplication->load('items', 'applicant'); // Load items requested and applicant details
    $bpmUsers = User::role('BPMStaff')->orderBy('name')->pluck('name', 'id'); // For 'Pegawai Pengeluar'
    $availableEquipment = \App\Models\Equipment::where('status', \App\Models\Equipment::STATUS_AVAILABLE)
      ->orderBy('tag_id')->get(['id', 'tag_id', 'brand', 'model', 'asset_type']);


    return view('loan_transactions.issue_form', compact('loanApplication', 'bpmUsers', 'availableEquipment')); // Ensure this view exists
  }

  /**
   * Process the issuance of equipment.
   */
  public function issueEquipment(IssueEquipmentRequest $request, LoanApplication $loanApplication): RedirectResponse
  {
    /** @var User $user */
    $user = Auth::user(); // Issuing Officer (BPM Staff)
    // Authorization is handled by IssueEquipmentRequest and policy on controller method access.
    // This check is an additional safeguard for the specific action.
    if ($user->cannot('issueEquipment', $loanApplication)) {
      Log::warning('User unauthorized to issue equipment.', ['user_id' => $user->id, 'loan_app_id' => $loanApplication->id]);
      return redirect()->back()->withInput()->with('error', 'Anda tidak dibenarkan untuk mengeluarkan peralatan bagi permohonan ini.');
    }

    $validatedData = $request->validated();
    Log::info('Issuing equipment.', ['loan_app_id' => $loanApplication->id, 'user_id' => $user->id, 'items_count' => count($validatedData['items'])]);

    try {
      $receivingOfficer = User::findOrFail($validatedData['receiving_officer_id']);
      $transaction = $this->loanTransactionService->createIssueTransaction(
        $loanApplication,
        $user, // Issuing officer
        $receivingOfficer,
        $validatedData['items'],
        $validatedData['issue_notes'] ?? null,
        $validatedData['accessories_overall'] ?? null
      );
      return redirect()->route('loan_transactions.show', $transaction->id) // Or back to loan application show page
        ->with('success', 'Peralatan berjaya dikeluarkan.');
    } catch (Exception $e) {
      Log::error('Error issuing equipment:', ['loan_app_id' => $loanApplication->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
      return redirect()->back()->withInput()->with('error', 'Ralat semasa mengeluarkan peralatan: ' . $e->getMessage());
    }
  }

  /**
   * Show the form for returning equipment against an original issue transaction.
   */
  public function showReturnForm(LoanTransaction $loanTransaction): View|RedirectResponse // This $loanTransaction is the ISSUE transaction
  {
    /** @var User $user */
    $user = Auth::user();
    // Policy check: Can user process return for this transaction?
    // This might be LoanTransactionPolicy@createReturn
    if ($user->cannot('createReturn', $loanTransaction)) {
      Log::warning('User unauthorized to show return form.', ['user_id' => $user->id, 'issue_tx_id' => $loanTransaction->id]);
      return redirect()->route('dashboard')->with('error', 'Anda tidak dibenarkan untuk memproses pemulangan bagi transaksi ini.');
    }

    if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
      return redirect()->back()->with('error', 'Transaksi yang dipilih bukan transaksi pengeluaran yang sah.');
    }
    // Check if all items already returned for this issue transaction?

    $loanTransaction->load('loanApplication.applicant', 'loanTransactionItems.equipment');
    $bpmUsers = User::role('BPMStaff')->orderBy('name')->pluck('name', 'id'); // For 'Pegawai Terima Pulangan'
    $allUsers = User::orderBy('name')->pluck('name', 'id'); // For 'Pegawai Yang Memulangkan'
    $conditionStatuses = \App\Models\Equipment::getConditionStatusOptions();
    $itemReturnStatuses = \App\Models\LoanTransactionItem::getStatuses(); // All statuses from LTI


    return view('loan_transactions.return_form', compact('loanTransaction', 'bpmUsers', 'allUsers', 'conditionStatuses', 'itemReturnStatuses')); // Ensure this view exists
  }

  /**
   * Process the return of equipment.
   */
  public function processReturn(ProcessReturnRequest $request, LoanTransaction $loanTransaction): RedirectResponse // This $loanTransaction is the ISSUE transaction
  {
    /** @var User $user */
    $user = Auth::user(); // Return Accepting Officer (BPM Staff)
    // Authorization is handled by ProcessReturnRequest and policy on controller method access
    if ($user->cannot('createReturn', $loanTransaction)) {
      Log::warning('User unauthorized to process return.', ['user_id' => $user->id, 'issue_tx_id' => $loanTransaction->id]);
      return redirect()->back()->withInput()->with('error', 'Anda tidak dibenarkan untuk memproses pemulangan bagi transaksi ini.');
    }

    $validatedData = $request->validated();
    Log::info('Processing equipment return.', ['issue_tx_id' => $loanTransaction->id, 'user_id' => $user->id]);

    try {
      $returningOfficer = User::findOrFail($validatedData['returning_officer_id']);
      $returnTransaction = $this->loanTransactionService->createReturnTransaction(
        $loanTransaction->loanApplication, // Pass the parent loan application
        $loanTransaction,                 // Pass the original issue transaction
        $returningOfficer,
        $user,                           // Return accepting officer
        $validatedData['items'],
        $validatedData['return_notes'] ?? null,
        $validatedData['accessories_overall'] ?? null // If you have this field
      );
      return redirect()->route('loan_transactions.show', $returnTransaction->id)
        ->with('success', 'Pemulangan peralatan berjaya diproses.');
    } catch (Exception $e) {
      Log::error('Error processing equipment return:', ['issue_tx_id' => $loanTransaction->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
      return redirect()->back()->withInput()->with('error', 'Ralat semasa memproses pemulangan: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified loan transaction.
   */
  public function show(LoanTransaction $loanTransaction): View
  {
    /** @var User $user */
    $user = Auth::user();
    // Add policy check: $this->authorize('view', $loanTransaction);
    if ($user->cannot('view', $loanTransaction)) { // Assuming a generic 'view' ability in LoanTransactionPolicy
      abort(403, __('Anda tidak dibenarkan melihat transaksi ini.'));
    }


    $loanTransaction->load([
      'loanApplication.applicant',
      'loanApplication.responsibleOfficer',
      'issuingOfficer',
      'receivingOfficer',
      'returningOfficer',
      'returnAcceptingOfficer',
      'loanTransactionItems.equipment',
      'loanTransactionItems.loanApplicationItem'
    ]);
    return view('loan_transactions.show', compact('loanTransaction')); // Ensure this view exists
  }

  /**
   * List issued loans (for BPM or Admin).
   * This might be better as a Livewire component or part of a report.
   */
  public function listIssuedLoans(Request $request): View
  {
    /** @var User $user */
    $user = Auth::user();
    // Policy: e.g., $this->authorize('viewAnyIssued', LoanTransaction::class);
    if (!$user->hasAnyRole(['Admin', 'BPMStaff'])) {
      abort(403, __('Anda tidak dibenarkan melihat senarai pinjaman dikeluarkan.'));
    }

    $issuedTransactions = $this->loanTransactionService->getIssuedLoanTransactions($request->all());
    return view('loan_transactions.issued_list', compact('issuedTransactions')); // Ensure this view exists
  }
}
