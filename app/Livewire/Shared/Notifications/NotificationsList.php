<?php

namespace App\Livewire\Shared\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * NotificationsList Livewire component.
 *
 * Displays a paginated, searchable list of notifications for the authenticated user,
 * and allows marking notifications as read.
 */
class NotificationsList extends Component
{
    use WithPagination;

    // Search text for filtering notifications (searches the 'data' column)
    public string $search = '';

    // Use Bootstrap for pagination controls
    protected string $paginationTheme = 'bootstrap';

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Computed property to get paginated notifications for the authenticated user.
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
     *
     * @param int|string $notificationId
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', Auth::id())
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            session()->flash('success', __('Notifikasi telah ditanda sebagai dibaca.'));
            $this->resetPage();
        }
    }

    /**
     * Render the notifications list view.
     */
    public function render()
    {
        return view('livewire.shared.notifications.notifications-list', [
            'notifications' => $this->getNotificationsProperty(),
        ]);
    }
}
