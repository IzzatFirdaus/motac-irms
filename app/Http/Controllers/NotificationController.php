<?php

namespace App\Http\Controllers;

use App\Models\Notification as UserNotification; // Alias to avoid conflicts if any, and specify our model
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Added for markAsRead if using request data
use Illuminate\Support\Facades\Auth;
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
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = $user
            ->notifications() // Uses the relationship from Notifiable trait
            ->latest()
            ->paginate(config('pagination.default', 15)); // Use a config value for pagination size

        // Optional: Mark notifications as read when the user views the index page.
        // Consider doing this only for the notifications displayed on the current page,
        // or via a separate "mark all visible as read" button.
        // For example, to mark only the currently paginated UNREAD notifications as read:
        // $user->unreadNotifications()->whereIn('id', $notifications->pluck('id')->all())->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var UserNotification|null $notification */
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead(); // Uses method from our App\Models\Notification
            return response()->json(['status' => 'success', 'message' => __('Notifikasi telah ditanda sebagai dibaca.')]);
        }
        return response()->json(['status' => 'error', 'message' => __('Notifikasi tidak ditemui.')], 404);
    }

    /**
     * Mark all unread notifications of the authenticated user as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Get all unread notifications (instances of App\Models\Notification) and mark them read
        $unreadNotifications = $user->unreadNotifications()->get(); // Get collection of App\Models\Notification
        foreach ($unreadNotifications as $notification) {
            /** @var UserNotification $notification */
            $notification->markAsRead();
        }
        // Laravel's default $user->unreadNotifications->markAsRead(); might work if your
        // custom Notification model correctly integrates or if Notifiable trait is overridden.
        // The loop above is more explicit with a custom model.

        return response()->json(['status' => 'success', 'message' => __('Semua notifikasi telah ditanda sebagai dibaca.')]);
    }
}
