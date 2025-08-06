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
    private array $lines;
    private ?string $actionUrl;
    private string $actionText;
    private array $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param string $subject The subject of the notification (for email, and display)
     * @param string $greeting The greeting message (for email)
     * @param array $lines An array of text lines to display in the notification body
     * @param string|null $actionUrl An optional URL for the call to action button
     * @param string $actionText The text for the call to action button
     * @param array $additionalData Any extra data to store with the notification (e.g., icon, custom type)
     */
    public function __construct(
        string $subject = 'New Notification',
        string $greeting = 'Hello!',
        array $lines = ['You have a new notification.'],
        ?string $actionUrl = null,
        string $actionText = 'View Details',
        array $additionalData = []
    ) {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->lines = $lines;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User  $notifiable The user receiving the notification
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        $channels = ['database'];
        // You might decide based on a user preference, or if email is always desired for this type
        if (!empty($notifiable->email) && ($this->additionalData['send_email'] ?? true)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User  $notifiable The user receiving the notification
     * @return MailMessage
     */
    public function toMail(User $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(__($this->subject)) // Support translation for subject
            ->greeting(__($this->greeting, ['name' => $notifiable->name])); // Personalize greeting

        foreach ($this->lines as $line) {
            $mailMessage->line(__($line)); // Add each line, supporting translation
        }

        if ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action(__($this->actionText), $this->actionUrl); // Add action button, supporting translation
        }

        $mailMessage->line(__('Thank you for using our application!')); // Generic closing line

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification (for database storage).
     *
     * @param  User  $notifiable The user receiving the notification
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        // Translate lines for the database message as well, so UI can display translated text
        $translatedLines = array_map(fn ($line) => __($line), $this->lines);

        return array_merge([
            'subject' => __($this->subject),
            'message' => implode("\n", $translatedLines), // Combine lines for a single message field in database
            'action_url' => ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) ? $this->actionUrl : null,
            'action_text' => __($this->actionText),
            'icon' => $this->additionalData['icon'] ?? 'ti ti-bell', // Default icon
            'notification_type' => $this->additionalData['type'] ?? 'user_general', // A general type identifier
            'user_id' => $notifiable->id, // Add recipient user ID for easy retrieval
            'user_name' => $notifiable->name, // Add recipient user name
            'created_at' => now()->toDateTimeString(), // Add timestamp for context
        ], $this->additionalData);
    }
}
