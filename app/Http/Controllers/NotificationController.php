<?php

namespace App\Http\Controllers;

use App\Models\Notification as CustomDatabaseNotification;
use App\Models\User; // For type hinting
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the authenticated user's notifications.
     * Marks all unread notifications as read upon viewing this page.
     * SDD Ref:
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // SDD "Uses Laravel's Notifiable trait features on the User model."
        $user->unreadNotifications()->update(['read_at' => now()]);

        $notifications = $user->notifications()
            ->latest()
            ->paginate(config('pagination.notifications_per_page', 15)); // Using a config for pagination

        return view('notifications.index', ['notifications' => $notifications]); // View path from SDD
    }

    /**
     * Mark a specific notification as read.
     * SDD Ref:
     *
     * @param  \App\Models\Notification  $notification  Route model bound instance
     */
    public function markAsRead(CustomDatabaseNotification $notification): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((string) $notification->notifiable_id !== (string) $user->id ||
            $notification->notifiable_type !== $user->getMorphClass()) {
            Log::warning(sprintf('User %d attempted to act on notification ID %s not belonging to them.', $user->id, $notification->id));

            return redirect()->back()->with('error', __('Anda tidak mempunyai kebenaran untuk mengubah notifikasi ini.'));
        }

        if ($notification->unread()) { // Assumes unread() method on CustomDatabaseNotification
            $notification->markAsRead(); // Assumes markAsRead() method on CustomDatabaseNotification
            Log::info(sprintf('Notification ID %s marked as read by User ID %d.', $notification->id, $user->id));

            return redirect()->back()->with('success', __('Notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Notifikasi ini telahpun dibaca.'));
    }

    /**
     * Mark all unread notifications of the authenticated user as read.
     * SDD Ref:
     */
    public function markAllAsRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $unreadNotifications = $user->unreadNotifications(); // Get query builder

        if ($unreadNotifications->count() > 0) {
            $unreadNotifications->update(['read_at' => now()]);
            Log::info(sprintf('All unread notifications marked as read for User ID %d.', $user->id));

            return redirect()->back()->with('success', __('Semua notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Tiada notifikasi baru untuk ditanda sebagai dibaca.'));
    }
}
