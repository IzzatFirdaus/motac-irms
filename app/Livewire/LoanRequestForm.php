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
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')] // Assumes layouts.app is your main layout file
class LoanRequestForm extends Component
{
    use AuthorizesRequests;

    public ?LoanApplication $loanApplication = null;
    public bool $isEdit = false;

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

    public function rules(): array
    {
        $isFinalSubmission = $this->applicant_confirmation;

        $rules = [
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
            Rule::requiredIf(fn() => !$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            'nullable', 'string', 'max:255'
          ],
          'manual_responsible_officer_mobile' => [
            Rule::requiredIf(fn() => !$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            'nullable', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'
          ],

          'items' => ['required', 'array', 'min:1'],
          'items.*.equipment_type' => ['required', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS ?? []))],
          'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'],
          'items.*.notes' => ['nullable', 'string', 'max:500'],
          'applicant_confirmation' => $isFinalSubmission ? ['accepted'] : ['boolean'],
        ];
        return $rules;
    }

    public function boot(LoanApplicationService $loanApplicationService): void
    {
        $this->loanApplicationService = $loanApplicationService;
    }

    public function mount(?int $loanApplicationId = null): void
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $this->equipmentTypeOptions = collect(['' => __('- Pilih Jenis Peralatan -')] + (Equipment::$ASSET_TYPES_LABELS ?? []));
        $this->systemUsersForResponsibleOfficer = collect(['' => __('- Pilih Pegawai (dari senarai sistem) -')])
          ->union(User::where('id', '!=', $user->id)->orderBy('name')->pluck('name', 'id'));

        if ($loanApplicationId) {
            $loanApplication = LoanApplication::with([
              'user', 'responsibleOfficer', 'supportingOfficer', 'loanApplicationItems'
            ])->findOrFail($loanApplicationId);
            $this->loanApplication = $loanApplication;
            $this->isEdit = true;
            $this->authorize('update', $this->loanApplication);
            $this->fillFormFromModel();
        } else {
            $this->loanApplication = new LoanApplication(['user_id' => $user->id]);
            $this->authorize('create', LoanApplication::class);
            $this->isEdit = false;
            $this->prefillApplicantDetails($user);
            $this->addItem();
        }
    }

    public function updatedIsApplicantResponsible(bool $value): void
    {
        if ($value) {
            $this->responsible_officer_id = null;
            $this->manual_responsible_officer_name = '';
            $this->manual_responsible_officer_jawatan_gred = '';
            $this->manual_responsible_officer_mobile = '';
        }
        $this->resetValidation(['responsible_officer_id', 'manual_responsible_officer_name', 'manual_responsible_officer_jawatan_gred', 'manual_responsible_officer_mobile']);
    }

    public function addItem(): void
    {
        $this->items[] = ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    }

    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function saveApplication(bool $isFinalSubmission = false): ?RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser) {
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));
            return $this->redirectRoute('login', navigate: true);
        }

        if ($isFinalSubmission) {
            $this->validateOnly('applicant_confirmation', ['applicant_confirmation' => 'accepted']);
        }

        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            $message = '';
            if ($this->isEdit && $this->loanApplication && $this->loanApplication->exists) {
                $this->authorize('update', $this->loanApplication);
                $this->loanApplication = $this->loanApplicationService->updateApplication(
                    $this->loanApplication,
                    $validatedData,
                    $currentUser
                );
                $message = __('Permohonan pinjaman berjaya dikemaskini.');

                if ($isFinalSubmission && in_array($this->loanApplication->status, [LoanApplication::STATUS_DRAFT, LoanApplication::STATUS_REJECTED])) {
                    $this->loanApplication = $this->loanApplicationService->submitApplicationForApproval($this->loanApplication, $currentUser);
                    $message = __('Permohonan pinjaman berjaya dikemaskini dan dihantar untuk kelulusan.');
                }

            } else {
                $this->authorize('create', LoanApplication::class);
                $this->loanApplication = $this->loanApplicationService->createAndSubmitApplication(
                    $validatedData,
                    $currentUser,
                    !$isFinalSubmission
                );

                $message = $isFinalSubmission ? __('Permohonan pinjaman berjaya dihantar.') : __('Draf permohonan pinjaman berjaya disimpan.');

                if ($isFinalSubmission && $this->loanApplication->status === LoanApplication::STATUS_DRAFT) {
                    $this->loanApplication = $this->loanApplicationService->submitApplicationForApproval($this->loanApplication, $currentUser);
                }
            }

            DB::commit();
            session()->flash('success', $message);
            $this->dispatch('toastr', type: 'success', message: $message);

            return $this->redirectRoute('loan-applications.index', navigate: true); // Assuming 'loan-applications.index' is correct

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning("LoanRequestForm: Validation failed for User ID: {$currentUser->id}", ['errors' => $e->errors()]);
            $this->dispatch('toastr', type: 'error', message: __('Sila perbetulkan ralat pada borang.'));
            return null;
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::error("LoanRequestForm: Authorization failed for User ID: {$currentUser->id}", ['message' => $e->getMessage()]);
            session()->flash('error', __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            return null;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("LoanRequestForm: Error submitting application for User ID: {$currentUser->id}", ['exception' => $e]);
            session()->flash('error', __('Berlaku ralat sistem semasa memproses permohonan anda: ') . $e->getMessage());
            $this->dispatch('toastr', type: 'error', message: __('Ralat sistem. Sila cuba lagi atau hubungi pentadbir.'));
            return null;
        }
    }

    public function saveAsDraft(): ?RedirectResponse
    {
        $originalConfirmation = $this->applicant_confirmation;
        $this->applicant_confirmation = false;
        $response = $this->saveApplication(false);
        $this->applicant_confirmation = $originalConfirmation;
        return $response;
    }

    public function submitForApproval(): ?RedirectResponse
    {
        $this->applicant_confirmation = true;
        return $this->saveApplication(true);
    }

    public function render(): View
    {
        return view('livewire.loan-request-form')
          ->title($this->isEdit ? __('Kemaskini Permohonan Pinjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT'));
    }

    protected function messages(): array
    {
        return [
          'items.required' => __('Sila tambah sekurang-kurangnya satu item peralatan.'),
          'items.min' => __('Sila tambah sekurang-kurangnya satu item peralatan.'),
          'items.*.equipment_type.required' => __('Sila pilih jenis peralatan untuk item #:position.'),
          'items.*.quantity_requested.required' => __('Sila masukkan kuantiti untuk item #:position.'),
          'items.*.quantity_requested.min' => __('Kuantiti mesti sekurang-kurangnya :min untuk item #:position.'),
          'applicant_confirmation.accepted' => __('Anda mesti membuat perakuan pemohon untuk menghantar permohonan.'),
          'responsible_officer_id.not_in' => __('Pegawai Bertanggungjawab tidak boleh pemohon sendiri.'),
          'applicant_mobile_number.required' => __('Sila masukkan nombor telefon pemohon.'),
        ];
    }

    private function prefillApplicantDetails(User $user): void
    {
        $this->applicant_name = $user->full_name ?? $user->name;
        $this->applicant_jawatan_gred = ($user->position?->name ?? '') . ($user->grade?->name ? ' (Gred ' . $user->grade->name . ')' : '');
        $this->applicant_bahagian_unit = $user->department?->name ?? '';
        $this->applicant_mobile_number = $user->mobile_number ?? '';
    }

    private function fillFormFromModel(): void
    {
        if (!$this->loanApplication) {
            return;
        }

        $applicantUser = $this->loanApplication->user ?? Auth::user();
        if ($applicantUser) {
            $this->prefillApplicantDetails($applicantUser);
        }

        $this->purpose = $this->loanApplication->purpose;
        $this->location = $this->loanApplication->location;
        $this->return_location = $this->loanApplication->return_location;
        $this->loan_start_date = $this->loanApplication->loan_start_date?->format('Y-m-d\TH:i');
        $this->loan_end_date = $this->loanApplication->loan_end_date?->format('Y-m-d\TH:i');

        $this->isApplicantResponsible = is_null($this->loanApplication->responsible_officer_id) ||
                                       $this->loanApplication->responsible_officer_id === $this->loanApplication->user_id;

        if (!$this->isApplicantResponsible && $this->loanApplication->responsibleOfficer) {
            $this->responsible_officer_id = $this->loanApplication->responsible_officer_id;
        }

        $this->items = $this->loanApplication->loanApplicationItems->map(function ($item) {
            return [
              'id' => $item->id,
              'equipment_type' => $item->equipment_type,
              'quantity_requested' => $item->quantity_requested,
              'notes' => $item->notes,
            ];
        })->toArray();

        $this->applicant_confirmation = (bool)$this->loanApplication->applicant_confirmation_timestamp;
    }

    private function determineSubmissionStatus(bool $isFinalButtonClicked): string
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if ($this->isEdit && $this->loanApplication &&
            $this->loanApplication->status !== LoanApplication::STATUS_DRAFT &&
            !$currentUser->hasRole('Admin')) {
            return $this->loanApplication->status;
        }
        return $isFinalButtonClicked ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_DRAFT;
    }
}
