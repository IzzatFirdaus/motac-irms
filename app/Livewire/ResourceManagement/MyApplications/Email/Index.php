<?php

namespace App\Livewire\ResourceManagement\MyApplications\Email;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Removed: use Livewire\Attributes\Computed; // Not strictly necessary if using public property access for computed
// use Illuminate\Database\Eloquent\Collection; // Not used directly

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public string $filterStatus = ''; // Empty string means 'all' or default filter
    protected string $paginationTheme = 'bootstrap'; // Changed to Bootstrap

    public function mount(): void
    {
        // Authorize that the user can view *any* of their own applications.
        // The query itself will scope to the user's own applications.
        $this->authorize('viewAny', EmailApplication::class);
    }

    // Using Livewire's automatic computed property hydration by making it a public property.
    // If you prefer explicit #[Computed] for caching or other reasons, that's also fine.
    public function getEmailApplicationsProperty()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            // Return an empty paginator if no user is authenticated
            return EmailApplication::where('id', -1)->paginate(10); // Query that returns no results
        }

        $query = EmailApplication::where('user_id', $user->id)
          ->orderBy('created_at', 'desc');

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('proposed_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%'); // 'purpose' was application_reason_notes in model
            });
        }

        if (!empty($this->filterStatus) && $this->filterStatus !== 'all') { // Add 'all' check
            $query->where('status', $this->filterStatus);
        }
        return $query->paginate(10);
    }

    public function getStatusOptionsProperty(): array
    {
        // Ensure EmailApplication::$STATUSES_LIST exists and provides label => key, or adjust as needed
        // Assuming EmailApplication::$STATUSES_LABELS (as in your model from turn_id: 36)
        return ['' => __('Semua Status')] + (EmailApplication::$STATUSES_LABELS ?? []);
    }

    // Reset pagination when filters change
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void // Corrected hook name
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.my-applications.email.index', [
          'applications' => $this->emailApplications, // Access the computed property
          'statusOptions' => $this->statusOptions,   // Access the computed property
        ])->title(__('Status Permohonan Emel/ID Saya'));
    }
}
