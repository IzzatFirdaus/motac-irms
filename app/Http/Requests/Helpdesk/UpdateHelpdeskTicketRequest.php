<?php

namespace App\Http\Requests\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHelpdeskTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled by controller policy.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     * Fields are optional for partial update, but validated if present.
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => ['sometimes', 'required', 'exists:helpdesk_categories,id'],
            'priority_id' => ['sometimes', 'required', 'exists:helpdesk_priorities,id'],
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'max:5000'],
            'status'      => ['sometimes', 'required', 'string', Rule::in([
                HelpdeskTicket::STATUS_OPEN,
                HelpdeskTicket::STATUS_IN_PROGRESS,
                HelpdeskTicket::STATUS_RESOLVED,
                HelpdeskTicket::STATUS_CLOSED,
            ])],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'resolution_notes'    => ['nullable', 'string', 'max:2000'],
            'attachments'         => ['nullable', 'array'],
            'attachments.*'       => ['nullable', 'file', 'max:5120'],
        ];

        // If status is being set to closed or resolved, require resolution_notes
        $status = $this->input('status', $this->route('ticket')->status ?? null);
        if (in_array($status, [HelpdeskTicket::STATUS_CLOSED, HelpdeskTicket::STATUS_RESOLVED])) {
            $rules['resolution_notes'] = ['required', 'string', 'max:2000'];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     * Normalize legacy/alternative fields and trim the input.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Support legacy/alternative field names
        if (isset($input['subject']) && ! isset($input['title'])) {
            $input['title'] = $input['subject'];
        }
        if (isset($input['resolution_details']) && ! isset($input['resolution_notes'])) {
            $input['resolution_notes'] = $input['resolution_details'];
        }

        // Normalize status to lowercase
        if (isset($input['status'])) {
            $input['status'] = strtolower(trim($input['status']));
        }

        // Trim strings
        foreach (['title', 'description', 'resolution_notes'] as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }

        $this->replace($input);
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'category_id.required'       => __('Kategori diperlukan.'),
            'category_id.exists'         => __('Kategori tidak sah.'),
            'priority_id.required'       => __('Keutamaan diperlukan.'),
            'priority_id.exists'         => __('Keutamaan tidak sah.'),
            'title.required'             => __('Tajuk diperlukan.'),
            'title.max'                  => __('Tajuk tidak boleh melebihi 255 aksara.'),
            'description.required'       => __('Deskripsi diperlukan.'),
            'description.max'            => __('Deskripsi tidak boleh melebihi 5000 aksara.'),
            'status.required'            => __('Status diperlukan.'),
            'status.in'                  => __('Status tidak sah.'),
            'assigned_to_user_id.exists' => __('Pengguna tugasan tidak sah.'),
            'resolution_notes.required'  => __('Catatan penyelesaian diperlukan apabila status ditutup atau diselesaikan.'),
            'resolution_notes.max'       => __('Catatan penyelesaian tidak boleh melebihi 2000 aksara.'),
            'attachments.*.file'         => __('Setiap lampiran mesti fail yang sah.'),
            'attachments.*.max'          => __('Setiap lampiran tidak boleh melebihi 5MB.'),
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'category_id'         => __('kategori'),
            'priority_id'         => __('keutamaan'),
            'title'               => __('tajuk'),
            'description'         => __('deskripsi'),
            'status'              => __('status'),
            'assigned_to_user_id' => __('ditugaskan kepada'),
            'resolution_notes'    => __('catatan penyelesaian'),
            'attachments'         => __('lampiran'),
        ];
    }
}
