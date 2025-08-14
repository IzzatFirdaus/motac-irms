<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OutstandingLoans extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $sortBy = 'updated_at'; // Default sort

    public string $sortDirection = 'desc'; // Default direction

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
    }

    // Toggles sort direction or changes sort column
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = in_array($field, ['updated_at', 'loan_end_date']) ? 'desc' : 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    // Computed property to get the applications
    public function getOutstandingApplicationsProperty()
    {
        $this->authorize('viewAny', LoanApplication::class);

        $query = LoanApplication::query()
            ->with(['user:id,name', 'loanApplicationItems'])
            ->where('status', LoanApplication::STATUS_APPROVED); // Fetches applications awaiting issuance

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $searchTerm = '%'.$this->searchTerm.'%';
            $query->where(function ($subQuery) use ($searchTerm): void {
                $subQuery->where('id', 'like', $searchTerm)
                    ->orWhere('purpose', 'like', $searchTerm)
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm): void {
                        $userQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        $validSorts = ['id', 'purpose', 'loan_end_date', 'updated_at'];
        if (in_array($this->sortBy, $validSorts)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate(10);
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.outstanding-loans', [
            'applications' => $this->outstandingApplications,
        ])->title(__('Permohonan Pinjaman Untuk Diproses (Tindakan BPM)'));
    }
}
