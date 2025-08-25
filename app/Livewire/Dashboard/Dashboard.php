<?php

namespace App\Livewire\Dashboard;

use App\Models\LoanApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Dashboard Livewire Component
 *
 * Determines user type and displays the appropriate dashboard view.
 * For normal users, shows loan stats and quick actions.
 * For privileged users, delegates to specialized dashboards (see dashboard-wrapper).
 */
class Dashboard extends Component
{
    public bool $isNormalUser = false;
    public string $displayUserName = '';
    public int $pending_loans_count = 0;
    public int $approved_loans_count = 0;
    public int $rejected_loans_count = 0;
    public int $total_loans_count = 0;

    public $recent_applications;

    public function mount()
    {
        $user = Auth::user();
        $this->displayUserName = $user->name ?? '';

        // Determine if user is a "normal user" (i.e., not admin, BPM, IT, etc.)
        $this->isNormalUser = !$user->hasAnyRole(['Admin', 'IT Admin', 'BPM Staff', 'Approver']);

        if ($this->isNormalUser) {
            $this->pending_loans_count = LoanApplication::where('user_id', $user->id)
                ->where('status', LoanApplication::STATUS_PENDING_SUPPORT)->count();
            $this->approved_loans_count = LoanApplication::where('user_id', $user->id)
                ->where('status', LoanApplication::STATUS_APPROVED)->count();
            $this->rejected_loans_count = LoanApplication::where('user_id', $user->id)
                ->where('status', LoanApplication::STATUS_REJECTED)->count();
            $this->total_loans_count = LoanApplication::where('user_id', $user->id)->count();

            // Show the 5 most recent applications
            $this->recent_applications = LoanApplication::with('equipment')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }
    }

    public function render()
    {
        // Normal users get their dashboard, others handled by dashboard-wrapper
        return view('livewire.dashboard.dashboard', [
            'isNormalUser' => $this->isNormalUser,
            'displayUserName' => $this->displayUserName,
            'pending_loans_count' => $this->pending_loans_count,
            'approved_loans_count' => $this->approved_loans_count,
            'rejected_loans_count' => $this->rejected_loans_count,
            'total_loans_count' => $this->total_loans_count,
            'recent_applications' => $this->recent_applications,
        ]);
    }
}
