<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\EmailAccount;

use App\Models\EmailApplication;
use App\Models\Grade;
use App\Models\User;
use App\Policies\EmailApplicationPolicy;
use App\Services\EmailApplicationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

/**
 * Livewire Component for Creating and Editing Email/User ID Applications.
 */
#[Layout('layouts.app')]
class ApplicationForm extends Component
{
  use AuthorizesRequests;

  public User $user;
  public ?EmailApplication $applicationToEdit = null;

  // Form Properties
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
  public ?string $supporting_officer_name = null;
  public ?string $supporting_officer_grade = null;
  public ?string $supporting_officer_email = null;
  public bool $cert_info_is_true = false;
  public bool $cert_data_usage_agreed = false;
  public bool $cert_email_responsibility_agreed = false;

  // Display-only properties
  public string $applicantName = '';
  public string $applicantPositionAndGrade = '';
  public string $applicantDepartment = '';
  public string $applicantEmail = '';
  public string $applicantPhone = '';

  // Dropdown options
  public array $serviceStatusOptions = [];
  public array $appointmentTypeOptions = [];
  public array $supportingOfficerGradeOptions = [];

  /**
   * Set the page title dynamically based on the form's mode (create or edit).
   * UPDATED: Uses keys from the forms.php language file for full translation support.
   */
  public function title(): string
  {
    $baseTitle = $this->applicationToEdit ? __('forms.email_app_edit_title') : __('forms.email_app_create_title');
    $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));

    return $baseTitle . ' - ' . $appName;
  }

  /**
   * Initialize the component, authorize the user, and load necessary data.
   */
  public function mount($email_application_id = null): void
  {
    /** @var User|null $loggedInUser */
    $loggedInUser = Auth::user();

    if (! $loggedInUser) {
      session()->flash('error', __('Sila log masuk untuk membuat atau mengemaskini permohonan.'));
      $this->user = new User;
      $this->populateApplicantDetails();
      $this->loadDropdownOptions();
      $this->skipRender(); // Redirecting to login might be a better user experience

      return;
    }

    $this->user = User::with(['position', 'grade', 'department'])->find($loggedInUser->id) ?? new User;
    if (! $this->user->exists) {
      session()->flash('error', __('Gagal memuatkan maklumat pengguna sepenuhnya. Sila pastikan profil anda lengkap.'));
      Log::error("EmailAccount\ApplicationForm mount: Failed to load full user details for authenticated user ID: " . $loggedInUser->id);
    }

    $this->populateApplicantDetails();
    $this->loadDropdownOptions();

    if ($email_application_id) {
      $this->applicationToEdit = EmailApplication::find($email_application_id);

      if (! $this->applicationToEdit) {
        session()->flash('error', __('Permohonan yang ingin dikemaskini tidak ditemui.'));
        Log::warning("EmailAccount\ApplicationForm mount: EmailApplication not found for ID: " . $email_application_id);
        $this->redirectRoute('email-applications.index', navigate: true);

        return;
      }
      $this->authorize('update', $this->applicationToEdit);
      $this->populateFormForEdit();
    } else {
      $this->applicationToEdit = null;
      $this->authorize('create', EmailApplication::class);
      $this->resetFormFields(true);
    }
  }

  /**
   * Populate read-only applicant details from the logged-in user's profile.
   * UPDATED: Uses language file keys for fallback text.
   */
  protected function populateApplicantDetails(): void
  {
    if ($this->user->exists) {
      $this->applicantName = trim(($this->user->title ?? '') . ' ' . ($this->user->name ?? ''));
      $positionName = optional($this->user->position)->name;
      $gradeName = optional($this->user->grade)->name;
      $this->applicantPositionAndGrade = ($positionName || $gradeName) ? trim("{$positionName} ({$gradeName})", ' ()') : __('forms.text_no_position_grade_info');
      $this->applicantDepartment = optional($this->user->department)->name ?? __('forms.text_no_department_info');
      $this->applicantEmail = $this->user->email ?? '';
      $this->applicantPhone = $this->user->mobile_number ?? '';
    } else {
      $this->applicantName = __('forms.text_unknown_user');
      $this->applicantPositionAndGrade = '';
      $this->applicantDepartment = '';
      $this->applicantEmail = '';
      $this->applicantPhone = '';
    }
  }

  /**
   * Fill the form fields with data from an existing application for editing.
   */
  protected function populateFormForEdit(): void
  {
    if (! $this->applicationToEdit) {
      return;
    }

    $this->service_status_selection = $this->user->service_status ?? $this->applicationToEdit->user->service_status ?? '';
    $this->appointment_type_selection = $this->user->appointment_type ?? $this->applicationToEdit->user->appointment_type ?? '';
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
    $this->supporting_officer_grade = $this->applicationToEdit->supporting_officer_grade;
    $this->supporting_officer_email = $this->applicationToEdit->supporting_officer_email;
    $this->cert_info_is_true = (bool) $this->applicationToEdit->cert_info_is_true;
    $this->cert_data_usage_agreed = (bool) $this->applicationToEdit->cert_data_usage_agreed;
    $this->cert_email_responsibility_agreed = (bool) $this->applicationToEdit->cert_email_responsibility_agreed;
  }

  /**
   * Load options for dropdowns from the centralized language files.
   * UPDATED: Now loads arrays directly from the lang file, which includes placeholders.
   */
  protected function loadDropdownOptions(): void
  {
    $this->serviceStatusOptions = trans('forms.service_status_options');
    $this->appointmentTypeOptions = trans('forms.appointment_type_options');

    // FIXED: Changed the key to use the correct array for supporting officer grades.
    $this->supportingOfficerGradeOptions = trans('forms.supporting_officer_grade_options');
  }

  /**
   * Define the validation rules for the form.
   */
  protected function rules(): array
  {
    $uniqueProposedEmailRule = Rule::unique('email_applications', 'proposed_email')
      ->when($this->applicationToEdit, fn($rule) => $rule->ignore($this->applicationToEdit->id))
      ->whereNull('deleted_at');

    return [
      'service_status_selection' => ['required', Rule::in(array_keys(trans('forms.service_status_options')))],
      'appointment_type_selection' => ['required', Rule::in(array_keys(trans('forms.appointment_type_options')))],
      'application_reason_notes' => ['required', 'string', 'min:10', 'max:2000'],
      'proposed_email' => ['nullable', 'email:rfc,dns', 'max:255', $uniqueProposedEmailRule],
      'cert_info_is_true' => ['accepted'],
      'cert_data_usage_agreed' => ['accepted'],
      'cert_email_responsibility_agreed' => ['accepted'],
      'supporting_officer_name' => ['required', 'string', 'max:255'],
      'supporting_officer_grade' => ['required', 'string', Rule::in(array_keys(trans('forms.grade_options_example') ?? []))],
      'supporting_officer_email' => ['required', 'email:rfc,dns', 'max:255'],
      'previous_department_name' => $this->shouldShowPreviousDepartmentFields() ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'],
      'previous_department_email' => $this->shouldShowPreviousDepartmentFields() ? ['required', 'email:rfc,dns', 'max:255'] : ['nullable', 'email:rfc,dns', 'max:255'],
      'service_start_date' => $this->shouldShowServiceDates() ? ['required', 'date'] : ['nullable', 'date'],
      'service_end_date' => $this->shouldShowServiceDates() ? ['required', 'date', 'after_or_equal:service_start_date'] : ['nullable', 'date', 'after_or_equal:service_start_date'],
      'group_email_request_name' => ['nullable', 'string', 'max:255'],
      'contact_person_name' => ['nullable', Rule::requiredIf((bool) ($this->group_email_request_name ?? '')), 'string', 'max:255'],
      'contact_person_email' => ['nullable', Rule::requiredIf((bool) ($this->group_email_request_name ?? '')), 'email:rfc,dns', 'max:255'],
    ];
  }

  /**
   * NOTE: The messages() method has been removed.
   * For better code organization and adherence to Laravel standards, custom validation
   * messages should be defined in the 'custom' array within your main language files,
   * such as `lang/en/validation.php` or `lang/ms/validation.php`.
   */

  /**
   * Prepare a common data array from validated form input.
   */
  private function prepareCommonApplicationData(array $validatedData): array
  {
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

    return $commonData;
  }

  /**
   * Save the application as a draft.
   */
  public function saveApplication(): void
  {
    $this->authorizeAction(false);
    $validatedData = $this->validate();
    $commonData = $this->prepareCommonApplicationData($validatedData);

    try {
      /** @var EmailApplicationService $emailAppService */
      $emailAppService = app(EmailApplicationService::class);
      $application = null;

      if ($this->applicationToEdit) {
        if (! $this->applicationToEdit->isDraft()) {
          session()->flash('error', __('Hanya draf permohonan yang boleh dikemaskini. Permohonan ini telah dihantar atau diproses.'));

          return;
        }
        $application = $emailAppService->updateDraftApplication($this->applicationToEdit, $commonData, $this->user);
        session()->flash('message', ['type' => 'success', 'content' => __('Draf permohonan #:id berjaya dikemaskini.', ['id' => $application->id])]);
      } else {
        $application = $emailAppService->createDraftApplication($commonData, $this->user);
        session()->flash('message', ['type' => 'success', 'content' => __('Draf permohonan e-mel berjaya disimpan. Anda boleh melihat dan menghantarnya dari senarai permohonan anda.')]);
      }
      if ($application) {
        $this->redirectRoute('email-applications.show', ['email_application' => $application->id], navigate: true);
      }
    } catch (Throwable $e) {
      Log::error('EmailAccount\ApplicationForm Error in saveApplication: ' . $e->getMessage(), ['exception' => $e, 'formData' => $this->all()]);
      session()->flash('error', __('Gagal menyimpan draf permohonan. Sila semak input anda atau hubungi pentadbir sistem.'));
    }
  }

  /**
   * Authorize the user action based on whether they are creating or updating.
   */
  private function authorizeAction(bool $isSubmitting = false): void
  {
    if ($this->applicationToEdit && $this->applicationToEdit->exists) {
      $ability = ($isSubmitting && method_exists(EmailApplicationPolicy::class, 'submit')) ? 'submit' : 'update';
      $this->authorize($ability, $this->applicationToEdit);
    } else {
      $this->authorize('create', EmailApplication::class);
    }
  }

  /**
   * Reset form fields to their initial state.
   */
  private function resetFormFields(bool $prefillFromUser = false): void
  {
    $this->resetErrorBag();
    $this->resetValidation();

    if ($prefillFromUser && $this->user->exists && ! $this->applicationToEdit) {
      $this->service_status_selection = $this->user->service_status ?? '';
      $this->appointment_type_selection = $this->user->appointment_type ?? '';
    } elseif (! $this->applicationToEdit) {
      $this->service_status_selection = '';
      $this->appointment_type_selection = '';
    }

    if (! $this->applicationToEdit) {
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
    $this->updatedServiceStatusSelection($this->service_status_selection);
    $this->updatedAppointmentTypeSelection($this->appointment_type_selection);
  }

  /**
   * Computed property to determine if the previous department fields should be shown.
   */
  #[Computed]
  public function shouldShowPreviousDepartmentFields(): bool
  {
    return $this->appointment_type_selection === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN;
  }

  /**
   * Computed property to determine if the group email fields should be shown.
   */
  #[Computed]
  public function showGroupEmailFields(): bool
  {
    // This logic can be expanded if needed
    return true;
  }

  /**
   * Computed property to determine if the service date fields should be shown.
   */
  #[Computed]
  public function shouldShowServiceDates(): bool
  {
    $statusesWithoutDates = [User::SERVICE_STATUS_TETAP, ''];

    return ! in_array($this->service_status_selection, $statusesWithoutDates, true);
  }

  /**
   * Handle updates to the service status selection.
   */
  public function updatedServiceStatusSelection($value): void
  {
    $this->resetValidation('service_status_selection');
    if (! $this->shouldShowServiceDates()) {
      $this->service_start_date = null;
      $this->service_end_date = null;
      $this->resetValidation(['service_start_date', 'service_end_date']);
    }
    Log::debug("EmailAccount\ApplicationForm: Service status selected: " . $value);
  }

  /**
   * Handle updates to the appointment type selection.
   */
  public function updatedAppointmentTypeSelection($value): void
  {
    $this->resetValidation('appointment_type_selection');
    if (! $this->shouldShowPreviousDepartmentFields()) {
      $this->previous_department_name = null;
      $this->previous_department_email = null;
      $this->resetValidation(['previous_department_name', 'previous_department_email']);
    }
    Log::debug("EmailAccount\ApplicationForm: Appointment type selected: " . $value);
  }

  /**
   * Render the component view.
   */
  public function render(): View
  {
    if (! $this->user->exists && Auth::check()) {
      $this->user = User::with(['position', 'grade', 'department'])->find(Auth::id()) ?? new User;
      $this->populateApplicantDetails();
    }

    return view('livewire.resource-management.email-account.application-form');
  }
}
