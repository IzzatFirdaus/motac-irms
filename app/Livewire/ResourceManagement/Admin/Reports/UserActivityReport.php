<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * UserActivityReport Livewire component.
 * Generates a report of user activities, with filtering by name, department, and role.
 */
#[Layout('layouts.app')]
#[Title('Laporan Aktiviti Pengguna')]
class UserActivityReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public string $searchTerm = ''; // Search by user name/email
    public ?int $filterDepartmentId = null;
    public ?string $filterRoleName = null;

    // Sorting properties
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';
    public int $perPage = 15;

    /**
     * Mount the component. You can add authorization here if needed.
     */
    public function mount(): void
    {
        // Optionally add authorization logic here
    }

    /**
     * Computed property: Get paginated, filtered, sorted user activity data.
     */
    public function getReportDataProperty()
    {
        $query = User::withCount([
                // 'emailApplications', // Uncomment if relationship exists
                'loanApplicationsAsApplicant',
                'approvalsMade',
            ])
            ->with(['department', 'roles'])
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->when($this->filterDepartmentId, function ($q) {
                $q->where('department_id', $this->filterDepartmentId);
            })
            ->when($this->filterRoleName, function ($q) {
                $q->whereHas('roles', function ($subQuery) {
                    $subQuery->where('name', $this->filterRoleName);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Options for department filter dropdown.
     */
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    /**
     * Options for role filter dropdown.
     */
    public function getRoleOptionsProperty(): \Illuminate\Support\Collection
    {
        return Role::orderBy('name')->pluck('name', 'name');
    }

    /**
     * Reset filters to default values.
     */
    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterDepartmentId', 'filterRoleName']);
        $this->resetPage();
    }

    /**
     * Apply filters (for explicit button-based filtering).
     */
    public function applyFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Set sorting column and direction.
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
     * Render the User Activity Report view.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.reports.user-activity-report', [
            'reportData' => $this->reportData,
            'departmentOptions' => $this->departmentOptions,
            'roleOptions' => $this->roleOptions,
        ]);
    }
}
