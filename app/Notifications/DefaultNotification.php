<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

// use Illuminate\Contracts\Queue\ShouldQueue; // Uncomment if queuing is desired

class DefaultNotification extends Notification // implements ShouldQueue
{
    use Queueable;

    private User $subjectUser; // User this notification is about

    private string $message;

    private ?string $url;

    private string $subject;

    private string $icon;

    public function __construct(
        User $subjectUser,
        string $subject,
        string $message,
        ?string $url = null,
        string $icon = 'ti ti-info-circle'
    ) {
        $this->subjectUser = $subjectUser;
        $this->subject = $subject;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toArray(User $notifiable): array
    {
        // $notifiable is the user receiving the notification.
        // $this->subjectUser is the user this notification is primarily about.

        return [
            'subject_user_id' => $this->subjectUser->id,
            'subject_user_name' => $this->subjectUser->name ?? __('Pengguna Tidak Dikenali'),
            'subject_user_identifier' => $this->subjectUser->identification_number ?? null, // e.g., NRIC
            'subject_user_photo_url' => $this->subjectUser->profile_photo_url ?? null,
            'subject' => $this->subject, // Subject for the notification
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'notification_type' => 'default_info', // Generic type
        ];
    }
}
