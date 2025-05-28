<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class ProcessReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        /** @var LoanTransaction|null $issueTransaction */
        $issueTransaction = $this->route('loanTransaction'); // This is the original issue transaction

        if ($issueTransaction && $issueTransaction->type === LoanTransaction::TYPE_ISSUE) {
            // User must have permission to create a return transaction against this issue.
            // Or specifically for the loan application.
            // Example: return $user->can('createReturnTransaction', $issueTransaction->loanApplication);
            return $user->hasAnyRole(['Admin', 'BPMStaff']); // Simplified role check
        }
        return false; // Cannot process return if issue transaction is not valid
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var LoanTransaction|null $issueTransactionFromRoute */
        $issueTransactionFromRoute = $this->route('loanTransaction');
        $issueTransactionId = $issueTransactionFromRoute?->id;

        return [
            'transaction_date' => ['required', 'date_format:Y-m-d H:i:s'], // Or just 'date'
            'returning_officer_id' => ['required', 'integer', Rule::exists('users', 'id')], // User physically returning
            'return_accepting_officer_id' => ['required', 'integer', Rule::exists('users', 'id')], // BPM Staff accepting
            'return_notes' => ['nullable', 'string', 'max:2000'], // Overall transaction notes

            'items' => ['required', 'array', 'min:1'],
            'items.*.loan_transaction_item_id' => [ // This is the ID of the item from the original ISSUE transaction
                'required', 'integer',
                Rule::exists('loan_transaction_items', 'id')->where(function ($query) use ($issueTransactionId) {
                    if ($issueTransactionId) {
                        $query->where('loan_transaction_id', $issueTransactionId);
                    } else {
                        $query->whereRaw('1 = 0'); // Fail validation if issue transaction not resolved
                    }
                }),
            ],
            'items.*.quantity_returned' => [ // This is quantity_transacted for the RETURN LoanTransactionItem
                'required', 'integer', 'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $issuedItemIdInput = $this->input("items.{$index}.loan_transaction_item_id");

                    if (!($issuedItemIdInput && is_numeric($issuedItemIdInput))) {
                        return; // Other validation handles this
                    }
                    $issuedTxItemId = (int) $issuedItemIdInput;
                    /** @var LoanTransactionItem|null $issuedItem */
                    $issuedItem = LoanTransactionItem::find($issuedTxItemId);

                    if (!$issuedItem) {
                        // $fail message here is fine, but exists rule on loan_transaction_item_id should catch it
                        return;
                    }

                    // Calculate how much of THIS specific issued item has already been returned in OTHER transactions
                    $alreadyReturnedForThisSpecificIssuedItem = LoanTransactionItem::where('loan_application_item_id', $issuedItem->loan_application_item_id)
                        ->where('equipment_id', $issuedItem->equipment_id) // Ensuring it's the same piece of equipment
                        ->whereHas('loanTransaction', function ($q) use ($issuedItem) {
                            $q->where('type', LoanTransaction::TYPE_RETURN)
                              ->where('related_transaction_id', $issuedItem->loan_transaction_id); // Returns linked to this specific issue transaction item's parent transaction
                        })
                        ->sum('quantity_transacted');

                    $maxCanReturnNowForThisItem = $issuedItem->quantity_transacted - $alreadyReturnedForThisSpecificIssuedItem;


                    if ((int) $value > $maxCanReturnNowForThisItem) {
                        $fail(__('Kuantiti pemulangan (:value) untuk item #:item_num melebihi baki yang boleh dipulangkan (:can_return).', [
                            'value' => $value,
                            'item_num' => ((int) $index) + 1,
                            'can_return' => $maxCanReturnNowForThisItem < 0 ? 0 : $maxCanReturnNowForThisItem,
                        ]));
                    }
                },
            ],
            'items.*.condition_on_return' => ['required', 'string', Rule::in(array_keys(Equipment::getConditionStatusOptions()))], // Must be a valid key from Equipment model
            'items.*.item_status_on_return' => ['required', 'string', Rule::in(LoanTransactionItem::$RETURN_APPLICABLE_STATUSES)], // From LoanTransactionItem model
            'items.*.return_item_notes' => ['nullable', 'string', 'max:1000'], // Notes for this specific returned item
            'items.*.accessories_checklist_item' => ['nullable', 'array'], // Accessories for this specific item
            'items.*.accessories_checklist_item.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_date.required' => __('Tarikh transaksi pemulangan wajib diisi.'),
            'returning_officer_id.required' => __('Pegawai Yang Memulangkan perlu dinyatakan.'),
            'return_accepting_officer_id.required' => __('Pegawai Penerima Pulangan (BPM) perlu dinyatakan.'),
            'items.required' => __('Sekurang-kurangnya satu item peralatan mesti dinyatakan untuk pemulangan.'),
            'items.*.loan_transaction_item_id.required' => __('Rujukan item pinjaman asal mesti dipilih untuk item #:position.'),
            'items.*.loan_transaction_item_id.exists' => __('Rujukan item pinjaman asal tidak sah atau bukan milik transaksi pengeluaran ini untuk item #:position.'),
            'items.*.quantity_returned.required' => __('Kuantiti pemulangan mesti diisi untuk item #:position.'),
            'items.*.quantity_returned.min' => __('Kuantiti pemulangan mesti sekurang-kurangnya 1 untuk item #:position.'),
            'items.*.condition_on_return.required' => __('Keadaan peralatan semasa pemulangan perlu dinyatakan untuk item #:position.'),
            'items.*.condition_on_return.in' => __('Keadaan peralatan yang dinyatakan tidak sah untuk item #:position.'),
            'items.*.item_status_on_return.required' => __('Status pemulangan item (cth: baik, rosak) mesti dinyatakan untuk item #:position.'),
            'items.*.item_status_on_return.in' => __('Status pemulangan item tidak sah untuk item #:position.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (!$this->has('transaction_date')) {
            $this->merge([
                'transaction_date' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
