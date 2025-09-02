<?php

namespace App\Livewire\Shared\Notifications;

use App\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * NotificationsList Livewire component.
 *
 * Displays a paginated, searchable list of notifications for the authenticated user,
 * and allows marking notifications as read.
 */
/**
 * @method void resetPage()
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
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Computed property to get paginated notifications for the authenticated user.
     */
    public function getNotificationsProperty(): LengthAwarePaginator
    {
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = Notification::query();

        $notifiableType = null;
        if (Auth::check() && Auth::user()) {
            $notifiableType = get_class(Auth::user());
        }

        $query->where('notifiable_id', Auth::id())
            ->where('notifiable_type', $notifiableType);

        if ($this->search !== '' && $this->search !== '0') {
            $query->where('data', 'like', '%'.$this->search.'%');
        }

        return $query->orderByDesc('created_at')->paginate(10);
    }

    /**
     * Mark a notification as read.
     *
     * @param int|string $notificationId
     */
    public function markAsRead($notificationId): void
    {
        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', Auth::id())
            ->first();

        if ($notification && is_null($notification->read_at)) {
            // Use the built-in markAsRead method if available; otherwise, manually set read_at
            if (method_exists($notification, 'markAsRead')) {
                $notification->markAsRead();
            } else {
                $notification->read_at = Carbon::now();
                $notification->save();
            }

            // Correct session flash usage
            Session::flash('success', __('messages.notification_marked_read'));
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
