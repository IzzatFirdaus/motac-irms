<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\LoanApplicationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

/**
 * Livewire component for the Loan Request Form.
 * Handles both creation and update of loan applications,
 * integrating with LoanApplicationService.
 */
#[Layout('layouts.app')]
class LoanRequestForm extends Component
{
    use AuthorizesRequests;

    // Loan application model for edit mode
    public ?LoanApplication $loanApplication = null;

    public bool $isEdit = false;

    public string $title = 'Permohonan Pinjaman Baru';

    // Applicant details (some are prefilled, some editable)
    public string $applicant_name = '';

    public string $applicant_jawatan_gred = '';

    public string $applicant_bahagian_unit = '';

    public string $applicant_mobile_number = '';

    // Main loan application fields
    public string $purpose = '';

    public string $location = '';

    public ?string $return_location = null;

    public ?string $loan_start_date = null;

    public ?string $loan_end_date = null;

    public bool $isApplicantResponsible = true;

    public ?int $responsible_officer_id = null;

    public ?string $manual_responsible_officer_name = '';

    public ?string $manual_responsible_officer_jawatan_gred = '';

    public ?string $manual_responsible_officer_mobile = '';

    public array $items = [];

    // Confirmation from applicant before submission
    public bool $applicant_confirmation = false;

    // Form select options
    public SupportCollection $equipmentTypeOptions;

    public SupportCollection $systemUsersForResponsibleOfficer;

    // Service dependency
    protected LoanApplicationService $loanApplicationService;

