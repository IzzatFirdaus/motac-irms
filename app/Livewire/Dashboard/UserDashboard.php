<?php

namespace App\Livewire\Dashboard;

use App\Models\LoanApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * UserDashboard Livewire Component.
 *
 * Shows the dashboard for a normal user, with loan stats, quick actions, and recent applications.
 * This is often included by the main Dashboard component.
 */
class UserDashboard extends Component
{
    public string $displayUserName = '';

    public int $pending_loans_count = 0;

    public int $approved_loans_count = 0;

    public int $rejected_loans_count = 0;

    public int $total_loans_count = 0;

    public $recent_applications;

    public function mount()
    {
        $user                  = Auth::user();
        $this->displayUserName = $user->name ?? '';

        $this->pending_loans_count = LoanApplication::where('user_id', $user->id)
            ->where('status', LoanApplication::STATUS_PENDING_SUPPORT)->count();
        $this->approved_loans_count = LoanApplication::where('user_id', $user->id)
            ->where('status', LoanApplication::STATUS_APPROVED)->count();
        $this->rejected_loans_count = LoanApplication::where('user_id', $user->id)
            ->where('status', LoanApplication::STATUS_REJECTED)->count();
        $this->total_loans_count = LoanApplication::where('user_id', $user->id)->count();

        $this->recent_applications = LoanApplication::with('equipment')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.user-dashboard', [
            'displayUserName'      => $this->displayUserName,
            'pending_loans_count'  => $this->pending_loans_count,
            'approved_loans_count' => $this->approved_loans_count,
            'rejected_loans_count' => $this->rejected_loans_count,
            'total_loans_count'    => $this->total_loans_count,
            'recent_applications'  => $this->recent_applications,
        ]);
    }
}
