<?php

namespace App\Livewire\Dashboard\ItAdmin;

use App\Models\EmailApplication;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class PendingEmailApplicationsList extends Component
{
  use WithPagination;

  public function render(): View
  {
    $pendingApplications = EmailApplication::whereIn('status', [
      EmailApplication::STATUS_PENDING_ADMIN,
      EmailApplication::STATUS_PROCESSING,
    ])
      ->with('user:id,name')
      ->latest('updated_at')
      ->paginate(5); // Show 5 per page on the dashboard

    return view('livewire.dashboard.it-admin.pending-email-applications-list', [
      'applications' => $pendingApplications,
    ]);
  }
}
