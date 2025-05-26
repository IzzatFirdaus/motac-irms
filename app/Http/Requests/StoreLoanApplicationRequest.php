<?php

namespace App\Http\Requests;

use App\Models\LoanApplication; // Import the model for policy check
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule for validation

class StoreLoanApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This method uses the LoanApplicationPolicy to check authorization.
     */
    public function authorize(): bool
    {
        // Use $this->user() to get the authenticated user within a FormRequest
        // and check if they can 'create' a LoanApplication.
        $user = $this->user();

        return $user && $user->can('create', LoanApplication::class);
    }

    /**
     * Get the validation rules that apply to the request.
     * These rules validate the data submitted from the loan application form (Parts 1-4).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Part 1: Loan Details
            'purpose' => ['required', 'string', 'max:500'],
            'location' => ['required', 'string', 'max:255'],
            'loan_start_date' => ['required', 'date', 'after_or_equal:today'],
            'loan_end_date' => ['required', 'date', 'after_or_equal:loan_start_date'],

            // Part 2: Responsible Officer & Supporting Officer
            'responsible_officer_id' => ['nullable', 'integer', 'exists:users,id'],
            'supporting_officer_id' => ['required', 'integer', 'exists:users,id'], // Made required based on service needs

            // Part 3: Equipment Items (Array of items)
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_type' => ['required', 'string', 'max:255'],
            'items.*.quantity_requested' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],

            // Part 4: Applicant Confirmation
            'applicant_confirmation' => ['required', 'accepted'],
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
            'purpose.required' => 'Tujuan Permohonan wajib diisi.',
            'location.required' => 'Lokasi wajib diisi.',
            'loan_start_date.required' => 'Tarikh Pinjaman wajib diisi.',
            'loan_start_date.date' => 'Format Tarikh Pinjaman tidak sah.',
            'loan_start_date.after_or_equal' => 'Tarikh Pinjaman mestilah pada atau selepas hari ini.',
            'loan_end_date.required' => 'Tarikh Dijangka Pulang wajib diisi.',
            'loan_end_date.date' => 'Format Tarikh Dijangka Pulang tidak sah.',
            'loan_end_date.after_or_equal' => 'Tarikh Dijangka Pulang mestilah pada atau selepas Tarikh Pinjaman.',
            'responsible_officer_id.exists' => 'Pegawai Bertanggungjawab yang dipilih tidak sah.',
            'supporting_officer_id.required' => 'Pegawai Penyokong wajib dipilih.',
            'supporting_officer_id.exists' => 'Pegawai Penyokong yang dipilih tidak sah.',
            'items.required' => 'Sila tambah sekurang-kurangnya satu item peralatan.',
            'items.min' => 'Sila tambah sekurang-kurangnya satu item peralatan.',
            'items.*.equipment_type.required' => 'Jenis Peralatan wajib diisi untuk setiap item.',
            'items.*.quantity_requested.required' => 'Kuantiti wajib diisi untuk setiap item.',
            'items.*.quantity_requested.min' => 'Kuantiti mestilah sekurang-kurangnya 1 untuk setiap item.',
            'applicant_confirmation.required' => 'Anda mesti mengesahkan perakuan pemohon.',
            'applicant_confirmation.accepted' => 'Anda mesti bersetuju dengan perakuan pemohon.',
        ];
    }
}
