<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // Import the Title attribute
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Laporan Permohonan Pinjaman Peralatan ICT')] // Set the title using the attribute (System Design Rev 3.5 refers to reports)
class LoanApplicationsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = '';
    public ?int $filterDepartmentId = null; // Filter by applicant's department
    public ?string $filterDateFrom = null; // Based on application creation date
    public ?string $filterDateTo = null;   // Based on application creation date range end
    public string $searchTerm = ''; // Search by applicant name, purpose, ID

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';
    public int $perPage = 15;

    public function mount()
    {
        // Example: $this->authorize('viewAnyReport', LoanApplication::class); // Policy for viewing reports
        Log::info("Livewire\\LoanApplicationsReport: Component mounted by Admin User ID: " . (Auth::id() ?? 'Guest'), [
            'ip_address' => request()->ip(),
        ]);
    }

    // Computed property for fetching report data
    public function getReportDataProperty()
    {
        // Eager loading based on what's displayed in the report blade and System Design fields
        $query = LoanApplication::with([
            'user:id,name,department_id',      // System Design: User model fields
            'user.department:id,name',         // System Design: Department model fields
            'loanApplicationItems',                // System Design: LoanApplicationItem relation
            // 'responsibleOfficer:id,name',   // Load if displayed in report
            // 'supportingOfficer:id,name'     // Load if displayed in report
        ]);

        if (!empty($this->searchTerm)) {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search) {
                // Searching ID with LIKE implies ID might be treated as a string or for partial matches.
                // If ID is purely numeric and exact match is needed, a direct where clause is better.
                $q->where('id', 'like', $search)
                  ->orWhereRaw('LOWER(purpose) LIKE ?', [$search]) // Purpose search from System Design
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->whereRaw('LOWER(name) LIKE ?', [$search]); // User name search
                  });
            });
        }
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus); // Status field from System Design
        }
        if (!empty($this->filterDepartmentId)) {
            $query->whereHas('user.department', function ($deptQuery) { // Department relation on User
                $deptQuery->where('id', $this->filterDepartmentId);
            });
        }
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom); // Filtering by application creation date
        }
        if (!empty($this->filterDateTo)) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info("Livewire\\LoanApplicationsReport: Fetched {$reportData->total()} loan applications for report.", [
            'admin_user_id' => Auth::id(),
            'filters' => $this->getPublicFilters(),
        ]);
        return $reportData;
    }

    // Options for filter dropdowns
    public function getStatusOptionsProperty(): array
    {
        // Assuming LoanApplication model has $STATUS_OPTIONS static array as per System Design & provided model
        return LoanApplication::$STATUS_OPTIONS ?? []; //
    }

    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        // Assuming Department model has is_active field as per System Design
        return Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    // Method to apply filters (called by a button or on property update)
    public function applyFilters(): void
    {
        $this->resetPage(); // Reset pagination when filters are applied
    }

    // Livewire lifecycle hook for when a public property is updated
    public function updating($property): void
    {
        // Reset pagination if any filter property changes
        if (in_array($property, ['filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm', 'perPage'])) {
            $this->resetPage();
        }
    }

    // Method for setting sort column and direction
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

    // Method to reset all filters
    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm']);
        $this->sortBy = 'created_at'; // Default sort
        $this->sortDirection = 'desc'; // Default direction
        $this->resetPage();
    }

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

    public function render()
    {
        return view('livewire.resource-management.admin.reports.loan-applications-report', [
            'reportData' => $this->reportDataProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
        ]);
    }
}
