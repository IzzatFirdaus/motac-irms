<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

/**
 * Livewire component for processing the return of ICT equipment for a given loan application.
 */
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

    /**
     * Validation messages for the return form.
     */
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

    /**
     * Validation rules for the return form.
     */
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

    /**
     * Mount the component, eager load data and set up form defaults.
     */
    public function mount(LoanTransaction $issueTransaction): void
    {
        $this->authorize('processReturn', $issueTransaction);

        $this->issueTransaction = $issueTransaction->load(['loanApplication', 'loanTransactionItems.equipment']);
        $this->loanApplication = $issueTransaction->loanApplication;

        // Initialize returnItems with issued items
        $this->returnItems = $this->issueTransaction->loanTransactionItems
            ->map(function ($item) {
                $isIssued = $item->status === LoanTransaction::STATUS_ISSUED;
                return [
                    'loan_transaction_item_id' => $item->id,
                    'equipment_id' => $item->equipment_id,
                    'tag_id' => $item->equipment->tag_id,
                    'brand' => $item->equipment->brand,
                    'model_name' => $item->equipment->model,
                    'initial_condition' => $item->condition_on_transaction,
                    'is_returning' => $isIssued,
                    'quantity_issued' => $item->quantity_transacted,
                    'quantity_returned_so_far' => $item->quantity_returned,
                    'condition_on_return' => Equipment::CONDITION_GOOD,
                    'return_item_notes' => null,
                ];
            })->values()->toArray();

        $this->conditionOptions = Equipment::getConditionStatusesList();
        $this->users = User::orderBy('name')->pluck('name', 'id')->all();
        $this->returning_officer_id = Auth::id();
        $this->transaction_date = now()->format('Y-m-d');
    }

    /**
     * Handle the submission of the return form.
     */
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
                    'quantity_returned' => 1,
                    'condition_on_return' => $item['condition_on_return'],
                    'return_item_notes' => $item['return_item_notes'],
                ];
            })->values()->toArray();

        if (empty($itemsPayload)) {
            $this->addError('returnItems', __('Tiada item dipilih. Sila tandakan sekurang-kurangnya satu item untuk dipulangkan.'));
            return null;
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

    /**
     * Render the Blade view for this component.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-return');
    }
}
