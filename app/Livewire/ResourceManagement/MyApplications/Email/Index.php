<?php

namespace App\Livewire\ResourceManagement\MyApplications\Email;

use App\Models\EmailApplication;
use App\Models\User; // Added for type hinting Auth::user()
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
  public string $filterStatus = ''; // Empty string or 'all' can mean 'all statuses'
  protected string $paginationTheme = 'bootstrap';

  public function mount(): void
  {
    $this->authorize('viewAny', EmailApplication::class);
  }

  /**
   * Computed property for email applications.
   */
  public function getEmailApplicationsProperty()
  {
    /** @var User $user */
    $user = Auth::user();
    if (!$user) {
      // Return an empty paginator if no user is authenticated
      return EmailApplication::whereRaw('1 = 0')->paginate(10);
    }

    $query = EmailApplication::where('user_id', $user->id)
      ->orderBy('created_at', 'desc');

    if (!empty($this->searchTerm)) {
      $query->where(function ($q) {
        $q->where('id', 'like', '%' . $this->searchTerm . '%')
          ->orWhere('proposed_email', 'like', '%' . $this->searchTerm . '%')
          ->orWhere('group_email', 'like', '%' . $this->searchTerm . '%') // Included based on provided code
          ->orWhere('application_reason_notes', 'like', '%' . $this->searchTerm . '%');
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
   * Assumes EmailApplication model has a public static property $STATUSES_LABELS
   * or a static method getStatusOptions().
   */
  public function getStatusOptionsProperty(): array
  {
    // Example: Using a static property on the model
    // return ['' => __('Semua Status')] + (EmailApplication::$STATUSES_LABELS ?? []);

    // Or, if you have a static method (recommended for consistency with Loan component):
    return ['' => __('Semua Status')] + (EmailApplication::getStatusOptions() ?? []);
  }

  /**
   * Reset pagination when searchTerm is updated.
   */
  public function updatingSearchTerm(): void
  {
    $this->resetPage();
  }

  /**
   * Reset pagination when filterStatus is updated.
   */
  public function updatedFilterStatus(): void
  {
    $this->resetPage();
  }

  /**
   * Render the component.
   */
  public function render(): View
  {
    // The title is typically set in the Blade view using @section('title', ...)
    // which is preferred if your layout supports it.
    return view('livewire.resource-management.my-applications.email.index', [
      'applications' => $this->emailApplications, // Accesses getEmailApplicationsProperty()
      'statusOptions' => $this->statusOptions,   // Accesses getStatusOptionsProperty()
    ]);
  }
}
