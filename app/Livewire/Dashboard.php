<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
// Removed: use Livewire\Attributes\Title; // No longer using the #[Title] attribute
use Livewire\Component;

/**
 * Livewire Component for the Main User Dashboard.
 * Provides data such as user details, application summaries, and recent activities.
 * System Design Reference: 6.2 (User Dashboard)
 */
class Dashboard extends Component
{
  // --- Component Properties ---
  public string $displayUserName = '';

  // ICT Loan related properties
  public int $pendingUserLoanApplicationsCount = 0;
  public int $activeUserLoansCount = 0;
  public EloquentCollection $userRecentLoanApplications;

  // Email/ID Application related properties
  public int $pendingUserEmailApplicationsCount = 0;
  public EloquentCollection $userRecentEmailApplications;

  // Public property to hold the page title value
  public string $pageTitleValue = '';

  /**
   * Initialize Eloquent collections to prevent type errors if queries return null
   * or if the user is not authenticated.
   */
  public function __construct()
  {
    $this->userRecentLoanApplications = new EloquentCollection();
    $this->userRecentEmailApplications = new EloquentCollection();
  }

  /**
   * Helper method to set the page title value.
   * Ensures the title includes the application's name and is translatable.
   */
  private function setPageTitle(): void
  {
    // Fetches the application name from config, with a MOTAC-specific fallback.
    $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
    $this->pageTitleValue = __('Papan Pemuka Utama') . ' - ' . $appName;
  }

  /**
   * Mount component: Fetches initial data required for the dashboard
   * for the currently authenticated user.
   */
  public function mount(): void
  {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    if ($user) {
      $this->displayUserName = $user->name ?? __('Pengguna Tidak Dikenali');

      // --- Fetch ICT Loan Data for the User ---
      $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
        ->whereIn('status', [
          LoanApplication::STATUS_DRAFT,
          LoanApplication::STATUS_PENDING_SUPPORT,
          LoanApplication::STATUS_PENDING_HOD_REVIEW,
          LoanApplication::STATUS_PENDING_BPM_REVIEW,
          LoanApplication::STATUS_APPROVED,
        ])->count();

      $this->activeUserLoansCount = LoanApplication::where('user_id', $user->id)
        ->whereIn('status', [
          LoanApplication::STATUS_ISSUED,
          LoanApplication::STATUS_PARTIALLY_ISSUED,
          LoanApplication::STATUS_OVERDUE,
        ])->count();

      $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
        ->with(['user:id,name'])
        ->latest('updated_at')
        ->limit(5)
        ->get();

      // --- Fetch Email/ID Application Data for the User ---
      $this->pendingUserEmailApplicationsCount = EmailApplication::where('user_id', $user->id)
        ->whereIn('status', [
          EmailApplication::STATUS_DRAFT,
          EmailApplication::STATUS_PENDING_SUPPORT,
          EmailApplication::STATUS_PENDING_ADMIN,
          EmailApplication::STATUS_APPROVED,
          EmailApplication::STATUS_PROCESSING,
        ])->count();

      $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
        ->latest('updated_at')
        ->limit(5)
        ->get();
    } else {
      $this->displayUserName = __('Pengguna Tetamu');
      Log::warning('MOTAC Dashboard (User): User not authenticated during mount. Dashboard data will be limited.');
      $this->pendingUserLoanApplicationsCount = 0;
      $this->activeUserLoansCount = 0;
      $this->pendingUserEmailApplicationsCount = 0;
    }

    $this->setPageTitle(); // Set the page title value
  }

  /**
   * Render the component's Blade view.
   */
  public function render(): View
  {
    // Corrected view path to reflect the subdirectory:
    // resources/views/livewire/dashboard/dashboard.blade.php
    return view('livewire.dashboard.dashboard');
  }
}
