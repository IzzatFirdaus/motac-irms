<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
  public string $displayUserName = '';
  public int $pendingUserLoanApplicationsCount = 0;
  public int $activeUserLoansCount = 0;
  public EloquentCollection $userRecentLoanApplications;
  public EloquentCollection $latestLoanTransactions;
  public EloquentCollection $upcomingReturns;
  public int $pendingUserEmailApplicationsCount = 0;
  public EloquentCollection $userRecentEmailApplications;
  public string $pageTitleValue = '';
  public array $quickActions;

  public function __construct()
  {
    $this->userRecentLoanApplications = new EloquentCollection();
    $this->userRecentEmailApplications = new EloquentCollection();
    $this->latestLoanTransactions = new EloquentCollection();
    $this->upcomingReturns = new EloquentCollection();
    $this->quickActions = [];
  }

  protected function setPageTitle(): void
  {
    $this->pageTitleValue = __('Dashboard Pengguna');
  }

  public function mount(): void
  {
    /** @var User|null $user */
    $user = Auth::user();

    if ($user) {
      $this->displayUserName = $user->name;

      $this->quickActions = [
        [
          'name' => 'Mohon Pinjaman ICT',
          // 'icon' => asset('assets/img/icons/quick-actions/loan-application.png'), // Old image path
          'icon' => 'bi bi-card-checklist text-primary', // MODIFIED: Bootstrap Icon class
          'route' => 'resource-management.my-loan-applications.create',
          'role' => ['User', 'Admin'],
        ],
        [
          'name' => 'Mohon Emel/ID',
          // 'icon' => asset('assets/img/icons/quick-actions/email-application.png'), // Old image path
          'icon' => 'bi bi-envelope-plus-fill text-info', // MODIFIED: Bootstrap Icon class
          'route' => 'resource-management.my-email-applications.create',
          'role' => ['User', 'Admin'],
        ],
        [
          'name' => 'Urus Pinjaman ICT',
          // 'icon' => asset('assets/img/icons/quick-actions/manage-loans.png'), // Old image path
          'icon' => 'bi bi-collection-fill text-success', // MODIFIED: Bootstrap Icon class
          'route' => 'loan-applications.index',
          'role' => ['User', 'Admin'],
        ],
        [
          'name' => 'Urus Emel/ID',
          // 'icon' => asset('assets/img/icons/quick-actions/manage-emails.png'), // Old image path
          'icon' => 'bi bi-envelope-open-fill text-warning', // MODIFIED: Bootstrap Icon class
          'route' => 'email-applications.index',
          'role' => ['User', 'Admin'],
        ],
        [
          'name' => 'Inventori Peralatan ICT',
          // 'icon' => asset('assets/img/icons/quick-actions/equipment-inventory.png'), // Old image path - Assuming this was manage-inventory.png
          'icon' => 'bi bi-archive-fill text-secondary', // MODIFIED: Bootstrap Icon class
          'route' => 'equipment.index',
          'role' => ['User', 'Admin'],
        ],
        [
          'name' => 'Laporan & Analisis',
          // 'icon' => asset('assets/img/icons/quick-actions/reports.png'), // Old image path
          'icon' => 'bi bi-file-earmark-bar-graph-fill text-danger', // MODIFIED: Bootstrap Icon class
          'route' => 'reports.index',
          'role' => ['Admin', 'BPM Staff'],
        ],
      ];

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
        ->with(['user:id,name', 'loanApplicationItems'])
        ->latest('updated_at')
        ->limit(5)
        ->get();

      $this->latestLoanTransactions = LoanTransaction::whereHas('loanApplication', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'loanTransactionItems.equipment:id,name,model',
            'loanApplication'
        ])
        ->latest()
        ->limit(5)
        ->get();

      $this->upcomingReturns = LoanApplication::where('user_id', $user->id)
        ->whereIn('status', [
            LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_PARTIALLY_ISSUED,
        ])
        ->with(['loanApplicationItems'])
        ->whereDate('loan_end_date', '>=', now())
        ->orderBy('loan_end_date', 'asc')
        ->limit(5)
        ->get();

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
      $this->userRecentLoanApplications = new EloquentCollection();
      $this->userRecentEmailApplications = new EloquentCollection();
      $this->latestLoanTransactions = new EloquentCollection();
      $this->upcomingReturns = new EloquentCollection();
      $this->quickActions = [];
    }
    $this->setPageTitle();
  }

  public function render(): View
  {
    return view('livewire.dashboard.dashboard');
  }
}
