<?php

namespace App\Livewire\Sections\Navbar;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * NotificationsDropdown Livewire Component.
 *
 * Renders a notifications dropdown, showing unread notifications for the user.
 * Allows marking notifications as read (single or all).
 */
class NotificationsDropdown extends Component
{
    // Stores the last 10 unread notifications for the user
    public $unreadNotifications;

    // Stores the unread notification count
    public $unreadCount = 0;

    // Listen for Livewire events to refresh notifications
    protected $listeners = ['refresh-notifications' => 'mount'];

    /**
     * Mount the component and fetch the initial notifications data.
     * Only fetch if the user is authenticated.
     */
    public function mount(): void
    {
        if (Auth::check()) {
            // Fetch unread notifications once for efficiency
            $notifications             = Auth::user()->unreadNotifications;
            $this->unreadCount         = $notifications->count();
            $this->unreadNotifications = $notifications->take(10);
        } else {
            $this->unreadNotifications = collect();
            $this->unreadCount         = 0;
        }
    }

    /**
     * Mark a single notification as read and redirect if a URL exists in the notification.
     */
    public function markAsRead(string $notificationId): void
    {
        $user         = Auth::user();
        $notification = $user ? $user->notifications()->find($notificationId) : null;
        if ($notification) {
            $notification->markAsRead();
            $this->mount(); // Refresh the list
            if (isset($notification->data['url'])) {
                $this->redirect($notification->data['url']);
            }
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->mount(); // Refresh the list
        }
    }

    /**
     * Render the notifications dropdown view.
     */
    public function render()
    {
        return view('livewire.sections.navbar.notifications-dropdown', [
            'unreadNotifications' => $this->unreadNotifications,
            'unreadCount'         => $this->unreadCount,
        ]);
    }
}
