<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\EmailApplication;
use App\Models\User; // For service status options
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class EmailAccountsReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterStatus = ''; // Use empty string for "All"
    public ?string $filterServiceStatus = ''; // Use empty string for "All"
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;
    public string $searchTerm = '';

    // Sorting properties
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected string $paginationTheme = 'bootstrap';
    public int $perPage = 15;

    public function mount()
    {
        // Example authorization, ensure policy exists
        // $this->authorize('viewAny', EmailApplication::class); // Or a specific report permission
        Log::info("Livewire\EmailAccountsReport: Generating Email Accounts Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function getReportDataProperty()
    {
        // Revision 3 - EmailApplication fields: user_id, proposed_email, final_assigned_email, status, created_at
        // User fields: name, service_status
        $query = EmailApplication::with(['user:id,name,service_status,department_id', 'user.department:id,name', 'supportingOfficer:id,name'])
            ->select('email_applications.*');

        if (!empty($this->searchTerm)) {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(proposed_email) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(final_assigned_email) LIKE ?', [$search])
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->whereRaw('LOWER(name) LIKE ?', [$search]);
                  });
            });
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->filterServiceStatus)) {
            $query->whereHas('user', function ($q) {
                $q->where('service_status', $this->filterServiceStatus);
            });
        }

        if (!empty($this->filterDateFrom)) {
            $query->whereDate('email_applications.created_at', '>=', $this->filterDateFrom);
        }
        if (!empty($this->filterDateTo)) {
            $query->whereDate('email_applications.created_at', '<=', $this->filterDateTo);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info("Livewire\EmailAccountsReport: Fetched {$reportData->total()} email applications.", ['admin_user_id' => Auth::id()]);
        return $reportData;
    }

    public function getStatusOptionsProperty(): array
    {
        // Ensure EmailApplication model has this static property/method as per "Revision 3" (4.2)
        return EmailApplication::$STATUS_OPTIONS ?? (defined(EmailApplication::class . '::STATUS_DRAFT') ? EmailApplication::getStatusOptions() : []);
    }

    public function getServiceStatusOptionsProperty(): array
    {
        // Ensure User model has this static method as per "Revision 3" (4.1)
        return User::getServiceStatusOptions();
    }

    public function applyFilters(): void // This can be triggered by a button if not using wire:model.live
    {
        $this->resetPage(); // Reset pagination when filters change
    }

    public function updating($property): void
    {
        if (in_array($property, ['filterStatus', 'filterServiceStatus', 'filterDateFrom', 'filterDateTo', 'searchTerm'])) {
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
        $this->reset(['filterStatus', 'filterServiceStatus', 'filterDateFrom', 'filterDateTo', 'searchTerm', 'sortBy', 'sortDirection']);
        $this->sortBy = 'created_at'; // Default sort
        $this->sortDirection = 'desc'; // Default direction
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
