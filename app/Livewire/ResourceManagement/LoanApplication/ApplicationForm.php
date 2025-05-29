<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\LoanApplication;

use App\Models\LoanApplication;
use App\Models\User; // For type hinting and fetching user details
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; // Correct import for ValidationException
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

final class ApplicationForm extends Component
{
  use AuthorizesRequests;

  // BAHAGIAN 1: MAKLUMAT PEMOHON (Partially pre-filled, some editable)
  public string $applicantName = ''; // Pre-filled, display only
  public string $applicantPositionAndGrade = ''; // Pre-filled, display only
  public string $applicantDepartment = ''; // Pre-filled, display only
  public string $applicant_phone = ''; // Editable, pre-filled
  public string $purpose = '';
  public string $location = ''; // Lokasi Penggunaan
  public ?string $return_location = ''; // Lokasi Dijangka Pulang / Pemulangan
  public ?string $loan_start_date = null; // Datetime string for input
  public ?string $loan_end_date = null;   // Datetime string for input

  // BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB
  public bool $applicant_is_responsible_officer = true;
  public string $responsible_officer_name = '';
  public string $responsible_officer_position_grade = '';
  public string $responsible_officer_phone = '';
  // Note: responsible_officer_id (FK) linking would happen in submit if a User selection mechanism is added.

  // BAHAGIAN 3: MAKLUMAT PERALATAN
  public array $loan_application_items = []; // Holds items for the form

  // BAHAGIAN 4: PENGESAHAN PEMOHON
  public bool $applicant_confirmation = false;

  // Component State
  public ?int $applicationId = null;
  public ?LoanApplication $loanApplicationInstance = null; // Explicitly typed

  // Not directly submitted, but useful for display if needed
  public int $totalItems = 0;

  public function mount(?LoanApplication $loanApplication = null): void
  {
    if (!Auth::check()) {
      session()->flash('error', __('You must be logged in to create or edit an application.'));
      return;
    }

    $this->populateApplicantDetails(); // Initial population

    $this->loanApplicationInstance = $loanApplication;

    if ($this->loanApplicationInstance && $this->loanApplicationInstance->exists) {
      $this->authorize('update', $this->loanApplicationInstance);
      $this->applicationId = $this->loanApplicationInstance->id;

      // Populate from existing application
      $this->applicant_phone = $this->loanApplicationInstance->applicant_phone ?? Auth::user()->mobile_number ?? '';
      $this->purpose = $this->loanApplicationInstance->purpose ?? '';
      $this->location = $this->loanApplicationInstance->location ?? '';
      $this->return_location = $this->loanApplicationInstance->return_location ?? '';
      $this->loan_start_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_start_date);
      $this->loan_end_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_end_date);

      // Responsible Officer details
      if ($this->loanApplicationInstance->responsible_officer_id && $this->loanApplicationInstance->responsible_officer_id !== Auth::id()) {
        $this->applicant_is_responsible_officer = false;
        $responsibleOfficer = $this->loanApplicationInstance->responsibleOfficer; // Use relation
        if ($responsibleOfficer) {
          $this->responsible_officer_name = $responsibleOfficer->name;
          $this->responsible_officer_position_grade = trim((optional($responsibleOfficer->position)->name ?? '') . ' (' . (optional($responsibleOfficer->grade)->name ?? '') . ')', ' ()');
          $this->responsible_officer_phone = $responsibleOfficer->mobile_number ?? '';
        } else {
          // If responsible_officer_id was set, but the related User model is not found (e.g., deleted user)
          // Clear the form fields for responsible officer details.
          $this->responsible_officer_name = '';
          $this->responsible_officer_position_grade = '';
          $this->responsible_officer_phone = '';
          Log::warning("Could not load responsible officer (User ID: {$this->loanApplicationInstance->responsible_officer_id}) for LoanApplication ID: {$this->loanApplicationInstance->id}. The user may have been deleted or the ID is invalid.");
        }
      } else {
        $this->applicant_is_responsible_officer = true;
        // Ensure fields are clear if applicant is responsible or no responsible_officer_id is set
        $this->responsible_officer_name = '';
        $this->responsible_officer_position_grade = '';
        $this->responsible_officer_phone = '';
      }

