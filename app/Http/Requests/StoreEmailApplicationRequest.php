<?php

namespace App\Http\Requests;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmailApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        if (!$user) {
            return false;
        }
        // System Design: EmailApplicationPolicy for 'create'
        return $user->can('create', EmailApplication::class);
    }

    public function rules(): array
    {
        // System Design: Dropdown options based on MyMail, use User model static methods
        $serviceStatusKeys = method_exists(User::class, 'getServiceStatusOptions') ?
            array_keys(User::getServiceStatusOptions()) : [];
        $appointmentTypeKeys = method_exists(User::class, 'getAppointmentTypeOptions') ?
            array_keys(User::getAppointmentTypeOptions()) : [];
        $titleOptionsKeys = method_exists(User::class, 'getTitleOptions') ?
            array_keys(User::getTitleOptions()) : [];
        $levelOptionsKeys = method_exists(User::class, 'getLevelOptions') ?
            array_keys(User::getLevelOptions()) : [];

        $rules = [
            // Applicant snapshot fields. These are sent by the Livewire component based on MyMail form.
            // Validate them here if they are used by the service layer before actual User model creation/update.
            // If the EmailApplication model stores these as a snapshot, ensure its $fillable allows them.
            'applicant_title' => ['nullable', 'string', 'max:50', Rule::in($titleOptionsKeys)],
            'applicant_name' => ['required', 'string', 'max:255'],
            'applicant_identification_number' => ['required', 'string', 'max:50', 'regex:/^\d{6}-\d{2}-\d{4}$/'],
            'applicant_passport_number' => ['nullable', 'string', 'max:50'],
            'applicant_jawatan_gred' => ['required', 'string', 'max:255'],
            'applicant_bahagian_unit' => ['required', 'string', 'max:255'],
            'applicant_level_aras' => ['nullable', 'string', 'max:50', Rule::in($levelOptionsKeys)],
            'applicant_mobile_number' => ['required', 'string', 'max:50'],
            'applicant_personal_email' => ['required', 'email:rfc,dns', 'max:255'],

            // Core application fields
            'service_status' => ['required', 'string', Rule::in($serviceStatusKeys)],
            'appointment_type' => ['required', 'string', Rule::in($appointmentTypeKeys)],

            'previous_department_name' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN), // User model constant [cite: 107]
                'nullable', 'string', 'max:255'
            ],
            'previous_department_email' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN),
                'nullable', 'email:rfc,dns', 'max:255'
            ],
            // Service start/end dates conditional on service_status [cite: 103]
            'service_start_date' => ['nullable', 'date_format:Y-m-d', Rule::requiredIf(fn () => in_array($this->input('service_status'), [
                User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI // User model constants
            ]))],
            'service_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:service_start_date', Rule::requiredIf(fn () => in_array($this->input('service_status'), [
                User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI
            ]))],

            'application_reason_notes' => ['required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable', 'string', 'max:255',
                 Rule::when(fn ($input) => !empty($input->proposed_email) && str_contains((string)$input->proposed_email, '@'), [
                    'email:rfc,dns',
                    Rule::unique('email_applications', 'proposed_email')->whereNull('deleted_at'),
                    Rule::unique('users', 'email')->whereNull('deleted_at'),
                    Rule::unique('users', 'motac_email')->whereNull('deleted_at')
                 ]),
                 Rule::when(fn ($input) => !empty($input->proposed_email) && !str_contains((string)$input->proposed_email, '@'), [
                    'regex:/^[a-zA-Z0-9._-]+$/',
                    // For a new application, proposed ID should not clash with any existing final assigned ID or user_id_assigned
                    Rule::unique('email_applications', 'final_assigned_user_id')->whereNull('deleted_at'),
                    Rule::unique('users', 'user_id_assigned')->whereNull('deleted_at')
                 ]),
            ],
            'group_email' => [Rule::requiredIf(fn() => $this->input('is_group_email_request') == true), 'nullable', 'email:rfc,dns', 'max:255', Rule::unique('email_applications', 'group_email')->whereNull('deleted_at')],
            'contact_person_name' => [Rule::requiredIf(fn () => $this->input('is_group_email_request') == true), 'nullable', 'string', 'max:255'],
            'contact_person_email' => [Rule::requiredIf(fn () => $this->input('is_group_email_request') == true), 'nullable', 'email:rfc,dns', 'max:255'],

            'supporting_officer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'supporting_officer_name' => [Rule::requiredIf(empty($this->input('supporting_officer_id'))), 'nullable', 'string', 'max:255'],
            'supporting_officer_grade' => [Rule::requiredIf(empty($this->input('supporting_officer_id'))), 'nullable', 'string', 'max:50'], // MyMail form structure
            'supporting_officer_email' => [Rule::requiredIf(empty($this->input('supporting_officer_id'))), 'nullable', 'email:rfc,dns', 'max:255'],

            'cert_info_is_true' => ['required', 'accepted'],
            'cert_data_usage_agreed' => ['required', 'accepted'],
            'cert_email_responsibility_agreed' => ['required', 'accepted'],
        ];
        return $rules;
    }

    public function messages(): array
    {
        return [
            'applicant_title.in' => __('Gelaran yang dipilih tidak sah.'),
            'applicant_name.required' => __('Nama Pemohon wajib diisi.'),
            'applicant_identification_number.required' => __('No. Pengenalan Pemohon wajib diisi.'),
            'applicant_identification_number.regex' => __('Format No. Pengenalan tidak sah. Gunakan format XXXXXX-XX-XXXX.'),
            'applicant_jawatan_gred.required' => __('Jawatan & Gred Pemohon wajib diisi.'),
            'applicant_bahagian_unit.required' => __('Bahagian/Unit Pemohon wajib diisi.'),
            'applicant_level_aras.in' => __('Aras yang dipilih tidak sah.'),
            'applicant_mobile_number.required' => __('No. Telefon Bimbit Pemohon wajib diisi.'),
            'applicant_personal_email.required' => __('E-mel Peribadi Pemohon wajib diisi.'),
            'applicant_personal_email.email' => __('Format E-mel Peribadi Pemohon tidak sah.'),

            'service_status.required' => __('Sila pilih Taraf Perkhidmatan.'),
            'service_status.in' => __('Taraf Perkhidmatan yang dipilih tidak sah.'),
            'appointment_type.required' => __('Sila pilih Jenis Pelantikan.'),
            'appointment_type.in' => __('Jenis Pelantikan yang dipilih tidak sah.'),

            'previous_department_name.required_if' => __('Nama Jabatan Terdahulu wajib diisi jika Jenis Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'previous_department_email.required_if' => __('E-mel Jabatan Terdahulu wajib diisi jika Jenis Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'previous_department_email.email' => __('Format E-mel Jabatan Terdahulu tidak sah.'),
            'service_start_date.required_if' => __('Tarikh Mula Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_start_date.date_format' => __('Format Tarikh Mula Khidmat tidak sah (YYYY-MM-DD).'),
            'service_end_date.required_if' => __('Tarikh Akhir Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_end_date.date_format' => __('Format Tarikh Akhir Khidmat tidak sah (YYYY-MM-DD).'),
            'service_end_date.after_or_equal' => __('Tarikh Akhir Khidmat mesti selepas atau sama dengan Tarikh Mula Khidmat.'),

            'application_reason_notes.required' => __('Tujuan Permohonan / Catatan wajib diisi.'),
            'application_reason_notes.min' => __('Tujuan Permohonan / Catatan mesti sekurang-kurangnya :min aksara.'),
            'proposed_email.email' => __('Format Cadangan E-mel tidak sah.'),
            'proposed_email.unique' => __('Cadangan E-mel atau ID Pengguna ini telah wujud dalam sistem.'),
            'proposed_email.regex' => __('Format Cadangan ID Pengguna tidak sah (hanya dibenarkan huruf, nombor, titik, garis bawah, sengkang).'),
            'group_email.required_if' => __('Alamat Group E-mel wajib diisi jika memohon Group E-mel.'),
            'group_email.email' => __('Format Group E-mel tidak sah.'),
            'group_email.unique' => __('Alamat Group E-mel ini telah wujud.'),
            'contact_person_name.required_if' => __('Nama Admin/EO/CC Group wajib diisi jika memohon Group E-mel.'),
            'contact_person_email.required_if' => __('E-mel Admin/EO/CC Group wajib diisi jika memohon Group E-mel.'),
            'contact_person_email.email' => __('Format E-mel Admin/EO/CC Group tidak sah.'),

            'supporting_officer_id.exists' => __('Pegawai Penyokong yang dipilih dari sistem tidak sah.'),
            'supporting_officer_name.required_if' => __('Nama Pegawai Penyokong (Manual) wajib diisi jika tidak memilih dari senarai sistem.'),
            'supporting_officer_grade.required_if' => __('Gred Pegawai Penyokong (Manual) wajib diisi jika tidak memilih dari senarai sistem.'),
            'supporting_officer_email.required_if' => __('E-mel Pegawai Penyokong (Manual) wajib diisi jika tidak memilih dari senarai sistem.'),
            'supporting_officer_email.email' => __('Format E-mel Pegawai Penyokong (Manual) tidak sah.'),

            'cert_info_is_true.accepted' => __('Anda mesti mengesahkan bahawa semua maklumat adalah BENAR.'),
            'cert_data_usage_agreed.accepted' => __('Anda mesti BERSETUJU maklumat diguna pakai oleh Bahagian Pengurusan Maklumat.'),
            'cert_email_responsibility_agreed.accepted' => __('Anda mesti BERSETUJU untuk bertanggungjawab ke atas penggunaan e-mel.'),
        ];
    }
}
