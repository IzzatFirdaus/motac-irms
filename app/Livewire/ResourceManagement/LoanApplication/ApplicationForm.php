<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\LoanApplication;

use App\Models\LoanApplication;
use App\Models\User; // For type hinting and fetching user details
use App\Services\LoanApplicationService; // For submission logic
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Throwable; // Import Throwable

// System Design Reference: Sections 3.1 (Livewire Components), 5.2 (ICT Loan Workflow), 6.3 (Dynamic Forms)

final class ApplicationForm extends Component
{
    use AuthorizesRequests;

    // BAHAGIAN 1: MAKLUMAT PEMOHON
    public string $applicantName = ''; // Pre-filled, display only
    public string $applicantPositionAndGrade = ''; // Pre-filled, display only
    public string $applicantDepartment = ''; // Pre-filled, display only
    public string $applicant_phone = ''; // Editable, pre-filled [cite: 69]
    public string $purpose = ''; // [cite: 85]
    public string $location = ''; // Lokasi Penggunaan [cite: 85]
    public ?string $return_location = ''; // Lokasi Dijangka Pulang / Pemulangan [cite: 85]
    public ?string $loan_start_date = null; // Datetime string for input [cite: 85]
    public ?string $loan_end_date = null;   // Datetime string for input [cite: 85]

    // BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB
    public bool $applicant_is_responsible_officer = true;
    public ?int $responsible_officer_id = null; // FK to users table [cite: 85]

    // MAKLUMAT PEGAWAI PENYOKONG (System Design Ref: 5.2.4)
    public ?int $supporting_officer_id = null; // FK to users table [cite: 85, 129]

    // BAHAGIAN 3: MAKLUMAT PERALATAN (System Design Ref: 5.2.2)
    public array $loan_application_items = []; // Holds items for the form [cite: 88]

    // BAHAGIAN 4: PENGESAHAN PEMOHON (System Design Ref: 5.2.3)
    public bool $applicant_confirmation = false; // [cite: 85]

    // Component State
    public ?int $editing_application_id = null; // Stores the ID of the application being edited
    public ?LoanApplication $loanApplicationInstance = null;

    public int $totalItems = 0; // Not directly submitted, but useful for display

    // For dropdowns
    public array $systemUsersForResponsibleOfficer = [];
    public array $systemUsersForSupportingOfficer = [];
    public array $equipmentTypeOptions = [];


    public function mount($loan_application_id = null): void
    {
        if (!Auth::check()) {
            session()->flash('error', __('Anda mesti log masuk untuk membuat atau mengemaskini permohonan.'));
            // In a real app, middleware should handle this, or you might redirect.
            return;
        }

        $this->populateApplicantDetails();
        $this->loadInitialDropdownData();

        if ($loan_application_id) {
            $this->editing_application_id = (int) $loan_application_id;
            $this->loanApplicationInstance = LoanApplication::with([
                'user', 'responsibleOfficer', 'supportingOfficer', 'applicationItems'
            ])->find($this->editing_application_id);

            if (!$this->loanApplicationInstance) {
                session()->flash('error', __('Permohonan pinjaman tidak ditemui.'));
                Log::error("LoanApplication not found for ID: " . $this->editing_application_id . " in ApplicationForm mount for editing.");
                $this->loanApplicationInstance = new LoanApplication(); // Initialize to prevent errors in populateForm
                // Ideally, redirect or show a distinct error state in render()
                return;
            }
            $this->authorize('update', $this->loanApplicationInstance); // Policy check
            $this->populateFormFromInstance();
        } else {
            // CREATE MODE
            $this->authorize('create', LoanApplication::class); // Policy check
            $this->loanApplicationInstance = new LoanApplication(); // For type consistency
            $this->resetFormForCreate();
        }
        $this->updateTotalItems();
    }

