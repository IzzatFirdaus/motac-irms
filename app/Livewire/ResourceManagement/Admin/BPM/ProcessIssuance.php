<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Carbon\Carbon; // Added for transaction_date

class ProcessIssuance extends Component
{
    public LoanApplication $loanApplication;
    public array $allAccessoriesList = [];
    public $availableEquipment = [];

    // Properties to hold the form data for issuance
    public array $issueItems = []; // Structure: [['loan_application_item_id' => null, 'equipment_id' => null, 'quantity_issued' => 1, 'issue_item_notes' => '', 'accessories_checklist_item' => []], ...]
    public $receiving_officer_id;
    public $transaction_date;
    public string $issue_notes = ''; // Overall issue notes

    public function mount(int $loanApplicationId): void
    {
        $this->loanApplication = LoanApplication::with([
            'applicationItems.equipment',
            'user'
        ])->findOrFail($loanApplicationId);

        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']);
        $this->loadAvailableEquipment();

        // Initialize with at least one item structure if needed, or based on application items
        // This part would depend on how your form is designed to be pre-filled or started
        if (empty($this->issueItems)) {
             // Example: Pre-fill based on approved items that haven't been fully issued.
             // This logic needs to be robust based on your UI.
             foreach ($this->loanApplication->applicationItems as $appItem) {
                 if (($appItem->quantity_approved ?? 0) > ($appItem->quantity_issued ?? 0)) {
                     $this->issueItems[] = [
                         'loan_application_item_id' => $appItem->id,
                         'equipment_id' => null, // User will select specific equipment
                         'quantity_issued' => 1, // Default to 1, user can change
                         'max_quantity_issuable' => ($appItem->quantity_approved ?? 0) - ($appItem->quantity_issued ?? 0),
                         'equipment_type' => $appItem->equipment_type, // For filtering available equipment
                         'issue_item_notes' => '',
                         'accessories_checklist_item' => [],
                     ];
                 }
             }
            if(empty($this->issueItems)){ // If still empty, add a blank one
                 $this->addIssueItem();
            }
        }

        $this->receiving_officer_id = $this->loanApplication->user_id; // Default to applicant
        $this->transaction_date = Carbon::now()->format('Y-m-d'); // Default to today
    }

