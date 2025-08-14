<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\EmailApplication;
use App\Models\Grade; // Using MOTAC User model
use App\Models\User; // For supporting officer grade options if dynamic from DB
use App\Services\EmailApplicationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection; // Renamed to avoid conflict
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout; // For layout definition
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')] // Bootstrap main layout
final class EmailApplicationForm extends Component
{
    use AuthorizesRequests;

    public ?EmailApplication $application = null;

    public bool $isEdit = false;

    public ?int $applicationId = null;

    // Applicant details
    public ?string $applicant_title = '';

    public ?string $applicant_name = '';

    public ?string $applicant_identification_number = '';

    public ?string $applicant_passport_number = '';

    public ?string $applicant_jawatan_gred = '';

    public ?string $applicant_bahagian_unit = '';

    public ?string $applicant_level_aras = '';

    public ?string $applicant_mobile_number = '';

    public ?string $applicant_personal_email = '';

    // Form fields
    public string $service_status = '';

    public string $appointment_type = '';

    public ?string $previous_department_name = '';

    public ?string $previous_department_email = '';

    public ?string $service_start_date = null;

    public ?string $service_end_date = null;

    public string $purpose = '';

    public ?string $proposed_email = null;

    public bool $is_group_email_request = false;

    public ?string $group_email = null;

    public ?string $group_admin_name = null;

    public ?string $group_admin_email = null;

    // Supporting Officer
    public ?int $supporting_officer_id = null;

    public ?string $manual_supporting_officer_name = '';

    public ?string $manual_supporting_officer_grade_key = '';

    public ?string $manual_supporting_officer_email = '';

    // Certification
    public bool $cert_info_is_true = false;

    public bool $cert_data_usage_agreed = false;

    public bool $cert_email_responsibility_agreed = false;

    // Admin-specific
    public string $current_status_key = '';

    public string $editable_status_for_admin_key = '';

    public ?string $rejection_reason = null;

    public ?string $final_assigned_email = null;

    public ?string $final_assigned_user_id = null;

    // Options
    public SupportCollection $serviceStatusOptions;

    public SupportCollection $appointmentTypeOptions;

    public SupportCollection $gradeOptionsForSupportingOfficer;

    public SupportCollection $levelOptions;

    public SupportCollection $systemSupportingOfficers;

    // UI state
    public bool $showServiceDates = false;

    public bool $showPreviousDepartment = false;

    public bool $showApplicantJawatanGred = true;

    public bool $isPassportInputMode = false;

    private EmailApplicationService $emailApplicationService;

