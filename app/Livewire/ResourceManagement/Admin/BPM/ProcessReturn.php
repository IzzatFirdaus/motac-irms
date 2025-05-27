<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransactionItem; // For identifying items to return
use App\Services\LoanTransactionService; // Assuming you have this service
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProcessReturn extends Component
{
    public LoanApplication $loanApplication;
    public array $selectedTransactionItemIds = []; // IDs of LoanTransactionItem to be returned
    public array $accessories_on_return = [];
    public string $return_notes = '';
    public array $allAccessoriesList = [];
    public $issuedTransactionItems = []; // Collection of LoanTransactionItem that are 'issued'

    // Add properties for condition of each item on return if needed
    // public array $item_conditions = []; Example: ['transaction_item_id_1' => 'good', 'transaction_item_id_2' => 'minor_damage']


    protected function rules(): array
    {
        return [
            'selectedTransactionItemIds' => ['required', 'array', 'min:1'],
            'selectedTransactionItemIds.*' => ['required', 'integer', Rule::exists('loan_transaction_items', 'id')], // Make sure these items belong to this loan app and are 'issued'
            'accessories_on_return' => ['nullable', 'array'],
            'accessories_on_return.*' => ['string'],
            'return_notes' => ['nullable', 'string'],
            // 'item_conditions' => ['nullable', 'array'], // If tracking condition per item here
            // 'item_conditions.*' => ['required', Rule::in(array_keys(Equipment::$CONDITION_STATUSES_LABELS))], //
        ];
    }

    public function mount(int $loanApplicationId): void
    {
        $this->loanApplication = LoanApplication::with('items', 'user', 'loanTransactions.transactionItems.equipment')->findOrFail($loanApplicationId); //
        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']);
        $this->loadIssuedItems();
    }

    public function loadIssuedItems(): void
    {
        // Fetch LoanTransactionItems that are currently 'issued' for this loanApplication
        // This logic needs to be precise based on your LoanTransaction and LoanTransactionItem structure
        $this->issuedTransactionItems = LoanTransactionItem::whereHas('loanTransaction', function ($query) {
            $query->where('loan_application_id', $this->loanApplication->id)
                  ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE); // Ensure you have TYPE_ISSUE constant
        })
        ->where('status', \App\Models\LoanTransactionItem::STATUS_ISSUED) // Ensure you have STATUS_ISSUED constant
        ->with('equipment') //
        ->get();
    }

    public function submitReturn(LoanTransactionService $loanTransactionService): void
    {
        $this->validate();

        try {
            // The service should handle:
            // 1. Creating LoanTransaction (type: return)
            // 2. Updating selected LoanTransactionItem(s) status (e.g., 'returned_good', 'returned_damaged')
            // 3. Updating Equipment status (e.g., to 'available' or 'under_maintenance') and condition_status
            // 4. Updating LoanApplicationItem->quantity_returned
            // 5. Updating LoanApplication status (e.g., to 'returned' or 'partially_returned')
            $loanTransactionService->processExistingReturn(
                $this->loanApplication,
                $this->selectedTransactionItemIds,
                $this->accessories_on_return,
                $this->return_notes,
                Auth::id(),
                // $this->item_conditions // Pass this if implemented
            );

            session()->flash('success', 'Peralatan berjaya dipulangkan.');
            $this->redirectRoute('resource-management.admin.bpm.issued-loans');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulangkan peralatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-return');
    }
}
