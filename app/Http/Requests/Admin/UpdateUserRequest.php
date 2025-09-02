<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = is_object($user) && property_exists($user, 'id') ? $user->id : (int) $user;

        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'identification_number' => ['nullable', 'string', 'max:255', Rule::unique('users', 'identification_number')->ignore($userId)],
            'department_id'         => ['required', 'exists:departments,id'],
            'position_id'           => ['required', 'exists:positions,id'],
            'grade_id'              => ['required', 'exists:grades,id'],
            'status'                => ['required', 'string', 'in:'.implode(',', array_keys(User::getStatusOptions()))],
            'roles'                 => ['nullable', 'array'],
            'roles.*'               => ['exists:roles,name'],
            'password'              => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}

