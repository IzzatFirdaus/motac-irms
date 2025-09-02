<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'branch_type'           => ['required', 'string', Rule::in(array_keys(Department::$BRANCH_TYPE_LABELS))],
            'code'                  => ['nullable', 'string', 'max:50', Rule::unique('departments', 'code')],
            'description'           => ['nullable', 'string', 'max:500'],
            'is_active'             => ['required', 'boolean'],
            'head_of_department_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
