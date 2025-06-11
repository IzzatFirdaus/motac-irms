<?php

namespace App\Livewire\Sections\Navbar;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsDropdown extends Component
{
    public $unreadNotifications;
    public $unreadCount = 0;

    protected $listeners = ['refresh-notifications' => 'mount'];

    /**
     * Mount the component and fetch initial data.
     */
    public function mount()
    {
        if (Auth::check()) {
            // REVISED: Fetches notifications once to prevent multiple DB queries.
            $notifications = Auth::user()->unreadNotifications;
            $this->unreadCount = $notifications->count();
            $this->unreadNotifications = $notifications->take(10);
        } else {
            $this->unreadNotifications = collect();
            $this->unreadCount = 0;
        }
    }

    /**
     * Mark a single notification as read and redirect if a URL exists.
     * @param string $notificationId
     */
    public function markAsRead(string $notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
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
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->mount(); // Refresh the list
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.sections.navbar.notifications-dropdown');
    }
}
