<?php

namespace App\Livewire;

use App\Models\User; // MOTAC User model
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
// use App\Models\EquipmentCategory; // If categories are used for selection
use App\Models\Equipment; // For asset type options
use App\Services\LoanApplicationService; // Assuming a service for creation logic
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection; // Use Illuminate Collection
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')] // Bootstrap main layout
class LoanRequestForm extends Component
{
  use AuthorizesRequests;

  public ?LoanApplication $loanApplication = null;
  public bool $isEdit = false;

  // Applicant Details
  public string $applicant_name = '';
  public string $applicant_jawatan_gred = '';
  public string $applicant_bahagian_unit = '';
  public string $applicant_mobile_number = '';

  // Form Fields
  public string $purpose = '';
  public string $location = '';
  public ?string $return_location = null;
  public ?string $loan_start_date = null;
  public ?string $loan_end_date = null;
  public bool $isApplicantResponsible = true;
  public ?int $responsible_officer_id = null;

  // Manual entry for responsible officer
  public ?string $manual_responsible_officer_name = '';
  public ?string $manual_responsible_officer_jawatan_gred = '';
  public ?string $manual_responsible_officer_mobile = '';

  public array $items = [];
  public bool $applicant_confirmation = false;

  // Options
  public SupportCollection $equipmentTypeOptions;
  public SupportCollection $systemUsersForResponsibleOfficer;

  protected LoanApplicationService $loanApplicationService;

