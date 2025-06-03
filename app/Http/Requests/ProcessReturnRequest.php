<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProcessReturnRequest extends FormRequest
{
  public function authorize(): bool
  {
    /** @var User|null $user */
    $user = $this->user();
    if (!$user) {
      return false;
    }

    /** @var LoanTransaction|null $issueTransaction */
    $issueTransaction = $this->route('loanTransaction'); // Original ISSUE transaction

    if ($issueTransaction && $issueTransaction->type === LoanTransaction::TYPE_ISSUE) {
      // System Design: Prefer policy check, e.g., $user->can('createReturnFor', $issueTransaction->loanApplication);
      // or $user->can('processReturn', $issueTransaction) in LoanTransactionPolicy.
      // BPM Staff processes returns. [cite: 43]
      return $user->hasAnyRole(['Admin', 'BPM Staff']);
    }
    return false;
  }

  public function rules(): array
  {
    /** @var LoanTransaction|null $issueTransactionFromRoute */
    $issueTransactionFromRoute = $this->route('loanTransaction');
    $issueTransactionId = $issueTransactionFromRoute?->id;

    return [
      'transaction_date' => ['required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
      'returning_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
      'return_accepting_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
      'return_notes' => ['nullable', 'string', 'max:2000'],

      'items' => ['required', 'array', 'min:1'],
      'items.*.loan_transaction_item_id' => [
        'required',
        'integer',
        Rule::exists('loan_transaction_items', 'id')->where(function ($query) use ($issueTransactionId) {
          if ($issueTransactionId) {
            $query->where('loan_transaction_id', $issueTransactionId);
          } else {
            $query->whereRaw('1 = 0');
          }
        }),
      ],
      'items.*.quantity_returned' => [
        'required',
        'integer',
        'min:1',
        function ($attribute, $value, $fail) use ($issueTransactionFromRoute) {
            $index = explode('.', $attribute)[1];
            $issuedTxItemIdInput = $this->input("items.{$index}.loan_transaction_item_id");

            if (!($issuedTxItemIdInput && is_numeric($issuedTxItemIdInput))) {
                return;
            }
            $issuedTxItemId = (int) $issuedTxItemIdInput;

            /** @var LoanTransactionItem|null $issuedItem */
            $issuedItem = LoanTransactionItem::find($issuedTxItemId);

            if (!$issuedItem || !$issueTransactionFromRoute || $issuedItem->loan_transaction_id !== $issueTransactionFromRoute->id) {
                return;
            }

            $alreadyReturnedForThisIssuedItem = (int) LoanTransactionItem::where('loan_application_item_id', $issuedItem->loan_application_item_id)
                ->where('equipment_id', $issuedItem->equipment_id)
                ->whereHas('loanTransaction', function ($q) use ($issueTransactionFromRoute) {
                    $q->where('type', LoanTransaction::TYPE_RETURN)
                      ->where('related_transaction_id', $issueTransactionFromRoute->id);
                })
                ->sum('quantity_transacted');

            $maxCanReturnNowForThisItem = $issuedItem->quantity_transacted - $alreadyReturnedForThisIssuedItem;

            if ((int) $value > $maxCanReturnNowForThisItem) {
                $fail(__('Kuantiti pemulangan (:value) untuk item #:item_num (Peralatan ID: :equip_id) melebihi baki yang boleh dipulangkan (:can_return). Telah dipulangkan sebelum ini: :already_returned.', [
                  'value' => $value,
                  'item_num' => ((int) $index) + 1,
                  'equip_id' => $issuedItem->equipment_id,
                  'can_return' => max(0, $maxCanReturnNowForThisItem),
                  'already_returned' => $alreadyReturnedForThisIssuedItem
                ]));
            }
        },
      ],
      'items.*.condition_on_return' => ['required', 'string', Rule::in(array_keys(Equipment::getConditionStatusOptions()))], //
      'items.*.item_status_on_return' => ['required', 'string', Rule::in(LoanTransactionItem::getReturnApplicableStatuses())], //
      'items.*.return_item_notes' => ['nullable', 'string', 'max:1000'],
      'items.*.accessories_checklist_item' => ['nullable', 'array'],
      'items.*.accessories_checklist_item.*' => ['nullable', 'string', 'max:255'],
    ];
  }

  public function messages(): array
  {
    return [
      'transaction_date.required' => __('Tarikh transaksi pemulangan wajib diisi.'),
      'transaction_date.date_format' => __('Format tarikh transaksi tidak sah. Gunakan Y-m-d H:i:s.'),
      'transaction_date.before_or_equal' => __('Tarikh transaksi tidak boleh melebihi tarikh semasa.'),
      'returning_officer_id.required' => __('Pegawai Yang Memulangkan perlu dinyatakan.'),
      'returning_officer_id.exists' => __('Pegawai Yang Memulangkan yang dipilih tidak sah.'),
      'return_accepting_officer_id.required' => __('Pegawai Penerima Pulangan (BPM) perlu dinyatakan.'),
      'return_accepting_officer_id.exists' => __('Pegawai Penerima Pulangan (BPM) yang dipilih tidak sah.'),
      'return_notes.max' => __('Nota pemulangan keseluruhan tidak boleh melebihi :max aksara.'),

      'items.required' => __('Sekurang-kurangnya satu item peralatan mesti dinyatakan untuk pemulangan.'),
      'items.min' => __('Sekurang-kurangnya satu item peralatan mesti dinyatakan untuk pemulangan.'),
      'items.*.loan_transaction_item_id.required' => __('Rujukan item pinjaman asal mesti dipilih untuk item di kedudukan :position.'),
      'items.*.loan_transaction_item_id.exists' => __('Rujukan item pinjaman asal tidak sah atau bukan milik transaksi pengeluaran ini untuk item di kedudukan :position.'),
      'items.*.quantity_returned.required' => __('Kuantiti pemulangan mesti diisi untuk item di kedudukan :position.'),
      'items.*.quantity_returned.integer' => __('Kuantiti pemulangan mesti nombor bulat untuk item di kedudukan :position.'),
      'items.*.quantity_returned.min' => __('Kuantiti pemulangan mesti sekurang-kurangnya 1 untuk item di kedudukan :position.'),
      'items.*.condition_on_return.required' => __('Keadaan peralatan semasa pemulangan perlu dinyatakan untuk item di kedudukan :position.'),
      'items.*.condition_on_return.in' => __('Keadaan peralatan yang dinyatakan tidak sah untuk item di kedudukan :position.'),
      'items.*.item_status_on_return.required' => __('Status pemulangan item (cth: baik, rosak) mesti dinyatakan untuk item di kedudukan :position.'),
      'items.*.item_status_on_return.in' => __('Status pemulangan item tidak sah untuk item di kedudukan :position.'),
      'items.*.return_item_notes.max' => __('Nota item pemulangan tidak boleh melebihi :max aksara untuk item di kedudukan :position.'),
      'items.*.accessories_checklist_item.*.max' => __('Setiap nama aksesori tidak boleh melebihi :max aksara untuk item di kedudukan :position.'),
    ];
  }

  protected function prepareForValidation(): void
  {
    if (!$this->has('transaction_date')) {
      $this->merge([
        'transaction_date' => now()->format('Y-m-d H:i:s'),
      ]);
    }
    if ($this->input('items') === null && !$this->has('items')) {
        $this->merge(['items' => []]);
    }
  }
}
