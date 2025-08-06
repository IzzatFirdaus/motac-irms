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

    // Properties for the User Dashboard view (only Loan applications remain)
    public int $pendingUserLoanApplicationsCount = 0;
    public EloquentCollection $userRecentLoanApplications;

    public function __construct()
    {
        // Initialize all collections to be safe
        $this->userRecentLoanApplications = new EloquentCollection();
    }

    /**
     * Mount the component and fetch data based on the user's role.
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
        $this->isNormalUser = $user->hasRole('User'); // Assuming 'User' is the role for normal users

        // Stat Card: Loan applications that are in process.
        $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
            ->whereIn('status', [
                LoanApplication::STATUS_DRAFT,
                LoanApplication::STATUS_PENDING_SUPPORT,
                LoanApplication::STATUS_PENDING_APPROVER_REVIEW, // Replaced STATUS_PENDING_ADMIN
                LoanApplication::STATUS_PENDING_BPM_REVIEW, // Replaced STATUS_PROCESSING if it implies review before approval
                LoanApplication::STATUS_APPROVED, // Include approved if still awaiting issuance
            ])
            ->count();

        // Table: The user's 5 most recently updated loan applications.
        $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
            ->with(['user:id,name'])
            ->latest('updated_at')
            ->limit(5)
            ->get();
    }

    /**
     * Render the correct dashboard view based on the user's role.
     */
    public function render(): View
    {
        if ($this->isNormalUser) {
            // Render the standard user dashboard
            return view('livewire.dashboard.user-dashboard');
        }

        // For Admins and other roles, render the main admin dashboard component
        return view('livewire.dashboard.admin-dashboard-wrapper');
    }
}
