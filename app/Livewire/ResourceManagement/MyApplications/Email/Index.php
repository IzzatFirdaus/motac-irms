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
// Removed: use Livewire\Attributes\Title; // No longer using the attribute on the method
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for listing the authenticated user's Email/ID Applications.
 * Handles searching, filtering by status, and pagination.
 * System Design Ref: User Dashboard (6.2), Email Application Workflow & Data (4.2, 5.1)
 */
#[Layout('layouts.app')] // Specifies the Blade layout file to use
class Index extends Component
{
  use AuthorizesRequests;
  use WithPagination;

  public string $searchTerm = '';
  public string $filterStatus = ''; // Empty string or 'all' defaults to showing all statuses
  protected string $paginationTheme = 'bootstrap'; // Use Bootstrap styling for pagination links

  protected int $perPage = 10; // Configurable items per page for pagination

  /**
   * Authorize the user's ability to view any email applications and set the page title when the component mounts.
   * System Design Ref: Role-Based Access & Security (2.0)
   */
  public function mount(): void
  {
    $this->authorize('viewAny', EmailApplication::class);

    // Set the page title by dispatching an event
    $pageTitle = $this->generatePageTitle();
    $this->dispatch('update-page-title', title: $pageTitle);
  }

  /**
   * Helper method to generate the page title string.
   * Ensures the title includes the application's name and is translatable.
   */
  public function generatePageTitle(): string
  {
    $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
    return __('Senarai Permohonan E-mel/ID Saya') . ' - ' . $appName;
  }

  /**
   * Computed property to fetch and paginate the user's email applications based on filters.
   * Data is scoped to the authenticated user.
   * Includes search by ID, proposed email, group email, and application reason.
   * Filters by status if a specific status is selected.
   * Ordered by the most recently updated applications.
   */
  public function getEmailApplicationsProperty() // Livewire's convention for computed property $this->emailApplications
  {
    /** @var User|null $user */
    $user = Auth::user();

    if (!$user) {
      Log::warning('MyApplications\Email\Index: Unauthenticated access attempt.');
      // Return an empty paginator to prevent errors in the view
      return EmailApplication::whereRaw('1 = 0')->paginate($this->perPage);
    }

    $query = EmailApplication::query()
      ->where('user_id', $user->id)
      // Order by 'updated_at' to show most recently modified (including drafts) first
      ->orderBy('updated_at', 'desc');

    // Apply search term filter
    if (!empty($this->searchTerm)) {
      $search = '%' . $this->searchTerm . '%';
      $query->where(function ($q) use ($search) {
        $q->where('id', 'like', $search)
          ->orWhere('proposed_email', 'like', $search)
          ->orWhere('group_email', 'like', $search) // As per original component logic
          ->orWhere('application_reason_notes', 'like', $search);
      });
    }

    // Apply status filter (ensuring 'all' or empty string means no status filter)
    if (!empty($this->filterStatus) && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
      $query->where('status', $this->filterStatus);
    }

    return $query->paginate($this->perPage);
  }

  /**
   * Computed property to provide status options for the filter dropdown.
   * Fetches options from the EmailApplication model (e.g., a static method).
   * Prepends an "All Statuses" option.
   * System Design Ref: EmailApplication Model Statuses (4.2)
   */
  public function getStatusOptionsProperty(): array
  {
    // Assumes EmailApplication::getStatusOptions() returns an associative array
    // of [status_value => Translatable Status Label]
    $options = EmailApplication::getStatusOptions() ?? [];
    return ['' => __('Semua Status')] + $options; // '' value for "All Statuses"
  }

  /**
   * Lifecycle hook: Reset pagination page when $searchTerm is updated.
   */
  public function updatingSearchTerm(): void
  {
    $this->resetPage();
  }

  /**
   * Lifecycle hook: Reset pagination page when $filterStatus is updated.
   */
  public function updatedFilterStatus(): void // Livewire's convention for reacting to specific property updates
  {
    $this->resetPage();
  }

  /**
   * Action to reset all filters and search term.
   */
  public function resetFilters(): void
  {
    $this->reset(['searchTerm', 'filterStatus']);
    $this->resetPage(); // Also reset pagination
  }

  /**
   * Render the component's Blade view.
   * Passes the paginated applications and status options to the view.
   */
  public function render(): View
  {
    return view('livewire.resource-management.my-applications.email.index', [
      'applications' => $this->emailApplications, // Accesses getEmailApplicationsProperty()
      'statusOptions' => $this->statusOptions,   // Accesses getStatusOptionsProperty()
    ]);
  }
}
