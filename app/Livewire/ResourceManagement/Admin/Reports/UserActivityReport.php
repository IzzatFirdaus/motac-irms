<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department; // Example model
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; //
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role; // Make sure this is imported

#[Layout('layouts.app')]
#[Title('Laporan Aktiviti Pengguna')] //
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

    public function mount(): void
    {
        // Ensure correct authorization. Example:
        // abort_unless(auth()->user()->can('view_user_activity_reports'), 403, 'Anda tidak mempunyai kebenaran untuk melihat laporan ini.');
        // Or using a policy:
        // $this->authorize('viewUserActivityReport', User::class); // Assuming a general policy or use a specific ReportPolicy
    }

    public function getReportDataProperty()
    {
        $query = User::withCount([
            // 'emailApplications', // Removed as per v4.0 refactoring
            'loanApplicationsAsApplicant', // Corrected to match User model's likely relationship name for loans initiated by user
            'approvalsMade', // Corrected to match User model's likely relationship name for approvals made
        ])
            ->with(['department', 'roles']) // Eager load relationships for display
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

        return $query->paginate($this->perPage); // Use the preferred pagination number
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
        ]); //
    }
}
