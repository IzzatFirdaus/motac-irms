<?php

namespace App\Livewire\ResourceManagement\Reports;

use App\Models\LoanApplication;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: LoanApplicationsReport
 * Displays a paginated, filterable report of all loan applications in the system.
 */
#[Layout('layouts.app')]
class LoanApplicationsReport extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterDepartment = '';

    public int $perPage = 15;

    protected string $paginationTheme = 'bootstrap';

    /**
     * Get the list of loan applications for the report, with applied filters.
     */
    public function getLoanApplicationsProperty()
    {
        $query = LoanApplication::query()
            ->with(['user', 'user.department'])
            ->orderByDesc('created_at');

        if ($this->search !== '' && $this->search !== '0') {
            $query->where(function ($q): void {
                $q->where('application_number', 'like', '%'.$this->search.'%')
                    ->orWhere('purpose', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterStatus !== '' && $this->filterStatus !== '0') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDepartment !== '' && $this->filterDepartment !== '0') {
            $query->whereHas('user.department', function ($q): void {
                $q->where('id', $this->filterDepartment);
            });
        }

        return $query->paginate($this->perPage);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $departments   = \App\Models\Department::orderBy('name')->get();
        $statusOptions = [
            ''                                                            => __('All'),
            LoanApplication::STATUS_DRAFT                                 => __('Draf'),
            LoanApplication::STATUS_PENDING_SUPPORT                       => __('Menunggu Sokongan Pegawai'),
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW               => __('Menunggu Kelulusan'),
            LoanApplication::STATUS_PENDING_BPM_REVIEW                    => __('Menunggu Pengesahan BPM'),
            LoanApplication::STATUS_APPROVED                              => __('Diluluskan'),
            LoanApplication::STATUS_REJECTED                              => __('Ditolak'),
            LoanApplication::STATUS_PARTIALLY_ISSUED                      => __('Dikeluarkan Sebahagian'),
            LoanApplication::STATUS_ISSUED                                => __('Dikeluarkan'),
            LoanApplication::STATUS_RETURNED                              => __('Telah Dipulangkan'),
            LoanApplication::STATUS_OVERDUE                               => __('Tertunggak'),
            LoanApplication::STATUS_CANCELLED                             => __('Dibatalkan'),
            LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => __('Dipulangkan Sebahagian'),
            LoanApplication::STATUS_COMPLETED                             => __('Selesai'),
        ];

        return view('livewire.resource-management.reports.loan-applications-report', [
            'loanApplications' => $this->getLoanApplicationsProperty(),
            'departments'      => $departments,
            'statusOptions'    => $statusOptions,
        ]);
    }
}