      $this->loan_application_items = $this->loanApplicationInstance->applicationItems
        ?->map(fn($item) => $item->only(['equipment_type', 'quantity_requested', 'notes']))
        ->toArray() ?? [];

      $this->applicant_confirmation = (bool)$this->loanApplicationInstance->applicant_confirmation_timestamp;
    } else {
      $this->resetForm(); // This will call populateApplicantDetails and addLoanItem
    }
    $this->updateTotalItems();
  }

  public function populateApplicantDetails(): void
  {
    $user = Auth::user();
    if ($user) {
      $this->applicantName = $user->name;
      $this->applicantPositionAndGrade = trim((optional($user->position)->name ?? '') . ' (' . (optional($user->grade)->name ?? '') . ')', ' ()');
      $this->applicantDepartment = optional($user->department)->name ?? '';
      if (empty($this->applicant_phone) || !$this->loanApplicationInstance) { // Only prefill if new or phone is empty
        $this->applicant_phone = $user->mobile_number ?? '';
      }
    }
  }

  public function addLoanItem(): void
  {
    $this->loan_application_items[] = ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    $this->updateTotalItems();
  }

  public function removeLoanItem(int $index): void
  {
    if (count($this->loan_application_items) > 1 && isset($this->loan_application_items[$index])) {
      unset($this->loan_application_items[$index]);
      $this->loan_application_items = array_values($this->loan_application_items);
      $this->updateTotalItems();
    } else {
      session()->flash('error', __('You must have at least one equipment item.'));
    }
  }

  public function submitLoanApplication(): ?RedirectResponse
  {
    $isUpdating = $this->loanApplicationInstance && $this->loanApplicationInstance->exists;
    $this->authorize($isUpdating ? 'update' : 'create', $this->loanApplicationInstance ?? LoanApplication::class);
    $validatedData = $this->validate($this->rules(), $this->messages());

    DB::beginTransaction();
    try {
      $currentUser = Auth::user();
      $applicationData = [
        'user_id' => $currentUser->id,
        'applicant_phone' => $validatedData['applicant_phone'],
        'purpose' => $validatedData['purpose'],
        'location' => $validatedData['location'],
        'return_location' => $validatedData['return_location'] ?? null,
        'loan_start_date' => Carbon::parse($validatedData['loan_start_date']),
        'loan_end_date' => Carbon::parse($validatedData['loan_end_date']),
      ];

      if (!$validatedData['applicant_is_responsible_officer']) {
        // If applicant is not the responsible officer, set responsible_officer_id to null.
        // A user selection mechanism would be needed to populate this ID from form input.
        // The PDF schema for loan_applications does not include text fields for resp. officer details[cite: 85].
        $applicationData['responsible_officer_id'] = null;
        Log::info('Applicant is not the responsible officer. Responsible officer details were entered as text. A user selection mechanism or manual linking for responsible_officer_id may be needed if a specific user is to be linked.', [
          'entered_responsible_officer_name' => $validatedData['responsible_officer_name'],
          'loan_application_id_on_update' => $this->applicationId,
        ]);
        // As per PDF schema[cite: 85], do not store text fields for responsible officer in loan_applications table.
        // If these text fields (e.g., responsible_officer_name_text) are required,
        // the LoanApplication model and database schema would need to be updated.
        // $applicationData['responsible_officer_name_text'] = $validatedData['responsible_officer_name'];
        // $applicationData['responsible_officer_details_text'] = $validatedData['responsible_officer_position_grade'];
        // $applicationData['responsible_officer_phone_text'] = $validatedData['responsible_officer_phone'];
      } else {
        $applicationData['responsible_officer_id'] = $currentUser->id;
      }

      $loanAppToProcess = $this->loanApplicationInstance;

      if ($isUpdating) {
        $loanAppToProcess->fill($applicationData);
        if (empty($loanAppToProcess->getOriginal('status')) && empty($loanAppToProcess->status)) {
          $loanAppToProcess->status = LoanApplication::STATUS_DRAFT;
        }
        $loanAppToProcess->save();

        $loanAppToProcess->applicationItems()->delete();
        $loanAppToProcess->applicationItems()->createMany($validatedData['loan_application_items']);
        $message = __('Loan application updated successfully.');
      } else {
        $applicationData['status'] = LoanApplication::STATUS_DRAFT;
        $loanAppToProcess = LoanApplication::create($applicationData);
        $loanAppToProcess->applicationItems()->createMany($validatedData['loan_application_items']);
        $message = __('Loan application draft saved successfully.');
      }

      if ($validatedData['applicant_confirmation']) {
        if ($loanAppToProcess->status === LoanApplication::STATUS_DRAFT || $isUpdating) { // Allow re-submission if updating
          // The submitForApproval method should handle setting applicant_confirmation_timestamp, submitted_at, and updating status.
          $loanAppToProcess->submitForApproval('Submitted by applicant via form.', $currentUser->id); // [cite: 127, 128]
          $message = __('Loan application submitted successfully for approval.');
        }
      }

      DB::commit();
      session()->flash('success', $message);

      return redirect()->route('my-applications.loan.show', $loanAppToProcess->id);
    } catch (ValidationException $e) {
      DB::rollBack();
      throw $e;
    } catch (Throwable $e) {
      DB::rollBack();
      Log::error('Error saving/updating loan application: ' . $e->getMessage(), [
        'exception' => $e,
        'data' => $validatedData ?? $this->all(),
      ]);
      session()->flash('error', __('An error occurred: ') . $e->getMessage());
      return null;
    }
  }

  public function resetForm(): void
  {
    $this->resetErrorBag();
    $this->resetValidation();

    $isEditMode = $this->loanApplicationInstance && $this->loanApplicationInstance->exists;

    // General fields
    $this->purpose = $isEditMode ? ($this->loanApplicationInstance->purpose ?? '') : '';
    $this->location = $isEditMode ? ($this->loanApplicationInstance->location ?? '') : '';
    $this->return_location = $isEditMode ? ($this->loanApplicationInstance->return_location ?? '') : '';
    $this->loan_start_date = $isEditMode ? $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_start_date) : null;
    $this->loan_end_date = $isEditMode ? $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_end_date) : null;

    // Responsible officer fields - reset first, then conditionally populate if editing
    $this->applicant_is_responsible_officer = true; // Default
    $this->responsible_officer_name = '';
    $this->responsible_officer_position_grade = '';
    $this->responsible_officer_phone = '';

    if ($isEditMode) {
      if ($this->loanApplicationInstance->responsible_officer_id && $this->loanApplicationInstance->responsible_officer_id !== Auth::id()) {
        $this->applicant_is_responsible_officer = false;
        $responsibleOfficer = $this->loanApplicationInstance->responsibleOfficer;
        if ($responsibleOfficer) {
          $this->responsible_officer_name = $responsibleOfficer->name;
          $this->responsible_officer_position_grade = trim((optional($responsibleOfficer->position)->name ?? '') . ' (' . (optional($responsibleOfficer->grade)->name ?? '') . ')', ' ()');
          $this->responsible_officer_phone = $responsibleOfficer->mobile_number ?? '';
        }
        // If $responsibleOfficer is not found, fields remain blank as per initial reset for these.
      }
      $this->loan_application_items = $this->loanApplicationInstance->applicationItems
        ?->map(fn($item) => $item->only(['equipment_type', 'quantity_requested', 'notes']))
        ->toArray() ?? [];
      if (empty($this->loan_application_items)) $this->addLoanItem(); // Ensure at least one item row if existing had none (unlikely but safe)
      $this->applicant_confirmation = (bool)$this->loanApplicationInstance->applicant_confirmation_timestamp;
    } else {
      $this->applicationId = null;
      $this->loanApplicationInstance = null; // Ensure it's null for new forms
      $this->loan_application_items = [];
      $this->addLoanItem(); // Add one default item for new forms
      $this->applicant_confirmation = false;
    }

    $this->populateApplicantDetails(); // Ensure applicant details are correctly set/refreshed
    $this->updateTotalItems();
    $this->dispatch('formResettled');
  }

  public function render(): View
  {
    if (empty($this->applicantName) && Auth::check()) {
      $this->populateApplicantDetails();
    }
    return view('livewire.resource-management.loan-application.application-form');
  }

  protected function updateTotalItems(): void
  {
    $this->totalItems = 0;
    foreach ($this->loan_application_items as $item) {
      $this->totalItems += (int)($item['quantity_requested'] ?? 0);
    }
  }

  protected function rules(): array
  {
    $now = Carbon::now()->startOfDay(); // Use a fixed "now" for consistent validation within the request

    return [
      'applicant_phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
      'purpose' => ['required', 'string', 'min:10', 'max:1000'],
      'location' => ['required', 'string', 'min:5', 'max:255'],
      'return_location' => ['nullable', 'string', 'max:255', Rule::when((bool)$this->location, ['different:location'])],
      'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:' . $now->toDateTimeString()],
      'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:loan_start_date'],

      'applicant_is_responsible_officer' => ['required', 'boolean'],
      'responsible_officer_name' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:255'],
      'responsible_officer_position_grade' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:255'],
      'responsible_officer_phone' => [Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],

      'loan_application_items' => ['required', 'array', 'min:1'],
      'loan_application_items.*.equipment_type' => ['required', 'string', 'max:255'],
      'loan_application_items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'],
      'loan_application_items.*.notes' => ['nullable', 'string', 'max:500'],

      'applicant_confirmation' => ['accepted'], // As per PDF, confirmation leads to submission [cite: 127, 128]
    ];
  }

  protected function messages(): array
  {
    $messages = [
      'applicant_phone.required' => __('Your phone number is required.'),
      'applicant_phone.regex' => __('Please enter a valid phone number.'),
      'purpose.required' => __('The purpose of the loan is required.'),
      'purpose.min' => __('The purpose must be at least 10 characters.'),
      'location.required' => __('The location of use is required.'),
      'location.min' => __('The location must be at least 5 characters.'),
      'return_location.different' => __('The return location must be different from the usage location if specified.'),
      'loan_start_date.required' => __('Loan start date and time are required.'),
      'loan_start_date.after_or_equal' => __('Loan start date cannot be in the past.'), // This is a sensible addition, PDF mentions date order [cite: 182]
      'loan_end_date.required' => __('Loan end date and time are required.'),
      'loan_end_date.after_or_equal' => __('Loan end date must be after or equal to the start date/time.'), // [cite: 182]

      'responsible_officer_name.requiredIf' => __('Responsible officer name is required if not the applicant.'),
      'responsible_officer_position_grade.requiredIf' => __('Responsible officer position & grade are required if not the applicant.'),
      'responsible_officer_phone.requiredIf' => __('Responsible officer phone is required if not the applicant.'),
      'responsible_officer_phone.regex' => __('Please enter a valid phone number for the responsible officer.'),

      'loan_application_items.min' => __('At least one equipment item must be requested.'), // [cite: 126] implicitly requires items
      'applicant_confirmation.accepted' => __('You must agree to the terms and conditions to proceed.'), // [cite: 127, 128]
    ];

    foreach ($this->loan_application_items as $index => $item) {
      $itemNumber = $index + 1;
      $messages["loan_application_items.{$index}.equipment_type.required"] = __("The equipment type for item #{$itemNumber} is required.");
      $messages["loan_application_items.{$index}.quantity_requested.required"] = __("The quantity for item #{$itemNumber} is required.");
      $messages["loan_application_items.{$index}.quantity_requested.min"] = __("The quantity for item #{$itemNumber} must be at least 1.");
    }
    return $messages;
  }

  // Format Carbon instance or date string to 'Y-m-d\TH:i' for datetime-local input
  private function formatDateForDatetimeLocalInput($dateValue): ?string
  {
    if ($dateValue instanceof Carbon) {
      return $dateValue->format('Y-m-d\TH:i');
    }
    if (is_string($dateValue)) {
      try {
        return Carbon::parse($dateValue)->format('Y-m-d\TH:i');
      } catch (\Exception $e) {
        Log::warning("Failed to parse date for datetime-local input: {$dateValue}", ['exception' => $e]);
        return null;
      }
    }
    return null;
  }
}
