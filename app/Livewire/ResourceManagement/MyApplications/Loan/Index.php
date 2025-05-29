<?php

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\LoanApplication;
use App\Models\User; // Assuming User model is used for Auth
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')] // Ensure your layout file is at resources/views/layouts/app.blade.php
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public string $filterStatus = ''; // Empty string means 'all'
    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
    }

    /**
     * Computed property for loan applications.
     * Accessed in Blade as $applications because it's passed as 'applications' from render().
     */
    public function getLoanApplicationsProperty()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            // Return an empty paginator if no user is authenticated
            return LoanApplication::where('id', -1)->paginate(10);
        }

        $query = LoanApplication::where('user_id', $user->id)
            // ->with(['applicationItems']) // Eager load items if needed for display in the list
            ->orderBy('created_at', 'desc');

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%');
                  // Add other searchable fields for loans if necessary
            });
        }

        // Ensure 'all' or empty string doesn't apply a status filter
        if (!empty($this->filterStatus) && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(10); // Adjust items per page as needed
    }

    /**
     * Computed property for status options.
     * Accessed in Blade as $statusOptions because it's passed as 'statusOptions' from render().
     */
    public function getStatusOptionsProperty(): array
    {
        // Ensure LoanApplication::getStatusOptions() exists and returns an array like:
        // ['draft' => 'Draf', 'pending_support' => 'Menunggu Sokongan', ...]
        // The '' key is for the "All Statuses" default option.
        return ['' => __('Semua Status')] + (LoanApplication::getStatusOptions() ?? []);
    }

    // Reset pagination when searchTerm changes
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    // Reset pagination when filterStatus changes
    public function updatedFilterStatus(): void // Corrected Livewire hook name from updatingFilterStatus
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.my-applications.loan.index', [
            'applications' => $this->loanApplications, // Accesses the getLoanApplicationsProperty()
            'statusOptions' => $this->statusOptions,   // Accesses the getStatusOptionsProperty()
        ])->title(__('Status Permohonan Pinjaman Saya'));
    }
}
