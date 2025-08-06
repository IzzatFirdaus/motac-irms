<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; // Consider uncommenting if you want to queue this notification

class DefaultNotification extends Notification // implements ShouldQueue
{
    use Queueable;

    private User $subjectUser; // The user this notification is primarily about
    private string $message;
    private ?string $url;
    private string $subject;
    private string $icon;

    /**
     * Create a new notification instance.
     *
     * @param User $subjectUser The user related to this notification (e.g., the one whose action triggered it)
     * @param string $subject The subject line for the notification
     * @param string $message The main message body of the notification
     * @param string|null $url An optional URL for the notification action
     * @param string $icon An optional icon to display with the notification
     */
    public function __construct(
        User $subjectUser,
        string $subject,
        string $message,
        ?string $url = null,
        string $icon = 'ti ti-info-circle' // Default generic info icon
    ) {
        $this->subjectUser = $subjectUser;
        $this->subject = $subject;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User  $notifiable The user receiving the notification
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        // For general system notifications, 'database' is often sufficient.
        // You can add 'mail' if you want an email for all default notifications,
        // but often specific mail notifications are preferred.
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  User  $notifiable The user receiving the notification
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        // $notifiable is the user actually receiving this notification (the recipient).
        // $this->subjectUser is the user the notification is "about" (e.g., a user who performed an action).
        return [
            'subject_user_id' => $this->subjectUser->id,
            'subject_user_name' => $this->subjectUser->name ?? __('Pengguna Tidak Dikenali'),
            'subject_user_identifier' => $this->subjectUser->identification_number ?? null, // Example: NRIC, Staff ID
            'subject_user_photo_url' => $this->subjectUser->profile_photo_url ?? null,
            'subject' => $this->subject,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'notification_type' => 'default_info', // A generic type identifier
            'created_at' => now()->toDateTimeString(), // Add timestamp for context
        ];
    }
}
