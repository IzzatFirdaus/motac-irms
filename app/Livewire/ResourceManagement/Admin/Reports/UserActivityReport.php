<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\User; // Example model
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserActivityReport extends Component
{
    use AuthorizesRequests, WithPagination;

    protected string $paginationTheme = 'bootstrap';

    // Filter properties
    public string $searchTerm = ''; // Search by user name/email
    public ?int $filterDepartmentId = null;
    public ?string $filterRoleName = null;

    // Sorting properties
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public function mount()
    {
        // $this->authorize('viewUserActivityReport');
    }

    public function getReportDataProperty()
    {
        $query = User::withCount([
            'emailApplications',
            'loanApplicationsAsApplicant',
            'approvalsMade'
        ])->with(['department', 'roles']);

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        }
        if ($this->filterDepartmentId) {
            $query->where('department_id', $this->filterDepartmentId);
        }
        if ($this->filterRoleName) {
            $query->whereHas('roles', function($q){
                $q->where('name', $this->filterRoleName);
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);
        return $query->paginate(15);
    }

    // Options for filters
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }
    public function getRoleOptionsProperty(): \Illuminate\Support\Collection
    {
        return Role::orderBy('name')->pluck('name', 'name'); // Using name as value for easy filtering
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
        return view('livewire.resource-management.admin.reports.user-activity-report', [
            'reportData' => $this->reportDataProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
            'roleOptions' => $this->roleOptionsProperty,
        ])->title(__('Laporan Aktiviti Pengguna'));
    }
}
