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
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Papan Pemuka')]
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

    public array $quickActions;

    public function __construct()
    {
        $this->userRecentLoanApplications = new EloquentCollection;
        $this->userRecentEmailApplications = new EloquentCollection;
        $this->latestLoanTransactions = new EloquentCollection;
        $this->upcomingReturns = new EloquentCollection;
        $this->quickActions = [];
    }

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->displayUserName = $user->name;

            // REVERTED: This array now uses the original 'role' key for visibility control.
            $this->quickActions = [
                [
                    'name' => 'menu.apply_for_resources.loan',
                    'icon' => 'bi bi-card-checklist text-primary',
                    'route' => 'loan-applications.create',
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
                ],
                [
                    'name' => 'menu.apply_for_resources.email',
                    'icon' => 'bi bi-envelope-plus-fill text-info',
                    'route' => 'email-applications.create',
                    'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
                ],
                [
                    'name' => 'menu.approvals_dashboard',
                    'icon' => 'bi bi-person-check-fill text-success',
                    'route' => 'approvals.dashboard',
                    'role' => ['Admin', 'Approver', 'BPM Staff', 'IT Admin', 'HOD'],
                ],
                [
                    'name' => 'menu.administration.equipment_management',
                    'icon' => 'bi bi-hdd-stack-fill text-danger',
                    'route' => 'resource-management.equipment-admin.index',
                    'role' => ['Admin', 'BPM Staff'],
                ],
                [
                    'name' => 'menu.administration.email_applications',
                    'icon' => 'bi bi-envelope-gear-fill text-warning',
                    'route' => 'resource-management.email-applications-admin.index',
                    'role' => ['Admin', 'IT Admin'],
                ],
                [
                    'name' => 'menu.reports.title',
                    'icon' => 'bi bi-file-earmark-bar-graph-fill text-secondary',
                    'route' => 'reports.index',
                    'role' => ['Admin', 'BPM Staff'],
                ],
            ];

            // Data fetching logic remains the same
            $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_DRAFT,
                    LoanApplication::STATUS_PENDING_SUPPORT,
                    LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
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

            $this->latestLoanTransactions = LoanTransaction::whereHas('loanApplication', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with(['loanApplication:id,purpose'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            $this->upcomingReturns = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                ])
                ->whereDate('loan_end_date', '>=', now())
                ->orderBy('loan_end_date', 'asc')
                ->limit(5)
                ->get();

            $this->pendingUserEmailApplicationsCount = EmailApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    EmailApplication::STATUS_DRAFT,
                    EmailApplication::STATUS_PENDING_SUPPORT,
                    EmailApplication::STATUS_PENDING_ADMIN,
                    EmailApplication::STATUS_PROCESSING,
                ])->count();

            $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
                ->latest('updated_at')
                ->limit(5)
                ->get();
        } else {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard (User): User not authenticated during mount. Dashboard data will be limited.');
        }
    }

    public function render(): View
    {
        return view('livewire.dashboard.dashboard');
    }
}