  // Rules, messages, boot, mount, prefill, fillFormFromModel, updated* methods remain the same
  // as provided in your uploaded file.
  // ... (Keep the existing methods from your uploaded LoanRequestForm.php here)
  // Ensure the methods are exactly as you provided them. Example:
  public function rules(): array
  {
    $isFinalSubmission = $this->applicant_confirmation;

    $rules = [
      'purpose' => ['required', 'string', 'min:10', 'max:1000'],
      'location' => ['required', 'string', 'min:5', 'max:255'],
      'return_location' => ['nullable', 'string', 'max:255'],
      'loan_start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
      'loan_end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:loan_start_date'],
      'isApplicantResponsible' => ['boolean'],
      'responsible_officer_id' => [
        Rule::requiredIf(!$this->isApplicantResponsible && empty($this->manual_responsible_officer_name) && $isFinalSubmission),
        'nullable',
        'exists:users,id',
        Rule::notIn([Auth::id() ?? 0]),
      ],
      'manual_responsible_officer_name' => [
        Rule::requiredIf(!$this->isApplicantResponsible && empty($this->responsible_officer_id) && $isFinalSubmission),
        'nullable',
        'string',
        'max:255'
      ],
      'manual_responsible_officer_jawatan_gred' => [
        Rule::requiredIf(!$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
        'nullable',
        'string',
        'max:255'
      ],
      'manual_responsible_officer_mobile' => [
        Rule::requiredIf(!$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
        'nullable',
        'string',
        'regex:/^[0-9\-\+\s\(\)]*$/',
        'min:9',
        'max:20'
      ],
      'items' => ['required', 'array', 'min:1'],
      'items.*.equipment_type' => ['required', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS))],
      'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:10'],
      'items.*.notes' => ['nullable', 'string', 'max:500'],
      'applicant_confirmation' => $isFinalSubmission ? ['accepted'] : ['boolean'],
    ];
    return $rules;
  }

  protected function messages(): array
  {
    return [
      'items.*.equipment_type.required' => __('Sila pilih jenis peralatan untuk item #:position.'),
      'items.*.quantity_requested.required' => __('Sila masukkan kuantiti untuk item #:position.'),
      'items.*.quantity_requested.min' => __('Kuantiti mesti sekurang-kurangnya :min untuk item #:position.'),
      'applicant_confirmation.accepted' => __('Anda mesti mengesahkan perakuan pemohon.'),
      'responsible_officer_id.not_in' => __('Pegawai Bertanggungjawab tidak boleh pemohon sendiri.'),
    ];
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

    $this->equipmentTypeOptions = collect(['' => __('- Pilih Jenis Peralatan -')] + Equipment::$ASSET_TYPES_LABELS);
    $this->systemUsersForResponsibleOfficer = collect(['' => __('- Pilih Pegawai (Sistem) -')])
      ->union(User::where('id', '!=', $user->id)->orderBy('name')->pluck('name', 'id'));

    if ($loanApplicationId) {
      $loanApplication = LoanApplication::with([
        'user',
        'responsibleOfficer',
        'supportingOfficer',
        'applicationItems'
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

  private function prefillApplicantDetails(User $user): void
  {
    $this->applicant_name = $user->name;
    $this->applicant_jawatan_gred = ($user->position?->name ?? '') . ($user->grade?->name ? ' (Gred ' . $user->grade->name . ')' : '');
    $this->applicant_bahagian_unit = $user->department?->name;
    $this->applicant_mobile_number = $user->mobile_number;
  }

  private function fillFormFromModel(): void
  {
    if (!$this->loanApplication) return;
    if ($this->loanApplication->user) $this->prefillApplicantDetails($this->loanApplication->user);

    $this->purpose = $this->loanApplication->purpose;
    $this->location = $this->loanApplication->location;
    $this->return_location = $this->loanApplication->return_location;
    $this->loan_start_date = $this->loanApplication->loan_start_date?->format('Y-m-d');
    $this->loan_end_date = $this->loanApplication->loan_end_date?->format('Y-m-d');

    $this->isApplicantResponsible = is_null($this->loanApplication->responsible_officer_id) || $this->loanApplication->responsible_officer_id === $this->loanApplication->user_id;
    if (!$this->isApplicantResponsible && $this->loanApplication->responsibleOfficer) {
      $this->responsible_officer_id = $this->loanApplication->responsible_officer_id;
    }

    $this->items = $this->loanApplication->applicationItems->map(function ($item) {
      return [
        'id' => $item->id,
        'equipment_type' => $item->equipment_type,
        'quantity_requested' => $item->quantity_requested,
        'notes' => $item->notes,
      ];
    })->toArray();

    $this->applicant_confirmation = (bool)$this->loanApplication->applicant_confirmation_timestamp;
  }

  public function updatedIsApplicantResponsible(bool $value): void
  {
    if ($value) {
      $this->responsible_officer_id = null;
      $this->manual_responsible_officer_name = '';
      $this->manual_responsible_officer_jawatan_gred = '';
      $this->manual_responsible_officer_mobile = '';
    }
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

  private function determineSubmissionStatus(bool $isFinalButtonClicked): string
  {
    /** @var User $currentUser */
    $currentUser = Auth::user();
    if ($this->isEdit && $this->loanApplication && $this->loanApplication->status !== LoanApplication::STATUS_DRAFT && !$currentUser->isAdmin()) {
      return $this->loanApplication->status;
    }
    return $isFinalButtonClicked ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_DRAFT;
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

    $targetStatus = $this->determineSubmissionStatus($isFinalSubmission);
    $validatedData = $this->validate();

    DB::beginTransaction();
    try {
      $applicationData = [
        'user_id' => $currentUser->id,
        'purpose' => $validatedData['purpose'],
        'location' => $validatedData['location'],
        'return_location' => $validatedData['return_location'],
        'loan_start_date' => $validatedData['loan_start_date'],
        'loan_end_date' => $validatedData['loan_end_date'],
        'status' => $targetStatus,
        'applicant_confirmation_timestamp' => $validatedData['applicant_confirmation'] ? now() : null,
      ];

      if ($this->isApplicantResponsible) {
        $applicationData['responsible_officer_id'] = $currentUser->id;
      } else {
        $applicationData['responsible_officer_id'] = $validatedData['responsible_officer_id'];
      }

      $message = '';
      if ($this->isEdit && $this->loanApplication->exists) {
        $this->authorize('update', $this->loanApplication);
        $this->loanApplicationService->updateApplication($this->loanApplication, $applicationData, $validatedData['items'], $currentUser);
        $message = __('Permohonan pinjaman berjaya dikemaskini.');
      } else {
        $this->authorize('create', LoanApplication::class);
        $this->loanApplication = $this->loanApplicationService->createApplication($applicationData, $validatedData['items'], $currentUser);
        $message = $isFinalSubmission ? __('Permohonan pinjaman berjaya dihantar.') : __('Draf permohonan pinjaman berjaya disimpan.');
      }

      if ($isFinalSubmission && $this->loanApplication->status === LoanApplication::STATUS_PENDING_SUPPORT) {
        $this->loanApplicationService->processApplicationSubmission($this->loanApplication, $currentUser);
      }

      DB::commit();
      session()->flash('success', $message);
      $this->dispatch('toastr', type: 'success', message: $message);

      return $this->redirectRoute('resource-management.my-applications.loan.index', navigate: true);
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
      session()->flash('error', __('Berlaku ralat tidak dijangka: ') . $e->getMessage());
      $this->dispatch('toastr', type: 'error', message: __('Ralat sistem. Sila cuba lagi.'));
      return null;
    }
  }

  public function saveAsDraft(): ?RedirectResponse
  {
    return $this->saveApplication(false);
  }

  public function submitForApproval(): ?RedirectResponse
  {
    if (!$this->applicant_confirmation) {
      $this->addError('applicant_confirmation', __('Sila tandakan kotak pengesahan untuk meneruskan.'));
      $this->dispatch('toastr', type: 'error', message: __('Sila buat pengesahan pemohon.'));
      return null;
    }
    return $this->saveApplication(true);
  }

  public function render(): View
  {
    return view('livewire.loan-request-form')
      ->title($this->isEdit ? __('Kemaskini Permohonan Pinjaman') : __('Borang Permohonan Pinjaman Peralatan ICT'));
  }
}
