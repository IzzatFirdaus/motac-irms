<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class IssuedLoans extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public int $perPage = 10;
    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        // Authorization check to ensure only permitted users can see this component.
        $this->authorize('viewAny', LoanTransaction::class);
    }

    /**
     * Resets the page when the search term is updated for accurate pagination.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * A computed property that fetches issued loans with advanced filtering and sorting.
     */
    public function getIssuedLoansProperty()
    {
        $query = LoanApplication::query()
            // Fetches all relevant statuses for loans that are out of the inventory.
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
                LoanApplication::STATUS_OVERDUE,
            ])
            // Eager loads all necessary relationships for display and searching, preventing N+1 issues.
            ->with([
                'user:id,name,department_id',
                'user.department:id,name',
                'loanApplicationItems.loanTransactionItems.equipment:id,tag_id,brand,model,serial_number',
                'loanApplicationItems.loanTransactionItems.loanTransaction:id,type',
                // ***** THIS IS THE FIX: Added 'type' to the selected columns *****
                'loanTransactions' => fn ($q) => $q->where('type', LoanTransaction::TYPE_ISSUE)->select('id', 'loan_application_id', 'transaction_date', 'type')->orderByDesc('transaction_date')
            ]);

        // Applies the search filter if the search term is not empty.
        if (! empty($this->searchTerm)) {
            $search = '%'.strtolower($this->searchTerm).'%';
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', $search) // Search by Application ID
                    ->orWhereHas('user', function ($sq) use ($search) {
                        $sq->whereRaw('LOWER(name) LIKE ?', [$search]); // Search by Applicant Name
                    })
                    ->orWhereHas('loanApplicationItems.loanTransactionItems.equipment', function ($sq) use ($search) {
                        // Search by Equipment Tag ID or Serial Number
                        $sq->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                           ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search]);
                    });
            });
        }

        // Default sorting to show overdue or soon-to-be-due items first.
        return $query->orderBy('loan_end_date', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Renders the component's view, passing the paginated data.
     */
    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.issued-loans', [
            'issuedLoans' => $this->issuedLoans,
        ]);
    }
}
