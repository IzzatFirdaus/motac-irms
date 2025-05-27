<?php

declare(strict_types=1);

namespace App\Livewire\Sections\Navbar;

use App\Models\Import; // System Design 3.1, for import progress bar
use App\Models\User;   // System Design 4.1
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // For type hinting
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Queue\Failed\FailedJobProviderInterface; // For checking failed jobs, though usage will be reconsidered

class Navbar extends Component
{
    public EloquentCollection $unreadNotifications;
    public bool $activeProgressBar = false;
    public int $percentage = 0;

    // Props passed from app.blade.php for dynamic styling
    public string $containerNav = 'container-xxl'; // Default value
    public string $navbarDetachedClass = ''; // Default value (e.g., 'navbar-detached' or empty)

    protected const LOG_AREA = 'NavbarComponent: ';

    public function mount(string $containerNav = 'container-xxl', string $navbarDetachedClass = ''): void
    {
        $this->containerNav = $containerNav;
        $this->navbarDetachedClass = $navbarDetachedClass;
        $this->loadUnreadNotifications();
    }

    protected function loadUnreadNotifications(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            // Assumes User model uses Laravel's default Notifiable trait and notifications table,
            // or that the 'notifications'/'unreadNotifications' relationships are correctly
            // overridden if using the custom App\Models\Notification table.
            // System Design 4.4 mentions a custom Notification model.
            $this->unreadNotifications = $user->unreadNotifications;
        } else {
            $this->unreadNotifications = new EloquentCollection(); // Initialize as empty Eloquent collection
        }
    }

    #[On('refreshNotifications')]
    public function refreshNotificationsHandler(): void
    {
        $this->loadUnreadNotifications();
    }

    /**
     * Update the import progress bar status.
     * This is triggered by a Livewire event or polled.
     * System Design implies an Import model for tracking (Section 3.1, 9.5).
     */
    #[On('activeProgressBar')] // Listen for this event to trigger an update
    public function updateProgressBar(): void
    {
        /** @var Import|null $latestImport */
        $latestImport = Import::latest()->first();

        if ($latestImport) {
            if ($latestImport->status === 'processing') {
                $this->activeProgressBar = true;
                if ($latestImport->total > 0) {
                    $this->percentage = (int) round(($latestImport->current / $latestImport->total) * 100);
                } else {
                    $this->percentage = 0; // Avoid division by zero
                }
                // Check for related failed jobs ONLY if the import is handled by a queue that uses the failed_jobs table.
                // This part is highly dependent on how imports are implemented.
                // If imports are synchronous or use a different failure tracking, this check might be irrelevant or harmful.
                // For now, let's assume direct status from Import model is primary.
            } elseif ($latestImport->status === 'completed') {
                $this->activeProgressBar = false;
                $this->percentage = 100;
                // Using session flash for temporary messages. Consider a more robust event-driven UI update.
                session()->flash('success', __('Import Selesai!'));
            } elseif (in_array($latestImport->status, ['failed', 'cancelled'])) {
                $this->activeProgressBar = false;
                $this->percentage = 0;
                session()->flash('error', __('Proses Import Gagal atau Dibatalkan.'));
            } else {
                // Status is pending or some other state not requiring active progress bar
                $this->activeProgressBar = false;
                $this->percentage = 0;
            }
        } else {
            // No import records found
            $this->activeProgressBar = false;
            $this->percentage = 0;
        }
    }


    public function markNotificationAsRead(string $notificationId): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
                $this->loadUnreadNotifications(); // Refresh the count/list
            }
        }
    }

    public function markAllNotificationsAsRead(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            $this->loadUnreadNotifications(); // Refresh the count/list
        }
    }

    public function render(): View
    {
        // CRITICAL: Removed DB::table('failed_jobs')->truncate();
        // Failed jobs should be managed via dedicated admin tools or scheduled tasks.
        return view('livewire.sections.navbar.navbar');
    }
}
