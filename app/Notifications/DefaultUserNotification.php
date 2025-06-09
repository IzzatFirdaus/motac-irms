<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

final class DefaultUserNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    private string $subject;

    private string $greeting;

    private array $lines; // Changed from single 'line' to 'lines' array for more flexibility

    private ?string $actionUrl;

    private string $actionText;

    private array $additionalData;

    public function __construct(
        string $subject = 'Notifikasi Baru',
        string $greeting = 'Salam Sejahtera!',
        array $lines = ['Anda mempunyai notifikasi baru.'], // Changed to array
        ?string $actionUrl = null,
        string $actionText = 'Lihat Butiran',
        array $additionalData = []
    ) {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->lines = $lines;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->additionalData = $additionalData;
    }

    public function via(User $notifiable): array
    {
        $channels = ['database'];
        // Send email only if user has an email and email notifications are enabled for this type (example)
        if (! empty($notifiable->email) && ($this->additionalData['send_email'] ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(__($this->subject)) // Assuming subject can be a translatable key or plain string
            ->greeting(__($this->greeting, ['name' => $notifiable->name])); // Personalize greeting

        foreach ($this->lines as $line) {
            $mailMessage->line(__($line)); // Translate each line
        }

        if ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action(__($this->actionText), $this->actionUrl); // Translate action text
        }

        $mailMessage->line(__('Terima kasih kerana menggunakan aplikasi kami!'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        // Translate lines for database message
        $translatedLines = array_map(fn ($line) => __($line), $this->lines);

        return array_merge([
            'subject' => __($this->subject),
            // 'greeting' => __($this->greeting), // Greeting usually part of the overall message
            'message' => implode("\n", $translatedLines), // Main message content
            'action_url' => ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) ? $this->actionUrl : null,
            'action_text' => __($this->actionText),
            'icon' => $this->additionalData['icon'] ?? 'ti ti-bell',
            'notification_type' => $this->additionalData['type'] ?? 'user_specific',
            // Adding these for better context in UI if listing notifications
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
        ], $this->additionalData);
    }
}
