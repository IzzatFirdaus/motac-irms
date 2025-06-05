<?php

declare(strict_types=1);

namespace App\Livewire; // Assuming this is the correct namespace

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Correct import for Eloquent Collection
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

    // Initialize collections in constructor or directly with type hints if PHP 7.4+
    public function __construct()
    {
        $this->userRecentLoanApplications = new EloquentCollection();
        $this->userRecentEmailApplications = new EloquentCollection();
        $this->latestLoanTransactions = new EloquentCollection();
        $this->upcomingReturns = new EloquentCollection();
        $this->quickActions = []; // Initialize quick actions array
    }

    protected function setPageTitle(): void
    {
        $this->pageTitleValue = __('Dashboard Pengguna');
        // If you dispatch browser events for page title, do it here or in render
        // $this->dispatch('update-page-title', title: $this->pageTitleValue);
    }

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->displayUserName = $user->name;

            // Define quick actions - ensure route names are correct as per your web.php
            $this->quickActions = [
                [
                    'name' => 'Mohon Pinjaman ICT',
                    'icon' => 'bi bi-card-checklist text-primary',
                    'route' => 'resource-management.my-loan-applications.create',
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'], // More inclusive
                ],
                [
                    'name' => 'Mohon Emel/ID',
                    'icon' => 'bi bi-envelope-plus-fill text-info',
                    'route' => 'resource-management.my-email-applications.create',
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'], // More inclusive
                ],
                [
                    'name' => 'Permohonan Pinjaman Saya',
                    'icon' => 'bi bi-collection-fill text-success',
                    'route' => 'loan-applications.index', // Lists user's loan applications
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
                ],
                [
                    'name' => 'Permohonan Emel/ID Saya',
                    'icon' => 'bi bi-envelope-open-fill text-warning',
                    'route' => 'email-applications.index', // Lists user's email applications
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
                ],
                [
                    'name' => 'Inventori Peralatan ICT',
                    'icon' => 'bi bi-archive-fill text-secondary',
                    'route' => 'equipment.index', // Public listing of equipment
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
                ],
                [
                    'name' => 'Laporan & Analisis',
                    'icon' => 'bi bi-file-earmark-bar-graph-fill text-danger',
                    'route' => 'reports.index',
                    'role' => ['Admin', 'BPM Staff'], // Restricted roles
                ],
            ];

            $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_DRAFT,
                    LoanApplication::STATUS_PENDING_SUPPORT,
                    LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
                    LoanApplication::STATUS_PENDING_BPM_REVIEW,
                    LoanApplication::STATUS_APPROVED, // User might need to acknowledge or prepare for collection
                ])->count();

            $this->activeUserLoansCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                    LoanApplication::STATUS_OVERDUE,
                ])->count();

            $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
                ->with(['user:id,name', 'loanApplicationItems.equipment']) // Load equipment through items for details if needed
                ->latest('updated_at')
                ->limit(5)
                ->get();

            $this->latestLoanTransactions = LoanTransaction::whereHas('loanApplication', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                // CORRECTED: Select actual columns from equipment table
                'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model',
                'loanApplication:id,purpose'
            ])
            ->latest('created_at') // Usually transactions are ordered by creation or transaction date
            ->limit(5)
            ->get();

            $this->upcomingReturns = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                ])
                ->with(['loanApplicationItems.equipment']) // Eager load equipment for upcoming returns
                ->whereDate('loan_end_date', '>=', now())
                ->orderBy('loan_end_date', 'asc')
                ->limit(5)
                ->get();

            $this->pendingUserEmailApplicationsCount = EmailApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    EmailApplication::STATUS_DRAFT,
                    EmailApplication::STATUS_PENDING_SUPPORT,
                    EmailApplication::STATUS_PENDING_ADMIN,
                    // EmailApplication::STATUS_APPROVED, // Approved might mean action is on Admin
                    EmailApplication::STATUS_PROCESSING,
                ])->count();

            $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
                ->latest('updated_at')
                ->limit(5)
                ->get();
        } else {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard (User): User not authenticated during mount. Dashboard data will be limited.');
            // Initialize properties to default empty states
            $this->pendingUserLoanApplicationsCount = 0;
            $this->activeUserLoansCount = 0;
            $this->pendingUserEmailApplicationsCount = 0;
            // Ensure these are always EloquentCollections
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
        // Ensure the view path 'livewire.dashboard.dashboard' matches your file structure.
        // If your component is App\Livewire\Dashboard, a common view path would be 'livewire.dashboard'.
        return view('livewire.dashboard.dashboard'); // Assuming view is resources/views/livewire/dashboard.blade.php
    }
}
