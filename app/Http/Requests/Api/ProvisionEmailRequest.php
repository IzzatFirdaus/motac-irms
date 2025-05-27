<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EmailApplication; // For status constant

class ProvisionEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Primary auth via Sanctum on route. Additional checks if token needs specific abilities.
        return true;
    }

    public function rules(): array
    {
        return [
            'application_id' => [
                'required',
                'integer',
                Rule::exists('email_applications', 'id')
                // ->where('status', EmailApplication::STATUS_APPROVED) // Could enforce status here, but controller gives better response
            ],
            'final_assigned_email' => 'required|email|max:255',
            'user_id_assigned' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'application_id.required' => __('The email application ID is required.'),
            'application_id.exists' => __('The selected email application ID is invalid or does not exist.'),
            'final_assigned_email.required' => __('The final assigned email address is required.'),
            'final_assigned_email.email' => __('The final assigned email must be a valid email address.'),
        ];
    }
}
