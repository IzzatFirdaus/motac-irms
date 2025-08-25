<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * LoanApplicationsReport Livewire component.
 * Generates a report of loan applications with search, filtering, and sorting.
 */
#[Layout('layouts.app')]
class LoanApplicationsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = '';
    public ?int $filterDepartmentId = null; // Filter by applicant's department
    public ?string $filterDateFrom = null; // Start date filter
    public ?string $filterDateTo = null;   // End date filter
    public string $searchTerm = ''; // Search by applicant name, purpose, ID

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 15;

    /**
     * Sets the page title dynamically using a translation key.
     * This method is called by Livewire at runtime to get the title.
     */
    #[Title]
    public function pageTitle(): string
    {
        return __('reports.loan_apps_title');
    }

    /**
     * Log when the component is mounted for audit purposes.
     */
    public function mount(): void
    {
        Log::info('Livewire\\LoanApplicationsReport: Component mounted by Admin User ID: '.(Auth::id() ?? 'Guest'), [
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Computed property to fetch paginated loan application report data with filters.
     */
    public function getReportDataProperty()
    {
        $query = LoanApplication::with([
            'user:id,name,department_id',
            'user.department:id,name',
            'loanApplicationItems',
        ]);

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $search = '%'.strtolower($this->searchTerm).'%';
            $query->where(function ($q) use ($search): void {
                $q->where('id', 'like', $search)
                    ->orWhereRaw('LOWER(purpose) LIKE ?', [$search])
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', [$search]);
                    });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDepartmentId) {
            $query->whereHas('user.department', function ($deptQuery): void {
                $deptQuery->where('id', $this->filterDepartmentId);
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);

        // Log the report query for audit.
        Log::info(sprintf('Livewire\LoanApplicationsReport: Fetched %d loan applications for report.', $reportData->total()), [
            'admin_user_id' => Auth::id(),
            'filters' => $this->getPublicFilters(),
        ]);

        return $reportData;
    }

    // Options for filter dropdowns
    public function getStatusOptionsProperty(): array
    {
        // Assuming LoanApplication model has $STATUS_OPTIONS static array
        return LoanApplication::$STATUS_OPTIONS ?? [];
    }

    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        // Department must have is_active field
        return Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Resets pagination when a filter or search term is updated.
     */
    public function updating($property): void
    {
        if (in_array($property, [
            'filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm', 'perPage'
        ])) {
            $this->resetPage();
        }
    }

    /**
     * Set the column to sort by and toggle the direction.
     */
    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Reset all filters to default.
     */
    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Helper for logging: get current filter state.
     */
    private function getPublicFilters(): array
    {
        return [
            'searchTerm' => $this->searchTerm,
            'filterStatus' => $this->filterStatus,
            'filterDepartmentId' => $this->filterDepartmentId,
            'filterDateFrom' => $this->filterDateFrom,
            'filterDateTo' => $this->filterDateTo,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
        ];
    }

    /**
     * Render the Livewire view for the loan applications report.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.reports.loan-applications-report', [
            'reportData' => $this->reportData,
            'statusOptions' => $this->statusOptions,
            'departmentOptions' => $this->departmentOptions,
        ]);
    }
}
