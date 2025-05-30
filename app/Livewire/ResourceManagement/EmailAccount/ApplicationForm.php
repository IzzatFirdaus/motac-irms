<?php

namespace App\Livewire\ResourceManagement\EmailAccount;

use App\Models\User;
use App\Models\EmailApplication;
use App\Models\Grade; // For supporting officer grade options
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Added for explicit 404

#[Layout('layouts.app')]
#[Title('Borang Permohonan E-mel / ID Pengguna MOTAC')]
class ApplicationForm extends Component
{
    use AuthorizesRequests;

    public User $user;

    public ?EmailApplication $applicationToEdit = null;
    public string $service_status_selection = '';
    public string $appointment_type_selection = '';
    public ?string $previous_department_name = null;
    public ?string $previous_department_email = null;
    public string $application_reason_notes = '';
    public ?string $proposed_email = null;
    public ?string $group_email_request_name = null;
    public ?string $contact_person_name = null;
    public ?string $contact_person_email = null;
    public ?string $service_start_date = null;
    public ?string $service_end_date = null;
    public ?string $supporting_officer_name = '';
    public ?string $supporting_officer_grade = null;
    public ?string $supporting_officer_email = '';

    public bool $cert_info_is_true = false;
    public bool $cert_data_usage_agreed = false;
    public bool $cert_email_responsibility_agreed = false;

    public string $applicantName = '';
    public string $applicantPositionAndGrade = '';
    public string $applicantDepartment = '';
    public string $applicantEmail = '';
    public string $applicantPhone = '';

    public array $serviceStatusOptions = [];
    public array $appointmentTypeOptions = [];
    public array $supportingOfficerGradeOptions = [];

    public function getPageTitleProperty(): string
    {
        return $this->applicationToEdit ? __('Kemaskini Draf Permohonan Emel/ID') : __('Borang Permohonan E-mel / ID Pengguna MOTAC');
    }

    protected function messages(): array
    {
        return [
            'service_status_selection.required' => __('Sila pilih taraf perkhidmatan.'),
            'appointment_type_selection.required' => __('Sila pilih jenis pelantikan.'),
            'application_reason_notes.required' => __('Sila nyatakan tujuan permohonan.'),
            'application_reason_notes.min' => __('Tujuan permohonan sekurang-kurangnya 10 aksara.'),
            'proposed_email.email' => __('Format cadangan e-mel tidak sah.'),
            'proposed_email.unique' => __('Cadangan e-mel ini telah wujud atau sedang diproses.'),
            'previous_department_name.required_if' => __('Sila nyatakan jabatan terdahulu untuk jenis pelantikan ini.'),
            'previous_department_email.required_if' => __('Sila nyatakan e-mel rasmi jabatan terdahulu untuk jenis pelantikan ini.'),
            'previous_department_email.email' => __('Format e-mel rasmi jabatan terdahulu tidak sah.'),
            'service_start_date.required_if' => __('Sila nyatakan tarikh mula berkhidmat untuk taraf perkhidmatan ini.'),
            'service_start_date.date' => __('Format tarikh mula berkhidmat tidak sah.'),
            'service_end_date.required_if' => __('Sila nyatakan tarikh akhir berkhidmat untuk taraf perkhidmatan ini.'),
            'service_end_date.date' => __('Format tarikh akhir berkhidmat tidak sah.'),
            'service_end_date.after_or_equal' => __('Tarikh akhir berkhidmat mesti selepas atau sama dengan tarikh mula.'),
            'contact_person_name.required_with' => __('Sila nyatakan nama pegawai dihubungi jika nama group e-mel diisi.'),
            'contact_person_email.required_with' => __('Sila nyatakan e-mel pegawai dihubungi jika nama group e-mel diisi.'),
            'contact_person_email.email' => __('Format e-mel pegawai dihubungi tidak sah.'),
            'supporting_officer_name.required' => __('Sila nyatakan nama penuh pegawai penyokong.'),
            'supporting_officer_grade.required' => __('Sila pilih gred pegawai penyokong.'),
            'supporting_officer_email.required' => __('Sila nyatakan e-mel rasmi pegawai penyokong.'),
            'supporting_officer_email.email' => __('Format e-mel pegawai penyokong tidak sah.'),
            'cert_info_is_true.accepted' => __('Anda mesti mengesahkan maklumat permohonan adalah benar.'),
            'cert_data_usage_agreed.accepted' => __('Anda mesti bersetuju dengan penggunaan data untuk pemprosesan.'),
            'cert_email_responsibility_agreed.accepted' => __('Anda mesti bersetuju untuk bertanggungjawab ke atas e-mel.'),
        ];
    }

