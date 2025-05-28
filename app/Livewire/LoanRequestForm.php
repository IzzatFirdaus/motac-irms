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
    // This property will be bound from the Blade for applicant's contact number for this loan
    public string $applicant_mobile_number = ''; // This matches the User model property name

    // Form Fields for Loan Application Details
    public string $purpose = '';
    public string $location = '';
    public ?string $return_location = null;
    public ?string $loan_start_date = null; // Expects Y-m-d from form for validation
    public ?string $loan_end_date = null;   // Expects Y-m-d from form for validation

    // This is the property that was causing the error due to case mismatch.
    // Blade will bind to 'isApplicantResponsible'.
    public bool $isApplicantResponsible = true; //

    // For selecting an existing user as responsible officer
    public ?int $responsible_officer_id = null;

    // For manual entry if responsible officer is not a system user or not selected
    public ?string $manual_responsible_officer_name = '';        //
    public ?string $manual_responsible_officer_jawatan_gred = ''; //
    public ?string $manual_responsible_officer_mobile = '';      //

    public array $items = []; // Stores equipment items for the loan
    public bool $applicant_confirmation = false; // For the declaration checkbox

    // Options for dropdowns
    public SupportCollection $equipmentTypeOptions;
    public SupportCollection $systemUsersForResponsibleOfficer;

    protected LoanApplicationService $loanApplicationService;

    public function rules(): array //
    {
        // $isFinalSubmission is true if the applicant_confirmation checkbox is ticked.
        // This implies some fields might only be mandatory upon final submission.
        $isFinalSubmission = $this->applicant_confirmation;

        $rules = [
          'applicant_mobile_number' => ['required', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'], // Added for applicant's phone
          'purpose' => ['required', 'string', 'min:10', 'max:1000'],
          'location' => ['required', 'string', 'min:5', 'max:255'],
          'return_location' => ['nullable', 'string', 'max:255'],
          'loan_start_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:today'], //datetime-local format
          'loan_end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:loan_start_date'], //datetime-local format
          'isApplicantResponsible' => ['boolean'], // Correct property name

          // Responsible Officer ID is required if applicant is not responsible AND manual name is not provided AND it's a final submission
          'responsible_officer_id' => [
            Rule::requiredIf(!$this->isApplicantResponsible && empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            'nullable',
            'exists:users,id',
            Rule::notIn([Auth::id() ?? 0]), // Cannot be the applicant themselves
          ],
          // Manual Responsible Officer Name is required if applicant is not responsible AND responsible_officer_id is not selected AND it's a final submission
          'manual_responsible_officer_name' => [
            Rule::requiredIf(!$this->isApplicantResponsible && empty($this->responsible_officer_id) && $isFinalSubmission),
            'nullable', 'string', 'max:255'
          ],
          'manual_responsible_officer_jawatan_gred' => [
            Rule::requiredIf(!$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            'nullable', 'string', 'max:255'
          ],
          'manual_responsible_officer_mobile' => [
            Rule::requiredIf(!$this->isApplicantResponsible && !empty($this->manual_responsible_officer_name) && $isFinalSubmission),
            'nullable', 'string', 'regex:/^[0-9\-\+\s\(\)]*$/', 'min:9', 'max:20'
          ],
          'items' => ['required', 'array', 'min:1'],
          'items.*.equipment_type' => ['required', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS ?? []))], // Ensure Equipment::$ASSET_TYPES_LABELS exists
          'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:100'], // Increased max quantity
          'items.*.notes' => ['nullable', 'string', 'max:500'],
          'applicant_confirmation' => $isFinalSubmission ? ['accepted'] : ['boolean'],
        ];
        return $rules;
    }

    public function boot(LoanApplicationService $loanApplicationService): void //
    {
        $this->loanApplicationService = $loanApplicationService;
    }

    public function mount(?int $loanApplicationId = null): void //
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $this->equipmentTypeOptions = collect(['' => __('- Pilih Jenis Peralatan -')] + (Equipment::$ASSET_TYPES_LABELS ?? [])); //
        $this->systemUsersForResponsibleOfficer = collect(['' => __('- Pilih Pegawai (dari senarai sistem) -')]) // Updated placeholder
          ->union(User::where('id', '!=', $user->id)->orderBy('name')->pluck('name', 'id'));

        if ($loanApplicationId) {
            $loanApplication = LoanApplication::with([
              'user', 'responsibleOfficer', 'supportingOfficer', 'applicationItems'
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
            $this->addItem(); // Add one item by default for new applications
        }
    }

    public function updatedIsApplicantResponsible(bool $value): void //
    {
        if ($value) { // If applicant is responsible, clear other officer selection/manual fields
            $this->responsible_officer_id = null;
            $this->manual_responsible_officer_name = '';
            $this->manual_responsible_officer_jawatan_gred = '';
            $this->manual_responsible_officer_mobile = '';
        }
        $this->resetValidation(['responsible_officer_id', 'manual_responsible_officer_name', 'manual_responsible_officer_jawatan_gred', 'manual_responsible_officer_mobile']);
    }

    public function addItem(): void //
    {
        $this->items[] = ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''];
    }

    public function removeItem(int $index): void //
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Re-index
        }
    }

    // This method was correctly named in your file
    public function saveApplication(bool $isFinalSubmission = false): ?RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser) { // Should have been caught by mount()
            session()->flash('error', __('Sila log masuk untuk meneruskan.'));
            return $this->redirectRoute('login', navigate: true);
        }

        if ($isFinalSubmission) {
            // Ensure confirmation is validated only for final submission
            $this->validateOnly('applicant_confirmation', ['applicant_confirmation' => 'accepted']);
        }

        $targetStatus = $this->determineSubmissionStatus($isFinalSubmission);
        $validatedData = $this->validate(); // This will validate all rules

        DB::beginTransaction();
        try {
            $applicationData = [
              'user_id' => $currentUser->id, // Applicant is always the logged-in user
              'purpose' => $validatedData['purpose'],
              'location' => $validatedData['location'],
              'return_location' => $validatedData['return_location'] ?? $validatedData['location'], // Default to usage location
              'loan_start_date' => $validatedData['loan_start_date'],
              'loan_end_date' => $validatedData['loan_end_date'],
              'status' => $targetStatus,
              'applicant_confirmation_timestamp' => $validatedData['applicant_confirmation'] ? now() : null,
              // 'applicant_phone_contact' => $validatedData['applicant_mobile_number'], // Store applicant's phone for this request
            ];

            if ($this->isApplicantResponsible) {
                $applicationData['responsible_officer_id'] = $currentUser->id;
                // Clear any manual entries if applicant is responsible
                // $applicationData['manual_responsible_officer_details'] = null; // If you have such a field
            } else {
                if (!empty($validatedData['responsible_officer_id'])) {
                    $applicationData['responsible_officer_id'] = $validatedData['responsible_officer_id'];
                    // $applicationData['manual_responsible_officer_details'] = null;
                } else {
                    // Store manual details, e.g., in a JSON field or separate fields if your DB supports it
                    // $applicationData['manual_responsible_officer_details'] = json_encode([
                    //     'name' => $validatedData['manual_responsible_officer_name'],
                    //     'jawatan_gred' => $validatedData['manual_responsible_officer_jawatan_gred'],
                    //     'mobile' => $validatedData['manual_responsible_officer_mobile'],
                    // ]);
                    $applicationData['responsible_officer_id'] = null; // Ensure it's null if manual entry is chosen
                }
            }

            $message = '';
            if ($this->isEdit && $this->loanApplication && $this->loanApplication->exists) {
                $this->authorize('update', $this->loanApplication); //
                // The service should handle how to store manual responsible officer details if applicable
                $this->loanApplication = $this->loanApplicationService->updateApplication($this->loanApplication, $applicationData, $validatedData['items'], $currentUser);
                $message = __('Permohonan pinjaman berjaya dikemaskini.');
            } else {
                $this->authorize('create', LoanApplication::class); //
                $this->loanApplication = $this->loanApplicationService->createApplication($applicationData, $validatedData['items'], $currentUser);
                $message = $isFinalSubmission ? __('Permohonan pinjaman berjaya dihantar.') : __('Draf permohonan pinjaman berjaya disimpan.');
            }

            // Only process submission if it's a final submission and status is now pending support
            if ($isFinalSubmission && $this->loanApplication->status === LoanApplication::STATUS_PENDING_SUPPORT) {
                $this->loanApplicationService->processApplicationSubmission($this->loanApplication); // Removed $currentUser if service can get it
            }

            DB::commit();
            session()->flash('success', $message); // Using Laravel session flash
            $this->dispatch('toastr', type: 'success', message: $message); // For a toast notification system

            return $this->redirectRoute('resource-management.my-applications.loan.index', navigate: true);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning("LoanRequestForm: Validation failed for User ID: {$currentUser->id}", ['errors' => $e->errors()]); //
            // Errors will be displayed by Livewire automatically next to fields.
            // A general toastr can also be dispatched.
            $this->dispatch('toastr', type: 'error', message: __('Sila perbetulkan ralat pada borang.'));
            return null;
        } catch (AuthorizationException $e) { // Catch specific AuthorizationException
            DB::rollBack();
            Log::error("LoanRequestForm: Authorization failed for User ID: {$currentUser->id}", ['message' => $e->getMessage()]); //
            session()->flash('error', __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            return null;
        } catch (Throwable $e) { // Catch any other error
            DB::rollBack();
            Log::error("LoanRequestForm: Error submitting application for User ID: {$currentUser->id}", ['exception' => $e]); //
            session()->flash('error', __('Berlaku ralat sistem semasa memproses permohonan anda: ') . $e->getMessage());
            $this->dispatch('toastr', type: 'error', message: __('Ralat sistem. Sila cuba lagi atau hubungi pentadbir.'));
            return null;
        }
    }

    public function saveAsDraft(): ?RedirectResponse //
    {
        // Temporarily set applicant_confirmation to false for draft validation to bypass 'accepted' rule
        $originalConfirmation = $this->applicant_confirmation;
        $this->applicant_confirmation = false;
        $response = $this->saveApplication(false);
        $this->applicant_confirmation = $originalConfirmation; // Restore original value
        return $response;
    }

    public function submitForApproval(): ?RedirectResponse //
    {
        $this->applicant_confirmation = true; // Ensure confirmation is set for final submission
        if (!$this->applicant_confirmation) { // This check is now redundant due to above line, but keep for explicit validation trigger
            $this->validateOnly('applicant_confirmation'); // Trigger validation for confirmation
            $this->dispatch('toastr', type: 'error', message: __('Sila buat pengesahan pemohon.'));
            return null;
        }
        return $this->saveApplication(true);
    }

    public function render(): View //
    {
        return view('livewire.loan-request-form')
          ->title($this->isEdit ? __('Kemaskini Permohonan Pinjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT'));
    }

    protected function messages(): array //
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

    private function prefillApplicantDetails(User $user): void //
    {
        $this->applicant_name = $user->full_name ?? $user->name; // Prefer full_name
        $this->applicant_jawatan_gred = ($user->position?->name ?? '') . ($user->grade?->name ? ' (Gred ' . $user->grade->name . ')' : '');
        $this->applicant_bahagian_unit = $user->department?->name ?? ''; // Add null coalescing
        $this->applicant_mobile_number = $user->mobile_number ?? ''; // Prefill applicant's own mobile
    }

    private function fillFormFromModel(): void //
    {
        if (!$this->loanApplication) {
            return;
        }

        $applicantUser = $this->loanApplication->user ?? Auth::user(); // Fallback to Auth user if not set on model
        if ($applicantUser) {
            $this->prefillApplicantDetails($applicantUser);
        }

        // If applicant_mobile_number for the loan form is different or specifically recorded
        // $this->applicant_mobile_number = $this->loanApplication->applicant_phone_contact ?? $applicantUser->mobile_number;

        $this->purpose = $this->loanApplication->purpose;
        $this->location = $this->loanApplication->location;
        $this->return_location = $this->loanApplication->return_location;
        $this->loan_start_date = $this->loanApplication->loan_start_date?->format('Y-m-d\TH:i'); // Ensure consistent format with <input type="datetime-local">
        $this->loan_end_date = $this->loanApplication->loan_end_date?->format('Y-m-d\TH:i');   // Ensure consistent format

        $this->isApplicantResponsible = is_null($this->loanApplication->responsible_officer_id) ||
                                       $this->loanApplication->responsible_officer_id === $this->loanApplication->user_id;

        if (!$this->isApplicantResponsible && $this->loanApplication->responsibleOfficer) {
            $this->responsible_officer_id = $this->loanApplication->responsible_officer_id;
            // If you also store manual details even when an ID is selected, prefill them:
            // $this->manual_responsible_officer_name = $this->loanApplication->responsibleOfficer->name; // Or from a stored manual field
            // $this->manual_responsible_officer_jawatan_gred = ...
            // $this->manual_responsible_officer_mobile = ...
        } elseif (!$this->isApplicantResponsible && !$this->loanApplication->responsibleOfficer && $this->loanApplication->manual_responsible_officer_details) {
            // Assuming manual details are stored as JSON if no ID
            // $details = json_decode($this->loanApplication->manual_responsible_officer_details, true);
            // $this->manual_responsible_officer_name = $details['name'] ?? '';
            // $this->manual_responsible_officer_jawatan_gred = $details['jawatan_gred'] ?? '';
            // $this->manual_responsible_officer_mobile = $details['mobile'] ?? '';
        }


        $this->items = $this->loanApplication->applicationItems->map(function ($item) {
            return [
              'id' => $item->id, // Keep track of existing item IDs for updates if necessary
              'equipment_type' => $item->equipment_type,
              'quantity_requested' => $item->quantity_requested,
              'notes' => $item->notes,
            ];
        })->toArray();

        $this->applicant_confirmation = (bool)$this->loanApplication->applicant_confirmation_timestamp;
    }

    // This method was correctly named in your file
    private function determineSubmissionStatus(bool $isFinalButtonClicked): string
    {
        /** @var User $currentUser */
        $currentUser = Auth::user(); // Should always be available due to mount check

        // If editing and status is already beyond draft and user is not admin, keep current status
        if ($this->isEdit && $this->loanApplication &&
            $this->loanApplication->status !== LoanApplication::STATUS_DRAFT &&
            !$currentUser->hasRole('Admin')) { // Use hasRole for clarity
            return $this->loanApplication->status;
        }
        return $isFinalButtonClicked ? LoanApplication::STATUS_PENDING_SUPPORT : LoanApplication::STATUS_DRAFT;
    }
}
