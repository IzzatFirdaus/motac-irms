<?php

namespace App\Livewire\Dashboard;

// use App\Models\EmailApplication; // REMOVED: EmailApplication import
use App\Models\HelpdeskTicket; // Corrected: Use HelpdeskTicket instead of Ticket
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class ItAdminDashboard extends Component
{
    // REMOVED: pending_email_applications_count, processing_email_applications_count
    public int $open_tickets_count = 0; // NEW: Count for open tickets
    public int $in_progress_tickets_count = 0; // NEW: Count for in-progress tickets
    public int $pending_user_feedback_tickets_count = 0; // NEW: Count for tickets pending user feedback

    /**
     * Mount the component and initialize the data.
     * Fetches counts for Helpdesk tickets awaiting IT admin action or currently being processed.
     */
    public function mount(): void
    {
        // NEW: Fetch counts for Helpdesk tickets
        // Use HelpdeskTicket model for counts
        $this->open_tickets_count = HelpdeskTicket::where('status', 'open')->count();
        $this->in_progress_tickets_count = HelpdeskTicket::where('status', 'in_progress')->count();
        $this->pending_user_feedback_tickets_count = HelpdeskTicket::where('status', 'pending_user_feedback')->count();
    }

    /**
     * Render the component.
     */
    #[Title('IT Admin Dashboard')] // Sets the browser page title
    public function render(): View
    {
        return view('livewire.dashboard.it-admin-dashboard');
            // ->layout('layouts.app'); // REMOVE THIS LINE
    }
}
