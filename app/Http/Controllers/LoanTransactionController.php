<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LoanTransaction;
use App\Models\LoanApplication;
use App\Models\LoanTransactionItem;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class LoanTransactionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Display a listing of loan transactions.
   * SDD Ref:
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function index(Request $request): View
  {
    try {
      // Assuming a policy exists for viewing any loan transactions
      // For example, you might need 'viewAny' permission for LoanTransaction
      $this->authorize('viewAny', LoanTransaction::class);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@index: Unauthorized attempt to view all LoanTransactions.", [
        'user_id' => Auth::id(),
        'error' => $e->getMessage(),
      ]);
      // Redirect to dashboard with an error message, or show a 403 page
      abort(403, __('Anda tidak mempunyai kebenaran untuk melihat senarai transaksi pinjaman.'));
    }

    // Fetch loan transactions, optionally with pagination
    // You might want to load relationships needed for the index view
    $loanTransactions = LoanTransaction::with([
      'loanApplication:id,user_id',
      'loanApplication.user:id,name',
    ])->latest()->paginate(10);

    Log::info("LoanTransactionController@index: User ID " . Auth::id() . " viewing list of LoanTransactions.");

    return view('loan-transactions.index', compact('loanTransactions'));
  }

  /**
   * Display details of a specific loan transaction.
   * SDD Ref:
   * @param  \App\Models\LoanTransaction  $loanTransaction Route model bound instance
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function show(LoanTransaction $loanTransaction): View|RedirectResponse
  {
    try {
      $this->authorize('view', $loanTransaction);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@show: Unauthorized attempt to view LoanTransaction ID {$loanTransaction->id}.", [
        'user_id' => Auth::id(),
        'error' => $e->getMessage(),
      ]);
      return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat transaksi ini.'));
    }

    Log::info("LoanTransactionController@show: User ID " . Auth::id() . " viewing LoanTransaction ID {$loanTransaction->id} (Type: {$loanTransaction->type}).");
    $loanTransaction->loadMissing([
      'loanApplication.user:id,name',
      'loanApplication.responsibleOfficer:id,name',
      'issuingOfficer:id,name',
      'receivingOfficer:id,name',
      'returningOfficer:id,name',
      'returnAcceptingOfficer:id,name',
      'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model,serial_number',
      'loanTransactionItems.loanApplicationItem:id,equipment_type,quantity_requested',
      'relatedIssueTransaction',
      'creator:id,name',
    ]);
    return view('loan-transactions.show', compact('loanTransaction'));
  }

  /**
   * Show the form for recording equipment issuance for a specific loan application.
   * This method is called from the route 'resource-management.bpm.loan-transactions.issue.form'
   *
   * @param  \App\Models\LoanApplication  $loanApplication
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function showIssueForm(LoanApplication $loanApplication): View|RedirectResponse
  {
      try {
          $this->authorize('processIssuance', $loanApplication);
      } catch (AuthorizationException $e) {
          Log::warning("LoanTransactionController@showIssueForm: Unauthorized attempt to access issue form for LoanApplication ID {$loanApplication->id}.", [
              'user_id' => Auth::id(),
              'error' => $e->getMessage(),
          ]);
          return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pengeluaran untuk permohonan ini.'));
      }

      // Fetch available equipment. You might want to filter this further
      // based on equipment type requested in the loan application.
      $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)->get();

      // Collect the loan applicant and responsible officer for the receiving officer dropdown
      $loanApplicantAndResponsibleOfficer = collect([
          $loanApplication->user,
          optional($loanApplication->responsibleOfficer)
      ])->filter()->unique('id');

      $allAccessoriesList = config('motac.loan_accessories_list', []);

      Log::info("LoanTransactionController@showIssueForm: User ID " . Auth::id() . " accessing issue form for LoanApplication ID {$loanApplication->id}.");

      return view('loan-transactions.issue', compact(
          'loanApplication',
          'availableEquipment',
          'loanApplicantAndResponsibleOfficer',
          'allAccessoriesList'
      ));
  }

  /**
   * Store the recorded equipment issuance.
   * This method is called from the route 'resource-management.bpm.loan-transactions.storeIssue'
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\LoanApplication  $loanApplication
   * @return \Illuminate\Http\RedirectResponse
   */
  public function storeIssue(Request $request, LoanApplication $loanApplication): RedirectResponse
  {
      try {
          $this->authorize('processIssuance', $loanApplication);
      } catch (AuthorizationException $e) {
          Log::warning("LoanTransactionController@storeIssue: Unauthorized attempt to store issue for LoanApplication ID {$loanApplication->id}.", [
              'user_id' => Auth::id(),
              'error' => $e->getMessage(),
          ]);
          return redirect()->back()->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pengeluaran ini.'));
      }

      $request->validate([
          'equipment_ids' => 'required|array|min:1',
          'equipment_ids.*' => 'exists:equipment,id',
          'accessories' => 'nullable|array',
          'accessories.*' => 'string|max:255',
          'issue_notes' => 'nullable|string|max:1000',
          'issuing_officer_id' => 'required|exists:users,id',
          'receiving_officer_id' => 'required|exists:users,id',
      ], [
          'equipment_ids.required' => __('Sila pilih sekurang-kurangnya satu peralatan untuk dikeluarkan.'),
          'equipment_ids.*.exists' => __('Salah satu peralatan yang dipilih tidak wujud.'),
          'issuing_officer_id.required' => __('Pegawai pemprosesan perlu dikenal pasti.'),
          'issuing_officer_id.exists' => __('Pegawai pemprosesan tidak sah.'),
          'receiving_officer_id.required' => __('Sila pilih pegawai yang akan menerima peralatan.'),
          'receiving_officer_id.exists' => __('Pegawai penerima tidak sah.'),
      ]);

      DB::beginTransaction();
      try {
          // Create the main loan transaction record
          $transaction = new LoanTransaction();
          $transaction->loan_application_id = $loanApplication->id;
          $transaction->type = LoanTransaction::TYPE_ISSUE;
          $transaction->transaction_date = now();
          $transaction->issuing_officer_id = $request->input('issuing_officer_id');
          $transaction->receiving_officer_id = $request->input('receiving_officer_id');
          $transaction->accessories_checklist_on_issue = $request->input('accessories') ? json_encode($request->input('accessories')) : null;
          $transaction->issue_notes = $request->input('issue_notes');
          $transaction->issue_timestamp = now();
          $transaction->status = LoanTransaction::STATUS_ISSUED;
          $transaction->created_by = Auth::id();
          $transaction->save();

          $issuedCount = 0;
          foreach ($request->input('equipment_ids') as $equipmentId) {
              $equipment = Equipment::findOrFail($equipmentId);

              // Update equipment status
              $equipment->status = Equipment::STATUS_ON_LOAN;
              $equipment->save();

              // Create LoanTransactionItem
              $transaction->loanTransactionItems()->create([
                  'equipment_id' => $equipmentId,
                  // Find the corresponding LoanApplicationItem for this equipment type
                  'loan_application_item_id' => $loanApplication->loanApplicationItems()
                                                                ->where('equipment_type', $equipment->asset_type)
                                                                ->first()?->id,
                  'quantity_transacted' => 1, // Assuming one equipment unit per transaction item
                  'status' => LoanTransactionItem::STATUS_ITEM_ISSUED,
                  'accessories_checklist_issue' => $request->input('accessories') ? json_encode($request->input('accessories')) : null,
                  'item_notes' => $request->input('issue_notes'),
                  'created_by' => Auth::id(),
              ]);

              // Increment quantity_issued on LoanApplicationItem
              $loanApplicationItem = $loanApplication->loanApplicationItems
                  ->where('equipment_type', $equipment->asset_type)
                  ->first();

              if ($loanApplicationItem) {
                  $loanApplicationItem->increment('quantity_issued');
                  $issuedCount++;
              }
          }

          // Update the overall LoanApplication status
          $loanApplication->updateOverallStatusAfterTransaction();

          DB::commit();

          Log::info("LoanTransactionController@storeIssue: User ID " . Auth::id() . " successfully recorded issuance for LoanApplication ID {$loanApplication->id}. Issued {$issuedCount} items.");
          return redirect()->route('resource-management.bpm.issued-loans')
                           ->with('success', __('Pengeluaran peralatan berjaya direkodkan.'));

      } catch (\Exception $e) {
          DB::rollBack();
          Log::error("LoanTransactionController@storeIssue: Error recording loan issuance for LoanApplication ID {$loanApplication->id}. Error: " . $e->getMessage(), [
              'user_id' => Auth::id(),
              'exception' => $e,
              'request_data' => $request->all()
          ]);
          return redirect()->back()->withInput()
                           ->with('error', __('Gagal merekodkan pengeluaran peralatan. Sila cuba lagi: ') . $e->getMessage());
      }
  }


  /**
   * Show the form for recording equipment return for a specific issue transaction.
   * This method is called from the route 'resource-management.bpm.loan-transactions.return.form'
   *
   * @param  \App\Models\LoanTransaction  $loanTransaction This is the ISSUE transaction being returned.
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function returnForm(LoanTransaction $loanTransaction): View|RedirectResponse
  {
    try {
      $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);
    } catch (AuthorizationException $e) {
      Log::warning("LoanTransactionController@returnForm: Unauthorized attempt to access return form for LoanTransaction ID {$loanTransaction->id}.", [
        'user_id' => Auth::id(),
        'error' => $e->getMessage(),
      ]);
      return redirect()->route('dashboard')->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pulangan ini.'));
    }

    if ($loanTransaction->type !== LoanTransaction::TYPE_ISSUE) {
      return redirect()->back()->with('error', __('Transaksi ini bukan transaksi pengeluaran peralatan.'));
    }

    $loanApplication = $loanTransaction->loanApplication;

    if (!$loanApplication) {
      Log::error("LoanTransactionController@returnForm: LoanApplication not found for LoanTransaction ID {$loanTransaction->id}.");
      return redirect()->back()->with('error', __('Permohonan pinjaman tidak ditemui untuk transaksi ini.'));
    }

    $issuedItemsForThisTransaction = $loanTransaction->loanTransactionItems()
      ->whereDoesntHave('returnTransactionItem')
      ->with('equipment')
      ->get();

    $allAccessoriesList = config('motac.loan_accessories_list', []);

    $loanApplicantAndResponsibleOfficer = collect([
      $loanApplication->user,
      optional($loanApplication->responsibleOfficer)
    ])->filter()->unique('id');

    Log::info("LoanTransactionController@returnForm: User ID " . Auth::id() . " accessing return form for LoanTransaction ID {$loanTransaction->id}.");

    return view('loan-transactions.return', compact(
      'loanTransaction',
      'loanApplication',
      'issuedItemsForThisTransaction',
      'allAccessoriesList',
      'loanApplicantAndResponsibleOfficer'
    ));
  }

  /**
   * Store the recorded equipment return.
   * This method is called from the route 'loan-transactions.storeReturn'
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\LoanTransaction  $loanTransaction (This is the original ISSUE transaction to be returned)
   * @return \Illuminate\Http\RedirectResponse
   */
  public function storeReturn(Request $request, LoanTransaction $loanTransaction): RedirectResponse
  {
    // The `try` block was incorrectly placed or terminated in your previous snippet,
    // causing the `catch` to be unexpected.
    // We ensure the whole logic of the method is wrapped correctly.
    try { // This `try` block should enclose the main logic of the method
        $this->authorize('processReturn', [$loanTransaction, $loanTransaction->loanApplication]);
    } catch (AuthorizationException $e) {
        Log::warning("LoanTransactionController@storeReturn: Unauthorized attempt to store return for LoanTransaction ID {$loanTransaction->id}.", [
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
        ]);
        return redirect()->back()->with('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pulangan ini.'));
    }

    // Validate the incoming request data
    $request->validate([
      'loan_application_id' => 'required|exists:loan_applications,id',
      'loan_transaction_item_ids' => 'required|array|min:1',
      'loan_transaction_item_ids.*' => 'exists:loan_transaction_items,id',
      'accessories_on_return' => 'nullable|array',
      'accessories_on_return.*' => 'string|max:255',
      'return_notes' => 'nullable|string|max:1000',
      'returning_officer_id' => 'required|exists:users,id',
    ], [
      'loan_transaction_item_ids.required' => __('Sila pilih sekurang-kurangnya satu item peralatan yang dipulangkan.'),
      'loan_transaction_item_ids.min' => __('Sila pilih sekurang-kurangnya satu item peralatan yang dipulangkan.'),
      'loan_transaction_item_ids.*.exists' => __('Item peralatan yang dipilih tidak sah.'),
      'returning_officer_id.required' => __('Sila pilih pegawai yang memulangkan peralatan.'),
      'returning_officer_id.exists' => __('Pegawai yang memulangkan tidak sah.'),
    ]);

    // Begin a database transaction to ensure atomicity
    try { // This `try` block should enclose the main logic of the method
      DB::transaction(function () use ($request, $loanTransaction) {
        // 1. Create a new LoanTransaction for the return
        $returnTransaction = new LoanTransaction();
        $returnTransaction->loan_application_id = $request->loan_application_id;
        $returnTransaction->type = LoanTransaction::TYPE_RETURN;
        $returnTransaction->transaction_date = now();
        $returnTransaction->return_accepting_officer_id = Auth::id();
        $returnTransaction->returning_officer_id = $request->returning_officer_id;
        $returnTransaction->accessories_checklist_on_return = json_encode($request->input('accessories_on_return', []));
        $returnTransaction->return_notes = $request->return_notes;
        $returnTransaction->return_timestamp = now();
        $returnTransaction->related_transaction_id = $loanTransaction->id;
        $returnTransaction->status = LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION;
        $returnTransaction->created_by = Auth::id();
        $returnTransaction->save();

        // 2. Update the status and link the selected LoanTransactionItems
        foreach ($request->loan_transaction_item_ids as $loanTransactionItemId) {
          $item = $loanTransaction->loanTransactionItems()->findOrFail($loanTransactionItemId);

          $returnLoanTransactionItem = new LoanTransactionItem();
          $returnLoanTransactionItem->loan_transaction_id = $returnTransaction->id;
          $returnLoanTransactionItem->equipment_id = $item->equipment_id;
          $returnLoanTransactionItem->loan_application_item_id = $item->loan_application_item_id;
          $returnLoanTransactionItem->status = LoanTransactionItem::STATUS_ITEM_RETURNED; // Changed from STATUS_RETURNED
          $returnLoanTransactionItem->issued_at = $item->issued_at;
          $returnLoanTransactionItem->returned_at = now();
          $returnLoanTransactionItem->condition_on_return = null;
          $returnLoanTransactionItem->notes_on_return = $request->return_notes;
          $returnLoanTransactionItem->save();

          if ($item->equipment) {
            $item->equipment->status = Equipment::STATUS_RETURNED_PENDING_INSPECTION;
            $item->equipment->save();
          }
        }

        // 3. Update the status of the original issue transaction
        $originalIssueItems = $loanTransaction->loanTransactionItems()->get();
        $returnedItemsCount = 0;
        foreach ($originalIssueItems as $originalItem) {
          $isReturned = LoanTransactionItem::where('equipment_id', $originalItem->equipment_id)
            ->where('loan_application_item_id', $originalItem->loan_application_item_id)
            ->where('status', LoanTransactionItem::STATUS_ITEM_RETURNED) // Changed from STATUS_RETURNED
            ->exists();
          if ($isReturned) {
            $returnedItemsCount++;
          }
        }

        if ($returnedItemsCount >= $originalIssueItems->count()) {
          $loanTransaction->status = LoanTransaction::STATUS_RETURNED_GOOD;
          $loanTransaction->save();
        } elseif ($returnedItemsCount > 0) {
          $loanTransaction->status = LoanTransaction::STATUS_PARTIALLY_RETURNED;
          $loanTransaction->save();
        }

        // 4. Update the parent LoanApplication status (if applicable)
        $loanApplication = $loanTransaction->loanApplication;
        if ($loanApplication && method_exists($loanApplication, 'updateOverallStatusAfterTransaction')) {
          $loanApplication->updateOverallStatusAfterTransaction();
        }
      });

      Log::info("LoanTransactionController@storeReturn: User ID " . Auth::id() . " successfully recorded return for LoanTransaction ID {$loanTransaction->id}.");
      return redirect()->route('loan-applications.show', $request->loan_application_id)
        ->with('success', __('Peralatan telah berjaya direkodkan pulangan.'));
    } catch (\Exception $e) {
      Log::error("LoanTransactionController@storeReturn: Error recording return for LoanTransaction ID {$loanTransaction->id}. Error: " . $e->getMessage(), [
        'user_id' => Auth::id(),
        'exception' => $e,
      ]);
      return redirect()->back()->withInput()
        ->with('error', __('Gagal merekodkan pulangan peralatan: ') . $e->getMessage());
    }
  }
}
