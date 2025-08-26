<?php

namespace App\Http\Requests\Admin;

use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $grade = $this->route('grade'); // Get the grade instance from route model binding

        return $grade && $this->user()->can('update', $grade);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $gradeId = $this->route('grade')->id;

        return [
            'name'                  => ['required', 'string', 'max:50', Rule::unique('grades', 'name')->ignore($gradeId)->whereNull('deleted_at')],
            'level'                 => ['required', 'integer', 'min:1', Rule::unique('grades', 'level')->ignore($gradeId)->whereNull('deleted_at')],
            'min_approval_grade_id' => ['nullable', 'integer', Rule::exists('grades', 'id'), Rule::notIn([$gradeId])], // Cannot be its own min approval grade
            'is_approver_grade'     => ['required', 'boolean'],
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
            'name.required'                => 'Nama Gred wajib diisi.',
            'name.unique'                  => 'Nama Gred ini telah wujud.',
            'level.required'               => 'Tahap Gred wajib diisi.',
            'level.unique'                 => 'Tahap Gred ini telah wujud.',
            'min_approval_grade_id.exists' => 'Gred Kelulusan Minima yang dipilih tidak sah.',
            'min_approval_grade_id.not_in' => 'Gred Kelulusan Minima tidak boleh sama dengan gred semasa.',
            'is_approver_grade.required'   => 'Status Gred Pelulus wajib dipilih.',
        ];
    }
}
