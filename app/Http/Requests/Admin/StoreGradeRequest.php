<?php

namespace App\Http\Requests\Admin;

use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assumes GradePolicy@create will be checked by authorizeResource in controller
        return $this->user()->can('create', Grade::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('grades', 'name')->whereNull('deleted_at')],
            'level' => ['required', 'integer', 'min:1', Rule::unique('grades', 'level')->whereNull('deleted_at')],
            'min_approval_grade_id' => ['nullable', 'integer', Rule::exists('grades', 'id')],
            'is_approver_grade' => ['required', 'boolean'],
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
            'name.required' => 'Nama Gred wajib diisi.',
            'name.unique' => 'Nama Gred ini telah wujud.',
            'level.required' => 'Tahap Gred wajib diisi.',
            'level.integer' => 'Tahap Gred mestilah nombor.',
            'level.min' => 'Tahap Gred mestilah sekurang-kurangnya 1.',
            'level.unique' => 'Tahap Gred ini telah wujud.',
            'min_approval_grade_id.exists' => 'Gred Kelulusan Minima yang dipilih tidak sah.',
            'is_approver_grade.required' => 'Status Gred Pelulus wajib dipilih.',
        ];
    }
}
