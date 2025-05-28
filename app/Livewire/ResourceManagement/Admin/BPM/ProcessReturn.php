<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransactionItem;
use App\Models\User; // Make sure User model is imported
use App\Services\LoanTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProcessReturn extends Component
{
    public LoanApplication $loanApplication;
    public array $selectedTransactionItemIds = [];
    public array $accessories_on_return = []; // This is currently passed as $itemSpecificDetails
    // Ensure its structure matches what processExistingReturn expects
    // or adjust the service method / data preparation here.
    public string $return_notes = '';
    public array $allAccessoriesList = [];
    public $issuedTransactionItems = [];

    public function mount(int $loanApplicationId): void
    {
        $this->loanApplication = LoanApplication::with([
            'applicationItems',
            'user',
            'loanTransactions.loanTransactionItems.equipment'
        ])->findOrFail($loanApplicationId);
        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']);
        $this->loadIssuedItems();
    }

    public function loadIssuedItems(): void
    {
        $this->issuedTransactionItems = LoanTransactionItem::whereHas('loanTransaction', function ($query) {
            $query->where('loan_application_id', $this->loanApplication->id)
                  ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE);
        })
        // Ensure this constant exists and is correct for items that can be returned
        ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
        ->with('equipment')
        ->get();
    }

    public function submitReturn(LoanTransactionService $loanTransactionService): void
    {
        $this->validate();

        /** @var \App\Models\User|null $returnAcceptingOfficer */
        $returnAcceptingOfficer = Auth::user();

        if (!$returnAcceptingOfficer) {
            session()->flash('error', 'Pengguna tidak disahkan. Sila log masuk semula.');
            Log::warning('ProcessReturn: Attempted to submit return without authenticated user.', [
                'loan_application_id' => $this->loanApplication->id,
            ]);
            return;
        }

        // CRITICAL: The $itemSpecificDetails argument for processExistingReturn in your service
        // expects an array keyed by LoanTransactionItem ID, with each element being an array
        // of details like 'condition_on_return', 'notes', 'accessories_data', 'item_status_on_return'.
        // Currently, $this->accessories_on_return is just a flat array of accessory strings.
        // You need to build the $itemSpecificDetails structure correctly here based on your form inputs.
        // For demonstration, I'm passing an empty array, assuming you'll build this.
        $itemSpecificDetailsForService = [];
        foreach ($this->selectedTransactionItemIds as $txItemId) {
            // Example: Fetch details for this item from your Livewire properties
            // $itemSpecificDetailsForService[$txItemId] = [
            //     'condition_on_return' => $this->itemConditions[$txItemId] ?? \App\Models\Equipment::CONDITION_GOOD,
            //     'notes' => $this->itemReturnNotes[$txItemId] ?? null,
            //     'accessories_data' => $this->itemReturnAccessories[$txItemId] ?? [], // Assuming this structure
            //     'item_status_on_return' => null, // Let service determine or get from form
            // ];
        }
        // If $this->accessories_on_return is global for the transaction, it might go into $return_notes
        // or the service method signature for processExistingReturn needs to be adjusted.
        // For now, I'm assuming $itemSpecificDetails needs to be properly constructed.
        // Using $this->accessories_on_return directly as $itemSpecificDetails is likely incorrect.

        try {
            $loanTransactionService->processExistingReturn(
                $this->loanApplication,
                $this->selectedTransactionItemIds,
                $itemSpecificDetailsForService, // Placeholder: Construct this properly based on your form
                $this->return_notes,
                $returnAcceptingOfficer // Pass the User object
            );

            session()->flash('success', 'Peralatan berjaya dipulangkan.');
            // Consider redirecting to a page showing the details of the transaction or the updated application
            $this->redirectRoute('resource-management.admin.bpm.loan-applications.view', ['id' => $this->loanApplication->id]); // Example redirect
        } catch (\Exception $e) {
            Log::error('Error processing return in ProcessReturn Livewire: ' . $e->getMessage(), [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => $returnAcceptingOfficer->id, // Use ID from the fetched user object
                'exception' => $e
            ]);
            session()->flash('error', 'Gagal memulangkan peralatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-return');
    }

    // For more detailed return, you would have properties like:
    // public array $itemConditions = []; // ['tx_item_id_1' => 'good', ...]
    // public array $itemReturnNotes = []; // ['tx_item_id_1' => 'note for item 1', ...]
    // public array $itemReturnAccessories = []; // ['tx_item_id_1' => ['cable', 'bag'], ...]

    protected function rules(): array
    {
        return [
            'selectedTransactionItemIds' => ['required', 'array', 'min:1'],
            'selectedTransactionItemIds.*' => [
                'required',
                'integer',
                Rule::exists('loan_transaction_items', 'id')
                    ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED) // Ensure item is still issued
            ],
            // The following rules depend on how you structure detailed item return data.
            // This is a simplified version:
            'accessories_on_return' => ['nullable', 'array'],
            'accessories_on_return.*' => ['string'], // If accessories_on_return is just a flat list
            'return_notes' => ['nullable', 'string'],
        ];
    }
}
