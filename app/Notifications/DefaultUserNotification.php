<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

final class DefaultUserNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    private string $subject;
    private string $greeting;
    private string $line;
    private ?string $actionUrl;
    private string $actionText;
    private array $additionalData; // For any other specific data to store

    public function __construct(
        string $subject = 'Notifikasi Baru',
        string $greeting = 'Salam Sejahtera!',
        string $line = 'Anda mempunyai notifikasi baru.',
        ?string $actionUrl = null,
        string $actionText = 'Lihat Butiran',
        array $additionalData = [] // Allow passing extra data
    ) {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->line = $line;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->additionalData = $additionalData;
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        $channels = ['database'];
        // Example condition for sending email, adapt as needed:
        // if ($notifiable->email_notifications_enabled && $notifiable->email) {
        //     $channels[] = 'mail';
        // }
        return $channels;
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $mailMessage = (new MailMessage())
            ->subject($this->subject)
            ->greeting($this->greeting.' '.($notifiable->name ?? '')) // Personalize greeting
            ->line($this->line);

        if ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action($this->actionText, $this->actionUrl);
        }

        $mailMessage->line(__('Terima kasih kerana menggunakan aplikasi kami!'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        return array_merge([
            'subject' => $this->subject,
            'greeting' => $this->greeting, // May not be directly used if message is specific
            'message' => $this->line, // Main message content
            'action_url' => ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) ? $this->actionUrl : null,
            'action_text' => $this->actionText,
            'icon' => $this->additionalData['icon'] ?? 'ti ti-bell', // Default icon
            'notification_type' => $this->additionalData['type'] ?? 'user_specific',
        ], $this->additionalData); // Merge any other custom data
    }
}