    // Rules, messages, boot, mount, prefill, fillFormFromModel, updated* methods remain the same
    // as provided in your uploaded file.
    // ... (Keep the existing methods from your uploaded EmailApplicationForm.php here)
    // Ensure the methods are exactly as you provided them. Example:
    public function rules(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $isFinalApplicantSubmission = false;

        if ((! $this->isEdit || $this->application instanceof \App\Models\EmailApplication && $this->application->status === EmailApplication::STATUS_DRAFT) && ($this->cert_info_is_true && $this->cert_data_usage_agreed && $this->cert_email_responsibility_agreed)) {
            $isFinalApplicantSubmission = true;
        }

        $rules = [
            'applicant_title' => ['nullable', 'string', 'max:50'],
            'applicant_name' => ['required', 'string', 'max:255'],
            'applicant_identification_number' => [Rule::requiredIf(! $this->isPassportInputMode), 'nullable', 'string', 'regex:/^\d{6}-\d{2}-\d{4}$/'],
            'applicant_passport_number' => [Rule::requiredIf($this->isPassportInputMode), 'nullable', 'string', 'max:100'],
            'applicant_jawatan_gred' => [Rule::requiredIf($this->showApplicantJawatanGred), 'nullable', 'string', 'max:255'],
            'applicant_bahagian_unit' => [Rule::requiredIf($this->showApplicantJawatanGred), 'nullable', 'string', 'max:255'],
            'applicant_level_aras' => ['nullable', 'string', 'max:10'],
            'applicant_mobile_number' => ['required', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'],
            'applicant_personal_email' => ['required', 'email:rfc,dns', 'max:255'],
            'service_status' => ['required', Rule::in(array_keys(User::$SERVICE_STATUS_LABELS))],
            'appointment_type' => ['required', Rule::in(array_keys(User::$APPOINTMENT_TYPE_LABELS))],
            'previous_department_name' => [Rule::requiredIf($this->showPreviousDepartment), 'nullable', 'string', 'max:255'],
            'previous_department_email' => [Rule::requiredIf($this->showPreviousDepartment), 'nullable', 'email:rfc,dns', 'max:255'],
            'service_start_date' => [Rule::requiredIf($this->showServiceDates), 'nullable', 'date_format:Y-m-d'],
            'service_end_date' => [Rule::requiredIf($this->showServiceDates), 'nullable', 'date_format:Y-m-d', 'after_or_equal:service_start_date'],
            'purpose' => ['required', 'string', 'min:10', 'max:2000'],
            'proposed_email' => [
                'nullable', 'string', 'max:255',
                Rule::when(
                    fn ($input): bool => ! empty($input->proposed_email),
                    [
                        'email:rfc,dns',
                        Rule::unique('email_applications', 'proposed_email')->ignore($this->applicationId),
                        Rule::unique('users', 'motac_email'),
                        'regex:/^[a-zA-Z0-9._%+-]+@'.preg_quote(config('motac.email_provisioning.default_domain', 'motac.gov.my'), '/').'$/i',
                    ]
                ),
            ],
            'is_group_email_request' => ['boolean'],
            'group_email' => [
                Rule::requiredIf($this->is_group_email_request), 'nullable', 'email:rfc,dns', 'max:255',
                Rule::unique('email_applications', 'group_email')->ignore($this->applicationId),
                Rule::unique('users', 'motac_email'),
                'regex:/^[a-zA-Z0-9._%+-]+@'.preg_quote(config('motac.email_provisioning.default_domain', 'motac.gov.my'), '/').'$/i',
            ],
            'group_admin_name' => [Rule::requiredIf($this->is_group_email_request), 'nullable', 'string', 'max:255'],
            'group_admin_email' => [Rule::requiredIf($this->is_group_email_request), 'nullable', 'email:rfc,dns', 'max:255'],
            'supporting_officer_id' => ['nullable', 'exists:users,id'],
            'manual_supporting_officer_name' => [Rule::requiredIf(($this->supporting_officer_id === null || $this->supporting_officer_id === 0) && $isFinalApplicantSubmission), 'nullable', 'string', 'max:255'],
            'manual_supporting_officer_grade_key' => [Rule::requiredIf(($this->supporting_officer_id === null || $this->supporting_officer_id === 0) && $isFinalApplicantSubmission), 'nullable', Rule::in(array_keys($this->gradeOptionsForSupportingOfficer->all()))],
            'manual_supporting_officer_email' => [Rule::requiredIf(($this->supporting_officer_id === null || $this->supporting_officer_id === 0) && $isFinalApplicantSubmission), 'nullable', 'email:rfc,dns', 'max:255'],
            'cert_info_is_true' => $isFinalApplicantSubmission ? ['accepted'] : ['boolean'],
            'cert_data_usage_agreed' => $isFinalApplicantSubmission ? ['accepted'] : ['boolean'],
            'cert_email_responsibility_agreed' => $isFinalApplicantSubmission ? ['accepted'] : ['boolean'],
        ];

        if ($this->isEdit && $user && ($user->isAdmin() || $user->isItAdmin())) {
            $rules['editable_status_for_admin_key'] = ['required', Rule::in(array_keys(EmailApplication::$STATUSES_LIST))];
            $rules['rejection_reason'] = [Rule::requiredIf($this->editable_status_for_admin_key === EmailApplication::STATUS_REJECTED), 'nullable', 'string', 'min:10', 'max:1000'];
            $rules['final_assigned_email'] = [
                Rule::requiredIf(in_array($this->editable_status_for_admin_key, [EmailApplication::STATUS_PROCESSING, EmailApplication::STATUS_COMPLETED])),
                'nullable', 'email:rfc,dns', 'max:255',
                Rule::unique('users', 'motac_email')->ignore($this->application?->user_id),
                Rule::unique('email_applications', 'final_assigned_email')->ignore($this->applicationId),
            ];
            $rules['final_assigned_user_id'] = [
                Rule::requiredIf(in_array($this->editable_status_for_admin_key, [EmailApplication::STATUS_PROCESSING, EmailApplication::STATUS_COMPLETED]) && $this->service_status === User::SERVICE_STATUS_PELAJAR_INDUSTRI),
                'nullable', 'string', 'max:100',
                Rule::unique('users', 'user_id_assigned')->ignore($this->application?->user_id),
                Rule::unique('email_applications', 'final_assigned_user_id')->ignore($this->applicationId),
            ];
        }

        return $rules;
    }

    public function boot(EmailApplicationService $emailApplicationService): void
    {
        $this->emailApplicationService = $emailApplicationService;
    }

    public function mount(?int $emailApplicationId = null): void
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user) {
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->serviceStatusOptions = collect(['' => __('- Pilih Taraf Perkhidmatan -')] + User::$SERVICE_STATUS_LABELS);
        $this->appointmentTypeOptions = collect(['' => __('- Pilih Pelantikan -')] + User::$APPOINTMENT_TYPE_LABELS);
        $this->gradeOptionsForSupportingOfficer = collect(['' => __('- Pilih Gred Penyokong -')] + Grade::orderBy('level', 'desc')->pluck('name', 'name')->all());
        $this->levelOptions = collect(['' => __('- Pilih Aras -')] + array_combine(range(1, 18), range(1, 18)));

        $minSupportGradeLevel = config('motac.approval.min_supporting_officer_grade_level_numeric', 9);
        $this->systemSupportingOfficers = collect(['' => __('- Pilih Pegawai Penyokong (Sistem) -')])
            ->union(
                User::whereHas('grade', fn ($q) => $q->where('level', '>=', $minSupportGradeLevel))
                    ->where('id', '!=', $user->id)
                    ->orderBy('name')->pluck('name', 'id')
            );

        if ($emailApplicationId !== null && $emailApplicationId !== 0) {
            $application = EmailApplication::with(['user.department', 'user.position', 'user.grade', 'supportingOfficer'])
                ->findOrFail($emailApplicationId);
            $this->application = $application;
            $this->applicationId = $application->id;
            $this->isEdit = true;
            $this->authorize('view', $this->application);
            $this->fillFormFromModel();
        } else {
            $this->application = new EmailApplication(['user_id' => $user->id]);
            $this->authorize('create', EmailApplication::class);
            $this->isEdit = false;
            $this->current_status_key = EmailApplication::STATUS_DRAFT;
            $this->editable_status_for_admin_key = EmailApplication::STATUS_DRAFT;
            $this->prefillApplicantDetails($user);
            $this->service_status = $user->service_status ?? '';
            $this->appointment_type = $user->appointment_type ?? '';
        }

        $this->updatedServiceStatus($this->service_status);
        $this->updatedAppointmentType($this->appointment_type);
        $this->is_group_email_request = $this->group_email !== null && $this->group_email !== '' && $this->group_email !== '0';
    }

