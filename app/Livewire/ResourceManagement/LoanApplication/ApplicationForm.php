<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\LoanApplication;

use App\Models\LoanApplication;
use App\Models\User;
use App\Models\Equipment;
use App\Services\LoanApplicationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class ApplicationForm extends Component
{
  use AuthorizesRequests;

  //--- Properties ---
  public string $applicantName = '';
  public string $applicantPositionAndGrade = '';
  public string $applicantDepartment = '';
  public string $applicant_phone = '';
  public string $purpose = '';
  public string $location = '';
  public ?string $return_location = null;
  public ?string $loan_start_date = null;
  public ?string $loan_end_date = null;
  public bool $applicant_is_responsible_officer = true;
  public string $responsible_officer_name = '';
  public string $responsible_officer_position = '';
  public string $responsible_officer_phone = '';
  public ?int $supporting_officer_id = null;
  public array $loan_application_items = [];
  public bool $applicant_confirmation = false;
  public bool $termsScrolled = false;
  public ?int $editing_application_id = null;
  public ?LoanApplication $loanApplicationInstance = null;
  public bool $isSubmittingForApproval = false;
  public bool $isEditMode = false;
  public ?string $completedSubmissionDate = null;
  public array $supportingOfficerOptions = [];
  public array $equipmentTypeOptions = [];

  public function updatedApplicantIsResponsibleOfficer(bool $value): void
  {
    if ($value) {
      $this->reset([
        'responsible_officer_name',
        'responsible_officer_position',
        'responsible_officer_phone'
      ]);
    }
    $this->resetErrorBag(['responsible_officer_name', 'responsible_officer_position', 'responsible_officer_phone']);
  }

  public function mount(?int $loan_application_id = null): void
  {
    if (!Auth::check()) {
      session()->flash('error', __('messages.session_expired'));
      return;
    }

    $this->populateApplicantDetails();
    $this->loadInitialDropdownData();

    if ($loan_application_id) {
      $this->isEditMode = true;
      $this->editing_application_id = $loan_application_id;
      $this->loanApplicationInstance = LoanApplication::with('user', 'supportingOfficer', 'loanApplicationItems', 'submittedByUser')->findOrFail($loan_application_id);
      $this->authorize('update', $this->loanApplicationInstance);
      $this->populateFormFromInstance();
    } else {
      $this->authorize('create', LoanApplication::class);
      $this->resetFormForCreate();
    }
  }

  protected function rules(bool $isSubmittingForApproval = false): array
  {
    $currentUserId = Auth::id();
    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);

    return [
      'applicant_phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
      'purpose' => ['required', 'string', 'min:10', 'max:1000'],
      'location' => ['required', 'string', 'min:5', 'max:255'],

      // The missing validation rule for the optional return_location field is added here.
      'return_location' => ['nullable', 'string', 'max:255'],

      'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
      'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after:loan_start_date'],
      'applicant_is_responsible_officer' => ['required', 'boolean'],
      'responsible_officer_name' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:255'],
      'responsible_officer_position' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:255'],
      'responsible_officer_phone' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:20'],
      'supporting_officer_id' => [
        Rule::requiredIf($isSubmittingForApproval),
        'nullable',
        'integer',
        Rule::exists('users', 'id')->where('status', 'active'),
        Rule::when($currentUserId, 'different:' . $currentUserId),
        function ($attribute, $value, $fail) use ($minSupportGradeLevel) {
          if ($value) {
            $officer = User::with('grade:id,level')->find($value);
            if (!$officer || !$officer->grade || $officer->grade->level < $minSupportGradeLevel) {
              $fail(__('messages.supporter_grade_requirement_failed', ['grade' => $minSupportGradeLevel]));
            }
          }
        },
      ],
      'applicant_confirmation' => Rule::when($isSubmittingForApproval, ['accepted']),
      'loan_application_items' => ['required', 'array', 'min:1'],
      'loan_application_items.*.equipment_type' => ['required', 'string'],
      'loan_application_items.*.quantity_requested' => ['required', 'integer', 'min:1'],
      'loan_application_items.*.notes' => ['nullable', 'string', 'max:500'],
    ];
  }

  public function saveAsDraft(): ?RedirectResponse
  {
    return $this->saveOrUpdateLoanApplication(true);
  }

  public function submitLoanApplication(): ?RedirectResponse
  {
    return $this->saveOrUpdateLoanApplication(false);
  }

  private function saveOrUpdateLoanApplication(bool $isDraft): ?RedirectResponse
  {
    $this->isSubmittingForApproval = !$isDraft;
    $this->authorizeAction();
    $this->resetErrorBag();
    $validatedData = $this->validate($this->rules(!$isDraft));

    DB::beginTransaction();
    try {
      /** @var User $currentUser */
      $currentUser = Auth::user();
      $loanAppService = app(LoanApplicationService::class);

      $dataForService = $validatedData;

      if ($this->editing_application_id) {
        $application = $loanAppService->updateApplication($this->loanApplicationInstance, $dataForService, $currentUser);
        if (!$isDraft) {
          $loanAppService->submitApplicationForApproval($application, $currentUser);
        }
      } else {
        $application = $loanAppService->createAndSubmitApplication($dataForService, $currentUser, $isDraft);
      }

      DB::commit();
      $message = $isDraft ? __('messages.draft_saved_successfully') : __('messages.application_submitted_successfully');
      session()->flash('success', $message);
      return redirect()->route('loan-applications.show', $application->id);
    } catch (ValidationException $e) {
      DB::rollBack();
      throw $e;
    } catch (Throwable $e) {
      DB::rollBack();
      Log::error('Error in saveOrUpdateLoanApplication: ' . $e->getMessage(), ['exception' => $e]);
      $this->addError('general_error', __('messages.system_error_generic'));
      return null;
    }
  }

  private function populateApplicantDetails(): void
  {
    /** @var User $user */
    $user = Auth::user();
    if ($user) {
      $this->applicantName = $user->name;
      $this->applicant_phone = $user->mobile_number ?? '';
      $this->applicantPositionAndGrade = trim(($user->position?->name ?? '') . ' (' . ($user->grade?->name ?? '') . ')');
      $this->applicantDepartment = $user->department?->name ?? '';
    }
  }

  private function populateFormFromInstance(): void
  {
    if (!$this->loanApplicationInstance) return;

    $instance = $this->loanApplicationInstance;
    $this->purpose = $instance->purpose;
    $this->location = $instance->location;
    $this->return_location = $instance->return_location;
    $this->loan_start_date = $this->formatDateForInput($instance->loan_start_date);
    $this->loan_end_date = $this->formatDateForInput($instance->loan_end_date);

    $this->applicant_is_responsible_officer = empty($instance->responsible_officer_name);
    if (!$this->applicant_is_responsible_officer) {
      $this->responsible_officer_name = $instance->responsible_officer_name;
      $this->responsible_officer_position = $instance->responsible_officer_position;
      $this->responsible_officer_phone = $instance->responsible_officer_phone;
    }

    $this->supporting_officer_id = $instance->supporting_officer_id;
    $this->applicant_confirmation = $instance->applicant_confirmation_timestamp !== null;

    $this->loan_application_items = $instance->loanApplicationItems->map(fn($item) => [
      'id' => $item->id,
      'equipment_type' => $item->equipment_type,
      'quantity_requested' => $item->quantity_requested,
      'notes' => $item->notes,
    ])->toArray();

    if ($instance->submitted_at) {
      $this->completedSubmissionDate = $instance->submitted_at->format(config('app.datetime_format_my', 'd M Y, h:i A'));
    }
  }

  public function loadInitialDropdownData(): void
  {
    $currentUserId = Auth::id();
    if (!$currentUserId) return;

    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);

    $this->supportingOfficerOptions = User::where('status', User::STATUS_ACTIVE)
      ->where('id', '!=', $currentUserId)
      ->whereHas('grade', fn($query) => $query->where('level', '>=', $minSupportGradeLevel))
      ->orderBy('name')->get()->pluck('name', 'id')->toArray();

    $this->equipmentTypeOptions = Equipment::getAssetTypeOptions() ?? [];
  }

  public function resetFormForCreate(): void
  {
    $this->reset([
      'purpose',
      'location',
      'return_location',
      'loan_start_date',
      'loan_end_date',
      'supporting_officer_id',
      'loan_application_items',
      'applicant_confirmation',
      'editing_application_id',
      'loanApplicationInstance',
      'isEditMode',
      'completedSubmissionDate',
      'responsible_officer_name',
      'responsible_officer_position',
      'responsible_officer_phone'
    ]);

    $this->resetValidation();
    $this->populateApplicantDetails();
    $this->addLoanItem(false);
  }

  public function addLoanItem(bool $dispatchEvent = true): void
  {
    $this->loan_application_items[] = ['id' => null, 'equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    if ($dispatchEvent) $this->dispatch('loanItemAdded');
  }

  public function removeLoanItem(int $index): void
  {
    if (count($this->loan_application_items) > 1) {
      unset($this->loan_application_items[$index]);
      $this->loan_application_items = array_values($this->loan_application_items);
    } else {
      session()->flash('error', __('messages.loan_requires_min_one_item'));
    }
  }

  /**
   * Loads form state from data saved in the browser's localStorage.
   * This is called by Alpine.js when the page initializes.
   *
   * @param array $cachedData The associative array of cached form data.
   * @return void
   */
  public function loadStateFromCache(array $cachedData): void
  {
    // For each field, assign the cached value if it exists, otherwise keep the current value.
    $this->purpose = $cachedData['purpose'] ?? $this->purpose;
    $this->location = $cachedData['location'] ?? $this->location;
    $this->return_location = $cachedData['return_location'] ?? $this->return_location;
    $this->loan_start_date = $cachedData['loan_start_date'] ?? $this->loan_start_date;
    $this->loan_end_date = $cachedData['loan_end_date'] ?? $this->loan_end_date;

    // Note: For security and simplicity, we are not caching officer selections.
    // Caching the dynamically added equipment items is also more complex and is omitted here.
  }

  private function authorizeAction(): void
  {
    $this->authorize($this->editing_application_id ? 'update' : 'create', $this->loanApplicationInstance ?? LoanApplication::class);
  }

  private function formatDateForInput(?Carbon $date): ?string
  {
    return $date ? $date->format('Y-m-d\TH:i') : null;
  }

  public function generatePageTitle(): string
  {
    return __('forms.ict_loan_form_title');
  }

  public function render(): View
  {
    return view('livewire.resource-management.loan-application.application-form');
  }
}
