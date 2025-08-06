<?php

namespace App\Http\Requests;

use App\Models\Approval;
use App\Models\LoanApplication; // Added
// Added for fetching item details
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordApprovalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user(); //
        if (! $user) { //
            return false;
        }

        /** @var Approval|null $approval */
        $approval = $this->route('approval'); //

        // System Design: ApprovalPolicy handles authorization
        return $approval instanceof Approval && $user->can('update', $approval); //
    }

    public function rules(): array
    {
        $rules = [ //
            'decision' => [ //
                'required', //
                Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]), //
            ],
            'comments' => $this->input('decision') === Approval::STATUS_REJECTED //
                ? ['required', 'string', 'min:10', 'max:2000'] //
                : ['nullable', 'string', 'max:2000'], //
        ];

        /** @var Approval|null $approval */
        $approval = $this->route('approval'); //

        if ($approval && //
            $approval->approvable instanceof LoanApplication && //
            // Only require quantity adjustments if the stage is relevant (e.g., support review)
            // and decision is approved. Adjust sta
            $this->input('decision') === Approval::STATUS_APPROVED) { //
            $rules['items_approved'] = ['required', 'array', 'min:1']; //
            $rules['items_approved.*.loan_application_item_id'] = [ //
                'required', 'integer',
                Rule::exists('loan_application_items', 'id')->where(function ($query) use ($approval) { //
                    $query->where('loan_application_id', $approval->approvable_id); //
                }),
            ];
            $rules['items_approved.*.quantity_approved'] = [ //
                'required', 'integer', 'min:0',
                function ($attribute, $value, $fail) use ($approval) { //
                    $index = explode('.', $attribute)[1]; // Get the array index
                    $loanApplicationItemId = $this->input("items_approved.{$index}.loan_application_item_id"); //
                    $loanAppItem = $approval->approvable->loanApplicationItems->find($loanApplicationItemId); //

                    if ($loanAppItem && $value > $loanAppItem->quantity_requested) { //
                        $fail(__('Kuantiti diluluskan tidak boleh melebihi kuantiti dipohon.')); //
                    }
                },
            ];
            $rules['items_approved.*.approval_item_notes'] = ['nullable', 'string', 'max:500']; //
        }

        return $rules; //
    }

    public function messages(): array
    {
        $messages = [
            'decision.required' => __('Sila pilih keputusan (Lulus/Tolak).'),
            'decision.in' => __('Keputusan yang dipilih tidak sah.'),
            'comments.required' => __('Ruangan ulasan wajib diisi apabila menolak permohonan.'),
            'comments.min' => __('Ulasan hendaklah sekurang-kurangnya :min aksara.'),
            'comments.max' => __('Ulasan tidak boleh melebihi :max aksara.'),

            'items_approved.required' => __('Sila nyatakan kuantiti diluluskan untuk setiap item.'),
            'items_approved.array' => __('Format kuantiti diluluskan tidak sah.'),
            'items_approved.min' => __('Sila nyatakan kuantiti diluluskan untuk sekurang-kurangnya satu item.'),
            'items_approved.*.loan_application_item_id.required' => __('ID item permohonan asal wajib ada.'),
            'items_approved.*.loan_application_item_id.exists' => __('Item permohonan asal tidak sah untuk permohonan ini.'),
            'items_approved.*.quantity_approved.min' => __('Kuantiti diluluskan tidak boleh kurang dari 0.'),
            'items_approved.*.approval_item_notes.max' => __('Catatan item kelulusan tidak boleh melebihi :max aksara.'),
        ];

        /** @var Approval|null $approval */
        $approval = $this->route('approval');

        if ($approval && $approval->approvable instanceof LoanApplication) {
            foreach ($this->input('items_approved', []) as $index => $item) {
                $loanApplicationItemId = $item['loan_application_item_id'] ?? null;
                $loanAppItem = null;
                if ($loanApplicationItemId) {
                    $loanAppItem = $approval->approvable->loanApplicationItems->find($loanApplicationItemId);
                }

                $itemTypeDisplay = 'Item'; // Default
                if ($loanAppItem && $loanAppItem->equipment_type) {
                    $itemTypeDisplay = optional(\App\Models\Equipment::getAssetTypeOptions())[$loanAppItem->equipment_type] ?? $loanAppItem->equipment_type; //
                } elseif ($loanApplicationItemId !== 0 && ($loanApplicationItemId !== '' && $loanApplicationItemId !== '0')) {
                    $itemTypeDisplay = 'Item ID '.$loanApplicationItemId; //
                }

                $maxQty = $loanAppItem ? $loanAppItem->quantity_requested : 0; //

                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.required'] = __('Kuantiti diluluskan untuk :itemType wajib diisi.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.integer'] = __('Kuantiti diluluskan untuk :itemType mesti nombor bulat.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.min'] = __('Kuantiti diluluskan untuk :itemType tidak boleh kurang dari 0.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.max'] = __('Kuantiti diluluskan untuk :itemType tidak boleh melebihi kuantiti dipohon (:max).', ['itemType' => $itemTypeDisplay, 'max' => $maxQty]); //
            }
        }
        // Removed the conditional block for EmailApplication as per the refactoring plan.
        /*
        // Original code snippet from your file (to be removed)
        } elseif ($approval && $approval->approvable instanceof EmailApplication) { //
            // No item-specific messages needed for EmailApplication as it doesn't have quantity.
            // Any specific messages for EmailApplication would go here if needed.
        */

        return $messages; //
    }
}