    public function updatedServiceStatus(string $value): void
    {
        $this->showServiceDates = in_array($value, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]);
        $this->showApplicantJawatanGred = ! in_array($value, [User::SERVICE_STATUS_PELAJAR_INDUSTRI, User::SERVICE_STATUS_OTHER_AGENCY]);
    }

    public function updatedAppointmentType(string $value): void
    {
        $this->showPreviousDepartment = ($value === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN);
    }

    public function toggleIdentifierInput(): void
    {
        $this->isPassportInputMode = ! $this->isPassportInputMode;
        if ($this->isPassportInputMode) {
            $this->applicant_identification_number = null;
        } else {
            $this->applicant_passport_number = null;
        }

        $this->resetValidation($this->isPassportInputMode ? 'applicant_identification_number' : 'applicant_passport_number');
    }

    public function submitApplication(bool $isFinalSubmission): ?RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser) {
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));

            return $this->redirectRoute('login', navigate: true);
        }

        $this->validateCertificationsIfFinal($isFinalSubmission);
        $targetStatus = $this->determineSubmissionStatus($isFinalSubmission);
        // $this->current_status_key = $targetStatus; // This was for validation rules, might not be needed if validateCertificationsIfFinal is used

        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            $applicationData = collect($validatedData)->except([
                'cert_info_is_true', 'cert_data_usage_agreed', 'cert_email_responsibility_agreed',
                'editable_status_for_admin_key',
            ])->toArray();

            $applicationData['cert_info_is_true'] = $this->cert_info_is_true;
            $applicationData['cert_data_usage_agreed'] = $this->cert_data_usage_agreed;
            $applicationData['cert_email_responsibility_agreed'] = $this->cert_email_responsibility_agreed;
            $applicationData['status'] = $targetStatus;

            if ($isFinalSubmission && (! $this->isEdit || ($this->application instanceof \App\Models\EmailApplication && $this->application->status === EmailApplication::STATUS_DRAFT))) {
                $applicationData['certification_timestamp'] = now();
            }

            if ($this->isEdit && $this->application instanceof \App\Models\EmailApplication && ($currentUser->isAdmin() || $currentUser->isItAdmin())) {
                $this->authorize('update', $this->application);
                $applicationData['rejection_reason'] = $this->rejection_reason;
                $applicationData['final_assigned_email'] = $this->final_assigned_email;
                $applicationData['final_assigned_user_id'] = $this->final_assigned_user_id;
            }

            $message = '';
            if ($this->isEdit && $this->application instanceof \App\Models\EmailApplication) {
                $this->emailApplicationService->updateApplication($this->application, $applicationData, $currentUser);
                $message = __('Permohonan emel/ID berjaya dikemaskini.');
            } else {
                $this->authorize('create', EmailApplication::class);
                $applicationData['user_id'] = $currentUser->id;
                $prefilledFields = ['applicant_title', 'applicant_name', 'applicant_identification_number', 'applicant_passport_number', 'applicant_jawatan_gred', 'applicant_bahagian_unit', 'applicant_level_aras', 'applicant_mobile_number', 'applicant_personal_email'];
                foreach ($prefilledFields as $field) {
                    if (isset($validatedData[$field])) {
                        $applicationData[$field] = $validatedData[$field];
                    }
                }

                $this->application = $this->emailApplicationService->createApplication($applicationData, $currentUser);
                $this->applicationId = $this->application->id;
                $this->isEdit = true;
                $message = $isFinalSubmission ? __('Permohonan emel/ID berjaya dihantar.') : __('Draf permohonan emel/ID berjaya disimpan.');
            }

            if ($isFinalSubmission && $this->application->status === EmailApplication::STATUS_PENDING_SUPPORT && ! $currentUser->isAdmin() && ! $currentUser->isItAdmin()) {
                $this->emailApplicationService->processApplicationSubmission($this->application, $currentUser);
            }

            DB::commit();
            session()->flash('success', $message);
            $this->dispatch('toastr', type: 'success', message: $message);

            if ($this->isEdit && ($currentUser->isAdmin() || $currentUser->isItAdmin())) {
                $this->application->refresh();
                $this->fillFormFromModel();

                return null;
            }

            return $this->redirectRoute('resource-management.my-applications.email.index', navigate: true);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('EmailApplicationForm: Validation failed for User ID: '.$currentUser->id, ['errors' => $e->errors()]);
            $this->dispatch('toastr', type: 'error', message: __('Sila perbetulkan ralat pada borang.'));

            return null;
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::error('EmailApplicationForm: Authorization failed for User ID: '.$currentUser->id, ['message' => $e->getMessage()]);
            session()->flash('error', __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));

            return null;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('EmailApplicationForm: Error submitting application for User ID: '.$currentUser->id, ['exception' => $e]);
            session()->flash('error', __('Berlaku ralat tidak dijangka semasa memproses permohonan anda: ').$e->getMessage());
            $this->dispatch('toastr', type: 'error', message: __('Ralat sistem. Sila cuba lagi.'));

            return null;
        }
    }

    public function saveAsDraft(): ?RedirectResponse
    {
        return $this->submitApplication(false);
    }

    public function submitForApproval(): ?RedirectResponse
    {
        if (! $this->cert_info_is_true || ! $this->cert_data_usage_agreed || ! $this->cert_email_responsibility_agreed) {
            $this->addError('cert_info_is_true', __('Sila tandakan semua kotak perakuan untuk meneruskan.'));
            $this->dispatch('toastr', type: 'error', message: __('Sila lengkapkan semua perakuan.'));

            return null;
        }

        return $this->submitApplication(true);
    }

    public function render(): View
    {
        return view('livewire.email-application-form')
            ->title($this->isEdit ? __('Kemaskini Permohonan Emel/ID') : __('Borang Permohonan Emel/ID Pengguna'));
    }

    private function messages(): array
    {
        return [
            'applicant_identification_number.regex' => __('Format No. Kad Pengenalan tidak sah (cth: 800101010001).'),
            'proposed_email.regex' => __('Cadangan e-mel mesti menggunakan domain rasmi :domain.', ['domain' => config('motac.email_provisioning.default_domain', 'motac.gov.my')]),
            'group_email.regex' => __('Group e-mel mesti menggunakan domain rasmi :domain.', ['domain' => config('motac.email_provisioning.default_domain', 'motac.gov.my')]),
            'cert_info_is_true.accepted' => __('Anda mesti mengesahkan semua maklumat adalah BENAR.'),
            'cert_data_usage_agreed.accepted' => __('Anda mesti BERSETUJU maklumat diguna pakai oleh BPM.'),
            'cert_email_responsibility_agreed.accepted' => __('Anda mesti BERSETUJU untuk bertanggungjawab ke atas e-mel.'),
            'purpose.required' => __('Sila nyatakan Tujuan/Catatan/Cadangan ID E-mel.'),
            'purpose.min' => __('Tujuan/Catatan mesti sekurang-kurangnya :min aksara.'),
            '*.required' => __('Medan ini diperlukan.'),
            '*.email' => __('Sila masukkan format e-mel yang sah.'),
        ];
    }

    private function prefillApplicantDetails(User $user): void
    {
        $this->applicant_title = $user->title;
        $this->applicant_name = $user->name;
        $this->isPassportInputMode = empty($user->identification_number) && ! empty($user->passport_number);
        $this->applicant_identification_number = $user->identification_number;
        $this->applicant_passport_number = $user->passport_number;
        $this->applicant_jawatan_gred = ($user->position?->name ?? '').($user->grade?->name ? ' (Gred '.$user->grade->name.')' : '');
        $this->applicant_bahagian_unit = $user->department?->name;
        $this->applicant_level_aras = (string) $user->level;
        $this->applicant_mobile_number = $user->mobile_number;
        $this->applicant_personal_email = $user->personal_email ?? $user->email;
    }

    private function fillFormFromModel(): void
    {
        if (! $this->application instanceof \App\Models\EmailApplication) {
            return;
        }

        if ($this->application->user) {
            $this->prefillApplicantDetails($this->application->user);
        }

        $this->service_status = $this->application->service_status ?? $this->applicant_name->service_status ?? '';
        $this->appointment_type = $this->application->appointment_type ?? $this->applicant_name->appointment_type ?? '';
        $this->previous_department_name = $this->application->previous_department_name;
        $this->previous_department_email = $this->application->previous_department_email;
        $this->service_start_date = $this->application->service_start_date ? $this->application->service_start_date->format('Y-m-d') : null;
        $this->service_end_date = $this->application->service_end_date ? $this->application->service_end_date->format('Y-m-d') : null;
        $this->purpose = $this->application->purpose;
        $this->proposed_email = $this->application->proposed_email;
        $this->is_group_email_request = ! empty($this->application->group_email);
        $this->group_email = $this->application->group_email;
        $this->group_admin_name = $this->application->group_admin_name;
        $this->group_admin_email = $this->application->group_admin_email;
        $this->supporting_officer_id = $this->application->supporting_officer_id;
        $this->manual_supporting_officer_name = $this->application->supporting_officer_name;
        $this->manual_supporting_officer_grade_key = $this->application->supporting_officer_grade;
        $this->manual_supporting_officer_email = $this->application->supporting_officer_email;
        $this->cert_info_is_true = (bool) $this->application->cert_info_is_true;
        $this->cert_data_usage_agreed = (bool) $this->application->cert_data_usage_agreed;
        $this->cert_email_responsibility_agreed = (bool) $this->application->cert_email_responsibility_agreed;
        $this->current_status_key = $this->application->status;
        $this->editable_status_for_admin_key = $this->application->status;
        $this->rejection_reason = $this->application->rejection_reason;
        $this->final_assigned_email = $this->application->final_assigned_email;
        $this->final_assigned_user_id = $this->application->final_assigned_user_id;
    }

    private function determineSubmissionStatus(bool $isFinalButtonClicked): string
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if ($this->isEdit && ($currentUser->isAdmin() || $currentUser->isItAdmin())) {
            return $this->editable_status_for_admin_key;
        }

        if ($isFinalButtonClicked) {
            return EmailApplication::STATUS_PENDING_SUPPORT;
        }

        return EmailApplication::STATUS_DRAFT;
    }

    private function validateCertificationsIfFinal(bool $isFinalSubmission): void
    {
        if ($isFinalSubmission) {
            $this->validateOnly('cert_info_is_true', ['cert_info_is_true' => 'accepted']);
            $this->validateOnly('cert_data_usage_agreed', ['cert_data_usage_agreed' => 'accepted']);
            $this->validateOnly('cert_email_responsibility_agreed', ['cert_email_responsibility_agreed' => 'accepted']);
        }
    }
} // Make sure this closing brace is present
