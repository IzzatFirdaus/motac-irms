<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransaction; // For constants if used directly in query
use App\Models\Equipment;       // For constants like ASSET_TYPES_LABELS
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use App\Helpers\Helpers; // Assuming your helper for badge classes

class IssuedLoans extends Component
{
    use WithPagination;

    public string $searchTerm = '';
    public int $perPage = 10;
    protected string $paginationTheme = 'bootstrap'; // Or your preferred theme

    protected $listeners = [
        'filtersUpdated' => 'applyFilters',
        'filtersCleared' => 'clearAllFilters'
    ];

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
                LoanApplication::STATUS_OVERDUE
            ])
            ->with([
                'user.department',
                'applicationItems.loanTransactionItems.equipment', // Eager load equipment details
                'loanTransactions' // Eager load transactions to find the latest issue date
            ]);

        if (!empty($this->searchTerm)) {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(id) LIKE ?', [$search]) // Search by Application ID
                  ->orWhereHas('user', function ($sq) use ($search) {
                      $sq->whereRaw('LOWER(name) LIKE ?', [$search]); // Search by Applicant Name
                  })
                  ->orWhereHas('applicationItems.loanTransactionItems.equipment', function ($sq) use ($search) {
                      // Search by Equipment Tag ID or Serial Number
                      $sq->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                         ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search]);
                  });
            });
        }

        return $query->orderBy('loan_end_date', 'asc') // Show soonest to be overdue or overdue first
                     ->orderBy('id', 'desc')
                     ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.resource-management.admin.bpm.issued-loans', [
            'issuedLoans' => $this->issuedLoans, // Use the accessor
            'assetTypeLabels' => Equipment::$ASSET_TYPES_LABELS, // Pass for display
            'loanTransactionTypeIssue' => LoanTransaction::TYPE_ISSUE, // Pass for filtering in blade
            'loanApplicationStatusReturned' => LoanApplication::STATUS_RETURNED, // Pass for conditional logic in blade
            'loanApplicationStatusCancelled' => LoanApplication::STATUS_CANCELLED, // Pass for conditional logic in blade
        ]);
    }
}
