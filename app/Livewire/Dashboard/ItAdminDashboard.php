<?php

namespace App\Livewire\Dashboard;

use App\Models\EmailApplication;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class ItAdminDashboard extends Component
{
    public int $pending_email_applications_count = 0;

    public int $processing_email_applications_count = 0;

    /**
     * Mount the component and initialize the data.
     * Fetches counts for email applications awaiting IT admin action or currently being processed.
     */
    public function mount(): void
    {
        $this->pending_email_applications_count = EmailApplication::where('status', EmailApplication::STATUS_PENDING_ADMIN)
            ->count(); //

        $this->processing_email_applications_count = EmailApplication::where('status', EmailApplication::STATUS_PROCESSING)
            ->count(); //
    }

    /**
     * Render the component.
     */
    #[Title('IT Admin Dashboard')] // Sets the browser page title
    public function render(): View
    {
        return view('livewire.dashboard.it-admin-dashboard')
            ->layout('layouts.app'); // Use the main application layout
    }
}