    /**
     * Mount the component.
     *
     * @param int|null $email_application_id The ID of the email application to edit, or null to create.
     * @return void
     */
    public function mount($email_application_id = null): void
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser) {
            $foundUser = User::with(['position', 'grade', 'department'])->find($loggedInUser->id);
            if ($foundUser && $foundUser->exists) {
                $this->user = $foundUser;
            } else {
                $this->user = new User();
                session()->flash('error', 'Gagal memuatkan maklumat pengguna sepenuhnya. Sila pastikan profil anda lengkap atau hubungi pentadbir.');
                Log::error("ApplicationForm mount: Failed to load full user details for authenticated user ID: " . $loggedInUser->id . ". User not found or 'exists' is false.");
            }
        } else {
            $this->user = new User();
            session()->flash('error', 'Sila log masuk untuk membuat permohonan.');
            // Consider redirecting to login if no user is logged in and this page requires authentication
            // For example: return redirect()->route('login');
        }

        $this->populateApplicantDetails();
        $this->loadDropdownOptions();

        $emailApplicationInstance = null;
        if ($email_application_id) {
            $emailApplicationInstance = EmailApplication::find($email_application_id);
            if (!$emailApplicationInstance) {
                // If an ID was provided for editing but the model was not found, throw 404
                throw (new ModelNotFoundException)->setModel(EmailApplication::class, $email_application_id);
            }
        }

        if ($emailApplicationInstance && $emailApplicationInstance->exists) {
            $this->applicationToEdit = $emailApplicationInstance;
            $this->authorize('update', $this->applicationToEdit);
            $this->populateFormForEdit();
        } else {
            // This is for creating a new application
            $this->applicationToEdit = null;
            $this->authorize('create', EmailApplication::class);
            $this->resetFormFields(true);
        }
    }

    protected function populateApplicantDetails(): void
    {
        if ($this->user && $this->user->exists) {
            $this->applicantName = trim(($this->user->title ?? '') . ' ' . ($this->user->full_name ?? $this->user->name ?? ''));
            $positionName = optional($this->user->position)->name;
            $gradeName = optional($this->user->grade)->name;
            $this->applicantPositionAndGrade = trim(($positionName ? $positionName : '') . ($positionName && $gradeName ? ' (' : '') . ($gradeName ? $gradeName : '') . ($positionName && $gradeName ? ')' : ''));
            $this->applicantDepartment = optional($this->user->department)->name ?? '';
            $this->applicantEmail = $this->user->email ?? '';
            $this->applicantPhone = $this->user->mobile_number ?? '';
        } else {
            // Clear applicant details if user is not valid/available
            $this->applicantName = '';
            $this->applicantPositionAndGrade = '';
            $this->applicantDepartment = '';
            $this->applicantEmail = '';
            $this->applicantPhone = '';
        }
    }

    protected function populateFormForEdit(): void
    {
        if (!$this->applicationToEdit) return;

        // Populate from user's current profile for these two, as per original logic.
        // If these should be from the application snapshot, they'd need to be stored on EmailApplication.
        $this->service_status_selection = $this->user->service_status ?? '';
        $this->appointment_type_selection = $this->user->appointment_type ?? '';

        $this->previous_department_name = $this->applicationToEdit->previous_department_name;
        $this->previous_department_email = $this->applicationToEdit->previous_department_email;
        $this->application_reason_notes = $this->applicationToEdit->application_reason_notes;
        $this->proposed_email = $this->applicationToEdit->proposed_email;
        $this->group_email_request_name = $this->applicationToEdit->group_email; // Matches EmailApplication model field
        $this->contact_person_name = $this->applicationToEdit->contact_person_name;
        $this->contact_person_email = $this->applicationToEdit->contact_person_email;
        $this->service_start_date = $this->applicationToEdit->service_start_date ? Carbon::parse($this->applicationToEdit->service_start_date)->format('Y-m-d') : null;
        $this->service_end_date = $this->applicationToEdit->service_end_date ? Carbon::parse($this->applicationToEdit->service_end_date)->format('Y-m-d') : null;
        $this->supporting_officer_name = $this->applicationToEdit->supporting_officer_name;
        $this->supporting_officer_grade = $this->applicationToEdit->supporting_officer_grade;
        $this->supporting_officer_email = $this->applicationToEdit->supporting_officer_email;
        $this->cert_info_is_true = (bool)$this->applicationToEdit->cert_info_is_true;
        $this->cert_data_usage_agreed = (bool)$this->applicationToEdit->cert_data_usage_agreed;
        $this->cert_email_responsibility_agreed = (bool)$this->applicationToEdit->cert_email_responsibility_agreed;
    }

    protected function loadDropdownOptions(): void
    {
        $this->serviceStatusOptions = ['' => '- ' . __('Pilih Taraf Perkhidmatan') . ' -'] + User::getServiceStatusOptions();
        $this->appointmentTypeOptions = ['' => '- ' . __('Pilih Pelantikan') . ' -'] + User::getAppointmentTypeOptions();

        $fixedSupportingGrades = [
            'Turus III' => 'Turus III', 'Jusa A' => 'Jusa A', 'Jusa B' => 'Jusa B', 'Jusa C' => 'Jusa C',
            '54' => '54', '52' => '52', '48' => '48', '44' => '44', '41' => '41',
            '38' => '38', '32' => '32', '29' => '29',
            '26' => '26', '22' => '22', '19' => '19',
            '14' => '14', '13' => '13', '12' => '12', '10' => '10', '9' => '9',
        ]; // This should ideally come from a config or the Grade model
        $this->supportingOfficerGradeOptions = ['' => '- ' . __('Pilih Gred Penyokong') . ' -'] + $fixedSupportingGrades;
    }

    protected function rules(): array
    {
        $rules = [
            'service_status_selection' => ['required', Rule::in(array_keys($this->serviceStatusOptions))],
            'appointment_type_selection' => ['required', Rule::in(array_keys($this->appointmentTypeOptions))],
            'application_reason_notes' => ['required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('email_applications', 'proposed_email')
                    ->when($this->applicationToEdit, fn ($rule) => $rule->ignore($this->applicationToEdit->id))
                    ->whereNull('deleted_at'),
            ],
            'cert_info_is_true' => ['accepted'],
            'cert_data_usage_agreed' => ['accepted'],
            'cert_email_responsibility_agreed' => ['accepted'],
            'supporting_officer_name' => ['required', 'string', 'max:255'],
            'supporting_officer_grade' => ['required', 'string', Rule::in(array_keys($this->supportingOfficerGradeOptions))],
            'supporting_officer_email' => ['required', 'email', 'max:255'],
        ];
        $rules['previous_department_name'] = $this->shouldShowPreviousDepartmentFields() ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
        $rules['previous_department_email'] = $this->shouldShowPreviousDepartmentFields() ? ['required', 'email', 'max:255'] : ['nullable', 'email', 'max:255'];
        $rules['service_start_date'] = $this->shouldShowServiceDates() ? ['required', 'date'] : ['nullable', 'date'];
        $rules['service_end_date'] = $this->shouldShowServiceDates() ? ['required', 'date', 'after_or_equal:service_start_date'] : ['nullable', 'date', 'after_or_equal:service_start_date'];
        $rules['group_email_request_name'] = ['nullable', 'string', 'max:255'];
        $rules['contact_person_name'] = ['nullable', 'required_with:group_email_request_name', 'string', 'max:255'];
        $rules['contact_person_email'] = ['nullable', 'required_with:group_email_request_name', 'email', 'max:255'];
        return $rules;
    }

    public function saveApplication(): void
    {
        $validatedData = $this->validate();
        $commonData = [
            'previous_department_name' => $this->shouldShowPreviousDepartmentFields() ? ($validatedData['previous_department_name'] ?? null) : null,
            'previous_department_email' => $this->shouldShowPreviousDepartmentFields() ? ($validatedData['previous_department_email'] ?? null) : null,
            'service_start_date' => $this->shouldShowServiceDates() ? ($validatedData['service_start_date'] ?? null) : null,
            'service_end_date' => $this->shouldShowServiceDates() ? ($validatedData['service_end_date'] ?? null) : null,
            'application_reason_notes' => $validatedData['application_reason_notes'],
            'proposed_email' => $validatedData['proposed_email'] ?? null,
            'group_email' => $validatedData['group_email_request_name'] ?? null,
            'contact_person_name' => $validatedData['contact_person_name'] ?? null,
            'contact_person_email' => $validatedData['contact_person_email'] ?? null,
            'supporting_officer_name' => $validatedData['supporting_officer_name'] ?? null,
            'supporting_officer_grade' => $validatedData['supporting_officer_grade'] ?? null,
            'supporting_officer_email' => $validatedData['supporting_officer_email'] ?? null,
            'cert_info_is_true' => $validatedData['cert_info_is_true'],
            'cert_data_usage_agreed' => $validatedData['cert_data_usage_agreed'],
            'cert_email_responsibility_agreed' => $validatedData['cert_email_responsibility_agreed'],
        ];
        if ($commonData['cert_info_is_true'] && $commonData['cert_data_usage_agreed'] && $commonData['cert_email_responsibility_agreed']) {
            $commonData['certification_timestamp'] = Carbon::now();
        } else {
            $commonData['certification_timestamp'] = null;
        }

        try {
            if ($this->applicationToEdit) {
                $this->authorize('update', $this->applicationToEdit);
                if (!$this->applicationToEdit->isDraft()) { // Assuming isDraft() method exists on EmailApplication
                     session()->flash('error', __('Hanya draf permohonan yang boleh dikemaskini. Permohonan ini telah dihantar atau diproses.'));
                     return;
                }
                $this->applicationToEdit->update($commonData);
                session()->flash('message', ['content' => __('Draf permohonan berjaya dikemaskini.'), 'level' => 'success']);
                $this->redirectRoute('email-applications.show', ['email_application' => $this->applicationToEdit->id], navigate: true);
            } else {
                $this->authorize('create', EmailApplication::class);
                $applicationData = $commonData + [
                    'user_id' => $this->user->id, // Make sure $this->user is valid
                    'status' => EmailApplication::STATUS_DRAFT,
                ];
                $newApplication = EmailApplication::create($applicationData);
                session()->flash('message', ['content' => __('Draf permohonan e-mel berjaya disimpan. Anda boleh melihat dan menghantarnya dari senarai permohonan.'), 'level' => 'success']);
                $this->redirectRoute('email-applications.show', ['email_application' => $newApplication->id], navigate: true);
            }
        } catch (\Exception $e) {
            Log::error('Email Application Save Error: ' . $e->getMessage(), ['exception' => $e, 'formData' => $this->all()]);
            session()->flash('error', __('Gagal menyimpan permohonan. Sila semak input anda atau hubungi pentadbir.'));
        }
    }

    private function resetFormFields(bool $prefillFromUser = false): void
    {
        if ($prefillFromUser && $this->user && $this->user->exists && !$this->applicationToEdit) {
            $this->service_status_selection = $this->user->service_status ?? '';
            $this->appointment_type_selection = $this->user->appointment_type ?? '';
        } else if (!$this->applicationToEdit) {
            $this->service_status_selection = '';
            $this->appointment_type_selection = '';
        }

        // Fields that should be reset for a new form or if not editing
        if (!$this->applicationToEdit) {
            $this->previous_department_name = null;
            $this->previous_department_email = null;
            $this->service_start_date = null;
            $this->service_end_date = null;
            $this->application_reason_notes = '';
            $this->proposed_email = null;
            $this->group_email_request_name = null;
            $this->contact_person_name = null;
            $this->contact_person_email = null;
            $this->supporting_officer_name = null;
            $this->supporting_officer_grade = null;
            $this->supporting_officer_email = null;
            $this->cert_info_is_true = false;
            $this->cert_data_usage_agreed = false;
            $this->cert_email_responsibility_agreed = false;
        }
        $this->resetValidation();
    }

    public function shouldShowPreviousDepartmentFields(): bool
    {
        return $this->appointment_type_selection === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN;
    }

    public function showGroupEmailFields(): bool
    {
        return true; // Or add logic based on other selections if needed
    }

    public function shouldShowServiceDates(): bool
    {
        $statusesWithoutDates = [User::SERVICE_STATUS_TETAP, ''];
        return !in_array($this->service_status_selection, $statusesWithoutDates, true);
    }

    public function updatedServiceStatusSelection($value): void
    {
        $this->resetValidation('service_status_selection');
        if (!$this->shouldShowServiceDates()) {
            $this->service_start_date = null;
            $this->service_end_date = null;
        }
        Log::debug("Service status selected in ApplicationForm: " . $value);
    }

    public function updatedAppointmentTypeSelection($value): void
    {
        $this->resetValidation('appointment_type_selection');
        if (!$this->shouldShowPreviousDepartmentFields()) {
            $this->previous_department_name = null;
            $this->previous_department_email = null;
        }
        Log::debug("Appointment type selected in ApplicationForm: " . $value);
    }

    public function render()
    {
        return view('livewire.resource-management.email-account.application-form');
    }
}
