<?php

namespace App\Livewire\ResourceManagement\EmailAccount;

use App\Models\User;
use App\Models\EmailApplication;
use App\Models\Grade; // For supporting officer grade options
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')] // Ensure your layout file is correct, e.g., resources/views/layouts/app.blade.php
class ApplicationForm extends Component
{
    use AuthorizesRequests;

    public User $user; // For displaying applicant's (logged-in user) details

    // Form properties based on your blade file
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
    public string $supporting_officer_name = '';
    public ?string $supporting_officer_grade = null; // Changed from supporting_officer_grade_id to match blade and MyMail string grades
    public string $supporting_officer_email = '';
    public bool $cert_info_is_true = false;
    public bool $cert_data_usage_agreed = false;
    public bool $cert_email_responsibility_agreed = false;

    // Read-only properties populated from $this->user for display
    public string $applicantName = '';
    public string $applicantPositionAndGrade = '';
    public string $applicantDepartment = '';
    public string $applicantEmail = '';
    public string $applicantPhone = '';

    public array $serviceStatusOptions = [];
    public array $appointmentTypeOptions = [];
    public array $supportingOfficerGradeOptions = [];

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
            'contact_person_name.required_if' => __('Sila nyatakan nama pegawai dihubungi untuk permohonan group e-mel/agensi luar.'),
            'contact_person_email.required_if' => __('Sila nyatakan e-mel pegawai dihubungi untuk permohonan group e-mel/agensi luar.'),
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

