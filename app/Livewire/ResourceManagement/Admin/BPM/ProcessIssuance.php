<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User; // Make sure User model is imported
use App\Services\LoanTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProcessIssuance extends Component
{
    public LoanApplication $loanApplication;
    public array $selectedEquipmentIds = [];
    public array $accessories = [];
    public string $issue_notes = '';
    public array $allAccessoriesList = [];
    public $availableEquipment = [];

    public function mount(int $loanApplicationId): void
    {
        // It's good practice to also eager load relations used in the view or processing
        $this->loanApplication = LoanApplication::with([
            'applicationItems.equipment', // If you display equipment details from application items
            'user' // Applicant details
        ])->findOrFail($loanApplicationId);
        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']);
        $this->loadAvailableEquipment();
    }

    public function loadAvailableEquipment(): void
    {
        $requestedTypes = $this->loanApplication->applicationItems->pluck('equipment_type')->unique()->toArray();

        $this->availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            // ->whereIn('asset_type', $requestedTypes) // Consider uncommenting if needed
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    public function updatedSelectedEquipmentIds($value)
    {
        // Placeholder for any logic if needed when equipment selection changes
    }

    public function submitIssue(LoanTransactionService $loanTransactionService): void
    {
        $this->validate();

        /** @var \App\Models\User|null $issuingOfficer */
        $issuingOfficer = Auth::user();

        if (!$issuingOfficer) {
            session()->flash('error', 'Pengguna tidak disahkan. Sila log masuk semula.');
            Log::warning('ProcessIssuance: Attempted to submit issue without authenticated user.', [
                'loan_application_id' => $this->loanApplication->id,
            ]);
            return;
        }

        try {
            $loanTransactionService->processNewIssue(
                $this->loanApplication,
                $this->selectedEquipmentIds,
                $this->accessories,
                $this->issue_notes,
                $issuingOfficer // Pass the User object instead of Auth::id()
            );

            session()->flash('success', 'Peralatan berjaya dikeluarkan.');
            // Consider redirecting to a page showing the details of the transaction or the updated application
            $this->redirectRoute('resource-management.admin.bpm.loan-applications.view', ['id' => $this->loanApplication->id]); // Example redirect
        } catch (\Exception $e) {
            Log::error('Error processing issuance in ProcessIssuance Livewire: ' . $e->getMessage(), [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => $issuingOfficer->id, // Use ID from the fetched user object
                'exception' => $e
            ]);
            session()->flash('error', 'Gagal mengeluarkan peralatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-issuance');
    }

    protected function rules(): array
    {
        return [
            'selectedEquipmentIds' => ['required', 'array', 'min:1'],
            'selectedEquipmentIds.*' => ['required', 'integer', Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE)],
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['string'],
            'issue_notes' => ['nullable', 'string'],
        ];
    }
}
