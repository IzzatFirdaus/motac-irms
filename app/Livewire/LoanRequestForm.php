<?php

namespace App\Livewire;

use App\Models\Equipment; // MOTAC User model
use App\Models\LoanApplication;
use App\Models\User; // For asset type options
use App\Services\LoanApplicationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; // Added for catch block
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')] // Assumes layouts.app is your main layout file
class LoanRequestForm extends Component
{
    use AuthorizesRequests;

    public ?LoanApplication $loanApplication = null;

    public bool $isEdit = false;

    // Public property for dynamic title
    public string $title = 'Permohonan Pinjaman Baru';

    // Applicant Details (prefilled, some parts might be editable if needed)
    public string $applicant_name = '';

    public string $applicant_jawatan_gred = '';

    public string $applicant_bahagian_unit = '';

    public string $applicant_mobile_number = '';

    // Form Fields for Loan Application Details
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

    public bool $applicant_confirmation = false;

    public SupportCollection $equipmentTypeOptions;

    public SupportCollection $systemUsersForResponsibleOfficer;

    protected LoanApplicationService $loanApplicationService;

    public function boot(LoanApplicationService $loanApplicationService): void
    {
        $this->loanApplicationService = $loanApplicationService;
        $this->equipmentTypeOptions = collect(Equipment::$ASSET_TYPES_LABELS);
        $this->systemUsersForResponsibleOfficer = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Support']);
        })->get(['id', 'name', 'jawatan_gred']);

        Log::debug('LoanRequestForm component booted with services.');
    }

    public function mount(int $loanApplicationId = 0): void
    {
        Log::info('LoanRequestForm component mounting.', ['loanApplicationId' => $loanApplicationId]);

        if ($loanApplicationId > 0) {
            $this->isEdit = true;
            $this->title = 'Kemaskini Permohonan Pinjaman'; // Set dynamic title
            try {
                $this->loanApplication = $this->loanApplicationService->findLoanApplicationById(
                    $loanApplicationId,
                    ['user', 'responsibleOfficer', 'loanApplicationItems']
                );

                $this->authorize('update', $this->loanApplication); // Authorize the action

                $this->fillFormWithLoanApplicationData();
            } catch (AuthorizationException $e) {
                Log::warning('User not authorized to view this loan application.', ['user_id' => Auth::id(), 'loan_application_id' => $loanApplicationId]);
                $this->dispatch('swal:error', ['message' => 'You are not authorized to view this loan application.']);
                $this->redirect(route('loan-applications.index'), navigate: true);
                return;
            } catch (Throwable $e) {
                Log::error('Error loading loan application for edit: ' . $e->getMessage(), ['loanApplicationId' => $loanApplicationId, 'exception' => $e]);
                $this->dispatch('swal:error', ['message' => 'Failed to load loan application for editing.']);
                $this->redirect(route('loan-applications.index'), navigate: true);
                return;
            }
        } else {
            // New Application - Prefill applicant details
            /** @var User $currentUser */
            $currentUser = Auth::user();
            $this->applicant_name = $currentUser->name ?? '';
            $this->applicant_jawatan_gred = $currentUser->jawatan_gred ?? '';
            $this->applicant_bahagian_unit = $currentUser->bahagian_unit ?? '';
            $this->applicant_mobile_number = $currentUser->mobile_number ?? '';

            $this->items[] = [ // Start with one empty item
                'equipment_type' => '',
                'quantity_requested' => 1,
                'notes' => '',
            ];
        }
    }

    public function rules(): array
    {
        $isFinalSubmission = $this->applicant_confirmation;

        return [
            'applicant_mobile_number' => ['required', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255'],
            'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:today'],
            'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:loan_start_date'],
            'isApplicantResponsible' => ['boolean'],

            'responsible_officer_id' => [
                'nullable', // Now truly optional
                'exists:users,id',
                Rule::notIn([Auth::id() ?? 0]),
                // MODIFIED: Removed: Rule::requiredIf(!$this->isApplicantResponsible && empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            ],

            'manual_responsible_officer_name' => [
                'nullable', // Now truly optional
                'string',
                'max:255',
                // MODIFIED: Removed: Rule::requiredIf(!$this->isApplicantResponsible && empty($this->responsible_officer_id) && $isFinalSubmission),
            ],

            // These rules remain: if manual_responsible_officer_name IS provided, then these become required.
            'manual_responsible_officer_jawatan_gred' => [
                Rule::requiredIf(fn (): bool => ! $this->isApplicantResponsible && ($this->manual_responsible_officer_name !== null && $this->manual_responsible_officer_name !== '' && $this->manual_responsible_officer_name !== '0') && $isFinalSubmission),
                'nullable', 'string', 'max:255',
            ],
            'manual_responsible_officer_mobile' => [
                Rule::requiredIf(fn (): bool => ! $this->isApplicantResponsible && ($this->manual_responsible_officer_name !== null && $this->manual_responsible_officer_name !== '' && $this->manual_responsible_officer_name !== '0') && $isFinalSubmission),
                'nullable', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20',
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_type' => ['required', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS ?? []))],
            'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'applicant_confirmation' => $isFinalSubmission ? ['accepted'] : ['boolean'],
        ];
    }

    public function render(): View
    {
        return view('livewire.loan-application.form', [
            'loanApplication' => $this->loanApplication,
        ]);
    }

    public function addItem(): void
    {
        $this->items[] = [
            'equipment_type' => '',
            'quantity_requested' => 1,
            'notes' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index the array
    }

    public function submitForm(bool $isFinalButtonClicked = false): RedirectResponse
    {
        Log::info('Attempting to submit Loan Request Form.', ['isFinalButtonClicked' => $isFinalButtonClicked, 'isEditMode' => $this->isEdit, 'loanApplicationId' => $this->loanApplication?->id]);

        try {
            // Dynamically adjust rules for final submission
            if ($isFinalButtonClicked) {
                // Ensure applicant_confirmation is checked only for final submission
                $this->rules()['applicant_confirmation'] = ['accepted'];
                $this->resetErrorBag('applicant_confirmation'); // Clear any previous errors
            } else {
                // If not final submission, applicant_confirmation is just a boolean
                $this->rules()['applicant_confirmation'] = ['boolean'];
            }

            // Manually validate to apply dynamic rules
            $this->validate($this->rules());

            DB::transaction(function () use ($isFinalButtonClicked): void {
                $status = $this->determineSubmissionStatus($isFinalButtonClicked);

                $data = [
                    'user_id' => Auth::id(),
                    'purpose' => $this->purpose,
                    'location' => $this->location,
                    'return_location' => $this->return_location,
                    'loan_start_date' => $this->loan_start_date ? \Carbon\Carbon::parse($this->loan_start_date) : null,
                    'loan_end_date' => $this->loan_end_date ? \Carbon\Carbon::parse($this->loan_end_date) : null,
                    'status' => $status,
                    'applicant_mobile_number' => $this->applicant_mobile_number,
                    'applicant_confirmation_timestamp' => $isFinalButtonClicked ? now() : null,
                    'responsible_officer_id' => null,
                    'manual_responsible_officer_name' => null,
                    'manual_responsible_officer_jawatan_gred' => null,
                    'manual_responsible_officer_mobile' => null,
                ];

                if (! $this->isApplicantResponsible) {
                    $data['responsible_officer_id'] = $this->responsible_officer_id;
                    if ($this->manual_responsible_officer_name) {
                        $data['manual_responsible_officer_name'] = $this->manual_responsible_officer_name;
                        $data['manual_responsible_officer_jawatan_gred'] = $this->manual_responsible_officer_jawatan_gred;
                        $data['manual_responsible_officer_mobile'] = $this->manual_responsible_officer_mobile;
                    }
                }

                if ($this->isEdit && $this->loanApplication) {
                    $this->authorize('update', $this->loanApplication);
                    $this->loanApplicationService->updateLoanApplication(
                        $this->loanApplication,
                        $data,
                        $this->items,
                        $isFinalButtonClicked
                    );
                    $message = 'Permohonan pinjaman berjaya dikemaskini!';
                } else {
                    $this->loanApplication = $this->loanApplicationService->createLoanApplication(
                        $data,
                        $this->items,
                        $isFinalButtonClicked
                    );
                    $message = 'Permohonan pinjaman berjaya dihantar!';
                }

                $this->dispatch('swal:success', ['message' => $message]);
                $this->redirect(route('loan-applications.index'), navigate: true);
            });
        } catch (ValidationException $e) {
            Log::warning('Loan Request Form validation failed.', ['errors' => $e->errors()]);
            $this->dispatch('swal:error', ['message' => 'Sila semak semula borang.']);
            // Re-throw to show validation errors on the form
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error submitting Loan Request Form: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('swal:error', ['message' => 'Gagal menghantar permohonan pinjaman: ' . $e->getMessage()]);
        }

        return redirect()->route('loan-applications.index');
    }

    private function fillFormWithLoanApplicationData(): void
    {
        if (! $this->loanApplication) {
            return;
        }

        $this->applicant_name = $this->loanApplication->user->name ?? '';
        $this->applicant_jawatan_gred = $this->loanApplication->user->jawatan_gred ?? '';
        $this->applicant_bahagian_unit = $this->loanApplication->user->bahagian_unit ?? '';
        $this->applicant_mobile_number = $this->loanApplication->applicant_mobile_number ?? ($this->loanApplication->user->mobile_number ?? '');
        $this->purpose = $this->loanApplication->purpose;
        $this->location = $this->loanApplication->location;
        $this->return_location = $this->loanApplication->return_location;
        $this->loan_start_date = $this->loanApplication->loan_start_date?->format('Y-m-d\TH:i');
        $this->loan_end_date = $this->loanApplication->loan_end_date?->format('Y-m-d\TH:i');

        $this->isApplicantResponsible = is_null($this->loanApplication->responsible_officer_id) ||
                                       $this->loanApplication->responsible_officer_id === $this->loanApplication->user_id;

        if (! $this->isApplicantResponsible && $this->loanApplication->responsibleOfficer) {
            $this->responsible_officer_id = $this->loanApplication->responsible_officer_id;
        }

        $this->items = $this->loanApplication->loanApplicationItems->map(function ($item): array {
            return [
                'id' => $item->id,
                'equipment_type' => $item->equipment_type,
                'quantity_requested' => $item->quantity_requested,
                'notes' => $item->notes,
            ];
        })->toArray();

        $this->applicant_confirmation = (bool) $this->loanApplication->applicant_confirmation_timestamp;
    }

    private function determineSubmissionStatus(bool $isFinalButtonClicked): string
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if ($this->isEdit && $this->loanApplication instanceof LoanApplication && // Simplified namespace
            $this->loanApplication->status !== LoanApplication::STATUS_DRAFT &&
            ! $currentUser->hasRole('Admin')) {
            return $this->loanApplication->status;
        }

        return $isFinalButtonClicked ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_DRAFT;
    }
}
