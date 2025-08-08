<?php

namespace App\Http\Requests\Api;

use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request validator for updating a Helpdesk ticket via API.
 *
 * Highlights:
 * - Accepts either "title" (preferred) or "subject" (legacy); legacy is mapped to "title".
 * - Accepts either "resolution_notes" (preferred) or "resolution_details" (legacy); legacy is mapped.
 * - Status is validated against the model's constants to avoid drift.
 * - Enforces that "resolution_notes" is present when status is moving to resolved or closed.
 * - Supports optional SLA due date updates.
 * - Attachment validation is included (5MB per file).
 */
class UpdateHelpdeskTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Consider implementing policy-based checks, e.g.:
     * return $this->user()?->can('update', $this->route('ticket')) ?? false;
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize legacy fields and clean inputs before validation.
     * - Map "subject" -> "title".
     * - Map "resolution_details" -> "resolution_notes".
     * - Trim string fields.
     * - Normalize "status" to lowercase to match constants.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Map legacy keys to canonical attribute names used by the model/service
        if (!isset($data['title']) && isset($data['subject'])) {
            $data['title'] = $data['subject'];
        }
        if (!isset($data['resolution_notes']) && isset($data['resolution_details'])) {
            $data['resolution_notes'] = $data['resolution_details'];
        }

        // Normalize strings (trim)
        foreach (['title', 'description', 'resolution_notes'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        // Normalize status casing
        if (isset($data['status']) && is_string($data['status'])) {
            $data['status'] = strtolower(trim($data['status']));
        }

        $this->replace($data);
    }

    /**
     * Build the allowed status list from the model constants to keep in sync.
     */
    protected function allowedStatuses(): array
    {
        return [
            HelpdeskTicket::STATUS_OPEN,
            HelpdeskTicket::STATUS_IN_PROGRESS,
            HelpdeskTicket::STATUS_RESOLVED,
            HelpdeskTicket::STATUS_CLOSED,
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * "sometimes|required" means the field, if present, cannot be empty.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'exists:helpdesk_categories,id'],
            'priority_id' => ['sometimes', 'required', 'exists:helpdesk_priorities,id'],

            // Canonical name in our model is "title" (not "subject")
            'title' => ['sometimes', 'required', 'string', 'max:255'],

            'description' => ['sometimes', 'required', 'string', 'max:5000'],

            // Ensure status matches the model's supported states
            'status' => ['sometimes', 'required', 'string', Rule::in($this->allowedStatuses())],

            // Require resolution notes when marking ticket as resolved or closed
            'resolution_notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(function () {
                    $status = $this->input('status');
                    return in_array($status, [HelpdeskTicket::STATUS_RESOLVED, HelpdeskTicket::STATUS_CLOSED], true);
                }),
            ],

            // Assignment and scheduling
            'assigned_to_user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'sla_due_at' => ['sometimes', 'nullable', 'date'],

            // Attachments (5MB each)
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ];
    }

    /**
     * Custom attribute names for clearer validation messages.
     */
    public function attributes(): array
    {
        return [
            'title' => __('Title'),
            'description' => __('Description'),
            'status' => __('Status'),
            'resolution_notes' => __('Resolution notes'),
            'category_id' => __('Category'),
            'priority_id' => __('Priority'),
            'assigned_to_user_id' => __('Assignee'),
            'sla_due_at' => __('SLA due at'),
            'attachments.*' => __('Attachment'),
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => __('The selected status is invalid. Allowed: :allowed.', [
                'allowed' => implode(', ', $this->allowedStatuses()),
            ]),
        ];
    }
}
