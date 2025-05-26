<?php

namespace App\Http\Requests;

use App\Models\LoanApplication; // Import the LoanApplication model
use Illuminate\Foundation\Http\FormRequest;

// Import Rule if needed for rules() method, not directly for authorize()
// use Illuminate\Validation\Rule;

class UpdateLoanApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This method uses the LoanApplicationPolicy to check authorization for updating the specific application.
     */
    public function authorize(): bool
    {
        // Retrieve the LoanApplication instance being updated.
        // Assuming the route parameter is named 'loanApplication' and uses route model binding.
        $loanApplication = $this->route('loanApplication');
        $user = $this->user(); // Get the authenticated user

        // Check if the authenticated user can update this specific LoanApplication.
        // This delegates the authorization check to the LoanApplicationPolicy.
        return $user && $loanApplication && $user->can('update', $loanApplication);
    }

    /**
     * Get the validation rules that apply to the request.
     * These rules validate the data submitted when updating a loan application.
     * Note: These rules are typically for updating a *draft* application,
     * so some fields might be nullable compared to the store request.
     * The Livewire component's 'submitApplication' method should apply stricter rules for final submission.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Part 1: Loan Details (Nullable for drafts)
            'purpose' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'loan_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'loan_end_date' => ['nullable', 'date', 'after_or_equal:loan_start_date'],
            'responsible_officer_id' => ['nullable', 'integer', 'exists:users,id'], // Added integer validation
            'supporting_officer_id' => ['nullable', 'integer', 'exists:users,id'], // Added supporting_officer_id

            // Part 3: Equipment Items (Array of items)
            'items' => ['nullable', 'array'],
            'items.*.equipment_type' => ['nullable', 'string', 'max:255'],
            'items.*.quantity_requested' => ['nullable', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],

            // Part 4: Applicant Confirmation
            'applicant_confirmation' => ['nullable', 'accepted'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'loan_start_date.date' => 'Format Tarikh Pinjaman tidak sah.',
            'loan_start_date.after_or_equal' => 'Tarikh Pinjaman mestilah pada atau selepas hari ini.',
            'loan_end_date.date' => 'Format Tarikh Dijangka Pulang tidak sah.',
            'loan_end_date.after_or_equal' => 'Tarikh Dijangka Pulang mestilah pada atau selepas Tarikh Pinjaman.',
            'responsible_officer_id.exists' => 'Pegawai Bertanggungjawab yang dipilih tidak sah.',
            'supporting_officer_id.exists' => 'Pegawai Penyokong yang dipilih tidak sah.',
            'items.array' => 'Format item peralatan tidak sah.',
            'items.*.quantity_requested.integer' => 'Kuantiti mestilah nombor bulat.',
            'items.*.quantity_requested.min' => 'Kuantiti mestilah sekurang-kurangnya 1 untuk setiap item.',
            'applicant_confirmation.accepted' => 'Anda mesti bersetuju dengan perakuan pemohon.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! is_array($this->input('items'))) {
            $this->merge(['items' => []]);
        }
    }
}
