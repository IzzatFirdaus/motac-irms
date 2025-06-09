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
            // and decision is approved. Adjust stage check as per your workflow.
            $approval->stage === Approval::STAGE_LOAN_SUPPORT_REVIEW && // Example stage check
            $this->input('decision') === Approval::STATUS_APPROVED) { //

            // 'items_approved' itself should be an array, and can be empty if no items are being approved with specific quantities.
            // 'present' means the key must exist in the input, even if its value is an empty array.
            $rules['items_approved'] = ['present', 'array']; // Key that holds all item quantity data

            // Loop through the submitted items_approved data (which is an associative array keyed by loan_application_item_id)
            if (is_array($this->input('items_approved'))) { //
                foreach ($this->input('items_approved') as $loanApplicationItemId => $itemData) { //
                    // Find the original loan application item to get its quantity_requested
                    $loanAppItem = null;
                    if ($approval->approvable && method_exists($approval->approvable, 'loanApplicationItems')) {
                        $loanAppItem = $approval->approvable->loanApplicationItems()->find($loanApplicationItemId); //
                    }
                    $maxQty = $loanAppItem ? $loanAppItem->quantity_requested : 0; //

                    // Rule for each item's quantity_approved
                    // The key is items_approved.ITEM_ID.quantity_approved
                    $rules['items_approved.'.$loanApplicationItemId.'.quantity_approved'] = [ //
                        'required', // Make it required if the parent array key items_approved[ITEM_ID] exists
                        'integer', //
                        'min:0', //
                        'max:'.$maxQty, //
                    ];
                }
            }
        }

        return $rules; //
    }

    public function messages(): array
    {
        $messages = [ //
            'decision.required' => __('Sila pilih keputusan (Diluluskan/Ditolak).'), //
            'decision.in' => __('Pilihan keputusan tidak sah. Sila pilih daripada pilihan yang diberikan.'), //
            'comments.required' => __('Sila berikan justifikasi atau komen untuk penolakan.'), //
            'comments.string' => __('Komen atau justifikasi mesti dalam format teks.'), //
            'comments.min' => __('Komen atau justifikasi penolakan mesti sekurang-kurangnya :min aksara.'), //
            'comments.max' => __('Komen atau justifikasi tidak boleh melebihi :max aksara.'), //
            'items_approved.array' => __('Format data item yang diluluskan tidak sah.'), //
            'items_approved.present' => __('Data kuantiti item yang diluluskan mesti dihantar.'),
        ];

        /** @var Approval|null $approval */
        $approval = $this->route('approval'); //
        // Generate dynamic messages for each item's quantity_approved
        if ($approval && $approval->approvable instanceof LoanApplication && is_array($this->input('items_approved'))) { //
            foreach ($this->input('items_approved') as $loanApplicationItemId => $itemData) { //
                $loanAppItem = null;
                if ($approval->approvable && method_exists($approval->approvable, 'loanApplicationItems')) {
                    $loanAppItem = $approval->approvable->loanApplicationItems()->find($loanApplicationItemId); //
                }
                $itemTypeDisplay = 'Item'; // Default
                if ($loanAppItem && $loanAppItem->equipment_type) {
                    $itemTypeDisplay = optional(\App\Models\Equipment::getAssetTypeOptions())[$loanAppItem->equipment_type] ?? $loanAppItem->equipment_type; //
                } elseif ($loanApplicationItemId) {
                    $itemTypeDisplay = "Item ID {$loanApplicationItemId}"; //
                }

                $maxQty = $loanAppItem ? $loanAppItem->quantity_requested : 0; //

                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.required'] = __('Kuantiti diluluskan untuk :itemType wajib diisi.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.integer'] = __('Kuantiti diluluskan untuk :itemType mesti nombor bulat.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.min'] = __('Kuantiti diluluskan untuk :itemType tidak boleh kurang dari 0.', ['itemType' => $itemTypeDisplay]); //
                $messages['items_approved.'.$loanApplicationItemId.'.quantity_approved.max'] = __('Kuantiti diluluskan untuk :itemType tidak boleh melebihi kuantiti dipohon (:max).', ['itemType' => $itemTypeDisplay, 'max' => $maxQty]); //
            }
        }

        return $messages; //
    }
}
