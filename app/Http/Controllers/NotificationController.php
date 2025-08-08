<?php

namespace App\Http\Controllers;

use App\Models\Notification as CustomDatabaseNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Handles user notification management - listing, marking as read, etc.
 * Integrates with app\Models\Notification and custom notification classes in app\Notifications.
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        // Require authentication for all notification routes
        $this->middleware('auth');
    }

    /**
     * Display a listing of the authenticated user's notifications.
     * Marks all unread notifications as read upon viewing this page.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Mark all unread notifications as read (updates read_at timestamp)
        $user->unreadNotifications()->update(['read_at' => now()]);

        // Fetch notifications ordered by latest, paginated
        $notifications = $user->notifications()
            ->latest()
            ->paginate(config('pagination.notifications_per_page', 15));

        // Return the notifications list view
        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  \App\Models\Notification  $notification  Route model bound instance
     * @return RedirectResponse
     */
    public function markAsRead(CustomDatabaseNotification $notification): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Authorize: notification must belong to the current user
        if ((string) $notification->notifiable_id !== (string) $user->id ||
            $notification->notifiable_type !== $user->getMorphClass()) {
            Log::warning(sprintf('User %d attempted to act on notification ID %s not belonging to them.', $user->id, $notification->id));
            return redirect()->back()->with('error', __('Anda tidak mempunyai kebenaran untuk mengubah notifikasi ini.'));
        }

        // Only mark as read if currently unread
        if ($notification->unread()) { // Uses is_null(read_at)
            $notification->markAsRead(); // Sets read_at timestamp
            Log::info(sprintf('Notification ID %s marked as read by User ID %d.', $notification->id, $user->id));
            return redirect()->back()->with('success', __('Notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Notifikasi ini telahpun dibaca.'));
    }

    /**
     * Mark all unread notifications of the authenticated user as read.
     * Useful for "Mark all as read" buttons.
     */
    public function markAllAsRead(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $unreadNotifications = $user->unreadNotifications(); // Query builder

        if ($unreadNotifications->count() > 0) {
            $unreadNotifications->update(['read_at' => now()]);
            Log::info(sprintf('All unread notifications marked as read for User ID %d.', $user->id));
            return redirect()->back()->with('success', __('Semua notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Tiada notifikasi baru untuk ditanda sebagai dibaca.'));
    }
}
