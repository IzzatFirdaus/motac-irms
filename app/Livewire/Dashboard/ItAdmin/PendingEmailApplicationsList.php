<?php

namespace App\Livewire\Dashboard\ItAdmin;

use App\Models\EmailApplication;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PendingEmailApplicationsList extends Component
{
    use WithPagination;

    /**
     * Render the component.
     */
    public function render(): View
    {
        // Fetch applications that require IT Admin action.
        $pendingApplications = EmailApplication::whereIn('status', [
            EmailApplication::STATUS_PENDING_ADMIN, // As per workflow
            EmailApplication::STATUS_PROCESSING,  // As per workflow
        ])
            ->with('user:id,name') // Eager load user for efficiency
            ->oldest('created_at') // UPDATE: Sort by oldest first to create a processing queue (FIFO)
            ->paginate(5); // Show 5 per page on the dashboard

        return view('livewire.dashboard.it-admin.pending-email-applications-list', [
            'applications' => $pendingApplications,
        ]);
    }
}
