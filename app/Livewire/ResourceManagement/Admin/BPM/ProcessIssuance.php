<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem; // ADDED
use App\Models\LoanTransaction; // ADDED for policy check
use App\Models\LoanTransactionItem; // ADDED for validation
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ADDED
use Throwable; // ADDED

class ProcessIssuance extends Component
{
    use AuthorizesRequests; // ADDED

    public LoanApplication $loanApplication;
    public array $allAccessoriesList = [];
    public $availableEquipment = [];

    public array $issueItems = [];
    public $receiving_officer_id;
    public $transaction_date;
    public string $issue_notes = '';
    // public array $overall_accessories_checklist = []; // Placeholder if overall accessories are added

    public function mount(int $loanApplicationId): void
    {
        $this->loanApplication = LoanApplication::with([
            'loanApplicationItems.equipment', // equipment relationship might not exist on LoanApplicationItem directly, but type is there
            'user'
        ])->findOrFail($loanApplicationId);

        // Authorization to even view/mount this form component
        // This uses LoanApplicationPolicy::processIssuance if defined, or view for LoanApplication
        // Consider a specific policy for "showing issuance form for loan application"
        try {
            $this->authorize('processIssuance', $this->loanApplication); // Policy on LoanApplication
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            session()->flash('error', __('Anda tidak mempunyai kebenaran untuk memproses pengeluaran untuk permohonan ini: ') . $e->getMessage());
            Log::warning('ProcessIssuance Mount: Authorization failed.', [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            // Redirect or show error state
            // For now, let render handle the flash message. A redirect might be cleaner.
            // Example: return $this->redirectRoute('dashboard', navigate: true);
            return;
        }


        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable']); //
        $this->loadAvailableEquipment();

        if (empty($this->issueItems)) {
             foreach ($this->loanApplication->loanApplicationItems as $appItem) {
                 if (($appItem->quantity_approved ?? 0) - ($appItem->quantity_issued ?? 0) > 0) { // Ensure there's a balance
                     $this->issueItems[] = [
                         'loan_application_item_id' => $appItem->id,
                         'equipment_id' => null,
                         'quantity_issued' => 1,
                         'max_quantity_issuable' => ($appItem->quantity_approved ?? 0) - ($appItem->quantity_issued ?? 0),
                         'equipment_type' => $appItem->equipment_type,
                         'issue_item_notes' => '',
                         'accessories_checklist_item' => [],
                     ];
                 }
             }
            if(empty($this->issueItems)){
                 $this->addIssueItem();
            }
        }

        $this->receiving_officer_id = $this->loanApplication->user_id;
        $this->transaction_date = Carbon::now()->format('Y-m-d');
    }

    public function loadAvailableEquipment(): void
    {
        $this->availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE) //
            ->orderBy('asset_type')
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    public function addIssueItem(): void
    {
        $this->issueItems[] = [
            'loan_application_item_id' => null,
            'equipment_id' => null,
            'quantity_issued' => 1,
            'max_quantity_issuable' => 0,
            'equipment_type' => null,
            'issue_item_notes' => '',
            'accessories_checklist_item' => [],
        ];
    }

    public function removeIssueItem(int $index): void
    {
        if (count($this->issueItems) > 1) {
            unset($this->issueItems[$index]);
            $this->issueItems = array_values($this->issueItems);
        }
    }


    public function submitIssue(LoanTransactionService $loanTransactionService): void //
    {
        // ADDED: Explicit Policy Check for the action of submitting
        try {
            // Authorize creating an issue transaction FOR this loan application
            $this->authorize('createIssue', [LoanTransaction::class, $this->loanApplication]); //
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            session()->flash('error', __('Anda tidak mempunyai kebenaran untuk merekodkan pengeluaran ini: ') . $e->getMessage());
            Log::warning('ProcessIssuance Submit: Authorization failed for submitting issue.', [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return;
        }

        $validatedFromComponent = $this->validate(); // Uses rules() method below

        /** @var \App\Models\User|null $issuingOfficer */
        $issuingOfficer = Auth::user();

        if (!$issuingOfficer) {
            session()->flash('error', __('Pengguna tidak disahkan. Sila log masuk semula.'));
            Log::warning('ProcessIssuance: Attempted to submit issue without authenticated user.', [
                'loan_application_id' => $this->loanApplication->id,
            ]);
            return;
        }

        $dataForService = [
            'items' => $validatedFromComponent['issueItems'],
            'receiving_officer_id' => $validatedFromComponent['receiving_officer_id'],
            // Ensure transaction_date includes time if service layer or DB expects datetime.
            // Assuming service layer handles parsing if only date is provided, or form provides datetime.
            // Let's make it explicit to use current time if only date is from form:
            'transaction_date' => Carbon::parse($validatedFromComponent['transaction_date'])->startOfDay()->format('Y-m-d H:i:s'), // Or ->now() if date isn't user-selectable for time
            'issue_notes' => $validatedFromComponent['issue_notes'] ?? null,
            // If overall_accessories_checklist is implemented:
            // 'accessories_checklist_on_issue' => $validatedFromComponent['overall_accessories_checklist'] ?? [],
        ];

        try {
            $loanTransactionService->processNewIssue( //
                $this->loanApplication,
                $dataForService,
                $issuingOfficer
            );

            session()->flash('success', 'Peralatan berjaya dikeluarkan.');
            $this->redirectRoute('loan-applications.show', ['loan_application' => $this->loanApplication->id], navigate: true); //
        } catch (Throwable $e) { // Changed from \Exception
            Log::error('Error processing issuance in ProcessIssuance Livewire: ' . $e->getMessage(), [
                'loan_application_id' => $this->loanApplication->id,
                'user_id' => $issuingOfficer->id,
                'exception_class' => get_class($e), // ADDED
                'exception_trace_snippet' => substr($e->getTraceAsString(),0,500), // ADDED
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal mengeluarkan peralatan disebabkan ralat sistem.');
            session()->flash('error', $errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.process-issuance', [ //
            'users' => User::orderBy('name')->get()
        ]);
    }

    protected function rules(): array
    {
        $loanApplicationId = $this->loanApplication->id;

        return [
            'issueItems' => ['required', 'array', 'min:1'],
            'issueItems.*.loan_application_item_id' => [
                'required',
                'integer',
                Rule::exists('loan_application_items', 'id')->where('loan_application_id', $loanApplicationId)
            ],
            'issueItems.*.equipment_id' => [
                'required',
                'integer',
                Rule::exists('equipment', 'id')->where('status', Equipment::STATUS_AVAILABLE) //
            ],
            'issueItems.*.quantity_issued' => [
                'required',
                'integer',
                'min:1',
                // ADDED: Robust server-side validation for quantity_issued for logic inspiration
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $loanAppItemIdInput = $this->issueItems[$index]['loan_application_item_id'] ?? null;

                    if (!$loanAppItemIdInput || !is_numeric($loanAppItemIdInput)) {
                        return; // Should be caught by other rules
                    }
                    $loanAppItemId = (int) $loanAppItemIdInput;

                    /** @var LoanApplicationItem|null $appItem */
                    $appItem = LoanApplicationItem::find($loanAppItemId); //

                    if (!$appItem || $appItem->loan_application_id !== $this->loanApplication->id) {
                        return; // Should be caught by Rule::exists
                    }

                    // Use the LoanApplicationItem's own quantity_issued as the source of truth for "already issued"
                    $currentAppItemIssuedQty = $appItem->quantity_issued ?? 0;
                    $quantityApprovedForItem = (int) ($appItem->quantity_approved ?? $appItem->quantity_requested);
                    $maxAllowedToIssueNow = $quantityApprovedForItem - $currentAppItemIssuedQty;

                    if ((int) $value > $maxAllowedToIssueNow) {
                        $fail(__('Kuantiti untuk dikeluarkan (:value) bagi item permohonan #:item_display_num melebihi baki yang boleh dikeluarkan (:can_issue). Diluluskan: :approved, Telah dikeluarkan: :already_issued.', [
                            'value' => $value,
                            'item_display_num' => ((int) $index) + 1,
                            'can_issue' => max(0, $maxAllowedToIssueNow),
                            'approved' => $quantityApprovedForItem,
                            'already_issued' => $currentAppItemIssuedQty
                        ]));
                    }
                },
            ],
            'issueItems.*.issue_item_notes' => ['nullable', 'string', 'max:1000'],
            'issueItems.*.accessories_checklist_item' => ['nullable', 'array'],
            'issueItems.*.accessories_checklist_item.*' => ['nullable', 'string', 'max:255'],

            'receiving_officer_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'transaction_date' => ['required', 'date_format:Y-m-d'], // UI provides date, service might append time
            'issue_notes' => ['nullable', 'string', 'max:2000'],
            // If overall_accessories_checklist is added:
            // 'overall_accessories_checklist' => ['nullable', 'array'],
            // 'overall_accessories_checklist.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updatedIssueItems(mixed $value, string $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 3 && $parts[0] === 'issueItems' && $parts[2] === 'loan_application_item_id') {
            $index = (int)$parts[1];
            $loanAppItemId = $this->issueItems[$index]['loan_application_item_id'] ?? null;

            if ($loanAppItemId) {
                $appItem = $this->loanApplication->loanApplicationItems()->find($loanAppItemId);
                if ($appItem) {
                    $this->issueItems[$index]['max_quantity_issuable'] = ($appItem->quantity_approved ?? 0) - ($appItem->quantity_issued ?? 0);
                    $this->issueItems[$index]['equipment_type'] = $appItem->equipment_type;
                    $this->issueItems[$index]['equipment_id'] = null; // Reset equipment on app item change
                    if ($this->issueItems[$index]['quantity_issued'] > $this->issueItems[$index]['max_quantity_issuable']) {
                         $this->issueItems[$index]['quantity_issued'] = $this->issueItems[$index]['max_quantity_issuable'];
                    }
                     if ($this->issueItems[$index]['quantity_issued'] <= 0 && $this->issueItems[$index]['max_quantity_issuable'] > 0) {
                        $this->issueItems[$index]['quantity_issued'] = 1;
                    } elseif ($this->issueItems[$index]['max_quantity_issuable'] <= 0) {
                        $this->issueItems[$index]['quantity_issued'] = 0;
                    }
                }
            } else {
                $this->issueItems[$index]['max_quantity_issuable'] = 0;
                $this->issueItems[$index]['equipment_type'] = null;
                $this->issueItems[$index]['equipment_id'] = null;
                $this->issueItems[$index]['quantity_issued'] = 0;
            }
        }
        if (count($parts) === 3 && $parts[0] === 'issueItems' && $parts[2] === 'quantity_issued') {
            $index = (int)$parts[1];
            if (isset($this->issueItems[$index]['max_quantity_issuable']) && $this->issueItems[$index]['quantity_issued'] > $this->issueItems[$index]['max_quantity_issuable']) {
                $this->issueItems[$index]['quantity_issued'] = $this->issueItems[$index]['max_quantity_issuable'];
            }
            if ($this->issueItems[$index]['quantity_issued'] < 0) { // Ensure not negative
                 $this->issueItems[$index]['quantity_issued'] = 0;
            }
        }
    }
}
