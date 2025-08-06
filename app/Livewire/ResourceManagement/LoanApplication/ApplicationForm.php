<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\LoanApplication;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanApplicationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
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

    // --- Component State ---
    public ?LoanApplication $loanApplicationInstance = null;
    public ?int $editing_application_id = null;
    public bool $isEditMode = false;

    // --- Form Data Properties ---
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
    public ?int $responsible_officer_id = null;
    public ?int $supporting_officer_id = null;
    public array $loan_application_items = [];
    public bool $applicant_confirmation = false;

    // --- UI & Select Options ---
    public bool $termsScrolled = false;
    public array $supportingOfficerOptions = [];
    public array $equipmentTypeOptions = [];
    public array $responsibleOfficerOptions = [];

    /**
     * Mounts the component, loading an existing application for editing or initializing a new one.
     */
    public function mount(?int $loan_application_id = null): void
    {
        $this->isEditMode = !is_null($loan_application_id);
        if ($this->isEditMode) {
            $this->editing_application_id = $loan_application_id;
            $this->loadExistingApplication();
        } else {
            $this->initializeNewApplication();
        }

        $this->loadSelectOptions();
        $this->updatedApplicantIsResponsibleOfficer($this->applicant_is_responsible_officer);
    }

    /**
     * Generates the title for the page based on the current mode (new/edit).
     * Uses language keys from the suffixed files.
     */
    public function generatePageTitle(): string
    {
        // 'forms.title_edit_application_ict' and 'forms.title_new_application_ict' are language keys in forms_en.php / forms_ms.php
        return $this->isEditMode
            ? __('forms.title_edit_application_ict')
            : __('forms.title_new_application_ict');
    }

    /**
     * Loads form state from the browser's localStorage cache.
     */
    public function loadStateFromCache(array $cachedData): void
    {
        // Only load from cache if we are creating a new application
        if (!$this->isEditMode) {
            $this->purpose = $cachedData['purpose'] ?? '';
            $this->location = $cachedData['location'] ?? '';
            $this->return_location = $cachedData['return_location'] ?? null;
            $this->loan_start_date = $cachedData['loan_start_date'] ?? null;
            $this->loan_end_date = $cachedData['loan_end_date'] ?? null;
            $this->applicant_phone = $cachedData['applicant_phone'] ?? '';
            $this->applicant_is_responsible_officer = $cachedData['applicant_is_responsible_officer'] ?? true;
            $this->responsible_officer_id = $cachedData['responsible_officer_id'] ?? null;
            $this->supporting_officer_id = $cachedData['supporting_officer_id'] ?? null;
            $this->loan_application_items = $cachedData['loan_application_items'] ?? [['equipment_type' => '', 'quantity_requested' => 1, 'notes' => '']];
        }
    }

    /**
     * Initializes the form for a new application, pre-filling applicant data.
     */
    private function initializeNewApplication(): void
    {
        $this->authorize('create', LoanApplication::class);
        $user = Auth::user();
        $this->applicantName = $user->name;
        // Use language keys for N/A if needed
        $this->applicantPositionAndGrade = ($user->position?->name ?? __('common.not_available')) . ' (' . ($user->grade?->name ?? __('common.not_available')) . ')';
        $this->applicantDepartment = $user->department?->name ?? __('common.not_available');
        $this->applicant_phone = $user->mobile_number ?? '';
        $this->addLoanItem();
    }

    /**
     * Loads an existing application's data into the form for editing.
     */
    private function loadExistingApplication(): void
    {
        try {
            $application = LoanApplication::with('user', 'responsibleOfficer', 'supportingOfficer', 'loanApplicationItems')->findOrFail($this->editing_application_id);
            $this->loanApplicationInstance = $application;
            $this->authorize('update', $this->loanApplicationInstance);

            $user = $application->user;
            $this->applicantName = $user?->name ?? '';
            $this->applicantPositionAndGrade = ($user?->position?->name ?? __('common.not_available')) . ' (' . ($user?->grade?->name ?? __('common.not_available')) . ')';
            $this->applicantDepartment = $user?->department?->name ?? __('common.not_available');
            $this->applicant_phone = $application->applicant_phone ?? $user->mobile_number ?? '';

            $this->purpose = $application->purpose;
            $this->location = $application->location;
            $this->return_location = $application->return_location;
            $this->loan_start_date = $this->formatDateForInput($application->loan_start_date);
            $this->loan_end_date = $this->formatDateForInput($application->loan_end_date);
            $this->applicant_is_responsible_officer = $application->responsible_officer_id === $application->user_id;
            $this->responsible_officer_id = $application->responsible_officer_id;
            $this->supporting_officer_id = $application->supporting_officer_id;
            $this->applicant_confirmation = !is_null($application->applicant_confirmation_timestamp);

            $this->loan_application_items = $application->loanApplicationItems->map(fn($item) => $item->only(['id', 'equipment_type', 'quantity_requested', 'notes']))->toArray();
            if (empty($this->loan_application_items)) {
                $this->addLoanItem();
            }
        } catch (Throwable $e) {
            Log::error('Error loading existing loan application: ' . $e->getMessage());
            session()->flash('error', __('messages.system_error')); // Use language key for error
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    /**
     * Loads select options for equipment types and officer lists.
     * Uses language keys for labels if needed.
     */
    private function loadSelectOptions(): void
    {
        $this->equipmentTypeOptions = Equipment::getAssetTypeOptions(); // These should be keys from language file
        $officerList = User::where('status', 'active')->where('id', '!=', Auth::id())->orderBy('name')->get(['id', 'name']);
        $this->supportingOfficerOptions = $officerList->pluck('name', 'id')->toArray();
        $this->responsibleOfficerOptions = $officerList->pluck('name', 'id')->toArray();
    }

    public function addLoanItem(): void
    {
        $this->loan_application_items[] = ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    }

    public function removeLoanItem(int $index): void
    {
        if (count($this->loan_application_items) > 1) {
            unset($this->loan_application_items[$index]);
            $this->loan_application_items = array_values($this->loan_application_items);
        } else {
            // Use language key for this info message
            $this->dispatch('swal:info', title: __('common.no_permission'), message: __('forms.text_no_equipment_added'));
        }
    }

    public function updatedApplicantIsResponsibleOfficer(bool $value): void
    {
        if ($value) $this->responsible_officer_id = null;
    }

    public function rules(bool $forSubmission = false): array
    {
        $rules = [
            'applicant_phone' => ['required', 'string', 'regex:/^(\+?6?01)[0-9]{8,9}$/'],
            'purpose' => ['required', 'string', 'min:10', 'max:500'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255', Rule::when($this->return_location, ['different:location'])],
            'loan_start_date' => ['required', 'date', 'after_or_equal:' . now()->startOfDay()->toDateTimeString()],
            'loan_end_date' => ['required', 'date', 'after:loan_start_date'],
            'applicant_is_responsible_officer' => ['boolean'],
            'responsible_officer_id' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'exists:users,id'],
            'supporting_officer_id' => ['nullable', 'exists:users,id'],
            'loan_application_items' => ['required', 'array', 'min:1'],
            'loan_application_items.*.id' => ['nullable', 'integer'],
            'loan_application_items.*.equipment_type' => ['required', 'string', Rule::in(array_keys($this->equipmentTypeOptions))],
            'loan_application_items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:10'],
            'loan_application_items.*.notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($forSubmission) {
            $rules['applicant_confirmation'] = ['accepted'];
            $rules['supporting_officer_id'][0] = 'required';
        }
        return $rules;
    }

    /**
     * Validation messages using language keys for multilingual support.
     */
    public function messages(): array
    {
        $messages = [
            'applicant_phone.regex' => __('forms.validation_phone_format'),
            'purpose.required' => __('forms.validation_purpose_required'),
            'loan_start_date.after_or_equal' => __('forms.validation_loan_start_date_after'),
            'loan_end_date.after' => __('forms.validation_loan_end_date_after'),
            'responsible_officer_id.required_if' => __('forms.validation_responsible_officer_required'),
            'supporting_officer_id.required' => __('forms.validation_supporting_officer_required'),
            'applicant_confirmation.accepted' => __('forms.validation_applicant_confirmation'),
            'return_location.different' => __('forms.validation_return_location_different'),
        ];
        foreach (array_keys($this->loan_application_items) as $index) {
            $itemNumber = $index + 1;
            // Item-specific validation messages, using keys if available
            $messages[sprintf('loan_application_items.%s.equipment_type.required', $index)] = sprintf(__('forms.validation_equipment_type_required'), $itemNumber);
            $messages[sprintf('loan_application_items.%s.quantity_requested.required', $index)] = sprintf(__('forms.validation_quantity_required'), $itemNumber);
        }
        return $messages;
    }

    /**
     * Saves the current application form as a draft.
     *
     * @param LoanApplicationService $service
     * @return RedirectResponse|null
     */
    public function saveAsDraft(LoanApplicationService $service): ?RedirectResponse
    {
        try {
            $validatedData = $this->validate($this->rules(false), $this->messages());
            DB::beginTransaction();
            $user = Auth::user();

            // Use the unified service method to handle creation or update for drafts
            $application = $service->createAndSubmitApplication($validatedData, $user, true, $this->loanApplicationInstance);

            DB::commit();
            session()->flash('success', __('forms.draft_saved_successfully'));
            return redirect()->route('loan-applications.edit', ['loan_application' => $application->id]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error saving draft loan application', ['message' => $e->getMessage()]);
            $this->dispatch('swal:error', title: __('common.error'), message: __('forms.error_saving_draft', ['error' => $e->getMessage()]));
            return null;
        }
    }

    /**
     * Submits the loan application for approval.
     *
     * @param LoanApplicationService $service
     * @return RedirectResponse|null
     */
    public function submitLoanApplication(LoanApplicationService $service): ?RedirectResponse
    {
        try {
            $validatedData = $this->validate($this->rules(true), $this->messages());
            DB::beginTransaction();
            $user = Auth::user();

            // Use the unified service method to handle creation or update for final submission
            $application = $service->createAndSubmitApplication($validatedData, $user, false, $this->loanApplicationInstance);

            DB::commit();
            session()->flash('success', __('forms.application_submitted_successfully'));
            return redirect()->route('loan-applications.show', ['loan_application' => $application->id]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error submitting loan application', ['message' => $e->getMessage()]);
            $this->dispatch('swal:error', title: __('common.error'), message: __('forms.error_submitting_application', ['error' => $e->getMessage()]));
            return null;
        }
    }

    private function formatDateForInput($date): ?string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d\TH:i') : null;
    }

    public function render(): View
    {
        // Blade view will use @lang for keys in suffixed language files
        return view('livewire.resource-management.loan-application.application-form');
    }
}
