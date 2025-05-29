<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class LoanApplicationsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = '';
    public ?int $filterDepartmentId = null; // Filter by applicant's department
    public ?string $filterDateFrom = null; // Based on loan_start_date
    public ?string $filterDateTo = null;   // Based on loan_start_date range end
    public string $searchTerm = ''; // Search by applicant name, purpose, ID

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';
    public int $perPage = 15;

    public function mount()
    {
        // $this->authorize('viewAny', LoanApplication::class); // Example policy
        Log::info("Livewire\LoanApplicationsReport: Generating Loan Applications Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function getReportDataProperty()
    {
        // Revision 3 LoanApplication fields: user_id, purpose, loan_start_date, loan_end_date, status
        // User fields: name, department_id
        $query = LoanApplication::with([
            'user:id,name,department_id', 'user.department:id,name',
            'applicationItems', // For item count or types if needed
            'responsibleOfficer:id,name',
            'supportingOfficer:id,name'
        ]);

        if (!empty($this->searchTerm)) {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(id) LIKE ?', [$search]) // Assuming ID is numeric, direct like might be tricky. Cast if needed.
                  ->orWhereRaw('LOWER(purpose) LIKE ?', [$search])
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->whereRaw('LOWER(name) LIKE ?', [$search]);
                  });
            });
        }
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        if (!empty($this->filterDepartmentId)) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('department_id', $this->filterDepartmentId);
            });
        }
        if (!empty($this->filterDateFrom)) {
            // Filter by application's creation date or loan_start_date as appropriate
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if (!empty($this->filterDateTo)) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info("Livewire\LoanApplicationsReport: Fetched {$reportData->total()} loan applications.", ['admin_user_id' => Auth::id()]);
        return $reportData;
    }

    // Options for filters
    public function getStatusOptionsProperty(): array
    {
        // Ensure LoanApplication model has this static property/method as per "Revision 3" (4.3)
        return LoanApplication::$STATUS_OPTIONS ?? (defined(LoanApplication::class . '::STATUS_DRAFT') ? LoanApplication::getStatusOptions() : []);
    }
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function updating($property): void
    {
        if (in_array($property, ['filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm'])) {
            $this->resetPage();
        }
    }

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

    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'filterDepartmentId', 'filterDateFrom', 'filterDateTo', 'searchTerm', 'sortBy', 'sortDirection']);
        $this->sortBy = 'created_at'; // Default sort
        $this->sortDirection = 'desc'; // Default direction
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.resource-management.admin.reports.loan-applications-report', [
            'reportData' => $this->reportDataProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
        ])->title(__('Laporan Permohonan Pinjaman Peralatan'));
    }
}
