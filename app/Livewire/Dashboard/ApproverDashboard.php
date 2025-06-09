<?php

namespace App\Livewire\Dashboard;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApproverDashboard extends Component
{
    public int $approved_last_30_days = 0;
    public int $rejected_last_30_days = 0;

    public function mount()
    {
        $this->approved_last_30_days = Approval::where('officer_id', Auth::id())
            ->where('status', 'approved')
            ->where('approval_timestamp', '>=', now()->subDays(30))
            ->count();

        $this->rejected_last_30_days = Approval::where('officer_id', Auth::id())
            ->where('status', 'rejected')
            ->where('approval_timestamp', '>=', now()->subDays(30))
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.approver-dashboard');
    }
}
