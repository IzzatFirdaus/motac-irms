<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\MyApplications\Email;

use App\Models\EmailApplication;
use App\Models\User; // For type hinting Auth::user()
use Illuminate\Contracts\View\View; // Standard type hint for render method
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging potential issues
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for listing the authenticated user's Email/ID Applications.
 * Handles searching, filtering by status, and pagination.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public string $filterStatus = '';
    protected string $paginationTheme = 'bootstrap';
    protected int $perPage = 10;

    /**
     * Set the page title when the component mounts.
     * The incorrect authorization check has been removed from this method.
     */
    public function mount(): void
    {
        // REMOVED: The call to `$this->authorize('viewAny', EmailApplication::class);` was here.
        // It was incorrect because this page only shows the user's *own* applications.
        // The query is already scoped to the user, which is the correct way to handle authorization for this page.

        $pageTitle = $this->generatePageTitle();
        $this->dispatch('update-page-title', title: $pageTitle);
    }

    /**
     * Helper method to generate the page title string.
     */
    public function generatePageTitle(): string
    {
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
        return __('Senarai Permohonan E-mel/ID Saya') . ' - ' . $appName;
    }

    /**
     * Computed property to fetch the user's email applications.
     */
    public function getEmailApplicationsProperty()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            Log::warning('MyApplications\Email\Index: Unauthenticated access attempt.');
            return EmailApplication::whereRaw('1 = 0')->paginate($this->perPage);
        }

        $query = EmailApplication::query()
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc');

        if (!empty($this->searchTerm)) {
            $search = '%' . $this->searchTerm . '%';
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', $search)
                    ->orWhere('proposed_email', 'like', 'search')
                    ->orWhere('group_email', 'like', 'search')
                    ->orWhere('application_reason_notes', 'like', 'search');
            });
        }

        if (!empty($this->filterStatus) && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Computed property for status filter options.
     */
    public function getStatusOptionsProperty(): array
    {
        $options = EmailApplication::getStatusOptions() ?? [];
        return ['' => __('Semua Status')] + $options;
    }

    /**
     * Reset pagination when searching.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filtering.
     */
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Action to reset all filters.
     */
    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterStatus']);
        $this->resetPage();
    }

    /**
     * Render the component's view.
     */
    public function render(): View
    {
        return view('livewire.resource-management.my-applications.email.index', [
            'applications' => $this->emailApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
