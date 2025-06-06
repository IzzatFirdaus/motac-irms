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

    //--- BAHAGIAN 1: MAKLUMAT PEMOHON ---
    public string $applicantName = '';
    public string $applicantPositionAndGrade = '';
    public string $applicantDepartment = '';
    public string $applicant_phone = '';
    public string $purpose = '';
    public string $location = '';
    public ?string $return_location = null;
    public ?string $loan_start_date = null;
    public ?string $loan_end_date = null;

    //--- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB ---
    public bool $applicant_is_responsible_officer = true;
    public ?int $responsible_officer_id = null;

    //--- MAKLUMAT PEGAWAI PENYOKONG (Workflow Stage 3) ---
    // ADJUSTMENT: Re-enabled to enforce workflow compliance.
    public ?int $supporting_officer_id = null;

    //--- BAHAGIAN 3: MAKLUMAT PERALATAN ---
    public array $loan_application_items = [];

    //--- BAHAGIAN 4: PENGESAHAN PEMOHON ---
    public bool $applicant_confirmation = false;
    public bool $termsScrolled = false;

    //--- Component State ---
    public ?int $editing_application_id = null;
    public ?LoanApplication $loanApplicationInstance = null;
    public bool $isSubmittingForApproval = false;

    //--- Data for Dropdowns ---
    public array $responsibleOfficerOptions = [];
    public array $supportingOfficerOptions = [];
    public array $equipmentTypeOptions = [];

    public function generatePageTitle(): string
    {
        return __('PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI');
    }

    public function mount(int $loan_application_id = null): void
    {
        if (!Auth::check()) {
            session()->flash('error', __('Sesi anda telah tamat. Sila log masuk semula.'));
            $this->dispatch('update-page-title', title: __('Akses Tidak Dibenarkan'));
            return;
        }

        $this->populateApplicantDetails();
        $this->loadInitialDropdownData();

        if ($loan_application_id) {
            $this->editing_application_id = (int) $loan_application_id;
            $this->loanApplicationInstance = LoanApplication::with([
                'user', 'responsibleOfficer', 'supportingOfficer', 'loanApplicationItems'
            ])->findOrFail($loan_application_id);

            $this->authorize('update', $this->loanApplicationInstance);
            $this->populateFormFromInstance();
        } else {
            $this->authorize('create', LoanApplication::class);
            $this->resetFormForCreate();
        }
    }

    private function populateFormFromInstance(): void
    {
        if (!$this->loanApplicationInstance) return;

        $this->applicant_phone = $this->loanApplicationInstance->applicant_phone ?? Auth::user()?->mobile_number ?? '';
        $this->purpose = $this->loanApplicationInstance->purpose ?? '';
        $this->location = $this->loanApplicationInstance->location ?? '';
        $this->return_location = $this->loanApplicationInstance->return_location;
        $this->loan_start_date = $this->formatDateForInput($this->loanApplicationInstance->loan_start_date);
        $this->loan_end_date = $this->formatDateForInput($this->loanApplicationInstance->loan_end_date);
        $this->supporting_officer_id = $this->loanApplicationInstance->supporting_officer_id;
        $this->applicant_is_responsible_officer = $this->loanApplicationInstance->responsible_officer_id === $this->loanApplicationInstance->user_id;
        $this->responsible_officer_id = $this->applicant_is_responsible_officer ? Auth::id() : $this->loanApplicationInstance->responsible_officer_id;
        $this->loan_application_items = $this->loanApplicationInstance->loanApplicationItems?->map(fn($item) => $item->only(['id', 'equipment_type', 'quantity_requested', 'notes']))->toArray() ?? [];
        $this->applicant_confirmation = (bool) $this->loanApplicationInstance->applicant_confirmation_timestamp;
    }

    public function populateApplicantDetails(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $this->applicantName = $user->name;
            $positionName = $user->position?->name ?? __('Tiada Jawatan');
            $gradeName = $user->grade?->name ?? __('Tiada Gred');
            $this->applicantPositionAndGrade = trim("{$positionName} ({$gradeName})");
            $this->applicantDepartment = $user->department?->name ?? __('Tiada Jabatan');
        }
    }

    public function loadInitialDropdownData(): void
    {
        $currentUserId = Auth::id();
        if (!$currentUserId) return;

        $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);

        $this->supportingOfficerOptions = User::where('status', User::STATUS_ACTIVE)
            ->where('id', '!=', $currentUserId)
            ->whereHas('grade', fn ($query) => $query->where('level', '>=', $minSupportGradeLevel))
            ->orderBy('name')->get()->pluck('name', 'id')->toArray();

        $this->responsibleOfficerOptions = User::where('status', User::STATUS_ACTIVE)
            ->where('id', '!=', $currentUserId)
            ->orderBy('name')->get()->pluck('name', 'id')->toArray();

        $this->equipmentTypeOptions = Equipment::getAssetTypeOptions() ?? [];
    }

    public function saveAsDraft(): ?RedirectResponse
    {
        $this->isSubmittingForApproval = false;
        $validatedData = $this->validate($this->rules(false));
        return $this->saveOrUpdateLoanApplication($validatedData, true);
    }

    public function submitLoanApplication(): ?RedirectResponse
    {
        $this->isSubmittingForApproval = true;
        $validatedData = $this->validate($this->rules(true));
        return $this->saveOrUpdateLoanApplication($validatedData, false);
    }

    private function saveOrUpdateLoanApplication(array $validatedData, bool $isDraft): ?RedirectResponse
    {
        $this->authorizeAction();
        DB::beginTransaction();
        try {
            $loanAppService = app(LoanApplicationService::class);
            /** @var User $currentUser */
            $currentUser = Auth::user();

            $dataForService = $validatedData;
            $dataForService['user_id'] = $this->loanApplicationInstance?->user_id ?? $currentUser->id;
            $dataForService['is_draft_submission'] = $isDraft;

            $application = $this->editing_application_id
                ? $loanAppService->updateApplication($this->loanApplicationInstance, $dataForService, $currentUser)
                : $loanAppService->createAndSubmitApplication($dataForService, $currentUser, $isDraft);

            if (!$isDraft) {
                $loanAppService->submitApplicationForApproval($application, $currentUser);
            }

            DB::commit();
            $message = $isDraft ? __('Draf permohonan berjaya disimpan.') : __('Permohonan pinjaman berjaya dihantar untuk kelulusan.');
            session()->flash('success', $message);
            return redirect()->route('loan-applications.show', $application->id);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('LoanApplicationForm Error in saveOrUpdateLoanApplication: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('general_error', __('Sistem menghadapi ralat semasa memproses permohonan anda. Sila cuba lagi.'));
            return null;
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
            'return_location' => ['nullable', 'string', 'max:255', Rule::when((bool)$this->location, ['different:location'])],
            'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after:loan_start_date'],
            'applicant_is_responsible_officer' => ['required', 'boolean'],
            'responsible_officer_id' => [
                Rule::requiredIf(!$this->applicant_is_responsible_officer),
                'nullable',
                'integer',
                'different:supporting_officer_id',
                Rule::exists('users', 'id')->where('status', 'active'),
            ],
            // ADJUSTMENT: This rule is now enforced for all final submissions to follow the workflow.
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
                            $fail(__('Pegawai Penyokong tidak memenuhi syarat gred minimum (Gred :grade).', ['grade' => $minSupportGradeLevel]));
                        }
                    }
                },
            ],
            'applicant_confirmation' => Rule::when($isSubmittingForApproval, ['accepted']),
            'loan_application_items' => ['required', 'array', 'min:1'],
            'loan_application_items.*.equipment_type' => ['required', 'string'],
            'loan_application_items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'],
            'loan_application_items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function authorizeAction(): void
    {
        $this->authorize($this->editing_application_id ? 'update' : 'create', $this->loanApplicationInstance ?? LoanApplication::class);
    }

    // Other helper methods like addLoanItem, removeLoanItem, resetFormForCreate etc.
    // ...

    private function formatDateForInput($date): ?string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d\TH:i') : null;
    }

    public function render(): View
    {
        return view('livewire.resource-management.loan-application.application-form');
    }
}
