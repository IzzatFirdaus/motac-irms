<?php

namespace App\Livewire\Shared\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;

/**
 * NotificationsList Livewire component.
 *
 * Displays a paginated list of notifications for the authenticated user.
 */
class NotificationsList extends Component
{
    use WithPagination;

    public $search = '';

    /**
     * Reset pagination on search update.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get paginated notifications for the authenticated user.
     */
    public function getNotificationsProperty()
    {
        return Notification::query()
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', Auth::user() ? get_class(Auth::user()) : null)
            ->when($this->search, function ($query) {
                $query->where('data', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->markAsRead();
            session()->flash('success', 'Notification marked as read.');
            $this->resetPage();
        }
    }

    /**
     * Render the notifications list view.
     */
    public function render()
    {
        return view('livewire.shared.notifications.notifications-list', [
            'notifications' => $this->notifications,
        ]);
    }
}
