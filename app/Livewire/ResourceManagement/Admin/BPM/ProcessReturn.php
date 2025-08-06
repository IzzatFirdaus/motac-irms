<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse; // Add this line
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

class ProcessReturn extends Component
{
    use AuthorizesRequests;

    public LoanTransaction $issueTransaction;

    public LoanApplication $loanApplication;

    public array $returnItems = [];

    public $returning_officer_id;

    public $transaction_date;

    public string $return_notes = '';

    public array $conditionOptions = [];

    public array $users = [];

    protected function messages(): array
    {
        return [
            'returnItems.required' => __('Sila pilih sekurang-kurangnya satu item untuk dipulangkan.'),
            'returnItems.*.condition_on_return.required_if' => __('Sila pilih status keadaan untuk setiap item yang dipulangkan.'),
            'returnItems.*.equipment_id.distinct' => __('Peralatan yang sama (Tag ID) tidak boleh dipilih lebih dari sekali.'),
            'returning_officer_id.required' => __('Sila pilih pegawai yang menerima pemulangan.'),
            'transaction_date.required' => __('Sila tetapkan tarikh pemulangan.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'returnItems' => ['required', 'array', function ($attribute, $value, $fail): void {
                $selectedCount = collect($value)->where('is_returning', true)->count();
                if ($selectedCount === 0) {
                    $fail(__('Sila pilih sekurang-kurangnya satu item untuk dipulangkan.'));
                }
            }],
            'returnItems.*.condition_on_return' => ['required_if:returnItems.*.is_returning,true', 'nullable', Rule::in(array_keys(Equipment::getConditionStatusesList()))],
            'returnItems.*.return_item_notes' => ['nullable', 'string', 'max:1000'],
            'returning_officer_id' => ['required', 'exists:users,id'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function mount(LoanTransaction $issueTransaction): void
    {
        $this->authorize('processReturn', $issueTransaction);

        $this->issueTransaction = $issueTransaction->load(['loanApplication', 'loanTransactionItems.equipment']);
        $this->loanApplication = $issueTransaction->loanApplication;

        // Initialize returnItems with data from the issue transaction
        $this->returnItems = $this->issueTransaction->loanTransactionItems
            ->map(function ($item) {
                // Ensure the status used here exists as a constant in LoanTransaction
                // Corrected: STATUS_ITEM_ISSUED to STATUS_ISSUED
                $isIssued = $item->status === LoanTransaction::STATUS_ISSUED; // Corrected line

                return [
                    'loan_transaction_item_id' => $item->id,
                    'equipment_id' => $item->equipment_id,
                    'tag_id' => $item->equipment->tag_id,
                    'brand' => $item->equipment->brand,
                    'model_name' => $item->equipment->model, // Assuming model field on Equipment
                    'initial_condition' => $item->condition_on_transaction,
                    'is_returning' => $isIssued, // Default to true if it was issued
                    'quantity_issued' => $item->quantity_transacted,
                    'quantity_returned_so_far' => $item->quantity_returned, // Assuming this is tracked
                    'condition_on_return' => Equipment::CONDITION_GOOD, // Default for form
                    'return_item_notes' => null,
                ];
            })->values()->toArray();


        $this->conditionOptions = Equipment::getConditionStatusesList();
        $this->users = User::orderBy('name')->pluck('name', 'id')->all();
        $this->returning_officer_id = Auth::id(); // Default to current user
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function submitReturn(LoanTransactionService $loanTransactionService): ?RedirectResponse
    {
        $validatedData = $this->validate();

        $returnAcceptingOfficer = User::findOrFail($validatedData['returning_officer_id']);

        $itemsPayload = collect($validatedData['returnItems'])
            ->filter(fn ($item) => $item['is_returning'])
            ->map(function ($item) {
                return [
                    'loan_transaction_item_id' => $item['loan_transaction_item_id'],
                    'equipment_id' => $item['equipment_id'],
                    'quantity_returned' => 1, // Assuming 1-to-1 return for now
                    'condition_on_return' => $item['condition_on_return'],
                    'return_item_notes' => $item['return_item_notes'],
                ];
            })->values()->toArray();

        if (empty($itemsPayload)) {
            $this->addError('returnItems', __('Tiada item dipilih. Sila tandakan sekurang-kurangnya satu item untuk dipulangkan.'));

            return null; // Return null if validation fails, instead of RedirectResponse
        }

        $transactionDetails = [
            'returning_officer_id' => $validatedData['returning_officer_id'],
            'transaction_date' => $validatedData['transaction_date'],
            'return_notes' => $validatedData['return_notes'],
        ];

        try {
            $loanTransactionService->processExistingReturn(
                $this->issueTransaction,
                $itemsPayload,
                $returnAcceptingOfficer,
                $transactionDetails
            );
            session()->flash('success', 'Rekod pemulangan peralatan telah berjaya disimpan.');

            return $this->redirectRoute('loan-applications.show', ['loan_application' => $this->loanApplication->id], navigate: true);

        } catch (Throwable $throwable) {
            Log::error('Error in ProcessReturn@submitReturn: ' . $throwable->getMessage(), ['exception' => $throwable]);
            session()->flash('error', __('Gagal merekodkan pemulangan: ') . $throwable->getMessage());
        }

        return null;
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-return');
    }
}
