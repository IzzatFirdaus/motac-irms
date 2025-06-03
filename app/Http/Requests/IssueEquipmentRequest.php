<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class IssueEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        /** @var LoanApplication|null $loanApplication */
        $loanApplication = $this->route('loanApplication');

        if ($loanApplication) {
            // System Design: Suggests LoanApplicationPolicy or LoanTransactionPolicy
            // Prefer a policy like: $user->can('createIssueTransaction', $loanApplication);
            // or $user->can('issueEquipmentFor', $loanApplication);
            // Ensure the policy method exists and is correctly implemented.
            // For now, using a direct role check as a fallback or primary if policy isn't granular enough.
            // 'BPM Staff' is authorized to issue equipment. [cite: 43]
            return $user->hasAnyRole(['Admin', 'BPM Staff']);
        }
        // If no specific loan application context, deny by default or rely on a general permission.
        // This request is typically bound to a specific loan application.
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var LoanApplication|null $loanApplicationFromRoute */
        $loanApplicationFromRoute = $this->route('loanApplication');
        $loanApplicationId = $loanApplicationFromRoute?->id;

        return [
          'transaction_date' => ['required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
          'receiving_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
          'issue_notes' => ['nullable', 'string', 'max:2000'],

          'items' => ['required', 'array', 'min:1'],
          'items.*.loan_application_item_id' => [
            'required',
            'integer',
            Rule::exists('loan_application_items', 'id')->where(function ($query) use ($loanApplicationId) {
                if ($loanApplicationId) {
                    $query->where('loan_application_id', $loanApplicationId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }),
          ],
          'items.*.equipment_id' => [
            'required',
            'integer',
            Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE),
          ],
          'items.*.quantity_issued' => [
            'required',
            'integer',
            'min:1',
            function ($attribute, $value, $fail) use ($loanApplicationFromRoute) {
                $index = explode('.', $attribute)[1];
                $loanAppItemIdInput = $this->input("items.{$index}.loan_application_item_id");

                if (!($loanAppItemIdInput && is_numeric($loanAppItemIdInput))) {
                    return;
                }
                $loanAppItemId = (int) $loanAppItemIdInput;

                /** @var LoanApplicationItem|null $appItem */
                $appItem = LoanApplicationItem::find($loanAppItemId);

                if (!$appItem || ($loanApplicationFromRoute && $appItem->loan_application_id !== $loanApplicationFromRoute->id)) {
                    return;
                }

                $alreadySuccessfullyIssued = (int) LoanTransactionItem::where('loan_application_item_id', $appItem->id)
                  ->whereHas('loanTransaction', function ($q) {
                        $q->where('type', LoanTransaction::TYPE_ISSUE)
                          ->whereIn('status', [
                              LoanTransaction::STATUS_ISSUED,
                              LoanTransaction::STATUS_COMPLETED,
                          ]);
                    })
                  ->sum('quantity_transacted');

                $quantityApprovedForItem = (int) ($appItem->quantity_approved ?? $appItem->quantity_requested);
                $maxAllowedToIssueNow = $quantityApprovedForItem - $alreadySuccessfullyIssued;

                if ((int) $value > $maxAllowedToIssueNow) {
                    $fail(__('Kuantiti untuk dikeluarkan (:value) bagi item #:item_num melebihi baki yang boleh dikeluarkan (:can_issue) daripada kuantiti diluluskan (:approved). Telah dikeluarkan sebelum ini: :already_issued.', [
                      'value' => $value,
                      'item_num' => ((int) $index) + 1,
                      'can_issue' => max(0, $maxAllowedToIssueNow),
                      'approved' => $quantityApprovedForItem,
                      'already_issued' => $alreadySuccessfullyIssued
                    ]));
                }
            },
          ],
          'items.*.issue_item_notes' => ['nullable', 'string', 'max:1000'],
          'items.*.accessories_checklist_item' => ['nullable', 'array'],
          'items.*.accessories_checklist_item.*' => ['nullable','string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
          'transaction_date.required' => __('Tarikh transaksi pengeluaran wajib diisi.'),
          'transaction_date.date_format' => __('Format tarikh transaksi tidak sah. Gunakan Y-m-d H:i:s.'),
          'transaction_date.before_or_equal' => __('Tarikh transaksi tidak boleh melebihi tarikh semasa.'),
          'receiving_officer_id.required' => __('Pegawai Penerima perlu dinyatakan.'),
          'receiving_officer_id.exists' => __('Pegawai Penerima yang dipilih tidak sah.'),
          'issue_notes.max' => __('Nota pengeluaran keseluruhan tidak boleh melebihi :max aksara.'),

          'items.required' => __('Sekurang-kurangnya satu item peralatan mesti dipilih untuk pengeluaran.'),
          'items.min' => __('Sekurang-kurangnya satu item peralatan mesti dipilih untuk pengeluaran.'),
          'items.*.loan_application_item_id.required' => __('Item permohonan asal mesti dipilih untuk item di kedudukan :position.'),
          'items.*.loan_application_item_id.exists' => __('Item permohonan asal tidak sah atau bukan milik permohonan ini untuk item di kedudukan :position.'),
          'items.*.equipment_id.required' => __('Peralatan mesti dipilih untuk item di kedudukan :position.'),
          'items.*.equipment_id.exists' => __('Peralatan yang dipilih tidak sah atau tidak lagi berstatus "Tersedia" untuk item di kedudukan :position.'),
          'items.*.quantity_issued.required' => __('Kuantiti untuk dikeluarkan mesti diisi untuk item di kedudukan :position.'),
          'items.*.quantity_issued.integer' => __('Kuantiti untuk dikeluarkan mesti nombor bulat untuk item di kedudukan :position.'),
          'items.*.quantity_issued.min' => __('Kuantiti untuk dikeluarkan mesti sekurang-kurangnya 1 untuk item di kedudukan :position.'),
          'items.*.issue_item_notes.max' => __('Nota item pengeluaran tidak boleh melebihi :max aksara untuk item di kedudukan :position.'),
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
