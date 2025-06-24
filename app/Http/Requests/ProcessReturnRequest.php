<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class ProcessReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        /** @var LoanTransaction|null $issueTransaction */
        // The route parameter name for the issue transaction is 'loanTransaction'
        // as seen in routes/web.php and LoanTransactionController methods.
        $issueTransaction = $this->route('loanTransaction');

        if ($issueTransaction instanceof LoanTransaction && $issueTransaction->type === LoanTransaction::TYPE_ISSUE) {
            // Use the 'processReturn' policy defined in LoanTransactionPolicy
            // The policy expects the issue transaction and its loan application.
            //
            return $user->can('processReturn', [$issueTransaction, $issueTransaction->loanApplication]);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var LoanTransaction|null $issueTransactionFromRoute */
        $issueTransactionFromRoute = $this->route('loanTransaction');
        $issueTransactionId = $issueTransactionFromRoute?->id;

        return [
            // Overall transaction details
            // transaction_date is now part of the 'items' structure for more flexibility,
            // or can be a single field. Assuming a single transaction_date for the whole return operation.
            'transaction_date' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
            'returning_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'return_notes' => ['nullable', 'string', 'max:2000'], // Overall notes for the return transaction

            // Item details
            'items' => ['required', 'array', 'min:1'],
            // For each item in the 'items' array, keyed by original_loan_transaction_item_id
            'items.*.loan_transaction_item_id' => [ // This key refers to the original issued LoanTransactionItem ID
                'required',
                'integer',
                Rule::exists('loan_transaction_items', 'id')->where(function ($query) use ($issueTransactionId): void {
                    if ($issueTransactionId) {
                        // Ensure the item ID belongs to the specific issue transaction being processed
                        $query->where('loan_transaction_id', $issueTransactionId)
                            ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED); // Only issued items can be returned
                    } else {
                        // Fail validation if no issue transaction context (should be caught by route model binding)
                        $query->whereRaw('1 = 0');
                    }
                }),
            ],
            'items.*.quantity_returned' => [
                'required',
                'integer',
                'min:1',
                // Custom rule to check against originally issued quantity and already returned for THAT item
                function ($attribute, $value, $fail) use ($issueTransactionFromRoute): void {
                    // $attribute is like 'items.123.quantity_returned' where 123 is original_tx_item_id
                    $parts = explode('.', $attribute);
                    $originalIssuedItemId = $parts[1] ?? null; // This is the key of the item in the items array

                    if ($originalIssuedItemId === null || $originalIssuedItemId === '' || $originalIssuedItemId === '0' || ! is_numeric($originalIssuedItemId)) {
                        return; // Other rules should catch invalid item ID
                    }

                    /** @var LoanTransactionItem|null $originalIssuedItem */
                    $originalIssuedItem = LoanTransactionItem::find((int) $originalIssuedItemId);

                    if (! $originalIssuedItem || ! $issueTransactionFromRoute || $originalIssuedItem->loan_transaction_id !== $issueTransactionFromRoute->id) {
                        return; // Not a valid item from this issue transaction
                    }

                    // This validation assumes quantity_transacted on original item is what was issued.
                    // And assumes we are not double-returning an item that was already part of another return record for this issue.
                    // A more robust check would look at all *other* return transactions related to this issue transaction
                    // and sum up quantities already returned for this specific original_loan_transaction_item_id.
                    // For simplicity here, we check against the issued item's quantity.
                    // The service layer (processExistingReturn) should have the ultimate responsibility for preventing over-return.
                    if ((int) $value > $originalIssuedItem->quantity_transacted) {
                        $fail(__('Kuantiti pemulangan (:value) untuk item rujukan #:original_item_id melebihi kuantiti asal dikeluarkan (:issued_qty).', [
                            'value' => $value,
                            'original_item_id' => $originalIssuedItemId,
                            'issued_qty' => $originalIssuedItem->quantity_transacted,
                        ]));
                    }
                },
            ],
            'items.*.condition_on_return' => ['required', 'string', Rule::in(Equipment::getConditionStatusesList())], //
            // item_status_on_return will be derived by the service or ProcessReturn Livewire component.
            // If you want to validate it here, ensure it's submitted and add:
            // 'items.*.item_status_on_return' => ['required', 'string', Rule::in(LoanTransactionItem::getReturnApplicableStatuses())],
            'items.*.return_item_notes' => ['nullable', 'string', 'max:1000'],
            'items.*.accessories_checklist_item' => ['nullable', 'array'],
            'items.*.accessories_checklist_item.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_date.required' => __('Tarikh transaksi pemulangan wajib diisi.'),
            'returning_officer_id.required' => __('Pegawai Yang Memulangkan perlu dinyatakan.'),
            'items.required' => __('Sila pilih sekurang-kurangnya satu item peralatan untuk dipulangkan.'),
            'items.min' => __('Sila pilih sekurang-kurangnya satu item peralatan untuk dipulangkan.'),

            'items.*.loan_transaction_item_id.required' => __('ID item rujukan asal mesti ada untuk setiap item yang dipulangkan.'),
            'items.*.loan_transaction_item_id.exists' => __('ID item rujukan asal tidak sah atau bukan milik transaksi pengeluaran ini.'),
            'items.*.quantity_returned.required' => __('Kuantiti pemulangan mesti diisi.'),
            'items.*.quantity_returned.integer' => __('Kuantiti pemulangan mesti nombor bulat.'),
            'items.*.quantity_returned.min' => __('Kuantiti pemulangan mesti sekurang-kurangnya 1.'),
            'items.*.condition_on_return.required' => __('Keadaan semasa pemulangan mesti dinyatakan.'),
            'items.*.condition_on_return.in' => __('Keadaan semasa pemulangan yang dipilih tidak sah.'),
            // Add messages for item_status_on_return if validated
            'items.*.return_item_notes.max' => __('Catatan item tidak boleh melebihi :max aksara.'),
            'items.*.accessories_checklist_item.array' => __('Senarai aksesori item mesti dalam format yang betul.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('transaction_date') && ! $this->filled('transaction_date')) {
            $this->merge([
                'transaction_date' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        if ($this->input('items') === null && ! $this->has('items')) {
            $this->merge(['items' => []]);
        }
    }
}
