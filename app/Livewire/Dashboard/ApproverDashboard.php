<?php

namespace App\Livewire\Dashboard;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Renders the main dashboard for users with approval tasks.
 * It provides key statistics and embeds the main approval task list component.
 */
class ApproverDashboard extends Component
{
    public int $approved_last_30_days = 0;
    public int $rejected_last_30_days = 0;

    /**
     * Fetch statistics when the component is initialized.
     */
    public function mount(): void
    {
        $officerId = Auth::id();

        $this->approved_last_30_days = Approval::where('officer_id', $officerId)
            ->where('status', Approval::STATUS_APPROVED)
            ->where('updated_at', '>=', now()->subDays(30)) // REVISED: Using updated_at for more accuracy
            ->count();

        $this->rejected_last_30_days = Approval::where('officer_id', $officerId)
            ->where('status', Approval::STATUS_REJECTED)
            ->where('updated_at', '>=', now()->subDays(30)) // REVISED: Using updated_at for more accuracy
            ->count();
    }

    /**
     * Render the approver dashboard view.
     */
    public function render(): View
    {
        return view('livewire.dashboard.approver-dashboard');
    }
}
