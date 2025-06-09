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
            $this->unreadNotifications = Auth::user()->unreadNotifications()->latest()->take(10)->get();
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        } else {
            $this->unreadNotifications = collect();
            $this->unreadCount = 0;
        }
    }

    /**
     * Mark a single notification as read and redirect if a URL exists.
     *
     * @param string $notificationId The ID of the notification to mark as read.
     */
    public function markAsRead(string $notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        // Refresh the notifications list
        $this->mount();

        // Redirect if the notification has a URL
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        // Refresh the notifications list
        $this->mount();
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.sections.navbar.notifications-dropdown');
    }
}