    /**
     * Boot: Inject LoanApplicationService and setup select options.
     */
    public function boot(LoanApplicationService $loanApplicationService): void
    {
        $this->loanApplicationService           = $loanApplicationService;
        $this->equipmentTypeOptions             = collect(Equipment::$ASSET_TYPES_LABELS);
        $this->systemUsersForResponsibleOfficer = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Support']);
        })->get(['id', 'name', 'jawatan_gred']);
    }

    /**
     * Mount: If editing, load data; else prefill applicant info for new form.
     */
    public function mount(int $loanApplicationId = 0): void
    {
        if ($loanApplicationId > 0) {
            $this->isEdit = true;
            $this->title  = 'Kemaskini Permohonan Pinjaman';
            try {
                $this->loanApplication = $this->loanApplicationService->findLoanApplicationById(
                    $loanApplicationId,
                    ['user', 'responsibleOfficer', 'loanApplicationItems']
                );

                $this->authorize('update', $this->loanApplication);
                $this->fillFormWithLoanApplicationData();
            } catch (AuthorizationException $e) {
                $this->dispatch('swal:error', ['message' => 'You are not authorized to view this loan application.']);
                $this->redirect(route('loan-applications.index'), navigate: true);

                return;
            } catch (Throwable $e) {
                $this->dispatch('swal:error', ['message' => 'Failed to load loan application for editing.']);
                $this->redirect(route('loan-applications.index'), navigate: true);

                return;
            }
        } else {
            // New Application - Prefill with logged-in user info
            /** @var User $currentUser */
            $currentUser                   = Auth::user();
            $this->applicant_name          = $currentUser->name          ?? '';
            $this->applicant_jawatan_gred  = $currentUser->jawatan_gred  ?? '';
            $this->applicant_bahagian_unit = $currentUser->bahagian_unit ?? '';
            $this->applicant_mobile_number = $currentUser->mobile_number ?? '';
            $this->items[]                 = [
                'equipment_type'     => '',
                'quantity_requested' => 1,
                'notes'              => '',
            ];
        }
    }

    /**
     * Validation rules for the loan application form.
     */
    public function rules(): array
    {
        $isFinalSubmission = $this->applicant_confirmation;

        return [
            'applicant_mobile_number' => ['required', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'],
            'purpose'                 => ['required', 'string', 'min:10', 'max:1000'],
            'location'                => ['required', 'string', 'min:5', 'max:255'],
            'return_location'         => ['nullable', 'string', 'max:255'],
            'loan_start_date'         => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:today'],
            'loan_end_date'           => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:loan_start_date'],
            'isApplicantResponsible'  => ['boolean'],
            'responsible_officer_id'  => [
                'nullable',
                'exists:users,id',
                Rule::notIn([Auth::id() ?? 0]),
            ],
            'manual_responsible_officer_name' => [
                'nullable',
                'string', 'max:255',
            ],
            'manual_responsible_officer_jawatan_gred' => [
                Rule::requiredIf(fn (): bool => ! $this->isApplicantResponsible && ($this->manual_responsible_officer_name !== null && $this->manual_responsible_officer_name !== '' && $this->manual_responsible_officer_name !== '0') && $isFinalSubmission),
                'nullable', 'string', 'max:255',
            ],
            'manual_responsible_officer_mobile' => [
                Rule::requiredIf(fn (): bool => ! $this->isApplicantResponsible && ($this->manual_responsible_officer_name !== null && $this->manual_responsible_officer_name !== '' && $this->manual_responsible_officer_name !== '0') && $isFinalSubmission),
                'nullable', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20',
            ],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.equipment_type'     => ['required', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS ?? []))],
            'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.notes'              => ['nullable', 'string', 'max:500'],
            'applicant_confirmation'     => $isFinalSubmission ? ['accepted'] : ['boolean'],
        ];
    }

    /**
     * Render the loan application form Blade view.
     */
    public function render(): View
    {
        return view('livewire.loan-application.form', [
            'loanApplication' => $this->loanApplication,
        ]);
    }

    /**
     * Add a new item row to the application (for multiple equipment types).
     */
    public function addItem(): void
    {
        $this->items[] = [
            'equipment_type'     => '',
            'quantity_requested' => 1,
            'notes'              => '',
        ];
    }

    /**
     * Remove an equipment item row by index.
     */
    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * Handles form submission for both new and updated loan applications.
     * Uses LoanApplicationService for all DB logic.
     */
    public function submitForm(bool $isFinalButtonClicked = false): RedirectResponse
    {
        try {
            // Adjust rules for final submission
            if ($isFinalButtonClicked) {
                $this->rules()['applicant_confirmation'] = ['accepted'];
                $this->resetErrorBag('applicant_confirmation');
            } else {
                $this->rules()['applicant_confirmation'] = ['boolean'];
            }
            $this->validate($this->rules());

            $user = Auth::user();

            // Gather payload for service
            $payload = [
                // User info may be filled by service itself, but can pass for completeness
                'applicant_mobile_number'                 => $this->applicant_mobile_number,
                'purpose'                                 => $this->purpose,
                'location'                                => $this->location,
                'return_location'                         => $this->return_location,
                'loan_start_date'                         => $this->loan_start_date,
                'loan_end_date'                           => $this->loan_end_date,
                'applicant_is_responsible_officer'        => $this->isApplicantResponsible,
                'responsible_officer_id'                  => $this->responsible_officer_id,
                'manual_responsible_officer_name'         => $this->manual_responsible_officer_name,
                'manual_responsible_officer_jawatan_gred' => $this->manual_responsible_officer_jawatan_gred,
                'manual_responsible_officer_mobile'       => $this->manual_responsible_officer_mobile,
                'items'                                   => $this->items,
                // Include applicant_confirmation in case service wants to use
                'applicant_confirmation' => $this->applicant_confirmation,
            ];

            DB::transaction(function () use ($isFinalButtonClicked, $user, $payload): void {
                // If editing, update application; otherwise, create new
                if ($this->isEdit && $this->loanApplication) {
                    $this->authorize('update', $this->loanApplication);
                    $this->loanApplication = $this->loanApplicationService->updateApplication(
                        $this->loanApplication,
                        $payload,
                        $user
                    );
                    $message = 'Permohonan pinjaman berjaya dikemaskini!';
                } else {
                    $this->loanApplication = $this->loanApplicationService->createAndSubmitApplication(
                        $payload,
                        $user,
                        ! $isFinalButtonClicked
                    );
                    $message = 'Permohonan pinjaman berjaya dihantar!';
                }

                $this->dispatch('swal:success', ['message' => $message]);
                $this->redirect(route('loan-applications.index'), navigate: true);
            });
        } catch (ValidationException $e) {
            $this->dispatch('swal:error', ['message' => 'Sila semak semula borang.']);
            throw $e;
        } catch (Throwable $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menghantar permohonan pinjaman: ' . $e->getMessage()]);
        }

        return redirect()->route('loan-applications.index');
    }

    /**
     * Fill the form with data from an existing loan application (for edit mode).
     */
    private function fillFormWithLoanApplicationData(): void
    {
        if (! $this->loanApplication) {
            return;
        }

        $this->applicant_name          = $this->loanApplication->user->name              ?? '';
        $this->applicant_jawatan_gred  = $this->loanApplication->user->jawatan_gred      ?? '';
        $this->applicant_bahagian_unit = $this->loanApplication->user->bahagian_unit     ?? '';
        $this->applicant_mobile_number = $this->loanApplication->applicant_mobile_number ?? ($this->loanApplication->user->mobile_number ?? '');
        $this->purpose                 = $this->loanApplication->purpose;
        $this->location                = $this->loanApplication->location;
        $this->return_location         = $this->loanApplication->return_location;
        $this->loan_start_date         = $this->loanApplication->loan_start_date?->format('Y-m-d\TH:i');
        $this->loan_end_date           = $this->loanApplication->loan_end_date?->format('Y-m-d\TH:i');
        // Responsible officer logic
        $this->isApplicantResponsible = is_null($this->loanApplication->responsible_officer_id)
            || $this->loanApplication->responsible_officer_id === $this->loanApplication->user_id;

        if (! $this->isApplicantResponsible && $this->loanApplication->responsibleOfficer) {
            $this->responsible_officer_id = $this->loanApplication->responsible_officer_id;
        }
        // Fill items
        $this->items = $this->loanApplication->loanApplicationItems->map(function ($item): array {
            return [
                'id'                 => $item->id,
                'equipment_type'     => $item->equipment_type,
                'quantity_requested' => $item->quantity_requested,
                'notes'              => $item->notes,
            ];
        })->toArray();

        $this->applicant_confirmation = (bool) $this->loanApplication->applicant_confirmation_timestamp;
    }

    // determineSubmissionStatus method removed (unused)
}