    public function loadAvailableEquipment(): void
    {
        // This can be more sophisticated, perhaps grouping by type for easier selection in the view
        $this->availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->orderBy('asset_type')
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    // Method to add a new item structure to the form (if your UI supports adding multiple items dynamically)
    public function addIssueItem(): void
    {
        $this->issueItems[] = [
            'loan_application_item_id' => null,
            'equipment_id' => null,
            'quantity_issued' => 1,
            'max_quantity_issuable' => 0,
            'equipment_type' => null,
            'issue_item_notes' => '',
            'accessories_checklist_item' => [],
        ];
    }

    // Method to remove an item (if UI supports it)
    public function removeIssueItem(int $index): void
    {
        if (count($this->issueItems) > 1) {
            unset($this->issueItems[$index]);
            $this->issueItems = array_values($this->issueItems); // Re-index
        }
    }


    public function submitIssue(LoanTransactionService $loanTransactionService): void
    {
        $validatedFromComponent = $this->validate(); // Uses rules() method below

        /** @var \App\Models\User|null $issuingOfficer */
        $issuingOfficer = Auth::user();

        if (!$issuingOfficer) {
            session()->flash('error', 'Pengguna tidak disahkan. Sila log masuk semula.');
            Log::warning('ProcessIssuance: Attempted to submit issue without authenticated user.', [
                'loan_application_id' => $this->loanApplication->id,
            ]);
            return;
        }

        // Prepare the $validatedData structure expected by the service
        $dataForService = [
            'items' => $validatedFromComponent['issueItems'], // Assuming 'issueItems' is the validated array of items
            'receiving_officer_id' => $validatedFromComponent['receiving_officer_id'],
            'transaction_date' => $validatedFromComponent['transaction_date'],
            'issue_notes' => $validatedFromComponent['issue_notes'] ?? null,
        ];

        try {
            // Call the service with the correctly structured $dataForService
            $loanTransactionService->processNewIssue(
                $this->loanApplication,
                $dataForService,
                $issuingOfficer
            );

            session()->flash('success', 'Peralatan berjaya dikeluarkan.');
            $this->redirectRoute('resource-management.admin.bpm.loan-applications.view', ['id' => $this->loanApplication->id]);
        } catch (\Exception $e) {
            Log::error('Error processing issuance in ProcessIssuance Livewire: ' . $e->getMessage(), [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => $issuingOfficer->id,
                'exception' => $e
            ]);
            session()->flash('error', 'Gagal mengeluarkan peralatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Ensure your view 'livewire.resource-management.admin.bpm.process-issuance'
        // is adapted to use $this->issueItems and other new properties for the form.
        return view('livewire.resource-management.admin.bpm.process-issuance', [
            'users' => User::orderBy('name')->get() // For receiving_officer_id dropdown
        ]);
    }

    protected function rules(): array
    {
        // Validation rules need to match the new structure, especially for $issueItems
        return [
            'issueItems' => ['required', 'array', 'min:1'],
            'issueItems.*.loan_application_item_id' => ['required', 'integer', Rule::exists('loan_application_items', 'id')->where('loan_application_id', $this->loanApplication->id)],
            'issueItems.*.equipment_id' => ['required', 'integer', Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE)],
            'issueItems.*.quantity_issued' => ['required', 'integer', 'min:1'], // Add custom rule for max_quantity_issuable if $this->issueItems[$index]['max_quantity_issuable'] is reliable
            'issueItems.*.issue_item_notes' => ['nullable', 'string', 'max:1000'],
            'issueItems.*.accessories_checklist_item' => ['nullable', 'array'],
            'issueItems.*.accessories_checklist_item.*' => ['string'], // Validate each accessory string

            'receiving_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'transaction_date' => ['required', 'date'],
            'issue_notes' => ['nullable', 'string', 'max:2000'], // Overall notes
        ];
    }

    // You might need updated() hooks to recalculate max_quantity_issuable for items
    // or to filter available equipment based on the selected loan_application_item_id's equipment_type.
    // For example:
    public function updatedIssueItems(mixed $value, string $key): void
    {
        // $key will be like 'issueItems.0.loan_application_item_id'
        $parts = explode('.', $key);
        if (count($parts) === 3 && $parts[0] === 'issueItems' && $parts[2] === 'loan_application_item_id') {
            $index = (int)$parts[1];
            $loanAppItemId = $this->issueItems[$index]['loan_application_item_id'] ?? null;

            if ($loanAppItemId) {
                $appItem = $this->loanApplication->loanApplicationItems()->find($loanAppItemId);
                if ($appItem) {
                    $this->issueItems[$index]['max_quantity_issuable'] = ($appItem->quantity_approved ?? 0) - ($appItem->quantity_issued ?? 0);
                    $this->issueItems[$index]['equipment_type'] = $appItem->equipment_type;
                    // Reset equipment_id if main item changes, forcing reselection
                    $this->issueItems[$index]['equipment_id'] = null;
                    if ($this->issueItems[$index]['quantity_issued'] > $this->issueItems[$index]['max_quantity_issuable']) {
                         $this->issueItems[$index]['quantity_issued'] = $this->issueItems[$index]['max_quantity_issuable'];
                    }
                     if ($this->issueItems[$index]['quantity_issued'] <= 0 && $this->issueItems[$index]['max_quantity_issuable'] > 0) {
                        $this->issueItems[$index]['quantity_issued'] = 1;
                    }
                }
            } else {
                $this->issueItems[$index]['max_quantity_issuable'] = 0;
                $this->issueItems[$index]['equipment_type'] = null;
                $this->issueItems[$index]['equipment_id'] = null;
            }
        }
         // Ensure quantity_issued does not exceed max_quantity_issuable
        if (count($parts) === 3 && $parts[0] === 'issueItems' && $parts[2] === 'quantity_issued') {
            $index = (int)$parts[1];
            if (isset($this->issueItems[$index]['max_quantity_issuable']) && $this->issueItems[$index]['quantity_issued'] > $this->issueItems[$index]['max_quantity_issuable']) {
                $this->issueItems[$index]['quantity_issued'] = $this->issueItems[$index]['max_quantity_issuable'];
            }
            if ($this->issueItems[$index]['quantity_issued'] < 0) {
                 $this->issueItems[$index]['quantity_issued'] = 0;
            }
        }
    }
}
