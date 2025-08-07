<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Dashboard Livewire Component
 *
 * Shows the user/admin dashboard with summary and recent activity.
 */
#[Title('Papan Pemuka')]
class Dashboard extends Component
{
    public string $displayUserName = '';
    public bool $isNormalUser = false;

    // For user dashboard
    public int $pendingUserLoanApplicationsCount = 0;
    public EloquentCollection $userRecentLoanApplications;

    public function __construct()
    {
        $this->userRecentLoanApplications = new EloquentCollection();
    }

    /**
     * Mount and initialize dashboard data.
     */
    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard: User not authenticated during mount.');
            return;
        }

        $this->displayUserName = $user->name;
        $this->isNormalUser = $user->hasRole('User');

        // Stat Card: Pending loan applications
        $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
            ->whereIn('status', [
                LoanApplication::STATUS_DRAFT,
                LoanApplication::STATUS_PENDING_SUPPORT,
                LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
                LoanApplication::STATUS_PENDING_BPM_REVIEW,
                LoanApplication::STATUS_APPROVED,
            ])
            ->count();

        // Table: Recent loan applications
        $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
            ->with(['user:id,name'])
            ->latest('updated_at')
            ->limit(5)
            ->get();
    }

    /**
     * Render the dashboard view depending on role.
     */
    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
