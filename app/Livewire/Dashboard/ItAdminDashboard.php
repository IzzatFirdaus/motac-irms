<?php

namespace App\Livewire\Dashboard;

use App\Models\HelpdeskTicket;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * IT Admin Dashboard Livewire Component
 *
 * Displays helpdesk ticket statistics for IT Admins.
 */
class ItAdminDashboard extends Component
{
    public int $pending_helpdesk_tickets_count = 0;
    public int $in_progress_helpdesk_tickets_count = 0;

    public function mount(): void
    {
        $this->pending_helpdesk_tickets_count = HelpdeskTicket::where('status', 'open')->count();
        $this->in_progress_helpdesk_tickets_count = HelpdeskTicket::where('status', 'in_progress')->count();
    }

    #[Title('IT Admin Dashboard')]
    public function render(): View
    {
        return view('livewire.dashboard.it-admin-dashboard');
    }
}
