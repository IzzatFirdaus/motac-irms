<?php

namespace App\Http\Requests\Api;

use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request validator for closing a Helpdesk ticket via API.
 *
 * Notes:
 * - Accepts either "resolution_notes" (preferred) or "resolution_details" (legacy).
 *   If "resolution_details" is provided, it will be mapped to "resolution_notes".
 * - Ensures status is "closed" (uses model constant).
 * - Trims string inputs to keep data clean.
 */
class CloseHelpdeskTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Consider implementing policy-based checks, e.g.:
     * return $this->user()?->can('close', $this->route('ticket')) ?? false;.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize incoming payload before validation.
     * - Map "resolution_details" -> "resolution_notes" (legacy alias).
     * - Default "status" to "closed" if not present.
     * - Trim string fields.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Map legacy key to canonical attribute used by the model/service
        if (! isset($data['resolution_notes']) && isset($data['resolution_details'])) {
            $data['resolution_notes'] = $data['resolution_details'];
        }

        // Normalize strings (trim)
        foreach (['resolution_notes'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        // Ensure status is present; controller/service expects an explicit "closed" status
        if (! isset($data['status'])) {
            $data['status'] = HelpdeskTicket::STATUS_CLOSED;
        } else {
            // Normalize case to match constants
            if (is_string($data['status'])) {
                $data['status'] = strtolower(trim($data['status']));
            }
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Require canonical key after mapping
            'resolution_notes' => ['required', 'string', 'max:2000'],
            'status'           => [
                'required',
                'string',
                Rule::in([HelpdeskTicket::STATUS_CLOSED]),
            ],
        ];
    }

    /**
     * Custom attribute names for clearer validation messages.
     */
    public function attributes(): array
    {
        return [
            'resolution_notes' => __('Resolution notes'),
            'status'           => __('Status'),
        ];
    }

    /**
     * Custom messages for validation errors (optional but helpful).
     */
    public function messages(): array
    {
        return [
            'status.in' => __('Status must be ":closed".', ['closed' => HelpdeskTicket::STATUS_CLOSED]),
        ];
    }
}
