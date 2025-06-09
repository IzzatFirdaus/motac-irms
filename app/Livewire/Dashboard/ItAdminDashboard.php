<?php

namespace App\Livewire\Dashboard;

use App\Models\EmailApplication;
use Livewire\Component;

class ItAdminDashboard extends Component
{
    public int $pending_email_applications_count = 0;
    public int $processing_email_applications_count = 0;

    public function mount()
    {
        $this->pending_email_applications_count = EmailApplication::where('status', EmailApplication::STATUS_PENDING_ADMIN)
            ->count();

        $this->processing_email_applications_count = EmailApplication::where('status', EmailApplication::STATUS_PROCESSING)
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.it-admin-dashboard');
    }
}
