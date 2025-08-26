<?php

namespace App\Livewire\Dashboard;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

/**
 * ApproverDashboard Livewire Component.
 *
 * Dashboard for officers with approval tasks.
 */
class ApproverDashboard extends Component
{
    public int $approved_last_30_days = 0;

    public int $rejected_last_30_days = 0;

    public function mount(): void
    {
        $officerId = Auth::id();

        $this->approved_last_30_days = Approval::where('officer_id', $officerId)
            ->where('status', Approval::STATUS_APPROVED)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        $this->rejected_last_30_days = Approval::where('officer_id', $officerId)
            ->where('status', Approval::STATUS_REJECTED)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
    }

    public function render(): View
    {
        return view('livewire.dashboard.approver-dashboard');
    }
}
