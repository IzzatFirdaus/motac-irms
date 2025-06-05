<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use App\Services\LoanTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

class ProcessReturn extends Component
{
    use AuthorizesRequests;

    public LoanApplication $loanApplication;
    public LoanTransaction $issueTransaction; // The original "issue" transaction object

    // Form bound properties for the new return transaction
    public array $selectedTransactionItemIds = []; // Stores IDs of LoanTransactionItem from the issueTransaction
    public string $return_notes = '';

    // Item-specific details, keys are the IDs of the *original issue transaction items*
    public array $itemConditions = [];           // ['original_issue_item_id' => 'condition_value']
    public array $itemReturnNotes = [];          // ['original_issue_item_id' => 'notes_text']
    public array $itemSpecificAccessories = [];  // ['original_issue_item_id' => ['accessory1', 'accessory2']]

    public $returningOfficerId; // User ID of the person physically returning the items

    // Component state
    public array $allAccessoriesList = [];
    public $itemsAvailableForReturn; // Collection of LoanTransactionItems from the issueTransaction

    public function mount(int $issueTransactionId, int $loanApplicationId): void // Accepts both IDs
    {
        $this->issueTransaction = LoanTransaction::with([
            'loanApplication.user',
            'loanApplication.responsibleOfficer',
            'loanTransactionItems.equipment',
            'loanTransactionItems.loanApplicationItem'
        ])->findOrFail($issueTransactionId);

        if ($this->issueTransaction->loan_application_id != $loanApplicationId) {
            session()->flash('error', __('Kesilapan data: Transaksi pengeluaran tidak sepadan dengan permohonan pinjaman.'));
            Log::error('ProcessReturn Mount: Mismatch between issueTransactionId and loanApplicationId.', [
                'issue_transaction_id' => $issueTransactionId,
                'expected_loan_application_id' => $this->issueTransaction->loan_application_id,
                'provided_loan_application_id' => $loanApplicationId,
                'user_id' => Auth::id(),
            ]);
            $this->itemsAvailableForReturn = collect();
            return;
        }
        $this->loanApplication = $this->issueTransaction->loanApplication;

        try {
            $this->authorize('processReturn', $this->loanApplication);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            session()->flash('error', __('Anda tidak mempunyai kebenaran untuk memproses pemulangan untuk permohonan ini: ') . $e->getMessage());
            Log::warning('ProcessReturn Mount: Authorization failed.', [
                'loan_application_id' => $this->loanApplication->id,
                'issue_transaction_id' => $this->issueTransaction->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->itemsAvailableForReturn = collect();
            return;
        }

        // Use the config value you provided: 'motac.loan_accessories_list'
        $this->allAccessoriesList = config('motac.loan_accessories_list', ['Power Cable', 'Bag', 'Mouse', 'HDMI Cable', 'User Manual', 'Charger', 'Keyboard', 'Stylus Pen']);
        $this->loadItemsAvailableForReturn();

        foreach ($this->itemsAvailableForReturn as $item) {
            $itemId = $item->id;
            $this->itemConditions[$itemId] = Equipment::CONDITION_GOOD;
            $this->itemReturnNotes[$itemId] = '';
            // Ensure accessories_checklist_issue exists on $item (LoanTransactionItem)
            // or fetch from $this->issueTransaction->accessories_checklist_on_issue if it's a global list for the issue.
            // Your blade has $item->accessories_checklist_issue, implying it's per item.
            $this->itemSpecificAccessories[$itemId] = $item->accessories_checklist_issue ?? [];
        }

        if ($this->loanApplication->user) {
            $this->returningOfficerId = $this->loanApplication->user->id;
        }
    }

    public function loadItemsAvailableForReturn(): void
    {
        if (!$this->issueTransaction) {
            $this->itemsAvailableForReturn = collect();
            return;
        }
        $this->itemsAvailableForReturn = $this->issueTransaction->loanTransactionItems()
            ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
            ->with('equipment')
            ->get();
    }

    private function determineItemStatusBasedOnCondition(?string $condition): string
    {
        if ($condition === null) return LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION; // Should ideally not be null if validated
        // Assuming Equipment model has these constants defined
        return match ($condition) {
            Equipment::CONDITION_MINOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            Equipment::CONDITION_MAJOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            Equipment::CONDITION_UNSERVICEABLE => LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
            Equipment::CONDITION_LOST => LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
            Equipment::CONDITION_GOOD, Equipment::CONDITION_FAIR, Equipment::CONDITION_NEW => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
            default => LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION,
        };
    }

    public function submitReturn(LoanTransactionService $loanTransactionService): void
    {
        if (!$this->issueTransaction) {
            session()->flash('error', __('Transaksi pengeluaran asal tidak dapat dikenal pasti. Sila muat semula halaman.'));
            Log::error('ProcessReturn Submit: Aborted due to missing issueTransaction.', ['loan_application_id' => $this->loanApplication->id, 'user_id' => Auth::id()]);
            return;
        }

        try {
            $this->authorize('processReturn', $this->loanApplication);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            session()->flash('error', __('Kebenaran ditolak: ') . $e->getMessage());
            Log::warning('ProcessReturn Submit: Re-authorization failed.', ['loan_application_id' => $this->loanApplication->id, 'issue_transaction_id' => $this->issueTransaction->id, 'user_id' => Auth::id()]);
            return;
        }

        $validatedData = $this->validate(); // Uses rules() method

        /** @var \App\Models\User|null $returnAcceptingOfficer */
        $returnAcceptingOfficer = Auth::user();
        if (!$returnAcceptingOfficer) {
            session()->flash('error', __('Pengguna tidak disahkan. Sila log masuk semula.'));
            return;
        }

        $itemsDataForService = [];
        foreach ($validatedData['selectedTransactionItemIds'] as $originalIssueItemId) {
            $condition = $validatedData['itemConditions'][$originalIssueItemId] ?? Equipment::CONDITION_GOOD;
            $itemsDataForService[] = [
                'loan_transaction_item_id' => (int) $originalIssueItemId,
                'quantity_returned' => 1,
                'condition_on_return' => $condition,
                'return_item_notes' => $validatedData['itemReturnNotes'][$originalIssueItemId] ?? null,
                'accessories_checklist_item' => $validatedData['itemSpecificAccessories'][$originalIssueItemId] ?? [],
                'item_status_on_return' => $this->determineItemStatusBasedOnCondition($condition),
            ];
        }

        if (empty($itemsDataForService)) {
            session()->flash('error', __('Tiada item dipilih untuk dipulangkan atau berlaku ralat data.'));
            return;
        }

        $dataForService = [
            'items' => $itemsDataForService,
            'returning_officer_id' => $validatedData['returningOfficerId'],
            'transaction_date' => now()->format('Y-m-d H:i:s'),
            'return_notes' => $validatedData['return_notes'] ?? null,
        ];

        try {
            $loanTransactionService->processExistingReturn(
                $this->issueTransaction,
                $dataForService,
                $returnAcceptingOfficer
            );

            session()->flash('success', __('Peralatan berjaya dipulangkan dan direkodkan.'));
            $this->redirectRoute('loan-applications.show', ['loan_application' => $this->loanApplication->id], navigate: true);
        } catch (Throwable $e) {
            Log::error('Error processing return in ProcessReturn Livewire: ' . $e->getMessage(), [
                'loan_application_id' => $this->loanApplication->id,
                'issue_transaction_id' => $this->issueTransaction->id,
                'user_id' => $returnAcceptingOfficer->id,
                'exception_class' => get_class($e),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 1000),
                'data_sent_to_service' => $dataForService,
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal memulangkan peralatan disebabkan ralat sistem. Sila hubungi pentadbir.');
            session()->flash('error', $errorMessage);
        }
    }

    protected function rules(): array
    {
        $rules = [
            'selectedTransactionItemIds' => ['required', 'array', 'min:1'],
            'selectedTransactionItemIds.*' => [
                'required',
                'integer',
                Rule::exists('loan_transaction_items', 'id')
                    ->where('loan_transaction_id', $this->issueTransaction?->id)
                    ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
            ],
            'returningOfficerId' => ['required', 'exists:users,id'],
            'return_notes' => ['nullable', 'string', 'max:2000'],
            'itemConditions' => ['required', 'array'],
            'itemReturnNotes' => ['nullable', 'array'],
            'itemSpecificAccessories' => ['nullable', 'array'],
        ];

        // Add rules for each selected item's condition, notes, and accessories
        foreach ($this->selectedTransactionItemIds as $itemId) {
            $itemId = (int) $itemId; // Ensure integer key for array access
            $rules['itemConditions.' . $itemId] = ['required', Rule::in(array_keys(Equipment::getConditionStatusOptions()))];
            $rules['itemReturnNotes.' . $itemId] = ['nullable', 'string', 'max:1000'];
            $rules['itemSpecificAccessories.' . $itemId] = ['nullable', 'array'];
            $rules['itemSpecificAccessories.' . $itemId . '.*'] = ['nullable', 'string', 'max:255']; // Each accessory string
        }
        return $rules;
    }

    public function updatedSelectedTransactionItemIds($value): void
    {
        $newConditions = [];
        $newNotes = [];
        $newAccessories = [];

        $selectedItemsModels = collect();
        if (!empty($this->selectedTransactionItemIds)) {
            $selectedItemsModels = LoanTransactionItem::with('equipment')
                ->whereIn('id', array_map('intval', $this->selectedTransactionItemIds)) // Ensure IDs are integers
                ->where('loan_transaction_id', $this->issueTransaction?->id)
                ->get()
                ->keyBy('id');
        }

        foreach ($this->selectedTransactionItemIds as $itemIdStr) {
            $itemId = (int)$itemIdStr;
            $originalItem = $selectedItemsModels->get($itemId);

            $newConditions[$itemId] = $this->itemConditions[$itemId] ?? Equipment::CONDITION_GOOD;
            $newNotes[$itemId] = $this->itemReturnNotes[$itemId] ?? '';
            // Ensure $originalItem->accessories_checklist_issue is an array or default to empty array
            $newAccessories[$itemId] = $this->itemSpecificAccessories[$itemId] ?? ($originalItem && is_array($originalItem->accessories_checklist_issue) ? $originalItem->accessories_checklist_issue : []);
        }

        $this->itemConditions = $newConditions;
        $this->itemReturnNotes = $newNotes;
        $this->itemSpecificAccessories = $newAccessories;
    }

    public function render()
    {
        $usersForDropdown = collect();
        if ($this->loanApplication && $this->loanApplication->user) {
            $usersForDropdown->push($this->loanApplication->user);
        }
        if ($this->loanApplication && $this->loanApplication->responsibleOfficer &&
            (!$this->loanApplication->user || $this->loanApplication->responsibleOfficer->id !== $this->loanApplication->user->id)) {
            $usersForDropdown->push($this->loanApplication->responsibleOfficer);
        }
        // Consider fetching a broader list of users if needed, e.g., all BPM staff or all active users
        // $allActiveUsers = User::where('status', User::STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']);
        // $usersForDropdown = $usersForDropdown->merge($allActiveUsers)->unique('id')->sortBy('name');


        return view('livewire.resource-management.admin.bpm.process-return', [
            'usersForDropdown' => $usersForDropdown->unique('id')->sortBy('name'),
        ]);
    }
}
