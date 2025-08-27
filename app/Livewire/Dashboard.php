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
        $this->userRecentLoanApplications = new EloquentCollection;
    }

    /**
     * Mount and initialize dashboard data.
     */
    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard: User not authenticated during mount.');

            return;
        }

        $this->displayUserName = $user->name;
        // Only treat as normal user if the user has ONLY the 'User' role
        // Check roles for both guards
        $webUser            = \Auth::guard('web')->user();
        $sanctumUser        = \Auth::guard('sanctum')->user();
        $webRoles           = $webUser     && $webUser->roles ? $webUser->roles->pluck('name') : collect();
        $sanctumRoles       = $sanctumUser && $sanctumUser->roles ? $sanctumUser->roles->pluck('name') : collect();
        $allRoles           = $webRoles->merge($sanctumRoles)->unique();
        $this->isNormalUser = ($allRoles->count() === 1 && $allRoles->first() === 'User');

        if ($this->isNormalUser) {
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
    }

    /**
     * Render the dashboard view depending on role.
     */
    public function render(): View
    {
        if ($this->isNormalUser) {
            // Render the UserDashboard Livewire component for normal users
            return view('livewire.dashboard.user-dashboard-wrapper');
        }

        // Check roles for both guards
        $webUser      = \Auth::guard('web')->user();
        $sanctumUser  = \Auth::guard('sanctum')->user();
        $webRoles     = $webUser     && $webUser->roles ? $webUser->roles->pluck('name') : collect();
        $sanctumRoles = $sanctumUser && $sanctumUser->roles ? $sanctumUser->roles->pluck('name') : collect();
        $allRoles     = $webRoles->merge($sanctumRoles)->unique();
        if ($allRoles->contains('Admin')) {
            return view('livewire.dashboard.admin-dashboard-wrapper');
        }
        if ($allRoles->contains('BPM')) {
            return view('livewire.dashboard.bpm-dashboard-wrapper');
        }
        if ($allRoles->contains('IT Admin')) {
            return view('livewire.dashboard.it-admin-dashboard-wrapper');
        }
        if ($allRoles->contains('Approver')) {
            return view('livewire.dashboard.approver-dashboard-wrapper');
        }

        // Default fallback: admin dashboard
        return view('livewire.dashboard.admin-dashboard-wrapper');
    }
}
