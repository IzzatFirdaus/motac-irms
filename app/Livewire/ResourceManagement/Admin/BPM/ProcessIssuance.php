<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\Equipment; //
use App\Services\LoanTransactionService; // Assuming you have this service
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProcessIssuance extends Component
{
    public LoanApplication $loanApplication;
    public array $selectedEquipmentIds = [];
    public array $accessories = [];
    public string $issue_notes = '';
    public array $allAccessoriesList = []; // Populate this in mount
    public $availableEquipment = [];

    protected function rules(): array
    {
        return [
            'selectedEquipmentIds' => ['required', 'array', 'min:1'],
            'selectedEquipmentIds.*' => ['required', 'integer', Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE)], //
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['string'],
            'issue_notes' => ['nullable', 'string'],
        ];
    }

    public function mount(int $loanApplicationId): void
    {
        $this->loanApplication = LoanApplication::with('items', 'user')->findOrFail($loanApplicationId);
        // Example: Populate from config or a helper
        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']);
        $this->loadAvailableEquipment();
    }

    public function loadAvailableEquipment(): void
    {
        // Basic filtering: only available equipment.
        // Advanced: Filter by types requested in $this->loanApplication->items
        // and ensure quantity approved is not exceeded by already issued + currently selected.
        // This requires more complex logic, potentially interacting with quantities in $this->loanApplication->items.
        $requestedTypes = $this->loanApplication->items->pluck('equipment_type')->unique()->toArray();

        $this->availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE) //
            // ->whereIn('asset_type', $requestedTypes) // Uncomment if you want to filter by requested types
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    public function updatedSelectedEquipmentIds($value)
    {
        // Placeholder for any logic if needed when equipment selection changes
        // e.g., verify quantity against approved quantity for the type.
    }

    public function submitIssue(LoanTransactionService $loanTransactionService): void
    {
        $this->validate();

        try {
            // The service should handle:
            // 1. Creating LoanTransaction (type: issue)
            // 2. Creating LoanTransactionItem(s) for each selectedEquipmentId
            // 3. Updating Equipment status to 'on_loan'
            // 4. Updating LoanApplicationItem->quantity_issued
            // 5. Updating LoanApplication status (e.g., to 'issued' or 'partially_issued')
            $loanTransactionService->processNewIssue(
                $this->loanApplication,
                $this->selectedEquipmentIds,
                $this->accessories,
                $this->issue_notes,
                Auth::id()
            );

            session()->flash('success', 'Peralatan berjaya dikeluarkan.');
            $this->redirectRoute('resource-management.admin.bpm.issued-loans'); // Or to loan application show page
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengeluarkan peralatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-issuance');
    }
}
