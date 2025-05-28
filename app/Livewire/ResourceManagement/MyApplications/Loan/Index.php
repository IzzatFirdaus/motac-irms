<?php

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public string $filterStatus = ''; // Empty string means 'all'
    protected string $paginationTheme = 'bootstrap'; // Changed to Bootstrap

    public function mount(): void
    {
        // Authorize that the user can view *any* of their own applications.
        // The query itself will scope to the user's own applications.
        $this->authorize('viewAny', LoanApplication::class);
    }

    public function getLoanApplicationsProperty()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return LoanApplication::where('id', -1)->paginate(10); // Return empty paginator
        }

        $query = LoanApplication::where('user_id', $user->id)
            ->with(['applicationItems']) // Eager load items for display
            ->orderBy('created_at', 'desc');

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%');
            });
        }
        if (!empty($this->filterStatus) && $this->filterStatus !== 'all') { // Check for 'all'
            $query->where('status', $this->filterStatus);
        }
        return $query->paginate(10);
    }

    public function getStatusOptionsProperty(): array
    {
        // Assumes LoanApplication model has a static method like getAllStatusesWithLabels()
        // or a static array $STATUSES_LABELS similar to EmailApplication model.
        return ['' => __('Semua Status')] + (LoanApplication::getStatusOptions() ?? []);
    }

    // Reset pagination when filters change
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void // Corrected Livewire hook name
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.my-applications.loan.index', [
            'applications' => $this->loanApplications, // Access computed property
            'statusOptions' => $this->statusOptions,   // Access computed property
        ])->title(__('Status Permohonan Pinjaman Saya'));
    }
}
