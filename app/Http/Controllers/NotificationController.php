<?php

namespace App\Http\Controllers;

// Use your custom Notification model, aliased for clarity if needed
use App\Models\Notification as CustomDatabaseNotification;
use Illuminate\Http\RedirectResponse;
// Request is not strictly needed if not accessing request data beyond route parameters
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Apply authentication middleware to this controller.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the authenticated user's notifications.
     * Marks all unread notifications as read upon viewing this page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        /** @var \App\Models\User $user Authenticated user from auth middleware */
        $user = Auth::user();

        // Mark all unread notifications as read when the user views the index page
        // This uses the `unreadNotifications` relationship from the Notifiable trait
        // and its `markAsRead` method.
        $user->unreadNotifications->markAsRead(); //

        $notifications = $user
            ->notifications() // Fetches notifications related to the user
            ->latest() // Order by most recent
            ->paginate(15); // Adjust pagination as needed

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  \App\Models\Notification  $notification Route model bound instance of your custom Notification model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(CustomDatabaseNotification $notification): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization: Ensure the notification belongs to the authenticated user.
        // Your custom Notification model has 'notifiable_id' and 'notifiable_type'.
        if ((string) $notification->notifiable_id !== (string) $user->id ||
            $notification->notifiable_type !== $user->getMorphClass()) { // Use getMorphClass() for correctness
            Log::warning("User {$user->id} attempted to mark notification ID {$notification->id} as read, but it does not belong to them or has incorrect notifiable type.");
            return redirect()->back()->with('error', __('Anda tidak mempunyai kebenaran untuk mengubah notifikasi ini.'));
        }

        if ($notification->unread()) { // Uses unread() method from App\Models\Notification
            $notification->markAsRead(); // Uses markAsRead() method from App\Models\Notification
            Log::info("Notification ID {$notification->id} marked as read by User ID {$user->id}.");
            return redirect()->back()->with('success', __('Notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Notifikasi ini telahpun dibaca.'));
    }

    /**
     * Mark all unread notifications of the authenticated user as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Uses the `unreadNotifications` relationship from the Notifiable trait
        // and its `markAsRead` method, which handles batch updates.
        $unreadNotifications = $user->unreadNotifications; //

        if ($unreadNotifications->isNotEmpty()) {
            $unreadNotifications->markAsRead();
            Log::info("All unread notifications marked as read for User ID {$user->id}.");
            return redirect()->back()->with('success', __('Semua notifikasi telah ditanda sebagai dibaca.'));
        }

        return redirect()->back()->with('info', __('Tiada notifikasi baru untuk ditanda sebagai dibaca.'));
    }
}
