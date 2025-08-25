<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component to display and manage issued loan applications for BPM/admin.
 * Allows searching, pagination, and links to transaction/return actions.
 */
class IssuedLoans extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public int $perPage = 10;

    protected string $paginationTheme = 'bootstrap';

    /**
     * Authorization check on mount.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', LoanTransaction::class);
    }

    /**
     * Reset pagination when search term is updated.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Computed property: gets the paginated, filtered issued loan applications.
     */
    public function getIssuedLoansProperty()
    {
        $query = LoanApplication::query()
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
                LoanApplication::STATUS_OVERDUE,
            ])
            ->with([
                'user:id,name,department_id',
                'user.department:id,name',
                'loanApplicationItems.loanTransactionItems.equipment:id,tag_id,brand,model,serial_number',
                'loanApplicationItems.loanTransactionItems.loanTransaction:id,type',
                // Eager-load only latest issue transaction for each application
                'loanTransactions' => fn ($q) => $q
                    ->where('type', LoanTransaction::TYPE_ISSUE)
                    ->select('id', 'loan_application_id', 'transaction_date', 'type')
                    ->orderByDesc('transaction_date'),
            ]);

        // Search by application ID, applicant name, tag ID, or serial number.
        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $search = '%'.strtolower($this->searchTerm).'%';
            $query->where(function ($q) use ($search): void {
                $q->where('id', 'like', $search)
                    ->orWhereHas('user', function ($sq) use ($search): void {
                        $sq->whereRaw('LOWER(name) LIKE ?', [$search]);
                    })
                    ->orWhereHas('loanApplicationItems.loanTransactionItems.equipment', function ($sq) use ($search): void {
                        $sq->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search]);
                    });
            });
        }

        // Sort: overdue/soonest due first.
        return $query->orderBy('loan_end_date', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Render the Blade view for this component.
     */
    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.issued-loans', [
            'issuedLoans' => $this->issuedLoans,
        ]);
    }
}
