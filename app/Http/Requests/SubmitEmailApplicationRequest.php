<?php

namespace App\Http\Requests;

use App\Models\EmailApplication; // For context, though not directly used in rules here
use App\Models\User; // For type hinting
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SubmitEmailApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This request is specifically for the 'submit' action (moving from draft to pending).
     * The controller or service invoking this should ideally check user's ability to 'submit' the specific EmailApplication.
     * System Design Reference: Policy check for submission [cite: 493]
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        if (! $user) {
            return false;
        }

        /** @var EmailApplication|null $emailApplication */
        $emailApplication = $this->route('email_application'); // Assuming route parameter name

        if ($emailApplication) {
            return $user->can('submit', $emailApplication); // Depends on EmailApplicationPolicy@submit
        }

        // If no specific application instance, basic auth check might be okay if it's a general ability
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     * These fields are expected to be true when submitting an Email Application.
     * System Design Reference: Certification checkboxes
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cert_info_is_true' => ['required', 'boolean', 'accepted'], // 'accepted' implies value must be true-like
            'cert_data_usage_agreed' => ['required', 'boolean', 'accepted'],
            'cert_email_responsibility_agreed' => ['required', 'boolean', 'accepted'],
            // No other fields are typically validated here, as this request is for the act of submission itself.
            // The main application data would have been validated by StoreEmailApplicationRequest or UpdateEmailApplicationRequest when saving draft.
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
            'cert_info_is_true.accepted' => __('Perakuan pengesahan maklumat adalah benar mesti ditanda.'),
            'cert_data_usage_agreed.accepted' => __('Perakuan persetujuan penggunaan data oleh BPM mesti ditanda.'),
            'cert_email_responsibility_agreed.accepted' => __('Perakuan persetujuan tanggungjawab penggunaan e-mel mesti ditanda.'),
        ];
    }
}
