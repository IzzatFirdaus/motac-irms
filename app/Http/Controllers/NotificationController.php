<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Handles user notifications.
 * Provides functionality for viewing and managing notifications.
 */
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
     *
     * Fetches the latest notifications for the logged-in user with pagination.
     *
     * NOTE: Microscope flagged this method (`index`) in the latest output.
     * This method appears to be intended as a page for users to view their notifications.
     * It contains logic to fetch user-specific notifications and returns a view.
     * KEPT the method as it seems necessary for this functionality.
     * This method **still needs a route** in your web.php that points to it.
     * Example route: `Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');`
     *
     * @return \Illuminate\View\View The view displaying the list of notifications.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(15);

        // Optional: Mark notifications as read when the user views the index page.
        // You might only want to mark *unread* notifications as read.
        // $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    // Optional: Add a method to mark a specific notification as read (e.g., via AJAX)
    // public function markAsRead(Request $request, $notificationId)
    // {
    //     // Use Auth::user() to get the authenticated user and their notifications
    //     $notification = Auth::user()->notifications()->where('id', $notificationId)->first();
    //     if ($notification) {
    //         $notification->markAsRead(); // Assuming your custom Notification model/trait has markAsRead
    //         return response()->json(['status' => 'success']);
    //     }
    //     return response()->json(['status' => 'not found'], 404);
    // }

    // Optional: Add a method to mark all notifications as read
    // public function markAllAsRead()
    // {
    //     $user = Auth::user(); // Get authenticated user
    //     // Use the unreadNotifications relationship provided by the Notifiable trait
    //     $user->unreadNotifications->markAsRead(); // Assuming your Notification model/trait has markAsRead
    //     return response()->json(['status' => 'success', 'message' => 'All notifications marked as read.']);
    // }
}
