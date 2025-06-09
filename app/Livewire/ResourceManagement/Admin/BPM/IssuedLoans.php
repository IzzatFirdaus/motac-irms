<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\Equipment;
use App\Models\LoanApplication; // For constants if used directly in query
use App\Models\LoanTransaction; // For constants like TYPE_ISSUE
use Illuminate\Contracts\View\View; // Added for render method type hint
use Livewire\Component;
use Livewire\WithPagination;

// Assuming your helper for badge classes if used in the view for this component.

class IssuedLoans extends Component
{
    use WithPagination;

    public string $searchTerm = '';

    public int $perPage = 10;

    protected string $paginationTheme = 'bootstrap'; // Or your preferred theme

    protected $listeners = [
        'filtersUpdated' => 'applyFilters', // Assuming these events are dispatched from elsewhere
        'filtersCleared' => 'clearAllFilters',
    ];

    public function mount(): void
    {
        // Consider adding authorization, e.g.:
        // abort_unless(auth()->user()->can('view_issued_loans'), 403, 'Anda tidak dibenarkan untuk melihat halaman ini.');
    }

    public function applyFilters(array $filters): void
    {
        if (isset($filters['searchTerm'])) {
            $this->searchTerm = $filters['searchTerm'];
        }
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->searchTerm = '';
        $this->resetPage();
    }

    public function getIssuedLoansProperty()
    {
        $query = LoanApplication::query()
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
                LoanApplication::STATUS_OVERDUE,
            ])
            ->with([
                'user.department:id,name', // Eager load user and their department (specific columns)
                'loanApplicationItems' => function ($query) { // Changed 'applicationItems' to 'loanApplicationItems'
                    $query->with([
                        'loanTransactionItems' => function ($subQuery) {
                            // For each LoanTransactionItem, load its parent LoanTransaction and the specific Equipment
                            $subQuery->with([
                                'loanTransaction:id,type,transaction_date', // Load related LoanTransaction with specific columns
                                'equipment:id,tag_id,brand,model', // Load related Equipment with specific columns
                            ]);
                        },
                    ]);
                },
                // Eager load top-level loanTransactions associated with the LoanApplication,
                // primarily to find the latest issue transaction date easily for display.
                'loanTransactions' => function ($query) {
                    $query->where('type', LoanTransaction::TYPE_ISSUE)->orderByDesc('transaction_date');
                },
            ]);

        if (! empty($this->searchTerm)) {
            $search = '%'.strtolower($this->searchTerm).'%';
            // Ensure case-insensitive search if your DB is case-sensitive by default
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(CAST(id AS TEXT)) LIKE ?', [$search]) // Search by Application ID (cast to TEXT if ID is integer)
                    ->orWhereHas('user', function ($sq) use ($search) {
                        $sq->whereRaw('LOWER(name) LIKE ?', [$search]); // Search by Applicant Name
                    })
                    ->orWhereHas('loanApplicationItems.loanTransactionItems.equipment', function ($sq) use ($search) { // Changed 'applicationItems' to 'loanApplicationItems'
                        // Search by Equipment Tag ID or Serial Number
                        $sq->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search]);
                    });
            });
        }

        return $query->orderBy('loan_end_date', 'asc') // Show soonest to be overdue or overdue first
            ->orderBy('id', 'desc') // Secondary sort for consistent ordering
            ->paginate($this->perPage);
    }

    public function render(): View
    {
        // Ensure the title is set using the event dispatch mechanism if #[Title] attribute causes issues
        // For example, in mount(): $this->dispatch('update-page-title', title: __('Senarai Pinjaman Telah Dikeluarkan'));

        return view('livewire.resource-management.admin.bpm.issued-loans', [
            'issuedLoans' => $this->issuedLoans, // Accesses the computed property
            'assetTypeLabels' => Equipment::$ASSET_TYPES_LABELS,
            'loanTransactionTypeIssue' => LoanTransaction::TYPE_ISSUE,
            'loanApplicationStatusReturned' => LoanApplication::STATUS_RETURNED,
            'loanApplicationStatusCancelled' => LoanApplication::STATUS_CANCELLED,
        ]);
    }
}
