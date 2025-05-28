<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\EmailApplication; // Example model
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EmailAccountsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = null;
    public ?string $filterServiceStatus = null;
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;
    public string $searchTerm = '';

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';

    public function mount()
    {
        // $this->authorize('viewEmailAccountsReport'); // Placeholder for authorization
    }

    public function getReportDataProperty()
    {
        // Replace with actual reporting logic, possibly from a ReportService
        $query = EmailApplication::with(['user.department', 'supportingOfficer'])
            ->select('email_applications.*'); // Ensure select for join if any

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('proposed_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('final_assigned_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterServiceStatus) {
            $query->whereHas('user', function ($q) {
                $q->where('service_status', $this->filterServiceStatus);
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatusOptionsProperty(): array
    {
        // Assuming EmailApplication model has a method or constant for status options
        return defined(EmailApplication::class . '::STATUS_OPTIONS') ? EmailApplication::$STATUS_OPTIONS : [];
    }

    public function getServiceStatusOptionsProperty(): array
    {
        return User::getServiceStatusOptions();
    }


    public function applyFilters(): void
    {
        $this->resetPage(); // Reset pagination when filters change
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
        return view('livewire.resource-management.admin.reports.email-accounts-report', [
            'reportData' => $this->reportDataProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'serviceStatusOptions' => $this->serviceStatusOptionsProperty,
        ])->title(__('Laporan Akaun E-mel'));
    }
}
