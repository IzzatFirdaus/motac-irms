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

    protected string $paginationTheme = 'bootstrap';

    // Added properties to control sorting
    public string $sortBy = 'updated_at';

    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
    }

    // Method to handle changing the sort column and direction
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Default to ascending when changing columns, or descending for dates
            $this->sortDirection = in_array($field, ['updated_at', 'loan_end_date']) ? 'desc' : 'asc';
        }
        $this->sortBy = $field;
        $this->resetPage();
    }

    public function getOutstandingApplicationsProperty()
    {
        $this->authorize('viewAny', LoanApplication::class);

        $query = LoanApplication::query()
            ->with([
                'user:id,name',
                'loanApplicationItems',
            ])
            ->where('status', LoanApplication::STATUS_APPROVED);

        if (! empty($this->searchTerm)) {
            $searchTerm = '%'.$this->searchTerm.'%';
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('id', 'like', $searchTerm)
                    ->orWhere('purpose', 'like', $searchTerm)
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        // The query now uses the dynamic sorting properties
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
