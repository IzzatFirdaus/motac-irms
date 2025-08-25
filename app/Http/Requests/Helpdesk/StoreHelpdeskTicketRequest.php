<?php

namespace App\Http\Requests\Helpdesk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHelpdeskTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only authenticated users are allowed, as enforced by middleware.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id'      => ['required', 'exists:helpdesk_categories,id'],
            'priority_id'      => ['required', 'exists:helpdesk_priorities,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string', 'max:5000'],
            'attachments'      => ['nullable', 'array'],
            'attachments.*'    => ['nullable', 'file', 'max:5120'], // 5MB per file
        ];
    }

    /**
     * Prepare the data for validation.
     * Normalize legacy/alternative fields and trim the input.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Support legacy/alternative field names if any (e.g., 'subject')
        if (isset($input['subject']) && !isset($input['title'])) {
            $input['title'] = $input['subject'];
        }

        // Trim all string fields
        if (isset($input['title'])) {
            $input['title'] = trim($input['title']);
        }
        if (isset($input['description'])) {
            $input['description'] = trim($input['description']);
        }

        $this->replace($input);
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'category_id.required'   => __('Kategori diperlukan.'),
            'category_id.exists'     => __('Kategori tidak sah.'),
            'priority_id.required'   => __('Keutamaan diperlukan.'),
            'priority_id.exists'     => __('Keutamaan tidak sah.'),
            'title.required'         => __('Tajuk diperlukan.'),
            'title.max'              => __('Tajuk tidak boleh melebihi 255 aksara.'),
            'description.required'   => __('Deskripsi diperlukan.'),
            'description.max'        => __('Deskripsi tidak boleh melebihi 5000 aksara.'),
            'attachments.*.file'     => __('Setiap lampiran mesti fail yang sah.'),
            'attachments.*.max'      => __('Setiap lampiran tidak boleh melebihi 5MB.'),
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'category_id'   => __('kategori'),
            'priority_id'   => __('keutamaan'),
            'title'         => __('tajuk'),
            'description'   => __('deskripsi'),
            'attachments'   => __('lampiran'),
        ];
    }
}
