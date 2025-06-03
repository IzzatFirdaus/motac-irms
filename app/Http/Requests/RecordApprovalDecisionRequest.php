<?php

namespace App\Http\Requests;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordApprovalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        if (!$user) {
            return false;
        }

        /** @var Approval|null $approval */
        $approval = $this->route('approval');

        // System Design: ApprovalPolicy handles authorization
        return $approval instanceof Approval && $user->can('update', $approval);
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]),
            ],
            'comments' => $this->input('decision') === Approval::STATUS_REJECTED
                ? ['required', 'string', 'min:10', 'max:2000']
                : ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => __('Sila pilih keputusan (Diluluskan/Ditolak).'),
            'decision.in' => __('Pilihan keputusan tidak sah. Sila pilih daripada pilihan yang diberikan.'),
            'comments.required' => __('Sila berikan justifikasi atau komen untuk penolakan.'),
            'comments.string' => __('Komen atau justifikasi mesti dalam format teks.'),
            'comments.min' => __('Komen atau justifikasi penolakan mesti sekurang-kurangnya :min aksara.'),
            'comments.max' => __('Komen atau justifikasi tidak boleh melebihi :max aksara.'),
        ];
    }
}
