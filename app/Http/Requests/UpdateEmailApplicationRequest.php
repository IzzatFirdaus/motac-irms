<?php

namespace App\Http\Requests;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var EmailApplication|null $emailApplication */
        $emailApplication = $this->route('emailApplication'); // Ensure route parameter name is 'emailApplication'
        /** @var User|null $user */
        $user = $this->user();

        // System Design: EmailApplicationPolicy for 'update'
        return $user && $emailApplication && $user->can('update', $emailApplication);
    }

    public function rules(): array
    {
        /** @var EmailApplication|null $currentApplication */
        $currentApplication = $this->route('emailApplication');
        $currentApplicationId = $currentApplication?->id;
        $applicantUserId = $currentApplication?->user_id;

        $serviceStatusKeys = method_exists(User::class, 'getServiceStatusOptions') ?
            array_keys(User::getServiceStatusOptions()) : [];
        $appointmentTypeKeys = method_exists(User::class, 'getAppointmentTypeOptions') ?
            array_keys(User::getAppointmentTypeOptions()) : [];

        return [
            'service_status' => ['sometimes', 'required', 'string', Rule::in($serviceStatusKeys)],
            'appointment_type' => ['sometimes', 'required', 'string', Rule::in($appointmentTypeKeys)],

            'previous_department_name' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN),
                'nullable', 'string', 'max:255',
            ],
            'previous_department_email' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN),
                'nullable', 'email:rfc,dns', 'max:255',
            ],
            'service_start_date' => ['nullable', 'date_format:Y-m-d', Rule::requiredIf(fn () => in_array($this->input('service_status'), [
                User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI,
            ]))],
            'service_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:service_start_date', Rule::requiredIf(fn () => in_array($this->input('service_status'), [
                User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI,
            ]))],

            'application_reason_notes' => ['sometimes', 'required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable', 'string', 'max:255',
                Rule::when(fn ($input) => ! empty($input->proposed_email) && str_contains((string) $input->proposed_email, '@'), [
                    'email:rfc,dns',
                    Rule::unique('email_applications', 'proposed_email')->ignore($currentApplicationId)->whereNull('deleted_at'),
                    Rule::unique('users', 'email')->where(fn ($query) => $applicantUserId ? $query->where('id', '!=', $applicantUserId) : $query)->whereNull('deleted_at'),
                    Rule::unique('users', 'motac_email')->where(fn ($query) => $applicantUserId ? $query->where('id', '!=', $applicantUserId) : $query)->whereNull('deleted_at'),
                ]),
                Rule::when(fn ($input) => ! empty($input->proposed_email) && ! str_contains((string) $input->proposed_email, '@'), [
                    'regex:/^[a-zA-Z0-9._-]+$/',
                    Rule::unique('email_applications', 'final_assigned_user_id')->ignore($currentApplicationId)->whereNull('deleted_at'), // Check against other apps
                    Rule::unique('users', 'user_id_assigned')->where(fn ($query) => $applicantUserId ? $query->where('id', '!=', $applicantUserId) : $query)->whereNull('deleted_at'),
                ]),
            ],
            'group_email' => ['nullable', 'email:rfc,dns', 'max:255', Rule::unique('email_applications', 'group_email')->ignore($currentApplicationId)->whereNull('deleted_at')],
            'contact_person_name' => [Rule::requiredIf(fn () => ! empty($this->input('group_email'))), 'nullable', 'string', 'max:255'],
            'contact_person_email' => [Rule::requiredIf(fn () => ! empty($this->input('group_email'))), 'nullable', 'email:rfc,dns', 'max:255'],

            'supporting_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'supporting_officer_name' => [Rule::requiredIf(empty($this->input('supporting_officer_id')) && ($this->filled('supporting_officer_grade') || $this->filled('supporting_officer_email'))), 'nullable', 'string', 'max:255'],
            'supporting_officer_grade' => [Rule::requiredIf(empty($this->input('supporting_officer_id')) && ($this->filled('supporting_officer_name') || $this->filled('supporting_officer_email'))), 'nullable', 'string', 'max:50'],
            'supporting_officer_email' => [Rule::requiredIf(empty($this->input('supporting_officer_id')) && ($this->filled('supporting_officer_name') || $this->filled('supporting_officer_grade'))), 'nullable', 'email:rfc,dns', 'max:255'],

            'cert_info_is_true' => ['sometimes', 'boolean'],
            'cert_data_usage_agreed' => ['sometimes', 'boolean'],
            'cert_email_responsibility_agreed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_status.required' => __('Sila pilih Taraf Perkhidmatan.'),
            'appointment_type.required' => __('Sila pilih Jenis Pelantikan.'),
            'previous_department_name.required_if' => __('Nama Jabatan Terdahulu wajib diisi jika Jenis Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'previous_department_email.required_if' => __('E-mel Jabatan Terdahulu wajib diisi jika Jenis Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'service_start_date.required_if' => __('Tarikh Mula Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_end_date.required_if' => __('Tarikh Akhir Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_end_date.after_or_equal' => __('Tarikh Akhir Khidmat mesti selepas atau sama dengan Tarikh Mula Khidmat.'),
            'application_reason_notes.required' => __('Tujuan Permohonan / Catatan wajib diisi.'),
            'application_reason_notes.min' => __('Tujuan Permohonan / Catatan mesti sekurang-kurangnya :min aksara.'),
            'proposed_email.unique' => __('Cadangan E-mel atau ID Pengguna ini telah wujud dalam sistem.'),
            'proposed_email.regex' => __('Format Cadangan ID Pengguna tidak sah.'),
            'group_email.unique' => __('Alamat Group E-mel ini telah wujud.'),
            'contact_person_name.required_if' => __('Nama Admin/EO/CC Group wajib diisi jika memohon Group E-mel.'),
            'contact_person_email.required_if' => __('E-mel Admin/EO/CC Group wajib diisi jika memohon Group E-mel.'),
            'supporting_officer_name.required_if' => __('Nama Pegawai Penyokong (Manual) wajib diisi jika butiran manual lain diisi dan ID tidak dipilih.'),
            'supporting_officer_grade.required_if' => __('Gred Pegawai Penyokong (Manual) wajib diisi jika butiran manual lain diisi dan ID tidak dipilih.'),
            'supporting_officer_email.required_if' => __('E-mel Pegawai Penyokong (Manual) wajib diisi jika butiran manual lain diisi dan ID tidak dipilih.'),
        ];
    }
}
