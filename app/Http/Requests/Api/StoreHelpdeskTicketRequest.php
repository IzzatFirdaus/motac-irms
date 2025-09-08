<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreHelpdeskTicketRequest extends FormRequest
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
            'category_id'   => ['required', 'exists:helpdesk_categories,id'],
            'priority_id'   => ['required', 'exists:helpdesk_priorities,id'],
            'subject'       => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
