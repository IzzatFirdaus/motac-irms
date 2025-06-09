<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department; // Example model
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role; // Make sure this is imported

#[Layout('layouts.app')]
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

    public function mount()
    {
        // Ensure correct authorization. Example:
        // abort_unless(auth()->user()->can('view_user_activity_reports'), 403, 'Anda tidak mempunyai kebenaran untuk melihat laporan ini.');
        // Or using a policy:
        // $this->authorize('viewUserActivityReport', User::class); // Assuming a general policy or use a specific ReportPolicy
    }

    public function getReportDataProperty()
    {
        $query = User::withCount([
            'emailApplications',
            'loanApplicationsAsApplicant', // Corrected to match User model's likely relationship name for loans initiated by user
            'approvalsMade', // Corrected to match User model's likely relationship name for approvals made by user
        ])->with(['department', 'roles']); // Eager load department and roles

        if (! empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            });
        }
        if ($this->filterDepartmentId) {
            $query->where('department_id', $this->filterDepartmentId);
        }
        if ($this->filterRoleName) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filterRoleName);
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15); // Or your preferred pagination number
    }

    // Options for filters
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    public function getRoleOptionsProperty(): \Illuminate\Support\Collection
    {
        return Role::orderBy('name')->pluck('name', 'name'); // Using name as value for easy filtering in whereHas
    }

    public function applyFilters(): void
    {
        $this->resetPage(); // Reset pagination when filters are applied
    }

    public function resetFilters(): void // Added method to reset filters
    {
        $this->reset(['searchTerm', 'filterDepartmentId', 'filterRoleName']);
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
        $this->resetPage(); // Reset pagination when sorting changes
    }

    public function render()
    {
        // Correctly access computed properties
        return view('livewire.resource-management.admin.reports.user-activity-report', [
            'reportData' => $this->reportData,
            'departmentOptions' => $this->departmentOptions,
            'roleOptions' => $this->roleOptions,
        ])->title(__('Laporan Aktiviti Pengguna'));
    }
}
