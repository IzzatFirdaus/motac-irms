<?php

namespace App\Http\Requests;

// Import the model for policy check
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $emailApplication = $this->route('emailApplication'); // Assumes route model binding
        $user = $this->user();

        return $user && $emailApplication && $user->can('update', $emailApplication);
    }

    public function rules(): array
    {
        // These rules are for updating a *draft* application.
        // Fields should be nullable if they are not mandatory for a draft save.
        // Final validation for submission (e.g., to make fields required) might be in a Livewire component or service.
        return [
            'supporting_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')], // Added based on service
            'application_reason_notes' => ['nullable', 'string', 'max:1000'], // 'purpose' was used in form, matching model
            'proposed_email' => ['nullable', 'string', 'max:255', 'email'],
            'group_email' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'string', 'max:255', 'email'],
            'service_start_date' => ['nullable', 'date_format:Y-m-d'],
            'service_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:service_start_date'],

            // Certification fields might not be updatable here directly if they trigger submission logic
            // 'cert_info_is_true' => ['sometimes', 'boolean'],
            // 'cert_data_usage_agreed' => ['sometimes', 'boolean'],
            // 'cert_email_responsibility_agreed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'supporting_officer_id.exists' => 'Pegawai Penyokong yang dipilih tidak sah.',
            'proposed_email.email' => 'Format Cadangan E-mel tidak sah.',
            'contact_person_email.email' => 'Format E-mel Admin/EO/CC tidak sah.',
            'service_end_date.after_or_equal' => 'Tarikh Akhir Khidmat mesti selepas atau sama dengan Tarikh Mula Khidmat.',
        ];
    }
}
