<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User; // For asset type options
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        // System Design: LoanApplicationPolicy for 'create'
        return $user && $user->can('create', LoanApplication::class);
    }

    public function rules(): array
    {
        // System Design: loan_start_date and loan_end_date are datetime
        $today = now()->startOfDay()->format('Y-m-d H:i:s'); // Compare with start of day for date part

        return [
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'location' => ['required', 'string', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255'],
            'loan_start_date' => ['required', 'date_format:Y-m-d H:i:s', "after_or_equal:$today"],
            'loan_end_date' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:loan_start_date'],

            'responsible_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'supporting_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'items' => ['required', 'array', 'min:1'],
            // System Design: Equipment model defines asset types
            'items.*.equipment_type' => ['required', 'string', 'max:255', Rule::in(array_keys(Equipment::getAssetTypeOptions()))],
            'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],

            'applicant_confirmation' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'purpose.required' => __('Tujuan Permohonan wajib diisi.'),
            'purpose.min' => __('Tujuan Permohonan mesti sekurang-kurangnya :min aksara.'),
            'purpose.max' => __('Tujuan Permohonan tidak boleh melebihi :max aksara.'),
            'location.required' => __('Lokasi penggunaan peralatan wajib diisi.'),
            'location.max' => __('Lokasi penggunaan peralatan tidak boleh melebihi :max aksara.'),
            'return_location.max' => __('Lokasi pemulangan tidak boleh melebihi :max aksara.'),

            'loan_start_date.required' => __('Tarikh & Masa Pinjaman wajib diisi.'),
            'loan_start_date.date_format' => __('Format Tarikh & Masa Pinjaman tidak sah (YYYY-MM-DD HH:MM:SS).'),
            'loan_start_date.after_or_equal' => __('Tarikh & Masa Pinjaman mestilah pada atau selepas hari/masa semasa.'),
            'loan_end_date.required' => __('Tarikh & Masa Dijangka Pulang wajib diisi.'),
            'loan_end_date.date_format' => __('Format Tarikh & Masa Dijangka Pulang tidak sah (YYYY-MM-DD HH:MM:SS).'),
            'loan_end_date.after_or_equal' => __('Tarikh & Masa Dijangka Pulang mestilah selepas atau sama dengan Tarikh & Masa Pinjaman.'),

            'responsible_officer_id.exists' => __('Pegawai Bertanggungjawab yang dipilih tidak sah.'),
            'supporting_officer_id.required' => __('Pegawai Penyokong wajib dipilih.'),
            'supporting_officer_id.exists' => __('Pegawai Penyokong yang dipilih tidak sah.'),

            'items.required' => __('Sila tambah sekurang-kurangnya satu item peralatan.'),
            'items.min' => __('Sila tambah sekurang-kurangnya satu item peralatan.'),
            'items.*.equipment_type.required' => __('Jenis Peralatan wajib diisi untuk item di kedudukan :position.'),
            'items.*.equipment_type.in' => __('Jenis Peralatan yang dipilih tidak sah untuk item di kedudukan :position.'),
            'items.*.quantity_requested.required' => __('Kuantiti wajib diisi untuk item di kedudukan :position.'),
            'items.*.quantity_requested.integer' => __('Kuantiti mesti nombor bulat untuk item di kedudukan :position.'),
            'items.*.quantity_requested.min' => __('Kuantiti mestilah sekurang-kurangnya 1 untuk item di kedudukan :position.'),
            'items.*.quantity_requested.max' => __('Kuantiti tidak boleh melebihi :max untuk item di kedudukan :position.'),
            'items.*.notes.max' => __('Catatan untuk item di kedudukan :position tidak boleh melebihi :max aksara.'),

            'applicant_confirmation.required' => __('Anda mesti membuat pengesahan permohonan.'),
            'applicant_confirmation.accepted' => __('Anda mesti bersetuju dengan perakuan dan syarat permohonan.'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('items') === null && ! $this->has('items')) {
            $this->merge(['items' => []]);
        }
    }
}
