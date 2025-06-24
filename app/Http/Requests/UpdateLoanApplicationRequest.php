<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User; // For asset type options
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var LoanApplication|null $loanApplication */
        $loanApplication = $this->route('loanApplication');
        /** @var User|null $user */
        $user = $this->user();

        // System Design: LoanApplicationPolicy for 'update'
        return $user && $loanApplication && $user->can('update', $loanApplication);
    }

    public function rules(): array
    {
        /** @var LoanApplication|null $loanApplicationFromRoute */
        $loanApplicationFromRoute = $this->route('loanApplication');
        // System Design: loan_start_date and loan_end_date are datetime
        $today = now()->startOfDay()->format('Y-m-d H:i:s'); // Ensure datetime comparison

        return [
            'purpose' => ['nullable', 'string', 'min:10', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'loan_start_date' => ['nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:'.$today],
            'loan_end_date' => ['nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:loan_start_date'],
            'return_location' => ['nullable', 'string', 'max:255'],

            'responsible_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'supporting_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],

            'items' => ['present', 'array'],
            'items.*.id' => ['nullable', 'integer', Rule::exists('loan_application_items', 'id')
                ->where('loan_application_id', $loanApplicationFromRoute?->id)],
            'items.*.equipment_type' => [
                Rule::requiredIf(function (): bool {
                    foreach ($this->input('items', []) as $item) {
                        if (! empty($item['equipment_type']) || ! empty($item['quantity_requested'])) {
                            return true;
                        }
                    }

                    return false;
                }),
                'nullable', 'string', 'max:255', Rule::in(array_keys(Equipment::getAssetTypeOptions())), //
            ],
            'items.*.quantity_requested' => [
                Rule::requiredIf(function (): bool {
                    foreach ($this->input('items', []) as $item) {
                        if (! empty($item['equipment_type']) || ! empty($item['quantity_requested'])) {
                            return true;
                        }
                    }

                    return false;
                }),
                'nullable', 'integer', 'min:1', 'max:99',
            ],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
            'items.*._delete' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'purpose.min' => __('Tujuan Permohonan mesti sekurang-kurangnya :min aksara.'),
            'purpose.max' => __('Tujuan Permohonan tidak boleh melebihi :max aksara.'),
            'location.max' => __('Lokasi Penggunaan tidak boleh melebihi :max aksara.'),
            'return_location.max' => __('Lokasi Pemulangan tidak boleh melebihi :max aksara.'),
            'loan_start_date.date_format' => __('Format Tarikh & Masa Pinjaman tidak sah (YYYY-MM-DD HH:MM:SS).'),
            'loan_start_date.after_or_equal' => __('Tarikh & Masa Pinjaman mestilah pada atau selepas hari/masa semasa.'),
            'loan_end_date.date_format' => __('Format Tarikh & Masa Dijangka Pulang tidak sah (YYYY-MM-DD HH:MM:SS).'),
            'loan_end_date.after_or_equal' => __('Tarikh & Masa Dijangka Pulang mestilah selepas atau sama dengan Tarikh & Masa Pinjaman.'),
            'responsible_officer_id.exists' => __('Pegawai Bertanggungjawab yang dipilih tidak sah.'),
            'supporting_officer_id.exists' => __('Pegawai Penyokong yang dipilih tidak sah.'),

            'items.array' => __('Format item peralatan tidak sah.'),
            'items.*.id.exists' => __('Item peralatan sedia ada yang cuba dikemaskini tidak sah untuk permohonan ini bagi item di kedudukan :position.'),
            'items.*.equipment_type.required_if' => __('Jenis Peralatan wajib diisi untuk item di kedudukan :position.'),
            'items.*.equipment_type.in' => __('Jenis Peralatan yang dipilih tidak sah untuk item di kedudukan :position.'),
            'items.*.quantity_requested.required_if' => __('Kuantiti wajib diisi untuk item di kedudukan :position.'),
            'items.*.quantity_requested.integer' => __('Kuantiti mesti nombor bulat untuk item di kedudukan :position.'),
            'items.*.quantity_requested.min' => __('Kuantiti mestilah sekurang-kurangnya 1 untuk item di kedudukan :position.'),
            'items.*.quantity_requested.max' => __('Kuantiti tidak boleh melebihi :max untuk item di kedudukan :position.'),
            'items.*.notes.max' => __('Catatan untuk item di kedudukan :position tidak boleh melebihi :max aksara.'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('items') === null && ! $this->has('items')) {
            $this->merge(['items' => []]);
        }
    }
}
