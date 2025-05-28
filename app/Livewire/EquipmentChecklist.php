<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse; // Added for return type
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Removed duplicate Illuminate\Http\RedirectResponse if it was already there from your original

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
            if ($this->loanApplicationId) {
                $this->loanApplication = $this->loanApplicationService->findLoanApplication(
                    $this->loanApplicationId,
                    ['user', 'applicationItems.equipment', 'responsibleOfficer', 'loanTransactions.loanTransactionItems.equipment', 'loanTransactions.issuingOfficer', 'loanTransactions.receivingOfficer', 'loanTransactions.returningOfficer', 'loanTransactions.returnAcceptingOfficer']
                );

                if (!$this->loanApplication) {
                    Log::warning("Loan Application ID {$this->loanApplicationId} not found during mount.");
                    throw new ModelNotFoundException("Loan Application ID {$this->loanApplicationId} not found.");
                }
                Log::debug("Loaded Loan Application ID {$this->loanApplicationId}.");

                if ($this->transactionType === LoanTransaction::TYPE_ISSUE) {
                    $this->availableEquipmentOptions = $this->loanApplication->applicationItems
                        ->filter(function ($item) {
                            return ($item->quantity_approved ?? 0) > ($item->quantity_issued ?? 0);
                        })
                        ->mapWithKeys(function ($appItem) {
                            return Equipment::where('asset_type', $appItem->equipment_type)
                                       ->where('status', Equipment::STATUS_AVAILABLE)
                                       ->get()
                                       ->mapWithKeys(function ($eq) {
                                           return [$eq->id => "{$eq->brand} {$eq->model} (Tag: {$eq->tag_id}) - Jenis: {$eq->asset_type_label}"];
                                       });
                        })->flatten()->toArray();
                }

                if ($loanTransactionId) {
                    $this->loanTransaction = $this->loanTransactionService->findTransaction(
                        $loanTransactionId,
                        ['loanTransactionItems.equipment']
                    );

                    if (!$this->loanTransaction || (int) $this->loanTransaction->loan_application_id !== (int) $this->loanApplicationId) {
                        Log::warning("Loan Transaction ID {$loanTransactionId} not found or does not belong to Loan Application ID {$this->loanApplicationId}.");
                        throw new ModelNotFoundException("Loan Transaction ID {$loanTransactionId} not found for this application.");
                    }
                    Log::debug("Loaded Loan Transaction ID {$loanTransactionId}.");

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
                        } elseif ($this->transactionType === LoanTransaction::TYPE_ISSUE && $isEdit) {
                            $this->notes = $this->loanTransaction->issue_notes ?? $firstTransactionItem->item_notes ?? '';
                            $this->accessories = json_decode($firstTransactionItem->accessories_checklist_on_issue ?? '[]', true);
                        }
                    }
                }
            }
            $this->initializeOfficerIds();
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Data tidak dijumpai: ') . $e->getMessage());
            Log::error('EquipmentChecklist: Model not found during mount: ' . $e->getMessage(), ['exception' => $e]);
            return;
        } catch (Exception $e) {
            session()->flash('error', __('Ralat berlaku semasa memuatkan senarai semak: ') . $e->getMessage());
            Log::error('EquipmentChecklist: Error during mount: ' . $e->getMessage(), ['exception' => $e]);
            return;
        }
    }

    public function processTransaction(): ?RedirectResponse // MODIFIED return type
    {
        Log::info('Attempting to process transaction.', [
            'loanApplicationId' => $this->loanApplicationId,
            'selectedEquipmentId' => $this->selectedEquipmentId,
            'transactionType' => $this->transactionType,
        ]);
        $this->validate($this->getValidationRulesForTransactionType());

        DB::beginTransaction();
        try {
            $currentUser = Auth::user();
            if (!$currentUser) {
                throw new Exception('Pengguna tidak dikenalpasti.');
            }

            if (!$this->loanApplication) {
                $this->loanApplication = $this->loanApplicationService->findLoanApplication($this->loanApplicationId);
                if (!$this->loanApplication) {
                    throw new ModelNotFoundException("Permohonan Pinjaman dengan ID {$this->loanApplicationId} tidak ditemui.");
                }
            }

            $equipmentToTransact = Equipment::find($this->selectedEquipmentId);
            if (!$equipmentToTransact) {
                throw new ModelNotFoundException("Peralatan dengan ID {$this->selectedEquipmentId} tidak ditemui.");
            }

            $applicationItem = $this->loanApplication->applicationItems()
                ->where('equipment_type', $equipmentToTransact->asset_type)
                ->first();

            if ($this->transactionType === LoanTransaction::TYPE_ISSUE) {
                $this->authorize('createIssue', [LoanTransaction::class, $this->loanApplication]);
                if ($equipmentToTransact->status !== Equipment::STATUS_AVAILABLE) {
                    throw new Exception("Peralatan '{$equipmentToTransact->tag_id}' tidak tersedia untuk pengeluaran. Status semasa: {$equipmentToTransact->status_label}");
                }

                $itemData = [[
                    'equipment_id' => $this->selectedEquipmentId,
                    'quantity_transacted' => 1,
                    'accessories_checklist_issue' => json_encode($this->accessories),
                    'item_notes' => $this->notes,
                    'loan_application_item_id' => $applicationItem?->id,
                ]];
                $extraDetails = [
                    'issuing_officer_id' => $this->officerId,
                    'receiving_officer_id' => $this->receivingOfficerId,
                    'issue_notes' => $this->notes,
                    'status' => LoanTransaction::STATUS_ISSUED,
                ];
                $transaction = $this->loanTransactionService->createTransaction(
                    $this->loanApplication,
                    LoanTransaction::TYPE_ISSUE,
                    $currentUser,
                    $itemData,
                    $extraDetails
                );
                session()->flash('success', 'Peralatan berjaya dikeluarkan.');

            } elseif ($this->transactionType === LoanTransaction::TYPE_RETURN) {
                $this->authorize('createReturn', [LoanTransaction::class, $this->loanApplication]);
                if ($equipmentToTransact->status !== Equipment::STATUS_ON_LOAN) {
                    throw new Exception("Peralatan '{$equipmentToTransact->tag_id}' tidak dalam status dipinjam. Status semasa: {$equipmentToTransact->status_label}");
                }

                $itemData = [[
                    'equipment_id' => $this->selectedEquipmentId,
                    'quantity_transacted' => 1,
                    'condition_on_return' => $this->equipmentConditionOnReturn,
                    'accessories_checklist_on_return' => json_encode($this->accessories),
                    'item_notes' => $this->returnNotes,
                    'loan_application_item_id' => $applicationItem?->id,
                ]];
                $extraDetails = [
                    'returning_officer_id' => $this->returningOfficerId,
                    'return_accepting_officer_id' => $this->returnAcceptingOfficerId,
                    'return_notes' => $this->returnNotes,
                    'status' => LoanTransaction::STATUS_RETURNED_GOOD,
                    'related_transaction_id' => $this->loanTransaction?->id,
                ];
                $transaction = $this->loanTransactionService->createTransaction(
                    $this->loanApplication,
                    LoanTransaction::TYPE_RETURN,
                    $currentUser,
                    $itemData,
                    $extraDetails
                );
                session()->flash('success', 'Peralatan berjaya dipulangkan.');
            } else {
                session()->flash('error', 'Jenis transaksi tidak sah.');
                DB::rollBack();
                return null; // MODIFIED: Explicitly return null for void path or if no redirect
            }
            DB::commit();
            $this->dispatch('toastr', type: 'success', message: session('success'));
            return redirect()->route('resource-management.my-applications.loan-applications.show', $this->loanApplicationId); // This is line 259 from original error

        } catch (ValidationException $e) {
            DB::rollBack();
            $this->dispatch('toastr', type: 'error', message: __('Sila perbetulkan ralat pada borang.'));
            throw $e;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            session()->flash('error', __('Data tidak ditemui: ') . $e->getMessage());
            $this->dispatch('toastr', type: 'error', message: __('Data tidak ditemui: ') . $e->getMessage());
        } catch (AuthorizationException $e) { // Assuming this is Illuminate\Auth\Access\AuthorizationException
            DB::rollBack();
            session()->flash('error', __('Anda tidak dibenarkan untuk tindakan ini: ') . $e->getMessage());
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk tindakan ini.'));
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', __('Ralat sistem semasa memproses transaksi: ') . $e->getMessage());
            Log::error('Error processing transaction: '.$e->getMessage(), ['exception' => $e]);
            $this->dispatch('toastr', type: 'error', message: __('Ralat sistem. Sila cuba lagi.'));
        }
        return null; // MODIFIED: Ensure a return path if an exception is caught and handled
    }

    public function resetForm(): void
    {
        $this->reset([
            'selectedEquipmentId', 'accessories', 'notes',
            'equipmentConditionOnReturn', 'returnNotes',
        ]);
        $this->accessories = [];
        Log::debug('Form properties reset.');
    }

    #[Computed]
    public function isIssue(): bool // Corrected to match property access pattern
    {
        return $this->transactionType === LoanTransaction::TYPE_ISSUE;
    }

    #[Computed]
    public function isReturn(): bool // Corrected to match property access pattern
    {
        return $this->transactionType === LoanTransaction::TYPE_RETURN;
    }

    #[Computed]
    public function isViewingOnly(): bool // Corrected to match property access pattern
    {
        return $this->transactionType === 'view' || (!$this->isIssue() && !$this->isReturn()); // Call as method here for internal logic
    }

    #[Computed]
    public function onLoanEquipment() // MODIFIED: Renamed method
    {
        if ($this->loanApplication && $this->transactionType === LoanTransaction::TYPE_RETURN) {
            return Equipment::whereHas('loanTransactionItems.loanTransaction', function ($query) {
                $query->where('loan_application_id', $this->loanApplication->id)
                      ->where('type', LoanTransaction::TYPE_ISSUE)
                      ->where('status', LoanTransaction::STATUS_ISSUED);
            })
            ->where('status', Equipment::STATUS_ON_LOAN)
            ->get();
        }
        return collect();
    }

    public function render()
    {
        // Using the optional refactor for render method
        $officerOptions = User::role(['Admin', 'BPM Staff', 'IT Admin'])->orderBy('name')->pluck('name', 'id')->toArray();
        $returningOfficerOptions = $this->loanApplication?->user ? [$this->loanApplication->user->id => $this->loanApplication->user->name] : [];
        $conditionStatusOptions = Equipment::getConditionStatusesList();
        $onLoanEquipmentOptions = $this->onLoanEquipment()->mapWithKeys(function ($eq) { // Call as method
            return [$eq->id => "{$eq->brand} {$eq->model} (Tag: {$eq->tag_id})"];
        })->toArray();

        // Public properties like $this->loanApplication, $this->equipment etc.
        // and computed properties like $this->isIssue, $this->isReturn are automatically available.

        return view('livewire.equipment-checklist', [
            'officerOptions' => $officerOptions,
            'returningOfficerOptions' => $returningOfficerOptions,
            'conditionStatusOptions' => $conditionStatusOptions,
            'onLoanEquipmentOptions' => $onLoanEquipmentOptions,
        ]);
    }

    protected function initializeOfficerIds(): void
    {
        $user = Auth::user();
        if ($user) {
            if ($user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin'])) {
                $this->officerId = $user->id;
                $this->returnAcceptingOfficerId = $user->id;
            }
            if ($this->loanApplication && $this->loanApplication->user) {
                $this->receivingOfficerId = $this->loanApplication->user_id;
                $this->returningOfficerId = $this->loanApplication->user_id;
            }
        }
    }

    protected function getValidationRulesForTransactionType(): array
    {
        switch ($this->transactionType) {
            case LoanTransaction::TYPE_ISSUE:
                return [
                    'loanApplicationId' => 'required|exists:loan_applications,id',
                    'selectedEquipmentId' => 'required|exists:equipment,id',
                    'officerId' => 'required|exists:users,id',
                    'receivingOfficerId' => 'required|exists:users,id',
                    'accessories' => 'nullable|array',
                    'accessories.*' => 'string',
                    'notes' => 'nullable|string|max:1000',
                ];
            case LoanTransaction::TYPE_RETURN:
                return [
                    'loanApplicationId' => 'required|exists:loan_applications,id',
                    'selectedEquipmentId' => 'required|exists:equipment,id',
                    'returningOfficerId' => 'required|exists:users,id',
                    'returnAcceptingOfficerId' => 'required|exists:users,id',
                    'equipmentConditionOnReturn' => ['required', 'string', Rule::in(array_keys(Equipment::getConditionStatusesList()))],
                    'returnNotes' => 'nullable|string|max:1000',
                    'accessories' => 'nullable|array',
                    'accessories.*' => 'string',
                ];
            case 'view':
                return [];
            default:
                Log::warning('EquipmentChecklist: Invalid transaction type for validation.', ['type' => $this->transactionType]);
                return [];
        }
    }
}