    private function populateFormFromInstance(): void
    {
        if (!$this->loanApplicationInstance || !$this->loanApplicationInstance->exists) return;

        $this->applicant_phone = $this->loanApplicationInstance->applicant_phone ?? Auth::user()->mobile_number ?? '';
        $this->purpose = $this->loanApplicationInstance->purpose ?? '';
        $this->location = $this->loanApplicationInstance->location ?? '';
        $this->return_location = $this->loanApplicationInstance->return_location ?? '';
        $this->loan_start_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_start_date);
        $this->loan_end_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_end_date);
        $this->supporting_officer_id = $this->loanApplicationInstance->supporting_officer_id;

        if ($this->loanApplicationInstance->responsible_officer_id &&
            $this->loanApplicationInstance->responsible_officer_id !== $this->loanApplicationInstance->user_id) {
            $this->applicant_is_responsible_officer = false;
            $this->responsible_officer_id = $this->loanApplicationInstance->responsible_officer_id;
        } else {
            $this->applicant_is_responsible_officer = true;
            $this->responsible_officer_id = null;
        }

        $this->loan_application_items = $this->loanApplicationInstance->applicationItems
            ?->map(fn($item) => $item->only(['equipment_type', 'quantity_requested', 'notes', 'id'])) // Include ID for updates
            ->toArray() ?? [];
        if (empty($this->loan_application_items)) {
            $this->addLoanItem();
        }

        $this->applicant_confirmation = (bool)$this->loanApplicationInstance->applicant_confirmation_timestamp;
    }

    public function populateApplicantDetails(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->applicantName = $user->name; // [cite: 69]
            $this->applicantPositionAndGrade = trim((optional($user->position)->name ?? '') . ' (' . (optional($user->grade)->name ?? '') . ')', ' ()'); // [cite: 69, 70]
            $this->applicantDepartment = optional($user->department)->name ?? ''; // [cite: 70]
            if (empty($this->applicant_phone) && !$this->editing_application_id) {
                $this->applicant_phone = $user->mobile_number ?? ''; // [cite: 70]
            }
        }
    }

    public function loadInitialDropdownData(): void
    {
        $this->systemUsersForResponsibleOfficer = User::where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->get()
            ->pluck('name_with_position_grade', 'id') // Assumes User model has name_with_position_grade accessor
            ->toArray();

        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41); // [cite: 62]
        $this->systemUsersForSupportingOfficer = User::where('status', User::STATUS_ACTIVE)
            ->whereHas('grade', function ($query) use ($minSupportGradeLevel) {
                $query->where('level', '>=', $minSupportGradeLevel); // [cite: 75, 129]
            })
            ->orderBy('name')
            ->get()
            ->pluck('name_with_position_grade', 'id')
            ->toArray();

        $this->equipmentTypeOptions = \App\Models\Equipment::getAssetTypeOptions(); // Assumes method exists [cite: 78]
    }

    public function addLoanItem(): void
    {
        $this->loan_application_items[] = ['id' => null, 'equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
        $this->updateTotalItems();
        $this->dispatch('loanItemAdded');
    }

    public function removeLoanItem(int $index): void
    {
        if (count($this->loan_application_items) > 1 && isset($this->loan_application_items[$index])) {
            // If item has an ID, it means it exists in DB, mark for deletion on save
            if (!empty($this->loan_application_items[$index]['id'])) {
                 $this->loan_application_items[$index]['_delete'] = true; // Mark for soft delete
            } else {
                unset($this->loan_application_items[$index]); // Remove if not yet saved
                $this->loan_application_items = array_values($this->loan_application_items);
            }
            $this->updateTotalItems();
        } else {
            session()->flash('error_toast', __('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));
        }
    }

    public function saveAsDraft(): ?RedirectResponse
    {
        if ($this->editing_application_id && $this->loanApplicationInstance) {
            $this->authorize('update', $this->loanApplicationInstance);
        } else {
            $this->authorize('create', LoanApplication::class);
        }
        $validatedData = $this->validate($this->rules(false), $this->messages());
        return $this->processSave($validatedData, true);
    }

    public function submitForApproval(): ?RedirectResponse
    {
        if ($this->editing_application_id && $this->loanApplicationInstance) {
            if (in_array($this->loanApplicationInstance->status, [LoanApplication::STATUS_DRAFT, LoanApplication::STATUS_REJECTED])) {
                $this->authorize('submit', $this->loanApplicationInstance);
            } else {
                $this->authorize('update', $this->loanApplicationInstance); // If updating an already non-draft/non-rejected app
            }
        } else {
            $this->authorize('create', LoanApplication::class);
        }
        $validatedData = $this->validate($this->rules(true), $this->messages());
        return $this->processSave($validatedData, false);
    }

    private function processSave(array $validatedData, bool $isDraft): ?RedirectResponse
    {
        DB::beginTransaction();
        try {
            $currentUser = Auth::user();
            $isUpdating = (bool) $this->editing_application_id;

            // Prepare core application data
            $applicationCoreData = [
                'user_id' => $isUpdating ? $this->loanApplicationInstance->user_id : $currentUser->id,
                'applicant_phone' => $validatedData['applicant_phone'],
                'purpose' => $validatedData['purpose'],
                'location' => $validatedData['location'],
                'return_location' => $validatedData['return_location'] ?? null,
                'loan_start_date' => Carbon::parse($validatedData['loan_start_date']),
                'loan_end_date' => Carbon::parse($validatedData['loan_end_date']),
                'supporting_officer_id' => $validatedData['supporting_officer_id'], // [cite: 85, 129]
                'responsible_officer_id' => $validatedData['applicant_is_responsible_officer']
                    ? ($isUpdating ? $this->loanApplicationInstance->user_id : $currentUser->id)
                    : ($validatedData['responsible_officer_id'] ?? null), // [cite: 85]
            ];

            $loanAppToProcess = $this->loanApplicationInstance;
            $message = '';

            if ($isUpdating && $loanAppToProcess && $loanAppToProcess->exists) {
                // Ensure status is DRAFT if saving as draft.
                // If submitting, the LoanApplicationService will handle status transition.
                $applicationCoreData['status'] = $isDraft ? LoanApplication::STATUS_DRAFT : $loanAppToProcess->status; // Preserve current status unless explicitly drafting

                // Update existing application
                app(LoanApplicationService::class)->updateApplication($loanAppToProcess, $applicationCoreData, $currentUser); // Service handles item sync
                $message = $isDraft ? __('Draf permohonan pinjaman berjaya dikemaskini.') : __('Permohonan pinjaman berjaya dikemaskini.');

                if ($isDraft && $loanAppToProcess->status !== LoanApplication::STATUS_DRAFT) {
                    $loanAppToProcess->status = LoanApplication::STATUS_DRAFT;
                    // Reset submission-related fields if moving back to draft
                    $loanAppToProcess->submitted_at = null;
                    $loanAppToProcess->applicant_confirmation_timestamp = null; // Or keep if preferred
                    $loanAppToProcess->current_approval_stage = null;
                    $loanAppToProcess->current_approval_officer_id = null;
                    $loanAppToProcess->save(); // Save status change
                }

            } else { // Creating new application
                // $applicationCoreData includes items and confirmation
                $loanAppToProcess = app(LoanApplicationService::class)->createAndSubmitApplication(array_merge($applicationCoreData, [
                    'items' => $validatedData['loan_application_items'],
                    'applicant_confirmation' => $validatedData['applicant_confirmation'] // Only for actual submission
                ]), $currentUser, $isDraft); // Pass isDraft to service
                $message = $isDraft ? __('Draf permohonan pinjaman berjaya disimpan.') : __('Permohonan pinjaman berjaya dibuat dan dihantar.');
            }

            // If submitting a non-draft application (either new or update of draft/rejected)
            if (!$isDraft) {
                if (!($isUpdating && $loanAppToProcess->status !== LoanApplication::STATUS_DRAFT && $loanAppToProcess->status !== LoanApplication::STATUS_REJECTED)) {
                    // This means it's a new submission or resubmission of draft/rejected
                     app(LoanApplicationService::class)->submitApplicationForApproval($loanAppToProcess, $currentUser);
                     $message = __('Permohonan pinjaman berjaya dihantar untuk kelulusan.');
                }
                // Else: if it's an update to an already submitted application, its status might be handled differently.
            }


            DB::commit();
            session()->flash('success', $message);
            // As per web.php, the show route for Livewire is 'my-applications.loan.show'
            return redirect()->route('loan-applications.show', $loanAppToProcess->id); // [cite: 218]

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e; // Livewire handles displaying these errors
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ApplicationForm processSave: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $validatedData ?? $this->all(),
            ]);
            $this->addError('general', __('Sistem ralat semasa memproses permohonan: ') . $e->getMessage());
            return null;
        }
    }

    public function resetFormForCreate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->editing_application_id = null;
        $this->loanApplicationInstance = new LoanApplication();

        $this->purpose = '';
        $this->location = '';
        $this->return_location = '';
        $this->loan_start_date = null;
        $this->loan_end_date = null;
        $this->supporting_officer_id = null;
        $this->applicant_is_responsible_officer = true;
        $this->responsible_officer_id = null;
        $this->loan_application_items = [];
        $this->addLoanItem();
        $this->applicant_confirmation = false;
        $this->updateTotalItems();
        $this->dispatch('formResettled');
    }

    public function render(): View
    {
        if (empty($this->applicantName) && Auth::check()) {
            $this->populateApplicantDetails();
        }
        if ((empty($this->systemUsersForSupportingOfficer) || empty($this->equipmentTypeOptions)) && Auth::check()) {
            $this->loadInitialDropdownData();
        }

        return view('livewire.resource-management.loan-application.application-form', [
            'isEdit' => (bool) $this->editing_application_id,
        ]);
    }

    protected function updateTotalItems(): void
    {
        $this->totalItems = 0;
        foreach ($this->loan_application_items as $item) {
            if (empty($item['_delete'])) { // Only count items not marked for deletion
                 $this->totalItems += (int)($item['quantity_requested'] ?? 0);
            }
        }
    }

    protected function rules(bool $isSubmittingForApproval = false): array
    {
        $now = Carbon::now()->startOfDay();

        $rules = [
            'applicant_phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255', Rule::when((bool)$this->location, ['different:location'])],
            'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:' . $now->toDateTimeString()],
            'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:loan_start_date'],

            'applicant_is_responsible_officer' => ['required', 'boolean'],
            'responsible_officer_id' => [
                Rule::requiredIf(!$this->applicant_is_responsible_officer),
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('status', User::STATUS_ACTIVE)->whereNull('deleted_at')) // [cite: 69]
            ],

            'supporting_officer_id' => [
                Rule::requiredIf($isSubmittingForApproval), // Required for actual submission
                'nullable', // Allow null for draft
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41); // [cite: 62]
                    $query->where('status', User::STATUS_ACTIVE) // [cite: 69]
                          ->whereHas('grade', fn($gq) => $gq->where('level', '>=', $minSupportGradeLevel)) // [cite: 75]
                          ->whereNull('deleted_at');
                })
            ],

            'loan_application_items' => ['required', 'array', 'min:1'],
            'loan_application_items.*.id' => ['nullable', 'integer'], // For existing items during update
            'loan_application_items.*._delete' => ['nullable', 'boolean'], // For marking items for deletion
            'loan_application_items.*.equipment_type' => ['required_unless:loan_application_items.*._delete,true', 'nullable', 'string', 'max:255', Rule::in(array_keys($this->equipmentTypeOptions))], // [cite: 78]
            'loan_application_items.*.quantity_requested' => ['required_unless:loan_application_items.*._delete,true', 'nullable', 'integer', 'min:1', 'max:100'],
            'loan_application_items.*.notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isSubmittingForApproval) {
            $rules['applicant_confirmation'] = ['accepted']; // [cite: 85]
        } else {
            $rules['applicant_confirmation'] = ['nullable','boolean'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        $messages = [
            'applicant_phone.required' => __('Nombor telefon anda diperlukan.'),
            'applicant_phone.regex' => __('Sila masukkan nombor telefon yang sah.'),
            'purpose.required' => __('Tujuan pinjaman diperlukan.'),
            'purpose.min' => __('Tujuan mesti sekurang-kurangnya 10 aksara.'),
            'location.required' => __('Lokasi penggunaan diperlukan.'),
            'location.min' => __('Lokasi mesti sekurang-kurangnya 5 aksara.'),
            'return_location.different' => __('Lokasi pemulangan mesti berbeza daripada lokasi penggunaan jika dinyatakan.'),
            'loan_start_date.required' => __('Tarikh dan masa mula pinjaman diperlukan.'),
            'loan_start_date.after_or_equal' => __('Tarikh mula pinjaman tidak boleh pada masa lalu.'),
            'loan_end_date.required' => __('Tarikh dan masa tamat pinjaman diperlukan.'),
            'loan_end_date.after_or_equal' => __('Tarikh tamat pinjaman mesti selepas atau sama dengan tarikh/masa mula.'),

            'responsible_officer_id.requiredIf' => __('Pegawai bertanggungjawab mesti dipilih jika bukan pemohon.'),
            'supporting_officer_id.requiredIf' => __('Pegawai penyokong mesti dipilih untuk menghantar permohonan.'),
            'supporting_officer_id.exists' => __('Pegawai penyokong yang dipilih tidak sah atau tidak memenuhi syarat gred minima.'),

            'loan_application_items.min' => __('Sekurang-kurangnya satu item peralatan mesti diminta.'),
            'applicant_confirmation.accepted' => __('Anda mesti bersetuju dengan perakuan untuk menghantar permohonan.'),
        ];

        foreach ($this->loan_application_items as $index => $item) {
            if (isset($item['_delete']) && $item['_delete']) continue; // Skip messages for items marked for deletion

            $itemNumber = $index + 1;
            $messages["loan_application_items.{$index}.equipment_type.required_unless"] = __("Jenis peralatan untuk item #{$itemNumber} diperlukan.");
            $messages["loan_application_items.{$index}.equipment_type.in"] = __("Jenis peralatan untuk item #{$itemNumber} tidak sah.");
            $messages["loan_application_items.{$index}.quantity_requested.required_unless"] = __("Kuantiti untuk item #{$itemNumber} diperlukan.");
            $messages["loan_application_items.{$index}.quantity_requested.min"] = __("Kuantiti untuk item #{$itemNumber} mesti sekurang-kurangnya 1.");
        }
        return $messages;
    }

    private function formatDateForDatetimeLocalInput($dateValue): ?string
    {
        if ($dateValue instanceof Carbon) {
            return $dateValue->format('Y-m-d\TH:i');
        }
        if (is_string($dateValue)) {
            try {
                return Carbon::parse($dateValue)->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
                Log::warning("Gagal memformat tarikh untuk input datetime-local: {$dateValue}", ['exception' => $e->getMessage()]);
                return null;
            }
        }
        return null;
    }
}