    public function mount(EmailApplication $emailApplication = null): void
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser) {
            // CRITICAL: Fetch a fresh instance of the user model with all its attributes
            // and eager-load relationships needed for the read-only applicant details section.
            $this->user = User::with(['position', 'grade', 'department'])->find($loggedInUser->id);
            if (!$this->user) { // Fallback if find fails, though unlikely for auth()->user()
                $this->user = new User(); // Initialize to prevent errors
                session()->flash('error', 'Gagal memuatkan maklumat pengguna sepenuhnya.');
            }
        } else {
            // This case should ideally be prevented by 'auth' middleware.
            $this->user = new User(); // Initialize to prevent errors in populateApplicantDetails
            session()->flash('error', 'Sila log masuk untuk membuat permohonan.');
            // Optionally redirect: return $this->redirectRoute('login', navigate: true);
            return; // Stop further execution if no authenticated user
        }

        $this->populateApplicantDetails(); // Populate read-only applicant info
        $this->loadDropdownOptions();    // Load options for select dropdowns

        if ($emailApplication && $emailApplication->exists) {
            $this->applicationToEdit = $emailApplication;
            $this->authorize('update', $this->applicationToEdit);
            $this->populateFormForEdit();
        } else {
            $this->applicationToEdit = null;
            $this->authorize('create', EmailApplication::class);
            $this->resetFormFields(true); // Pass true to pre-fill from user for new form
        }
    }

    protected function populateApplicantDetails(): void
    {
        if ($this->user && $this->user->exists) { // Check if user model is properly loaded
            $this->applicantName = trim(($this->user->title ?? '') . ' ' . ($this->user->name ?? '')); // Access title here
            $this->applicantPositionAndGrade = trim((optional($this->user->position)->name ?? '') . ' (' . (optional($this->user->grade)->name ?? '') . ')');
            $this->applicantPositionAndGrade = ($this->applicantPositionAndGrade === ' ()' || $this->applicantPositionAndGrade === '()') ? '' : $this->applicantPositionAndGrade;
            $this->applicantDepartment = optional($this->user->department)->name ?? '';
            $this->applicantEmail = $this->user->email ?? ''; // Login email
            $this->applicantPhone = $this->user->mobile_number ?? '';
        }
    }

    protected function populateFormForEdit(): void
    {
        if (!$this->applicationToEdit) return;

        // Use the applicant of the application being edited for pre-filling service status & appointment type
        $applicantOfRecord = $this->applicationToEdit->user()->withDefault()->first() ?? $this->user; // Fallback to current user if relation fails

        $this->service_status_selection = $applicantOfRecord->service_status ?? '';
        $this->appointment_type_selection = $applicantOfRecord->appointment_type ?? '';

        $this->previous_department_name = $this->applicationToEdit->previous_department_name;
        $this->previous_department_email = $this->applicationToEdit->previous_department_email;
        $this->application_reason_notes = $this->applicationToEdit->application_reason_notes;
        $this->proposed_email = $this->applicationToEdit->proposed_email;
        $this->group_email_request_name = $this->applicationToEdit->group_email;
        $this->contact_person_name = $this->applicationToEdit->contact_person_name;
        $this->contact_person_email = $this->applicationToEdit->contact_person_email;
        $this->service_start_date = $this->applicationToEdit->service_start_date ? Carbon::parse($this->applicationToEdit->service_start_date)->format('Y-m-d') : null;
        $this->service_end_date = $this->applicationToEdit->service_end_date ? Carbon::parse($this->applicationToEdit->service_end_date)->format('Y-m-d') : null;
        $this->supporting_officer_name = $this->applicationToEdit->supporting_officer_name;
        $this->supporting_officer_grade = $this->applicationToEdit->supporting_officer_grade; // String grade name
        $this->supporting_officer_email = $this->applicationToEdit->supporting_officer_email;
        $this->cert_info_is_true = $this->applicationToEdit->cert_info_is_true;
        $this->cert_data_usage_agreed = $this->applicationToEdit->cert_data_usage_agreed;
        $this->cert_email_responsibility_agreed = $this->applicationToEdit->cert_email_responsibility_agreed;
    }


    protected function loadDropdownOptions(): void
    {
        $this->serviceStatusOptions = ['' => '- ' . __('Pilih Taraf Perkhidmatan') . ' -'] + User::getServiceStatusOptions();
        $this->appointmentTypeOptions = ['' => '- ' . __('Pilih Pelantikan') . ' -'] + User::getAppointmentTypeOptions();

        // Example grades; ideally fetch from Grade model where level >= min_level or use a config
        // Storing as text values to match `supporting_officer_grade` string field.
        $grades = [
            'Turus III' => 'Turus III', 'Jusa A' => 'Jusa A', 'Jusa B' => 'Jusa B', 'Jusa C' => 'Jusa C',
            '54' => '54', '52' => '52', '48' => '48', '44' => '44', '41' => '41',
            'N19' => 'N19', 'N29' => 'N29', // Common examples
            '9' => '9', // Min supporting grade from MyMail context
        ];
        // You might want to sort these grades or fetch them dynamically.
        // For example: $this->supportingOfficerGradeOptions = Grade::where('level', '>=', 9)->orderBy('name')->pluck('name', 'name')->all();
        $this->supportingOfficerGradeOptions = ['' => '- ' . __('Pilih Gred Penyokong') . ' -'] + $grades;
    }

    protected function rules(): array
    {
        $rules = [
            'service_status_selection' => ['required', Rule::in(array_keys($this->serviceStatusOptions))],
            'appointment_type_selection' => ['required', Rule::in(array_keys($this->appointmentTypeOptions))],
            'application_reason_notes' => ['required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('email_applications', 'proposed_email')
                    ->when($this->applicationToEdit, fn ($rule) => $rule->ignore($this->applicationToEdit->id))
                    ->whereNull('deleted_at'),
                 // Consider additional unique check against users.motac_email if necessary
            ],
            'cert_info_is_true' => ['accepted'],
            'cert_data_usage_agreed' => ['accepted'],
            'cert_email_responsibility_agreed' => ['accepted'],
            'supporting_officer_name' => ['required', 'string', 'max:255'],
            'supporting_officer_grade' => ['required', 'string', Rule::in(array_keys($this->supportingOfficerGradeOptions))],
            'supporting_officer_email' => ['required', 'email', 'max:255'],
        ];

        $rules['previous_department_name'] = $this->shouldShowPreviousDepartmentFields()
            ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
        $rules['previous_department_email'] = $this->shouldShowPreviousDepartmentFields()
            ? ['required', 'email', 'max:255'] : ['nullable', 'email', 'max:255'];

        $rules['service_start_date'] = $this->shouldShowServiceDates()
            ? ['required', 'date'] : ['nullable', 'date'];
        $rules['service_end_date'] = $this->shouldShowServiceDates()
            ? ['required', 'date', 'after_or_equal:service_start_date']
            : ['nullable', 'date', 'after_or_equal:service_start_date'];

        $rules['group_email_request_name'] = ['nullable', 'string', 'max:255'];
        $rules['contact_person_name'] = $this->showGroupEmailFields() // If group fields depend on a condition
            ? ['required_with:group_email_request_name', 'string', 'max:255']
            : ['nullable', 'string', 'max:255'];
        $rules['contact_person_email'] = $this->showGroupEmailFields()
            ? ['required_with:group_email_request_name', 'email', 'max:255']
            : ['nullable', 'email', 'max:255'];

        return $rules;
    }

    public function submitApplication(): void
    {
        $validatedData = $this->validate();

        $commonData = [
            'previous_department_name' => $this->shouldShowPreviousDepartmentFields() ? ($validatedData['previous_department_name'] ?? null) : null,
            'previous_department_email' => $this->shouldShowPreviousDepartmentFields() ? ($validatedData['previous_department_email'] ?? null) : null,
            'service_start_date' => $this->shouldShowServiceDates() ? ($validatedData['service_start_date'] ?? null) : null,
            'service_end_date' => $this->shouldShowServiceDates() ? ($validatedData['service_end_date'] ?? null) : null,
            'application_reason_notes' => $validatedData['application_reason_notes'],
            'proposed_email' => $validatedData['proposed_email'] ?? null,
            'group_email' => $this->showGroupEmailFields() ? ($validatedData['group_email_request_name'] ?? null) : null,
            'contact_person_name' => $this->showGroupEmailFields() ? ($validatedData['contact_person_name'] ?? null) : null,
            'contact_person_email' => $this->showGroupEmailFields() ? ($validatedData['contact_person_email'] ?? null) : null,
            'supporting_officer_name' => $validatedData['supporting_officer_name'] ?? null,
            'supporting_officer_grade' => $validatedData['supporting_officer_grade'] ?? null, // Storing string grade name
            'supporting_officer_email' => $validatedData['supporting_officer_email'] ?? null,
            'cert_info_is_true' => $validatedData['cert_info_is_true'],
            'cert_data_usage_agreed' => $validatedData['cert_data_usage_agreed'],
            'cert_email_responsibility_agreed' => $validatedData['cert_email_responsibility_agreed'],
            'certification_timestamp' => Carbon::now(),
        ];

        try {
            if ($this->applicationToEdit) {
                $this->authorize('update', $this->applicationToEdit);
                $this->applicationToEdit->update($commonData);
                session()->flash('message', ['content' => __('Permohonan berjaya dikemaskini.'), 'level' => 'success']);
            } else {
                $this->authorize('create', EmailApplication::class);
                $applicationData = $commonData + [
                    'user_id' => $this->user->id,
                    'status' => EmailApplication::STATUS_PENDING_SUPPORT,
                ];
                EmailApplication::create($applicationData);
                session()->flash('message', ['content' => __('Permohonan akaun e-mel anda telah berjaya dihantar.'), 'level' => 'success']);
                $this->resetFormFields(true); // Reset and pre-fill for next new application
            }
            $this->dispatch('formSubmitted'); // Optional: For JS listeners
            $this->redirectRoute('email-applications.index', navigate: true);

        } catch (\Exception $e) {
            Log::error('Email Application Submission Error: ' . $e->getMessage(), [
                'exception' => $e, 'formData' => $this->all(),
            ]);
            session()->flash('error', __('Gagal memproses permohonan. Sila semak input anda atau hubungi pentadbir.'));
        }
    }

    private function resetFormFields(bool $prefillFromUser = false): void
    {
        if ($prefillFromUser && $this->user && $this->user->exists && !$this->applicationToEdit) {
            $this->service_status_selection = $this->user->service_status ?? '';
            $this->appointment_type_selection = $this->user->appointment_type ?? '';
        } else if (!$this->applicationToEdit) { // Truly new form, no prefill if no user info
            $this->service_status_selection = '';
            $this->appointment_type_selection = '';
        }
        // If editing, fields are set by populateFormForEdit(), not reset here.

        $this->previous_department_name = null;
        $this->previous_department_email = null;
        $this->service_start_date = null;
        $this->service_end_date = null;
        $this->application_reason_notes = '';
        $this->proposed_email = null;
        $this->group_email_request_name = null;
        $this->contact_person_name = null;
        $this->contact_person_email = null;
        $this->cert_info_is_true = false;
        $this->cert_data_usage_agreed = false;
        $this->cert_email_responsibility_agreed = false;
        $this->supporting_officer_name = null;
        $this->supporting_officer_grade = null;
        $this->supporting_officer_email = null;
        $this->resetValidation();
    }

    // Helper methods for conditional field visibility based on current form selections
    public function shouldShowPreviousDepartmentFields(): bool
    {
        return $this->service_status_selection !== '' && // Ensure a service status is selected
               $this->appointment_type_selection === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN;
    }

    public function showGroupEmailFields(): bool
    {
        // Example: show if service status implies group email or if user is an external agency needing MOTAC backup
        // Or if it's a generic "group email request" section.
        // Based on System Design (Rev. 3) - MyMail form seems to always show these fields
        // but they might only be *required* conditionally.
        // Also related to SERVICE_STATUS_OTHER_AGENCY.
        // The System Design (Rev. 3), Page 7, section 4.2 for email_applications shows 'group_email', 'contact_person_name', 'contact_person_email' as nullable.
        // Let's show if service status is 'other_agency_existing_mailbox', OR if user explicitly wants to provide group email (if that's an option)
        // A simpler approach: always show, validate if main group_email_request_name is filled.
        return $this->service_status_selection === User::SERVICE_STATUS_OTHER_AGENCY; // Example condition
    }

    public function shouldShowServiceDates(): bool
    {
        // Not for 'tetap' (permanent) or 'other_agency_existing_mailbox'
        // Required for contract, intern etc.
        $nonDateServiceStatuses = [
            User::SERVICE_STATUS_TETAP,
            User::SERVICE_STATUS_OTHER_AGENCY,
            '' // No selection yet
        ];
        return !in_array($this->service_status_selection, $nonDateServiceStatuses, true);
    }

    // Livewire lifecycle hooks for dynamic updates if needed
    public function updatedServiceStatusSelection($value): void
    {
        $this->resetValidation('service_status_selection');
        // Potentially reset dependent fields:
        if (!$this->shouldShowServiceDates()) {
            $this->service_start_date = null;
            $this->service_end_date = null;
        }
        if (!$this->showGroupEmailFields()) {
            $this->group_email_request_name = null;
            $this->contact_person_name = null;
            $this->contact_person_email = null;
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
        return view('livewire.resource-management.email-account.application-form')
               ->title($this->applicationToEdit ? __('Kemaskini Permohonan Emel/ID') : __('Borang Permohonan E-mel / ID Pengguna MOTAC'));
    }
}
