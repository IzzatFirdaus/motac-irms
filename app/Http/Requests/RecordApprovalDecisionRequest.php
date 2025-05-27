<?php

namespace App\Http\Requests;

use App\Models\Approval; //
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // For Rule::in

class RecordApprovalDecisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * The authorization logic checks if the authenticated user can 'update'
     * the specific approval task, leveraging the ApprovalPolicy.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var \App\Models\Approval|null $approval */
        $approval = $this->route('approval'); // Retrieves the Approval model instance from the route

        // Check if the approval instance exists and if the user is authorized by ApprovalPolicy
        return $approval instanceof Approval && $this->user()->can('update', $approval);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]), // Uses constants from Approval model
            ],
            'comments' => $this->input('decision') === Approval::STATUS_REJECTED
                ? ['required', 'string', 'min:10', 'max:2000'] // Comments are required if the decision is 'rejected'
                : ['nullable', 'string', 'max:2000'], // Comments are optional otherwise
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decision.required' => __('Sila pilih keputusan (Diluluskan/Ditolak).'),
            'decision.in' => __('Pilihan keputusan tidak sah. Sila pilih daripada pilihan yang diberikan.'),
            'comments.required' => __('Sila berikan justifikasi atau komen untuk penolakan.'),
            'comments.min' => __('Komen atau justifikasi penolakan mesti sekurang-kurangnya :min aksara.'),
            'comments.max' => __('Komen atau justifikasi tidak boleh melebihi :max aksara.'),
        ];
    }
}
