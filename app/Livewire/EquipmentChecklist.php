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
use Illuminate\Auth\Access\AuthorizationException; // Corrected import for AuthorizationException
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
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

#[Layout('layouts.app')]
#[Title('Equipment Checklist')]
class EquipmentChecklist extends Component
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public $loanApplicationId;

    public $selectedEquipmentId;

    public $transactionType;

    public $officerId;

    public array $accessories = [];

    public string $notes = '';

    public ?LoanTransaction $loanTransaction = null;

    public ?LoanApplication $loanApplication = null;

    public ?Equipment $equipment = null;

    public array $allAccessoriesList = [];

    public string $equipmentConditionOnReturn = '';

    public string $returnNotes = '';

    public $receivingOfficerId;

    public $returnAcceptingOfficerId;

    public $returningOfficerId;

    public array $viewData = [];

    public array $availableEquipmentOptions = [];

    protected LoanApplicationService $loanApplicationService;

    protected LoanTransactionService $loanTransactionService;

    public function boot(
        LoanApplicationService $loanApplicationService,
        LoanTransactionService $loanTransactionService
    ): void {
        $this->loanApplicationService = $loanApplicationService;
        $this->loanTransactionService = $loanTransactionService;
        Log::debug('EquipmentChecklist component booted with services.');
    }

    public function mount(
        ?int $loanApplicationId = null,
        ?string $type = null,
        ?int $loanTransactionId = null
    ): void {
        Log::info('EquipmentChecklist component mounting.', [
            'loanApplicationId' => $loanApplicationId,
            'type' => $type,
            'loanTransactionId' => $loanTransactionId,
        ]);

        $this->loanApplicationId = $loanApplicationId;
        $this->transactionType = $type ?? 'view';

        $this->allAccessoriesList = Equipment::getDefaultAccessoriesList();

        try {
            if ($this->loanApplicationId !== null && $this->loanApplicationId !== 0) {
                // FIX: Changed findLoanApplication to findLoanApplicationById
                $this->loanApplication = $this->loanApplicationService->findLoanApplicationById(
                    $this->loanApplicationId,
                    ['user', 'loanApplicationItems.equipment', 'responsibleOfficer', 'loanTransactions.loanTransactionItems.equipment', 'loanTransactions.issuingOfficer', 'loanTransactions.receivingOfficer', 'loanTransactions.returningOfficer', 'loanTransactions.returnAcceptingOfficer'] // FIX: Changed 'applicationItems' to 'loanApplicationItems' here for consistency and correctness
                );

                if (! $this->loanApplication instanceof LoanApplication) { // Simplified namespace
                    Log::warning(sprintf('Loan Application ID %s not found during mount.', $this->loanApplicationId));
                    throw new ModelNotFoundException(sprintf('Loan Application ID %s not found.', $this->loanApplicationId));
                }

                Log::debug(sprintf('Loaded Loan Application ID %s.', $this->loanApplicationId));

                if ($this->transactionType === LoanTransaction::TYPE_ISSUE) {
                    // FIX: Changed 'applicationItems' to 'loanApplicationItems' here
                    $this->availableEquipmentOptions = $this->loanApplication->loanApplicationItems
                        ->filter(function ($item): bool {
                            return ($item->quantity_approved ?? 0) > ($item->quantity_issued ?? 0);
                        })
                        ->mapWithKeys(function ($appItem) {
                            return Equipment::where('asset_type', $appItem->equipment_type)
                                ->where('status', Equipment::STATUS_AVAILABLE)
                                ->get()
                                ->mapWithKeys(function ($eq) {
                                    return [$eq->id => sprintf('%s %s (Tag: %s) - Jenis: %s', $eq->brand, $eq->model, $eq->tag_id, $eq->asset_type_label)];
                                });
                        })->flatten()->toArray();
                }

                if ($loanTransactionId !== null && $loanTransactionId !== 0) {
                    // FIX: Changed to direct model find instead of service method
                    $this->loanTransaction = LoanTransaction::find($loanTransactionId);

                    if (! $this->loanTransaction || (int) $this->loanTransaction->loan_application_id !== (int) $this->loanApplicationId) {
                        Log::warning(sprintf('Loan Transaction ID %s not found or does not belong to Loan Application ID %s.', $loanTransactionId, $this->loanApplicationId));
                        throw new ModelNotFoundException(sprintf('Loan Transaction ID %s not found for this application.', $loanTransactionId));
                    }

                    Log::debug(sprintf('Loaded Loan Transaction ID %s.', $loanTransactionId));

                    $firstTransactionItem = $this->loanTransaction->loanTransactionItems()->first();
                    if ($firstTransactionItem && $firstTransactionItem->equipment) {
                        $this->selectedEquipmentId = $firstTransactionItem->equipment_id;
                        $this->equipment = $firstTransactionItem->equipment;
                        // Assuming 'isEdit' is a property or logic you might add for editing scenarios
                        $isEdit = false; // Placeholder for edit logic determination
                        if ($this->transactionType === LoanTransaction::TYPE_RETURN) {
                            $this->equipmentConditionOnReturn = $firstTransactionItem->condition_on_return ?? ($this->equipment->condition_status ?? '');
                            $this->returnNotes = $this->loanTransaction->return_notes ?? $firstTransactionItem->item_notes ?? '';
                            $this->accessories = json_decode($firstTransactionItem->accessories_checklist_on_return ?? '[]', true);
                        } elseif ($this->transactionType === LoanTransaction::TYPE_ISSUE) {
                            $this->notes = $this->loanTransaction->issue_notes ?? $firstTransactionItem->item_notes ?? '';
                            $this->accessories = json_decode($firstTransactionItem->accessories_checklist_on_issue ?? '[]', true);
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('Error in EquipmentChecklist mount: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('swal:error', ['message' => 'An error occurred while loading data.']);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.equipment-checklist');
    }

    public function updateQuantity(int $index, int $quantity): void
    {
        if (isset($this->loanApplicationItems[$index])) {
            $this->loanApplicationItems[$index]['quantity_transacted'] = max(0, $quantity); // Ensure non-negative
        }
    }

    public function removeAccessory(string $accessory): void
    {
        $this->accessories = array_values(array_filter($this->accessories, fn ($item) => $item !== $accessory));
    }

    public function addAccessory(): void
    {
        $this->accessories[] = ''; // Add an empty input for a new accessory
    }

    public function updateAccessory(int $index, string $value): void
    {
        if (isset($this->accessories[$index])) {
            $this->accessories[$index] = $value;
        }
    }

    public function saveChecklist(): void
    {
        $this->validate([
            'loanApplicationItems.*.quantity_transacted' => 'required|integer|min:0',
            'accessories.*' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () {
                /** @var LoanTransaction $transaction */
                $transaction = LoanTransaction::find($this->loanTransactionId); // Corrected to direct model find

                if (! $transaction) {
                    throw new Exception('Loan Transaction not found.');
                }

                $transaction->issue_notes = $this->notes;
                $transaction->accessories_checklist_on_issue = array_values(array_filter($this->accessories)); // Clean empty accessories
                $transaction->save();

                foreach ($this->loanApplicationItems as $itemData) {
                    /** @var LoanTransactionItem $transactionItem */
                    $transactionItem = LoanTransactionItem::find($itemData['id']);

                    if ($transactionItem) {
                        $transactionItem->quantity_transacted = $itemData['quantity_transacted'];
                        $transactionItem->notes = $itemData['notes']; // Assuming notes field exists in LoanTransactionItem
                        $transactionItem->save();

                        // Update equipment status based on transaction type
                        if ($transaction->isIssue()) {
                            $transactionItem->equipment->status = Equipment::STATUS_ON_LOAN;
                            $transactionItem->equipment->current_loan_id = $transaction->id;
                            $transactionItem->equipment->save();
                        } elseif ($transaction->isReturn()) {
                            // Logic for return, mark equipment as available or other status
                            $transactionItem->equipment->status = Equipment::STATUS_AVAILABLE;
                            $transactionItem->equipment->current_loan_id = null;
                            $transactionItem->equipment->save();
                        }
                    }
                }

                $this->dispatch('swal:success', ['message' => 'Checklist saved successfully!']);
                $this->dispatch('transaction-updated'); // Notify parent or other components
            });
        } catch (Throwable $e) {
            Log::error('Error saving checklist: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('swal:error', ['message' => 'Failed to save checklist: ' . $e->getMessage()]);
        }
    }

    public function markAsIssued(): void
    {
        $this->validate([
            'loanApplicationItems.*.quantity_transacted' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () {
                /** @var LoanTransaction $transaction */
                $transaction = LoanTransaction::find($this->loanTransactionId); // Corrected to direct model find

                if (! $transaction) {
                    throw new Exception('Loan Transaction not found.');
                }

                $this->loanTransactionService->issueLoanTransaction(
                    $transaction,
                    $this->loanApplicationItems,
                    array_values(array_filter($this->accessories)),
                    $this->notes
                );

                // Update equipment status for all items in this transaction
                foreach ($transaction->loanTransactionItems as $item) {
                    if ($item->equipment) {
                        $item->equipment->status = Equipment::STATUS_ON_LOAN;
                        $item->equipment->current_loan_id = $transaction->id;
                        $item->equipment->save();
                    }
                }

                $this->dispatch('swal:success', ['message' => 'Loan successfully issued!']);
                $this->dispatch('transaction-completed'); // Notify parent for redirect or refresh
            });
        } catch (Throwable $e) {
            Log::error('Error marking as issued: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('swal:error', ['message' => 'Failed to mark as issued: ' . $e->getMessage()]);
        }
    }

    public function markAsReturned(): void
    {
        $this->validate([
            'loanApplicationItems.*.quantity_transacted' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () {
                /** @var LoanTransaction $transaction */
                $transaction = LoanTransaction::find($this->loanTransactionId); // Corrected to direct model find

                if (! $transaction) {
                    throw new Exception('Loan Transaction not found.');
                }

                $this->loanTransactionService->returnLoanTransaction(
                    $transaction,
                    $this->loanApplicationItems,
                    array_values(array_filter($this->accessories)),
                    $this->notes
                );

                // No direct equipment status update here, as the returnLoanTransaction service method should handle it
                // based on inspection outcomes (e.g., returned_good, returned_damaged, etc.).

                $this->dispatch('swal:success', ['message' => 'Loan successfully returned!']);
                $this->dispatch('transaction-completed'); // Notify parent for redirect or refresh
            });
        } catch (Throwable $e) {
            Log::error('Error marking as returned: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('swal:error', ['message' => 'Failed to mark as returned: ' . $e->getMessage()]);
        }
    }
}
