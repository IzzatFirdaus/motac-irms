<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\LoanApplication;

use App\Models\LoanApplication;
use App\Models\User;
use App\Models\Equipment; // Added for Equipment::getAssetTypeOptions()
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
// use Livewire\Attributes\Title; // Using event dispatch for title as per original file
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class ApplicationForm extends Component
{
    use AuthorizesRequests;

    // --- BAHAGIAN 1: MAKLUMAT PEMOHON ---
    public string $applicantName = '';
    public string $applicantPositionAndGrade = '';
    public string $applicantDepartment = '';
    public string $applicant_phone = '';
    public string $purpose = '';
    public string $location = '';
    public ?string $return_location = null;
    public ?string $loan_start_date = null;
    public ?string $loan_end_date = null;

    // --- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB ---
    public bool $applicant_is_responsible_officer = true;
    public ?int $responsible_officer_id = null;

    // --- MAKLUMAT PEGAWAI PENYOKONG ---
    public ?int $supporting_officer_id = null;

    // --- BAHAGIAN 3: MAKLUMAT PERALATAN ---
    public array $loan_application_items = [];

    // --- BAHAGIAN 4: PENGESAHAN PEMOHON ---
    public bool $applicant_confirmation = false;

    // --- Component State ---
    public ?int $editing_application_id = null;
    public ?LoanApplication $loanApplicationInstance = null;

    public int $totalQuantityRequested = 0;

    // --- Data for Dropdowns ---
    public array $responsibleOfficerOptions = [];
    public array $supportingOfficerOptions = [];
    public array $equipmentTypeOptions = [];

    // Method to generate page title
    public function generatePageTitle(): string
    {
        $baseTitle = $this->editing_application_id ? __('Kemaskini Permohonan Pinjaman ICT') : __('Borang Permohonan Pinjaman ICT Baharu');
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
        return $baseTitle . ' - ' . $appName;
    }

    public function mount($loan_application_id = null): void
    {
        if (!Auth::check()) {
            session()->flash('error', __('Sesi anda telah tamat. Sila log masuk semula.'));
            $this->dispatch('update-page-title', title: __('Akses Tidak Dibenarkan') . ' - ' . __(config('variables.templateName', 'Sistem')));
            // Consider redirecting to login page
            // $this->redirectRoute('login'); // Example
            return;
        }

        $this->populateApplicantDetails();
        $this->loadInitialDropdownData();

        if ($loan_application_id) {
            $this->editing_application_id = (int) $loan_application_id;
            $this->loanApplicationInstance = LoanApplication::with([
                'user', 'responsibleOfficer', 'supportingOfficer', 'applicationItems',
            ])->find($this->editing_application_id);

            if (!$this->loanApplicationInstance) {
                session()->flash('error', __('Permohonan pinjaman ICT dengan ID :id tidak ditemui.', ['id' => $this->editing_application_id]));
                Log::error("LoanApplicationForm: LoanApplication not found for ID: " . $this->editing_application_id . " during mount for editing.");
                $this->loanApplicationInstance = new LoanApplication(); // Initialize to prevent errors on view
                $this->dispatch('update-page-title', title: __('Ralat Permohonan Tidak Ditemui') . ' - ' . __(config('variables.templateName', 'Sistem')));
                // Potentially redirect if not found
                // $this->redirectRoute('loan-applications.index'); // Example
                return;
            }
            $this->authorize('update', $this->loanApplicationInstance);
            $this->populateFormFromInstance();
        } else {
            $this->authorize('create', LoanApplication::class);
            $this->loanApplicationInstance = new LoanApplication();
            $this->resetFormForCreate(false); // Don't dispatch event on initial mount for new form
        }
        $this->dispatch('update-page-title', title: $this->generatePageTitle());
        $this->updateTotalQuantityRequested();
    }

    private function populateFormFromInstance(): void
    {
        if (!$this->loanApplicationInstance || !$this->loanApplicationInstance->exists) return;

        $this->applicant_phone = $this->loanApplicationInstance->applicant_phone ?? Auth::user()?->mobile_number ?? '';
        $this->purpose = $this->loanApplicationInstance->purpose ?? '';
        $this->location = $this->loanApplicationInstance->location ?? '';
        $this->return_location = $this->loanApplicationInstance->return_location;
        $this->loan_start_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_start_date);
        $this->loan_end_date = $this->formatDateForDatetimeLocalInput($this->loanApplicationInstance->loan_end_date);
        $this->supporting_officer_id = $this->loanApplicationInstance->supporting_officer_id;

        if ($this->loanApplicationInstance->responsible_officer_id &&
            $this->loanApplicationInstance->responsible_officer_id !== $this->loanApplicationInstance->user_id) {
            $this->applicant_is_responsible_officer = false;
            $this->responsible_officer_id = $this->loanApplicationInstance->responsible_officer_id;
        } else {
            $this->applicant_is_responsible_officer = true;
            $this->responsible_officer_id = Auth::id(); // Default to current user if applicant is responsible
        }

        $this->loan_application_items = $this->loanApplicationInstance->applicationItems
            ?->map(fn($item) => $item->only(['id', 'equipment_type', 'quantity_requested', 'notes']))
            ->toArray() ?? [];

        if (empty($this->loan_application_items)) {
            $this->addLoanItem(false); // Add one item if none exist, don't dispatch event
        }
        $this->applicant_confirmation = (bool) $this->loanApplicationInstance->applicant_confirmation_timestamp;
    }

    public function populateApplicantDetails(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $this->applicantName = $user->name;
            $this->applicantPositionAndGrade = trim(($user->position?->name ?? __('Tiada Jawatan')) . ' (' . ($user->grade?->name ?? __('Tiada Gred')) . ')', ' ()');
            $this->applicantDepartment = $user->department?->name ?? __('Tiada Jabatan');
            if (empty($this->applicant_phone) && !$this->editing_application_id) { // Only prefill phone for new applications if empty
                $this->applicant_phone = $user->mobile_number ?? '';
            }
        }
    }

    public function loadInitialDropdownData(): void
    {
        $this->responsibleOfficerOptions = User::where('status', User::STATUS_ACTIVE)
            ->orderBy('name')->get()->pluck('name_with_position_grade', 'id')->toArray();

        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
        $this->supportingOfficerOptions = User::where('status', User::STATUS_ACTIVE)
            ->whereHas('grade', fn ($query) => $query->where('level', '>=', $minSupportGradeLevel))
            ->orderBy('name')->get()->pluck('name_with_position_grade', 'id')->toArray();

        $this->equipmentTypeOptions = Equipment::getAssetTypeOptions() ?? [];
    }

    public function addLoanItem(bool $dispatchEvent = true): void
    {
        $this->loan_application_items[] = ['id' => null, 'equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
        $this->updateTotalQuantityRequested();
        if($dispatchEvent) $this->dispatch('loanItemAdded');
    }

    public function removeLoanItem(int $index): void
    {
        $activeItemsCount = collect($this->loan_application_items)->filter(fn($item) => empty($item['_delete']))->count();

        if ($activeItemsCount <= 1 && isset($this->loan_application_items[$index]) && empty($this->loan_application_items[$index]['_delete'])) {
            $this->dispatch('error-toast', ['type' => 'warning', 'message' => __('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan aktif.')]);
            return;
        }

        if (isset($this->loan_application_items[$index])) {
            if (!empty($this->loan_application_items[$index]['id'])) { // Item from DB
                $this->loan_application_items[$index]['_delete'] = true;
            } else { // Newly added item, not yet saved
                unset($this->loan_application_items[$index]);
                $this->loan_application_items = array_values($this->loan_application_items); // Re-index
            }
            $this->updateTotalQuantityRequested();
            $this->dispatch('loanItemRemoved');
        }
    }

    private function authorizeAction(): void
    {
        if ($this->editing_application_id && $this->loanApplicationInstance && $this->loanApplicationInstance->exists) {
            $this->authorize('update', $this->loanApplicationInstance);
        } else {
            $this->authorize('create', LoanApplication::class);
        }
    }

    public function saveAsDraft(): ?RedirectResponse
    {
        $this->authorizeAction();
        $validatedData = $this->validate($this->rules(false), $this->messages());
        return $this->processSave($validatedData, true);
    }

    public function submitForApproval(): ?RedirectResponse
    {
        $this->authorizeAction();
        $validatedData = $this->validate($this->rules(true), $this->messages());
        return $this->processSave($validatedData, false);
    }

    private function processSave(array $validatedData, bool $isDraft): ?RedirectResponse
    {
        DB::beginTransaction();
        try {
            /** @var User $currentUser */
            $currentUser = Auth::user();
            $isUpdating = (bool) $this->editing_application_id;

            $dataForService = $validatedData;
            $dataForService['user_id'] = $isUpdating && $this->loanApplicationInstance ? $this->loanApplicationInstance->user_id : $currentUser->id;
            $dataForService['responsible_officer_id'] = $validatedData['applicant_is_responsible_officer']
                ? ($isUpdating && $this->loanApplicationInstance ? $this->loanApplicationInstance->user_id : $currentUser->id)
                : ($validatedData['responsible_officer_id'] ?? null);
            $dataForService['applicant_confirmation_timestamp'] = (!$isDraft && ($validatedData['applicant_confirmation'] ?? false)) ? now() : null;
            $dataForService['items'] = $this->prepareItemsForService($validatedData['loan_application_items']);
            $dataForService['is_draft_submission'] = $isDraft;

            $loanAppService = app(LoanApplicationService::class);
            $processedApplication = $this->loanApplicationInstance ?? new LoanApplication();
            $successMessage = '';

            if ($isUpdating && $processedApplication->exists) {
                $processedApplication = $loanAppService->updateApplication(
                    $processedApplication,
                    $dataForService,
                    $currentUser
                );
                $successMessage = $isDraft ? __('Draf permohonan pinjaman berjaya dikemaskini.') : __('Permohonan pinjaman berjaya dikemaskini.');
            } else {
                $processedApplication = $loanAppService->createAndSubmitApplication(
                    $dataForService,
                    $currentUser,
                    $isDraft
                );
                $successMessage = $isDraft ? __('Draf permohonan pinjaman berjaya disimpan.') : __('Permohonan pinjaman berjaya dibuat.');
            }

            if (!$isDraft && $processedApplication && $processedApplication->status === LoanApplication::STATUS_DRAFT) {
                 if (method_exists($loanAppService, 'submitApplicationForApproval')) {
                    $loanAppService->submitApplicationForApproval($processedApplication, $currentUser);
                 }
                 $successMessage = __('Permohonan pinjaman #:id berjaya dihantar untuk kelulusan.', ['id' => $processedApplication->id]);
            } else if (!$isDraft && $processedApplication->status !== LoanApplication::STATUS_DRAFT) {
                 $successMessage = __('Permohonan pinjaman #:id telah dihantar untuk kelulusan.', ['id' => $processedApplication->id]);
            }

            DB::commit();
            session()->flash('success', $successMessage); // Use standard 'success' flash key
            // Dispatch toastr event for immediate feedback
            $this->dispatch('toastr', type: 'success', message: $successMessage);
            return redirect()->route('loan-applications.show', $processedApplication->id);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('LoanApplicationForm ValidationException during processSave.', ['user_id' => Auth::id(), 'errors' => $e->errors()]);
            // Let Livewire handle displaying validation errors automatically
            return null;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('LoanApplicationForm Error in processSave: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'data' => $validatedData ?? $this->all(),
            ]);
            $this->addError('general_error', __('Sistem menghadapi ralat semasa memproses permohonan anda. Sila cuba lagi atau hubungi pentadbir jika masalah berterusan.'));
            $this->dispatch('toastr', type: 'error', message: __('Ralat tidak dijangka semasa menyimpan permohonan.'));
            return null;
        }
    }

    private function prepareItemsForService(array $formItems): array
    {
        return collect($formItems)->map(function ($item) {
            if (isset($item['quantity_requested'])) {
                $item['quantity_requested'] = (int) $item['quantity_requested'];
            }
            return $item;
        })->all();
    }

    public function resetFormForCreate(bool $dispatchEvent = true): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->editing_application_id = null;
        $this->loanApplicationInstance = new LoanApplication();
        $this->purpose = '';
        $this->location = '';
        $this->return_location = null;
        $this->loan_start_date = null;
        $this->loan_end_date = null;
        $this->supporting_officer_id = null;
        $this->applicant_is_responsible_officer = true;
        $this->responsible_officer_id = Auth::id();
        $this->loan_application_items = [];
        $this->addLoanItem(false);
        $this->applicant_confirmation = false;
        $this->populateApplicantDetails();
        $this->updateTotalQuantityRequested();
        if($dispatchEvent) $this->dispatch('formResettled');
    }

    public function render(): View
    {
        if (Auth::check() && (empty($this->responsibleOfficerOptions) || empty($this->supportingOfficerOptions) || empty($this->equipmentTypeOptions))) {
            $this->loadInitialDropdownData();
        }
        $this->updateTotalQuantityRequested();
        return view('livewire.resource-management.loan-application.application-form', [
            'isEditMode' => (bool) $this->editing_application_id,
        ]);
    }

    protected function updateTotalQuantityRequested(): void
    {
        $this->totalQuantityRequested = collect($this->loan_application_items)
            ->whereNull('_delete')
            ->sum(fn($item) => (int)($item['quantity_requested'] ?? 0));
    }

    protected function rules(bool $isSubmittingForApproval = false): array
    {
        $nowForValidation = Carbon::now()->startOfMinute()->toDateTimeString();

        $rules = [
            'applicant_phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255', Rule::when((bool)$this->location && $this->location !== ($this->return_location ?? ''), ['different:location'])],
            'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:' . $nowForValidation],
            'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after:loan_start_date'],
            'applicant_is_responsible_officer' => ['required', 'boolean'],
            'responsible_officer_id' => [
                Rule::requiredIf(!$this->applicant_is_responsible_officer), 'nullable', 'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('status', User::STATUS_ACTIVE)->whereNull('deleted_at')),
                Rule::when(!$this->applicant_is_responsible_officer && Auth::check(), ['different:' . Auth::id()]),
            ],
            'supporting_officer_id' => [
                Rule::requiredIf($isSubmittingForApproval), 'nullable', 'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
                    $query->where('status', User::STATUS_ACTIVE)
                          ->whereHas('grade', fn($gq) => $gq->where('level', '>=', $minSupportGradeLevel))
                          ->whereNull('deleted_at');
                }),
                 Rule::when(Auth::check(), ['different:' . Auth::id()]),
            ],
            'loan_application_items' => ['required', 'array', 'min:1'],
            'loan_application_items.*.id' => ['nullable', 'integer'],
            'loan_application_items.*._delete' => ['nullable', 'boolean'],
            'loan_application_items.*.equipment_type' => [
                Rule::requiredIf(function (string $attribute) {
                    $index = explode('.', $attribute)[1];
                    return empty($this->loan_application_items[$index]['_delete'] ?? false);
                }),
                'nullable', 'string', 'max:255', Rule::in(array_keys($this->equipmentTypeOptions))
            ],
            'loan_application_items.*.quantity_requested' => [
                Rule::requiredIf(function (string $attribute) {
                    $index = explode('.', $attribute)[1];
                    return empty($this->loan_application_items[$index]['_delete'] ?? false);
                }),
                'nullable', 'integer', 'min:1', 'max:100'
            ],
            'loan_application_items.*.notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isSubmittingForApproval) {
            $rules['applicant_confirmation'] = ['accepted'];
        } else {
            $rules['applicant_confirmation'] = ['nullable','boolean'];
        }
        return $rules;
    }

    protected function messages(): array
    {
        $messages = [
            'applicant_phone.required' => __('Sila masukkan nombor telefon pemohon.'),
            'applicant_phone.regex' => __('Format nombor telefon tidak sah. Gunakan format seperti 012-3456789.'),
            'purpose.required' => __('Sila nyatakan tujuan permohonan.'),
            'purpose.min' => __('Tujuan permohonan mesti sekurang-kurangnya :min aksara.'),
            'location.required' => __('Sila nyatakan lokasi penggunaan peralatan.'),
            'loan_start_date.required' => __('Sila masukkan tarikh dan masa pinjaman bermula.'),
            'loan_start_date.after_or_equal' => __('Tarikh pinjaman mesti bermula dari tarikh dan masa semasa atau akan datang.'),
            'loan_end_date.required' => __('Sila masukkan tarikh dan masa jangkaan pulang.'),
            'loan_end_date.after' => __('Tarikh pulang mesti selepas tarikh dan masa pinjaman bermula.'),
            'responsible_officer_id.required_if' => __('Sila pilih Pegawai Bertanggungjawab.'),
            'responsible_officer_id.different' => __('Pegawai Bertanggungjawab tidak boleh sama dengan pemohon.'),
            'supporting_officer_id.required_if' => __('Sila pilih Pegawai Penyokong untuk menghantar permohonan.'),
            'supporting_officer_id.different' => __('Pegawai Penyokong tidak boleh sama dengan pemohon.'),
            'loan_application_items.required' => __('Sila tambah sekurang-kurangnya satu item peralatan.'),
            'loan_application_items.min' => __('Sila tambah sekurang-kurangnya :min item peralatan.'),
            'applicant_confirmation.accepted' => __('Anda mesti bersetuju dengan perakuan pemohon untuk menghantar permohonan.'),
            'return_location.different' => __('Lokasi pemulangan mesti berbeza daripada lokasi penggunaan jika diisi.')
        ];

        foreach ($this->loan_application_items as $index => $item) {
            if (isset($item['_delete']) && $item['_delete']) continue;

            $itemNumber = $index + 1;
            $messages["loan_application_items.{$index}.equipment_type.required_if"] = __("Sila pilih jenis peralatan untuk Item #{$itemNumber}.");
            $messages["loan_application_items.{$index}.equipment_type.in"] = __("Jenis peralatan yang dipilih untuk Item #{$itemNumber} tidak sah.");
            $messages["loan_application_items.{$index}.quantity_requested.required_if"] = __("Sila masukkan kuantiti untuk Item #{$itemNumber}.");
            $messages["loan_application_items.{$index}.quantity_requested.integer"] = __("Kuantiti untuk Item #{$itemNumber} mesti nombor.");
            $messages["loan_application_items.{$index}.quantity_requested.min"] = __("Kuantiti untuk Item #{$itemNumber} mesti sekurang-kurangnya :min.");
            $messages["loan_application_items.{$index}.quantity_requested.max"] = __("Kuantiti untuk Item #{$itemNumber} tidak boleh melebihi :max.");
            $messages["loan_application_items.{$index}.notes.max"] = __("Nota untuk Item #{$itemNumber} tidak boleh melebihi :max aksara.");
        }
        return $messages;
    }

    private function formatDateForDatetimeLocalInput($dateValue): ?string
    {
        if ($dateValue instanceof Carbon) {
            return $dateValue->format('Y-m-d\TH:i');
        }
        if (is_string($dateValue) && !empty($dateValue)) {
            try {
                return Carbon::parse($dateValue)->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
                Log::warning("LoanApplicationForm: Failed to parse date '{$dateValue}' for datetime-local input.", ['exception' => $e->getMessage()]);
                return null;
            }
        }
        return null;
    }
}
