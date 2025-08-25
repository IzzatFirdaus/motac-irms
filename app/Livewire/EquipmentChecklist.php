<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

/**
 * EquipmentChecklist Livewire Component
 *
 * Handles the equipment checklist for loan transactions (issue and return).
 * Manages issuing equipment, returning equipment, accessories, and condition tracking.
 */
#[Layout('layouts.app')]
#[Title('Senarai Semak Peralatan')]
class EquipmentChecklist extends Component
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    // Main properties for the component
    public $loanApplicationId;
    public array $selectedEquipmentIds = []; // For multiple equipment selection
    public string $transactionType = 'issue'; // 'issue' or 'return'
    public $officerId;

    // Accessories and notes
    public array $accessories = [];
    public string $notes = '';

    // Return-specific properties
    public string $equipmentConditionOnReturn = '';
    public string $returnNotes = '';

    // Model instances
    public ?LoanTransaction $loanTransaction = null;
    public ?LoanApplication $loanApplication = null;

    // Configuration and options
    public array $allAccessoriesList = [];

    // Service dependencies
    protected LoanApplicationService $loanApplicationService;
    protected LoanTransactionService $loanTransactionService;

    /**
     * Boot method - Dependency injection for services
     */
    public function boot(
        LoanApplicationService $loanApplicationService,
        LoanTransactionService $loanTransactionService
    ): void {
        $this->loanApplicationService = $loanApplicationService;
        $this->loanTransactionService = $loanTransactionService;
        Log::debug('EquipmentChecklist: Component booted with required services.');
    }

    /**
     * Mount the component with initial data
     */
    public function mount(
        ?int $loanApplicationId = null,
        ?string $type = null,
        ?int $loanTransactionId = null
    ): void {
        Log::info('EquipmentChecklist: Mounting component.', [
            'loanApplicationId' => $loanApplicationId,
            'type' => $type,
            'loanTransactionId' => $loanTransactionId,
            'user_id' => Auth::id(),
        ]);

        // Initialize properties
        $this->loanApplicationId = $loanApplicationId;
        $this->transactionType = $type ?? 'issue';
        $this->officerId = Auth::id();
        $this->allAccessoriesList = Equipment::getDefaultAccessoriesList();

        try {
            // Load loan application if ID is provided
            if ($this->loanApplicationId !== null && $this->loanApplicationId !== 0) {
                $this->loadLoanApplication();
            }

            // Load existing transaction if ID is provided (for edit mode)
            if ($loanTransactionId !== null && $loanTransactionId !== 0) {
                $this->loadExistingTransaction($loanTransactionId);
            }

        } catch (Throwable $e) {
            Log::error('EquipmentChecklist: Error during component mount.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'loanApplicationId' => $loanApplicationId,
                'loanTransactionId' => $loanTransactionId,
            ]);
            session()->flash('error', __('Ralat semasa memuatkan data. Sila cuba lagi.'));
            $this->dispatch('swal:error', ['message' => __('Ralat semasa memuatkan data peralatan.')]);
        }
    }

    /**
     * Load the loan application with required relationships
     */
    private function loadLoanApplication(): void
    {
        $this->loanApplication = $this->loanApplicationService->findLoanApplicationById(
            $this->loanApplicationId,
            [
                'user',
                'loanApplicationItems.equipment',
                'responsibleOfficer',
                'loanTransactions.loanTransactionItems.equipment',
                'loanTransactions.issuingOfficer',
                'loanTransactions.receivingOfficer',
                'loanTransactions.returningOfficer',
                'loanTransactions.returnAcceptingOfficer',
            ]
        );

        if (!$this->loanApplication instanceof LoanApplication) {
            Log::warning('EquipmentChecklist: Loan application not found.', [
                'loanApplicationId' => $this->loanApplicationId,
            ]);
            throw new ModelNotFoundException(
                sprintf('Permohonan pinjaman ID %s tidak ditemui.', $this->loanApplicationId)
            );
        }
    }

    /**
     * Load existing transaction for edit mode
     */
    private function loadExistingTransaction(int $loanTransactionId): void
    {
        $this->loanTransaction = LoanTransaction::with([
            'loanTransactionItems.equipment',
            'issuingOfficer',
            'receivingOfficer',
            'returningOfficer',
            'returnAcceptingOfficer',
        ])->find($loanTransactionId);

        if (
            !$this->loanTransaction ||
            (int)$this->loanTransaction->loan_application_id !== (int)$this->loanApplicationId
        ) {
            Log::warning('EquipmentChecklist: Transaction not found or mismatched.', [
                'loanTransactionId' => $loanTransactionId,
                'loanApplicationId' => $this->loanApplicationId,
            ]);
            throw new ModelNotFoundException(
                sprintf('Transaksi pinjaman ID %s tidak ditemui untuk permohonan ini.', $loanTransactionId)
            );
        }

        // Populate form fields from existing transaction
        $this->populateFormFromTransaction();
    }

    /**
     * Populate form fields from transaction data for edit
     */
    private function populateFormFromTransaction(): void
    {
        if (!$this->loanTransaction) {
            return;
        }
        // Get equipment IDs from transaction items
        $this->selectedEquipmentIds = $this->loanTransaction->loanTransactionItems
            ->pluck('equipment_id')
            ->toArray();

        // Load transaction-specific data based on type
        $firstItem = $this->loanTransaction->loanTransactionItems->first();
        if ($this->transactionType === LoanTransaction::TYPE_RETURN) {
            if ($firstItem) {
                $this->equipmentConditionOnReturn = $firstItem->condition_on_return
                    ?? ($firstItem->equipment->condition_status ?? Equipment::CONDITION_GOOD);
                $this->returnNotes = $this->loanTransaction->return_notes ?? $firstItem->item_notes ?? '';
                $this->accessories = json_decode(
                    $firstItem->accessories_checklist_on_return ?? '[]',
                    true
                ) ?: [];
            }
        } elseif ($this->transactionType === LoanTransaction::TYPE_ISSUE) {
            if ($firstItem) {
                $this->notes = $this->loanTransaction->issue_notes ?? $firstItem->item_notes ?? '';
                $this->accessories = json_decode(
                    $firstItem->accessories_checklist_on_issue ?? '[]',
                    true
                ) ?: [];
            }
        }
    }

    /**
     * Computed property: Get available equipment for issue
     */
    #[Computed]
    public function getAvailableEquipmentProperty(): Collection
    {
        if (!$this->loanApplication || $this->transactionType !== 'issue') {
            return collect();
        }

        // Get equipment types requested in the application
        $requestedTypes = $this->loanApplication->loanApplicationItems
            ->filter(function ($item) {
                return ($item->quantity_approved ?? 0) > ($item->quantity_issued ?? 0);
            })
            ->pluck('equipment_type')
            ->unique();

        // Find available equipment of requested types
        return Equipment::whereIn('asset_type', $requestedTypes)
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    /**
     * Computed property: Get equipment currently on loan for return
     */
    #[Computed]
    public function getOnLoanEquipmentProperty(): Collection
    {
        if (!$this->loanApplication || $this->transactionType !== 'return') {
            return collect();
        }

        // Get equipment currently on loan for this application
        return Equipment::whereHas('loanTransactionItems', function ($query) {
            $query->whereHas('loanTransaction', function ($subQuery) {
                $subQuery->where('loan_application_id', $this->loanApplicationId)
                    ->where('type', LoanTransaction::TYPE_ISSUE)
                    ->whereNull('return_timestamp');
            });
        })
            ->where('status', Equipment::STATUS_ON_LOAN)
            ->get();
    }

    /**
     * Validation rules for the form
     */
    protected function rules(): array
    {
        $rules = [
            'selectedEquipmentIds' => [
                'required',
                'array',
                'min:1',
            ],
            'selectedEquipmentIds.*' => [
                'required',
                'exists:equipment,id',
            ],
            'accessories' => [
                'array',
            ],
            'accessories.*' => [
                'string',
                'max:255',
            ],
        ];

        // Add specific rules based on transaction type
        if ($this->transactionType === 'issue') {
            $rules['notes'] = ['nullable', 'string', 'max:1000'];
        } elseif ($this->transactionType === 'return') {
            $rules['returnNotes'] = ['nullable', 'string', 'max:1000'];
            $rules['equipmentConditionOnReturn'] = [
                'required',
                'string',
                Rule::in(array_keys(Equipment::getConditionStatusesList())),
            ];
        }

        return $rules;
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'selectedEquipmentIds.required' => __('Sila pilih sekurang-kurangnya satu peralatan.'),
            'selectedEquipmentIds.min' => __('Sila pilih sekurang-kurangnya satu peralatan.'),
            'selectedEquipmentIds.*.exists' => __('Peralatan yang dipilih tidak sah.'),
            'equipmentConditionOnReturn.required' => __('Sila pilih keadaan peralatan semasa pulangan.'),
            'equipmentConditionOnReturn.in' => __('Keadaan peralatan yang dipilih tidak sah.'),
        ];
    }

    /**
     * Save the transaction (main method)
     */
    public function saveTransaction(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->transactionType === 'issue') {
                    $this->processIssueTransaction();
                } elseif ($this->transactionType === 'return') {
                    $this->processReturnTransaction();
                }
                // After transaction, update the loan application overall status
                if ($this->loanApplication) {
                    $this->loanApplication->updateOverallStatusAfterTransaction();
                }
            });

            // Success message and redirect
            $message = $this->transactionType === 'issue'
                ? __('Pengeluaran peralatan berjaya direkodkan!')
                : __('Pulangan peralatan berjaya direkodkan!');

            session()->flash('success', $message);
            $this->dispatch('swal:success', ['message' => $message]);

            $this->redirectAfterSave();

        } catch (ValidationException $e) {
            Log::warning('EquipmentChecklist: Validation failed.', [
                'errors' => $e->errors(),
                'transactionType' => $this->transactionType,
            ]);
            throw $e;
        } catch (Throwable $e) {
            Log::error('EquipmentChecklist: Error saving transaction.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transactionType' => $this->transactionType,
                'loanApplicationId' => $this->loanApplicationId,
            ]);

            $errorMessage = __('Gagal menyimpan transaksi: ') . $e->getMessage();
            session()->flash('error', $errorMessage);
            $this->dispatch('swal:error', ['message' => $errorMessage]);
        }
    }

    /**
     * Process equipment issue transaction
     */
    private function processIssueTransaction(): void
    {
        Log::info('EquipmentChecklist: Processing issue transaction.', [
            'loanApplicationId' => $this->loanApplicationId,
            'equipmentIds' => $this->selectedEquipmentIds,
            'officer_id' => Auth::id(),
        ]);

        // Create or update the loan transaction
        $transactionData = [
            'loan_application_id' => $this->loanApplicationId,
            'type' => LoanTransaction::TYPE_ISSUE,
            'issue_timestamp' => now(),
            'issuing_officer_id' => Auth::id(),
            'issue_notes' => $this->notes,
            'accessories_checklist_on_issue' => json_encode(array_filter($this->accessories)),
            'status' => LoanTransaction::STATUS_COMPLETED,
        ];

        if ($this->loanTransaction) {
            $this->loanTransaction->update($transactionData);
            $transaction = $this->loanTransaction;
        } else {
            $transaction = LoanTransaction::create($transactionData);
        }

        // Process each selected equipment
        foreach ($this->selectedEquipmentIds as $equipmentId) {
            $this->processEquipmentIssue($transaction, $equipmentId);
        }
    }

    /**
     * Process individual equipment issue
     */
    private function processEquipmentIssue(LoanTransaction $transaction, int $equipmentId): void
    {
        $equipment = Equipment::findOrFail($equipmentId);

        // Create transaction item
        LoanTransactionItem::create([
            'loan_transaction_id' => $transaction->id,
            'equipment_id' => $equipmentId,
            'quantity_transacted' => 1,
            'condition_on_issue' => $equipment->condition_status,
            'accessories_checklist_on_issue' => json_encode(array_filter($this->accessories)),
            'item_notes' => $this->notes,
        ]);

        // Update equipment status
        $equipment->update([
            'status' => Equipment::STATUS_ON_LOAN,
            'current_loan_application_id' => $this->loanApplicationId,
            'loaned_at' => now(),
        ]);
    }

    /**
     * Process equipment return transaction
     */
    private function processReturnTransaction(): void
    {
        Log::info('EquipmentChecklist: Processing return transaction.', [
            'loanApplicationId' => $this->loanApplicationId,
            'equipmentIds' => $this->selectedEquipmentIds,
            'officer_id' => Auth::id(),
        ]);

        // Find or create return transaction
        if ($this->loanTransaction && $this->loanTransaction->type === LoanTransaction::TYPE_ISSUE) {
            $this->loanTransaction->update([
                'return_timestamp' => now(),
                'return_accepting_officer_id' => Auth::id(),
                'return_notes' => $this->returnNotes,
                'accessories_checklist_on_return' => json_encode(array_filter($this->accessories)),
            ]);
            $transaction = $this->loanTransaction;
        } else {
            $transactionData = [
                'loan_application_id' => $this->loanApplicationId,
                'type' => LoanTransaction::TYPE_RETURN,
                'return_timestamp' => now(),
                'return_accepting_officer_id' => Auth::id(),
                'return_notes' => $this->returnNotes,
                'accessories_checklist_on_return' => json_encode(array_filter($this->accessories)),
                'status' => LoanTransaction::STATUS_COMPLETED,
            ];
            $transaction = LoanTransaction::create($transactionData);
        }

        // Process each returned equipment
        foreach ($this->selectedEquipmentIds as $equipmentId) {
            $this->processEquipmentReturn($transaction, $equipmentId);
        }
    }

    /**
     * Process individual equipment return
     */
    private function processEquipmentReturn(LoanTransaction $transaction, int $equipmentId): void
    {
        $equipment = Equipment::findOrFail($equipmentId);

        // Find or create transaction item
        $transactionItem = LoanTransactionItem::where('loan_transaction_id', $transaction->id)
            ->where('equipment_id', $equipmentId)
            ->first();

        if ($transactionItem) {
            $transactionItem->update([
                'condition_on_return' => $this->equipmentConditionOnReturn,
                'accessories_checklist_on_return' => json_encode(array_filter($this->accessories)),
                'return_notes' => $this->returnNotes,
            ]);
        } else {
            LoanTransactionItem::create([
                'loan_transaction_id' => $transaction->id,
                'equipment_id' => $equipmentId,
                'quantity_transacted' => 1,
                'condition_on_return' => $this->equipmentConditionOnReturn,
                'accessories_checklist_on_return' => json_encode(array_filter($this->accessories)),
                'return_notes' => $this->returnNotes,
            ]);
        }

        // Update equipment status based on condition
        $newStatus = $this->determineEquipmentStatusAfterReturn($this->equipmentConditionOnReturn);
        $equipment->update([
            'status' => $newStatus,
            'condition_status' => $this->equipmentConditionOnReturn,
            'current_loan_application_id' => null,
            'loaned_at' => null,
            'returned_at' => now(),
        ]);
    }

    /**
     * Determine equipment status after return based on condition.
     * Uses constants from Equipment.php, mapping to status.
     */
    private function determineEquipmentStatusAfterReturn(string $condition): string
    {
        // Map the returned condition to the appropriate status
        return match ($condition) {
            Equipment::CONDITION_GOOD, Equipment::CONDITION_FAIR, Equipment::CONDITION_NEW => Equipment::STATUS_AVAILABLE,
            Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE => Equipment::STATUS_UNDER_MAINTENANCE,
            Equipment::CONDITION_LOST => Equipment::STATUS_LOST,
            default => Equipment::STATUS_AVAILABLE,
        };
    }

    /**
     * Redirect after successful save
     */
    private function redirectAfterSave(): void
    {
        if ($this->loanApplication) {
            $this->redirect(
                route('loan-applications.show', $this->loanApplication->id),
                navigate: true
            );
        } else {
            $this->redirect(
                route('loan-applications.index'),
                navigate: true
            );
        }
    }

    /**
     * Add a new accessory input field
     */
    public function addAccessory(): void
    {
        $this->accessories[] = '';
    }

    /**
     * Remove an accessory from the list
     */
    public function removeAccessory(int $index): void
    {
        unset($this->accessories[$index]);
        $this->accessories = array_values($this->accessories);
    }

    /**
     * Reset the form to initial state
     */
    public function resetForm(): void
    {
        $this->selectedEquipmentIds = [];
        $this->accessories = [];
        $this->notes = '';
        $this->returnNotes = '';
        $this->equipmentConditionOnReturn = '';
        $this->resetErrorBag();
    }

    /**
     * Render the component view
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.equipment-checklist', [
            'availableEquipment' => $this->availableEquipment,
            'onLoanEquipment' => $this->onLoanEquipment,
            'allAccessoriesList' => $this->allAccessoriesList,
            'loanApplication' => $this->loanApplication,
            'loanTransaction' => $this->loanTransaction,
            'transactionType' => $this->transactionType,
        ]);
    }
}
