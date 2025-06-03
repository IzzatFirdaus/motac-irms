<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\LoanApplication;
use App\Models\User; // For type hinting Auth::user()
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
// Removed: use Livewire\Attributes\Title as LivewireTitle; // No longer using the attribute on the method
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for listing the authenticated user's ICT Loan Applications.
 * Handles searching, filtering by status, and pagination.
 * System Design Ref: User Dashboard (6.2), ICT Equipment Loan Workflow & Data (4.3, 5.2)
 */
#[Layout('layouts.app')] // Specifies the Blade layout file
class Index extends Component
{
  use AuthorizesRequests;
  use WithPagination;

  public string $searchTerm = '';
  public string $filterStatus = ''; // Empty string or 'all' defaults to showing all statuses
  protected string $paginationTheme = 'bootstrap'; // Use Bootstrap styling for pagination

  protected int $perPage = 10; // Configurable items per page for pagination

  /**
   * Authorize the user and set the page title when the component mounts.
   * System Design Ref: Role-Based Access & Security (2.0)
   */
  public function mount(): void
  {
    $this->authorize('viewAny', LoanApplication::class);

    // Set the page title by dispatching an event
    $pageTitle = $this->generatePageTitle();
    $this->dispatch('update-page-title', title: $pageTitle);
  }

  /**
   * Helper method to generate the page title string.
   */
  public function generatePageTitle(): string
  {
    $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
    return __('Senarai Permohonan Pinjaman ICT Saya') . ' - ' . $appName;
  }

  /**
   * Computed property to fetch and paginate the user's loan applications.
   * Accessed in Blade as $this->loanApplications.
   */
  public function getLoanApplicationsProperty()
  {
    /** @var User|null $user */
    $user = Auth::user();

    if (!$user) {
      Log::warning('MyApplications\Loan\Index: Unauthenticated access attempt.');
      return LoanApplication::whereRaw('1 = 0')->paginate($this->perPage); // Return empty paginator
    }

    $query = LoanApplication::where('user_id', $user->id)
      ->orderBy('updated_at', 'desc');

    if (!empty($this->searchTerm)) {
      $search = '%' . $this->searchTerm . '%';
      $query->where(function ($q) use ($search) {
        $q->where('id', 'like', $search)
          ->orWhere('purpose', 'like', 'search');
      });
    }

    if (!empty($this->filterStatus) && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
      $query->where('status', $this->filterStatus);
    }

    return $query->paginate($this->perPage);
  }

  /**
   * Computed property to provide status options for the filter dropdown.
   */
  public function getStatusOptionsProperty(): array
  {
    $options = LoanApplication::getStatusOptions() ?? [];
    return ['' => __('Semua Status')] + $options;
  }

  public function updatingSearchTerm(): void
  {
    $this->resetPage();
  }

  public function updatedFilterStatus(): void
  {
    $this->resetPage();
  }

  public function resetFilters(): void
  {
    $this->reset(['searchTerm', 'filterStatus']);
    $this->resetPage();
  }

  public function render(): View
  {
    return view('livewire.resource-management.my-applications.loan.index', [
      'applications' => $this->loanApplications,
      'statusOptions' => $this->statusOptions,
    ]);
  }
}
