<?php

namespace App\Http\Requests;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreEmailApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->can('create', EmailApplication::class);
    }

    public function rules(): array
    {
        // Ensure User model has getServiceStatusKeys() and getAppointmentTypeKeys()
        $serviceStatusKeys = method_exists(User::class, 'getServiceStatusKeys') ? User::getServiceStatusKeys() : array_keys(User::$SERVICE_STATUS_LABELS ?? []);
        $appointmentTypeKeys = method_exists(User::class, 'getAppointmentTypeKeys') ? User::getAppointmentTypeKeys() : array_keys(User::$APPOINTMENT_TYPE_LABELS ?? []);

        return [
            // Applicant snapshot fields
            'applicant_title' => ['nullable', 'string', 'max:50', Rule::in(array_keys(User::getTitleOptions()))],
            'applicant_name' => ['required', 'string', 'max:255'],
            'applicant_identification_number' => ['required', 'string', 'max:50'], // Consider NRIC regex: 'regex:/^\d{6}-\d{2}-\d{4}$/'
            'applicant_passport_number' => ['nullable', 'string', 'max:50'],
            'applicant_jawatan_gred' => ['required', 'string', 'max:255'], // Should combine position & grade
            'applicant_bahagian_unit' => ['required', 'string', 'max:255'], // Department/Unit
            'applicant_level_aras' => ['nullable', 'string', 'max:50', Rule::in(array_keys(User::getLevelOptions()))],
            'applicant_mobile_number' => ['required', 'string', 'max:50'], // Consider E.164 format
            'applicant_personal_email' => ['required', 'email:rfc,dns', 'max:255'],

            // Core application fields
            'service_status' => ['required', 'string', Rule::in($serviceStatusKeys)],
            'appointment_type' => ['required', 'string', Rule::in($appointmentTypeKeys)],

            'previous_department_name' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN),
                'nullable', 'string', 'max:255'
            ],
            'previous_department_email' => [
                Rule::requiredIf(fn () => $this->input('appointment_type') === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN),
                'nullable', 'email:rfc,dns', 'max:255'
            ],
            'service_start_date' => ['nullable', 'date_format:Y-m-d', Rule::requiredIf(fn () => in_array($this->input('service_status'), [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]))],
            'service_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:service_start_date', Rule::requiredIf(fn () => in_array($this->input('service_status'), [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]))],

            'application_reason_notes' => ['required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable', 'string', 'max:255',
                 // Validate only if it doesn't look like an ID (contains @)
                 Rule::when(fn ($input) => !empty($input->proposed_email) && str_contains((string)$input->proposed_email, '@'), [
                    'email:rfc,dns',
                    Rule::unique('email_applications', 'proposed_email')->ignore($this->email_application?->id)
                        ->whereNull('deleted_at'),
                    Rule::unique('users', 'email')->ignore($this->user()?->id) // Also check against users table if it's a new primary email
                        ->whereNull('deleted_at'),
                    Rule::unique('users', 'motac_email')->ignore($this->user()?->id)
                        ->whereNull('deleted_at')
                 ]),
                 // Validate as potential User ID (alphanumeric, dots, underscores) if no @
                 Rule::when(fn ($input) => !empty($input->proposed_email) && !str_contains((string)$input->proposed_email, '@'), [
                    'regex:/^[a-zA-Z0-9._-]+$/',
                    Rule::unique('email_applications', 'final_assigned_user_id')->ignore($this->email_application?->id) // Check if proposed ID is already assigned
                        ->whereNull('deleted_at'),
                    Rule::unique('users', 'user_id_assigned')->ignore($this->user()?->id) // Also check against users table user_id_assigned
                        ->whereNull('deleted_at')
                 ]),
            ],
            'group_email' => ['nullable', 'email:rfc,dns', 'max:255', Rule::unique('email_applications', 'group_email')->ignore($this->email_application?->id)->whereNull('deleted_at')],
            'contact_person_name' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => !empty($this->input('group_email')))],
            'contact_person_email' => ['nullable', 'email:rfc,dns', 'max:255', Rule::requiredIf(fn () => !empty($this->input('group_email')))],

            'supporting_officer_id' => ['nullable', 'integer', 'exists:users,id'],
            'supporting_officer_name' => [Rule::requiredIf(empty($this->supporting_officer_id)), 'nullable', 'string', 'max:255'],
            'supporting_officer_grade' => [Rule::requiredIf(empty($this->supporting_officer_id)), 'nullable', 'string', 'max:50'],
            'supporting_officer_email' => [Rule::requiredIf(empty($this->supporting_officer_id)), 'nullable', 'email:rfc,dns', 'max:255'],

            'cert_info_is_true' => ['required', 'accepted'],
            'cert_data_usage_agreed' => ['required', 'accepted'],
            'cert_email_responsibility_agreed' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_title.in' => __('Gelaran yang dipilih tidak sah.'),
            'applicant_name.required' => __('Nama Pemohon wajib diisi.'),
            'applicant_identification_number.required' => __('No. Pengenalan Pemohon wajib diisi.'),
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
            'previous_department_name.required_if' => __('Nama Jabatan Terdahulu wajib diisi jika Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'previous_department_email.required_if' => __('E-mel Jabatan Terdahulu wajib diisi jika Pelantikan adalah Kenaikan Pangkat/Pertukaran.'),
            'service_start_date.required_if' => __('Tarikh Mula Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_end_date.required_if' => __('Tarikh Akhir Khidmat wajib diisi untuk taraf perkhidmatan ini.'),
            'service_end_date.after_or_equal' => __('Tarikh Akhir Khidmat mesti selepas atau sama dengan Tarikh Mula Khidmat.'),


            'application_reason_notes.required' => __('Tujuan Permohonan / Catatan wajib diisi.'),
            'application_reason_notes.min' => __('Tujuan Permohonan / Catatan mesti sekurang-kurangnya :min aksara.'),
            'proposed_email.email' => __('Format Cadangan E-mel tidak sah.'),
            'proposed_email.unique' => __('Cadangan E-mel/ID ini telah wujud.'),
            'proposed_email.regex' => __('Format Cadangan ID Pengguna tidak sah (hanya huruf, nombor, titik, garis bawah, sengkang dibenarkan).'),
            'group_email.email' => __('Format Group E-mel tidak sah.'),
            'group_email.unique' => __('Group E-mel ini telah wujud.'),
            'contact_person_name.required_if' => __('Nama Admin/EO/CC Kumpulan wajib diisi jika memohon e-mel kumpulan.'),
            'contact_person_email.required_if' => __('E-mel Admin/EO/CC Kumpulan wajib diisi jika memohon e-mel kumpulan.'),
            'contact_person_email.email' => __('Format E-mel Admin/EO/CC tidak sah.'),


            'supporting_officer_name.required_if' => __('Nama Pegawai Penyokong wajib diisi jika tidak memilih dari sistem.'),
            'supporting_officer_grade.required_if' => __('Gred Pegawai Penyokong wajib diisi jika tidak memilih dari sistem.'),
            'supporting_officer_email.required_if' => __('E-mel Pegawai Penyokong wajib diisi jika tidak memilih dari sistem.'),
            'supporting_officer_email.email' => __('Format E-mel Pegawai Penyokong tidak sah.'),

            'cert_info_is_true.accepted' => __('Anda mesti mengesahkan bahawa semua maklumat adalah BENAR.'),
            'cert_data_usage_agreed.accepted' => __('Anda mesti BERSETUJU maklumat diguna pakai oleh BPM.'),
            'cert_email_responsibility_agreed.accepted' => __('Anda mesti BERSETUJU untuk bertanggungjawab ke atas e-mel.'),
        ];
    }
}
