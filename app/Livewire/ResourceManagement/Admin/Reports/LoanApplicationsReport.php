<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department; // Example model
use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class LoanApplicationsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = null;
    public ?int $filterDepartmentId = null; // Filter by applicant's department
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;
    public string $searchTerm = ''; // Search by applicant name, purpose, ID

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';

    public function mount()
    {
        // $this->authorize('viewLoanApplicationsReport');
    }

    public function getReportDataProperty()
    {
        $query = LoanApplication::with(['user.department', 'applicationItems.equipment', 'responsibleOfficer']);

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterDepartmentId) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('department_id', $this->filterDepartmentId);
            });
        }
        if ($this->filterDateFrom) {
            $query->whereDate('loan_start_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('loan_end_date', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);
        return $query->paginate(15);
    }

    // Options for filters
    public function getStatusOptionsProperty(): array
    {
        return defined(LoanApplication::class . '::STATUS_OPTIONS') ? LoanApplication::$STATUS_OPTIONS : [];
    }
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    public function applyFilters(): void
    {
        $this->resetPage();
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

    public function render()
    {
        return view('livewire.resource-management.admin.reports.loan-applications-report', [
            'reportData' => $this->reportDataProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
        ])->title(__('Laporan Permohonan Pinjaman Peralatan'));
    }
}
