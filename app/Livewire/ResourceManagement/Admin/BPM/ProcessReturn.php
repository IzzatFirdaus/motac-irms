<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    protected function rules(): array
    {
        return [
            'returnItems' => ['required', 'array', function ($attribute, $value, $fail) {
                $selectedCount = collect($value)->where('is_returning', true)->count();
                if ($selectedCount === 0) {
                    $fail(__('Sila pilih sekurang-kurangnya satu item untuk dipulangkan.'));
                }
            }],
            'returnItems.*.condition_on_return' => ['required_if:returnItems.*.is_returning,true', 'nullable', Rule::in(array_keys(Equipment::getConditionStatusesList()))],
            'returnItems.*.return_item_notes' => ['nullable', 'string', 'max:1000'],
            'returning_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
            'return_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'returnItems.*.condition_on_return.required_if' => 'Sila pilih keadaan untuk item yang ditanda.',
            'returning_officer_id.required' => 'Sila pilih pegawai yang memulangkan peralatan.',
            'transaction_date.required' => 'Sila tetapkan tarikh pemulangan.',
        ];
    }

    public function mount(int $issueTransactionId): void
    {
        $this->issueTransaction = LoanTransaction::with(['loanApplication.user', 'loanTransactionItems.equipment'])->findOrFail($issueTransactionId);
        $this->loanApplication = $this->issueTransaction->loanApplication;
        $this->authorize('processReturn', $this->loanApplication);

        $this->conditionOptions = Equipment::getConditionStatusesList();
        $this->users = User::where('status', User::STATUS_ACTIVE)->orderBy('name')->get(['id', 'name', 'profile_photo_path'])->toArray();
        $this->returning_officer_id = $this->loanApplication->user_id;
        $this->transaction_date = now()->format('Y-m-d');

        // Pre-populate items based on the original issuance transaction
        $itemsToReturn = $this->issueTransaction->loanTransactionItems()
            ->where('status', 'issued')
            // EDITED: Eager load only the specific columns needed from the equipment model
            ->with('equipment:id,brand,model,tag_id')->get();

        foreach ($itemsToReturn as $issuedItem) {
            $this->returnItems[] = [
                'is_returning' => true,
                'loan_transaction_item_id' => $issuedItem->id,
                // EDITED: Use the correct 'brand' and 'model' attributes instead of the non-existent 'name'
                'equipment_name' => trim(($issuedItem->equipment->brand ?? '') . ' ' . ($issuedItem->equipment->model ?? '')) . ' (Tag: ' . ($issuedItem->equipment->tag_id ?? 'N/A') . ')',
                'condition_on_return' => Equipment::CONDITION_GOOD,
                'return_item_notes' => '',
            ];
        }
    }

    public function submitReturn(LoanTransactionService $loanTransactionService): void
    {
        $this->authorize('processReturn', $this->loanApplication);
        $validatedData = $this->validate();
        $returnAcceptingOfficer = Auth::user();

        $itemsPayload = collect($validatedData['returnItems'])
            ->where('is_returning', true)
            ->map(function ($item) {
                return [
                    'loan_transaction_item_id' => $item['loan_transaction_item_id'],
                    'condition_on_return' => $item['condition_on_return'],
                    'return_item_notes' => $item['return_item_notes'],
                ];
            })->values()->toArray();

        if (empty($itemsPayload)) {
            $this->addError('returnItems', __('Tiada item dipilih. Sila tandakan sekurang-kurangnya satu item untuk dipulangkan.'));
            return;
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
            $this->redirectRoute('loan-applications.show', ['loan_application' => $this->loanApplication->id], navigate: true);
        } catch (Throwable $e) {
            Log::error('Error in ProcessReturn@submitReturn: ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('Gagal merekodkan pemulangan: ') . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-return')->title(__('Proses Pemulangan Peralatan'));
    }
}
