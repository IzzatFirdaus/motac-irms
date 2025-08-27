<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for BPM/admin to manage and process outstanding loan applications
 * (applications that are approved and pending issuance).
 */
#[Layout('layouts.app')]
/**
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $outstandingApplications
 */
class OutstandingLoans extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $sortBy = 'updated_at'; // Default sort column

    public string $sortDirection = 'desc'; // Default sort direction

    protected string $paginationTheme = 'bootstrap';

    /**
     * Authorization check on mount.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
    }

    /**
     * Handles column sorting and toggling direction.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = in_array($field, ['updated_at', 'loan_end_date']) ? 'desc' : 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    /**
     * Computed property: gets paginated, filtered outstanding applications.
     */
    public function getOutstandingApplicationsProperty()
    {
        $this->authorize('viewAny', LoanApplication::class);

        $query = LoanApplication::query()
            ->with(['user:id,name', 'loanApplicationItems'])
            ->where('status', LoanApplication::STATUS_APPROVED);

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $searchTerm = '%' . $this->searchTerm . '%';
            $query->where(function ($subQuery) use ($searchTerm): void {
                $subQuery->where('id', 'like', $searchTerm)
                    ->orWhere('purpose', 'like', $searchTerm)
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm): void {
                        $userQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        $validSorts = ['id', 'purpose', 'loan_end_date', 'updated_at'];
        if (in_array($this->sortBy, $validSorts)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate(10);
    }

    /**
     * Reset pagination when search term is updated.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Render the Blade view for this component.
     */
    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.outstanding-loans', [
            'applications' => $this->getOutstandingApplicationsProperty(),
        ]);
    }
}
