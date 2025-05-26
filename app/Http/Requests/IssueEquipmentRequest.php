<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction; // Added for policy check example
use App\Models\LoanTransactionItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class IssueEquipmentRequest extends FormRequest
{
  public function authorize(): bool
  {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    if (!$user) return false;

    /** @var LoanApplication|null $loanApplication */
    $loanApplication = $this->route('loanApplication'); // Assuming route model binding

    // Prefer policy-based authorization
    if ($loanApplication) {
      return $user->can('createIssueTransaction', $loanApplication); // Assumes LoanApplicationPolicy method
    }
    // Fallback role check if policy isn't specific enough or LA not resolved yet
    return $user->hasAnyRole(['Admin', 'BPMStaff']);
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
      'transaction_date' => ['required', 'date_format:Y-m-d H:i:s'], // Added for consistency
      'receiving_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
      'issue_notes' => ['nullable', 'string', 'max:2000'], // Overall transaction notes
      // 'accessories_overall' => ['nullable', 'array'], // If overall checklist, otherwise per item

      'items' => ['required', 'array', 'min:1'],
      'items.*.loan_application_item_id' => [
        'required',
        'integer',
        Rule::exists('loan_application_items', 'id')->where(function ($query) use ($loanApplicationId) {
          if ($loanApplicationId) {
            $query->where('loan_application_id', $loanApplicationId);
          } else {
            // This case should ideally not happen if route model binding for loanApplication is enforced
            $query->whereRaw('1 = 0'); // Fail validation if loanApplicationId is not available
          }
        }),
      ],
      'items.*.equipment_id' => [
        'required',
        'integer',
        Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE), // Must be available
      ],
      'items.*.quantity_issued' => [ // This is quantity_transacted for LoanTransactionItem
        'required',
        'integer',
        'min:1',
        function ($attribute, $value, $fail) use ($loanApplicationFromRoute) {
          $index = explode('.', $attribute)[1];
          $loanAppItemIdInput = $this->input("items.{$index}.loan_application_item_id");

          if (!($loanAppItemIdInput && is_numeric($loanAppItemIdInput))) {
            return; // Other validation will catch this
          }
          $loanAppItemId = (int) $loanAppItemIdInput;
          /** @var LoanApplicationItem|null $appItem */
          $appItem = LoanApplicationItem::find($loanAppItemId);

          if (!$appItem || ($loanApplicationFromRoute && $appItem->loan_application_id !== $loanApplicationFromRoute->id)) {
            // $fail message here is fine, but exists rule should catch invalid appItem ID
            return;
          }

          $alreadyIssuedForThisAppItem = LoanTransactionItem::where('loan_application_item_id', $appItem->id)
            ->whereHas('loanTransaction', fn($q) => $q->where('type', LoanTransaction::TYPE_ISSUE)
              ->whereIn('status', [LoanTransaction::STATUS_ISSUED, LoanTransaction::STATUS_COMPLETED])) // Count only successfully issued
            ->sum('quantity_transacted');


          $maxAllowedToIssueNow = ($appItem->quantity_approved ?? $appItem->quantity_requested) - $alreadyIssuedForThisAppItem;

          if ((int) $value > $maxAllowedToIssueNow) {
            $fail(__('Kuantiti untuk dikeluarkan (:value) bagi item #:item_num melebihi baki yang boleh dikeluarkan (:can_issue).', [
              'value' => $value,
              'item_num' => ((int) $index) + 1,
              'can_issue' => $maxAllowedToIssueNow < 0 ? 0 : $maxAllowedToIssueNow, // Display 0 if negative
            ]));
          }
        },
      ],
      'items.*.issue_item_notes' => ['nullable', 'string', 'max:1000'], // Notes for this specific item
      'items.*.accessories_checklist_item' => ['nullable', 'array'], // Accessories for this specific item
      'items.*.accessories_checklist_item.*' => ['string', 'max:255'], // Validate each accessory string
    ];
  }

  public function messages(): array
  {
    return [
      'transaction_date.required' => __('Tarikh transaksi pengeluaran wajib diisi.'),
      'receiving_officer_id.required' => __('Pegawai Penerima perlu dinyatakan.'),
      'items.required' => __('Sekurang-kurangnya satu item peralatan mesti dipilih untuk pengeluaran.'),
      'items.min' => __('Sekurang-kurangnya satu item peralatan mesti dipilih untuk pengeluaran.'),
      // Item specific messages
      'items.*.loan_application_item_id.required' => __('Item permohonan asal mesti dipilih untuk item #:position.'),
      'items.*.loan_application_item_id.exists' => __('Item permohonan asal tidak sah atau bukan milik permohonan ini untuk item #:position.'),
      'items.*.equipment_id.required' => __('Peralatan mesti dipilih untuk item #:position.'),
      'items.*.equipment_id.exists' => __('Peralatan yang dipilih tidak sah atau tidak tersedia untuk item #:position.'),
      'items.*.quantity_issued.required' => __('Kuantiti untuk dikeluarkan mesti diisi untuk item #:position.'),
      'items.*.quantity_issued.min' => __('Kuantiti untuk dikeluarkan mesti sekurang-kurangnya 1 untuk item #:position.'),
    ];
  }

  /**
   * Prepare the data for validation.
   * This can be used to merge overall accessories into item accessories if needed,
   * or ensure transaction_date is set.
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
