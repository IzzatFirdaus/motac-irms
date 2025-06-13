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
     * @return string The translated page title.
     */
    public function generatePageTitle(): string
    {
        return $this->isEditMode
            ? __('forms.title_edit_application_ict')
            : __('forms.title_new_application_ict');
    }

    /**
     * Loads form state from the browser's localStorage cache.
     * This is called by the Alpine.js component on initialization.
     * @param array $cachedData The data retrieved from localStorage.
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
        }
    }

    /**
     * Initializes the form for a new application, pre-filling applicant data.
     */
    private function initializeNewApplication(): void
    {
        $this->authorize('create', LoanApplication::class);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->applicantName = $user->name;
        $this->applicantPositionAndGrade = ($user->position?->name ?? 'N/A') . ' (' . ($user->grade?->name ?? 'N/A') . ')';
        $this->applicantDepartment = $user->department?->name ?? 'N/A';
        $this->applicant_phone = $user->mobile_number ?? '';
        $this->addLoanItem(); // Start with one empty item row
    }

    /**
     * Loads an existing application's data into the form for editing.
     */
    private function loadExistingApplication(): void
    {
        try {
            /** @var LoanApplication $application */
            $application = LoanApplication::with([
                'user', 'responsibleOfficer', 'supportingOfficer', 'loanApplicationItems'
            ])->findOrFail($this->editing_application_id);

            $this->loanApplicationInstance = $application;
            $this->authorize('update', $this->loanApplicationInstance);

            $user = $application->user;
            $this->applicantName = $user?->name ?? '';
            $this->applicantPositionAndGrade = ($user?->position?->name ?? 'N/A') . ' (' . ($user?->grade?->name ?? 'N/A') . ')';
            $this->applicantDepartment = $user?->department?->name ?? 'N/A';
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

            $this->loan_application_items = $application->loanApplicationItems->map(fn ($item) =>
                $item->only(['id', 'equipment_type', 'quantity_requested', 'notes'])
            )->toArray();

            if (empty($this->loan_application_items)) {
                $this->addLoanItem();
            }
        } catch (Throwable $e) {
            Log::error('Error loading existing loan application: ' . $e->getMessage(), ['id' => $this->editing_application_id]);
            session()->flash('error', 'Gagal memuatkan data permohonan yang sedia ada.');
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    /**
     * Loads options for select dropdowns like equipment types and officers.
     */
    private function loadSelectOptions(): void
    {
        $this->equipmentTypeOptions = Equipment::getAssetTypeOptions();

        // Load users who can be responsible/supporting officers, excluding the current user.
        $officerList = User::where('status', 'active')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->supportingOfficerOptions = $officerList->pluck('name', 'id')->toArray();
        $this->responsibleOfficerOptions = $officerList->pluck('name', 'id')->toArray();
    }

    /**
     * Adds a new, empty item row to the application.
     */
    public function addLoanItem(): void
    {
        $this->loan_application_items[] = ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    }

    /**
     * Removes an item row from the application.
     */
    public function removeLoanItem(int $index): void
    {
        if (count($this->loan_application_items) > 1) {
            unset($this->loan_application_items[$index]);
            $this->loan_application_items = array_values($this->loan_application_items); // Re-index array
        } else {
            $this->dispatch('swal:info', title: 'Tidak Dibenarkan', message: 'Setiap permohonan mesti mempunyai sekurang-kurangnya satu item.');
        }
    }

    /**
     * Handles changes to the "applicant is responsible officer" checkbox.
     */
    public function updatedApplicantIsResponsibleOfficer(bool $value): void
    {
        if ($value) {
            $this->responsible_officer_id = null;
        }
    }

    /**
     * Defines the validation rules for the form.
     */
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
            'responsible_officer_id' => [
                Rule::requiredIf(!$this->applicant_is_responsible_officer),
                'nullable',
                'exists:users,id',
            ],
            'supporting_officer_id' => [
                'nullable', // Supporting officer is optional on draft, but validated by the service on submission.
                'exists:users,id',
            ],
            'loan_application_items' => ['required', 'array', 'min:1'],
            'loan_application_items.*.id' => ['nullable', 'integer'],
            'loan_application_items.*.equipment_type' => ['required', 'string', Rule::in(array_keys($this->equipmentTypeOptions))],
            'loan_application_items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:10'],
            'loan_application_items.*.notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($forSubmission) {
            $rules['applicant_confirmation'] = ['accepted'];
            // Make supporting officer required only on final submission.
            $rules['supporting_officer_id'][0] = 'required';
        }

        return $rules;
    }

    /**
     * Defines custom validation messages.
     */
    public function messages(): array
    {
        $messages = [
            'applicant_phone.regex' => 'Format nombor telefon tidak sah. Cth: 0123456789',
            'purpose.required' => 'Sila nyatakan tujuan permohonan.',
            'loan_start_date.after_or_equal' => 'Tarikh pinjaman mesti bermula dari hari ini atau akan datang.',
            'loan_end_date.after' => 'Tarikh pulang mesti selepas tarikh pinjaman.',
            'responsible_officer_id.required_if' => 'Sila pilih Pegawai Bertanggungjawab.',
            'supporting_officer_id.required' => 'Sila pilih Pegawai Penyokong untuk menghantar permohonan.',
            'applicant_confirmation.accepted' => 'Anda mesti bersetuju dengan perakuan pemohon untuk menghantar.',
            'return_location.different' => 'Lokasi pemulangan mesti berbeza daripada lokasi penggunaan.',
        ];

        foreach ($this->loan_application_items as $index => $item) {
            $itemNumber = $index + 1;
            $messages["loan_application_items.{$index}.equipment_type.required"] = "Sila pilih jenis peralatan untuk Item #{$itemNumber}.";
            $messages["loan_application_items.{$index}.quantity_requested.required"] = "Sila masukkan kuantiti untuk Item #{$itemNumber}.";
        }

        return $messages;
    }

    /**
     * Saves the form data as a draft. This action is now completely independent.
     */
    public function saveAsDraft(LoanApplicationService $service): ?RedirectResponse
    {
        try {
            // Rule `false` means it's a draft, no confirmation needed.
            $validatedData = $this->validate($this->rules(false), $this->messages());

            $serviceData = Arr::except($validatedData, ['loan_application_items', 'applicant_is_responsible_officer']);
            $serviceData['items'] = $validatedData['loan_application_items'];

            DB::beginTransaction();
            $user = Auth::user();
            $application = null;

            if ($this->isEditMode && $this->loanApplicationInstance) {
                $this->authorize('update', $this->loanApplicationInstance);
                // In edit mode, we just update the application as a draft.
                $application = $service->updateApplication($this->loanApplicationInstance, $serviceData, $user);
            } else {
                $this->authorize('create', LoanApplication::class);
                // In create mode, we create a new application as a draft.
                $application = $service->createAndSubmitApplication($serviceData, $user, true);
            }

            DB::commit();

            session()->flash('success', 'Draf permohonan anda telah berjaya disimpan.');
            return redirect()->route('loan-applications.edit', ['loan_application' => $application->id]);

        } catch (ValidationException $e) {
            // Re-throw validation exception to let Livewire handle displaying errors.
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error saving draft loan application', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $this->dispatch('swal:error', title: 'Ralat!', message: 'Gagal menyimpan draf: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Submits the form data for approval. This action is now completely independent.
     */
    public function submitLoanApplication(LoanApplicationService $service): ?RedirectResponse
    {
        try {
            // Rule `true` enforces final submission rules (e.g., confirmation checkbox).
            $validatedData = $this->validate($this->rules(true), $this->messages());

            $serviceData = Arr::except($validatedData, ['loan_application_items', 'applicant_is_responsible_officer']);
            $serviceData['items'] = $validatedData['loan_application_items'];

            DB::beginTransaction();
            $user = Auth::user();
            $application = null;

            if ($this->isEditMode && $this->loanApplicationInstance) {
                $this->authorize('update', $this->loanApplicationInstance);
                // First, update the application with any changes.
                $application = $service->updateApplication($this->loanApplicationInstance, $serviceData, $user);
                // Then, submit the updated application for approval.
                $application = $service->submitApplicationForApproval($application, $user);
            } else {
                $this->authorize('create', LoanApplication::class);
                // In create mode, create and submit the application in one go.
                $application = $service->createAndSubmitApplication($serviceData, $user, false);
            }

            DB::commit();

            session()->flash('success', 'Permohonan anda telah berjaya dihantar untuk kelulusan.');
            return redirect()->route('loan-applications.show', ['loan_application' => $application->id]);

        } catch (ValidationException $e) {
            // Re-throw validation exception.
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error submitting loan application', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $this->dispatch('swal:error', title: 'Ralat!', message: 'Gagal menghantar permohonan: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Formats a date string for a date input field.
     */
    private function formatDateForInput($date): ?string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d\TH:i') : null;
    }

    /**
     * Renders the component view.
     */
    public function render(): View
    {
        return view('livewire.resource-management.loan-application.application-form');
    }
}
