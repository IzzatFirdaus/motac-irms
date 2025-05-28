<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User; // For type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

final class DefaultUserNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    private string $subjectKey;
    private string $greetingKey;
    private array $lines; // Array of lines (can be translatable keys or plain strings)
    private ?string $actionUrl;
    private string $actionTextKey;
    private array $mailData; // Data to pass to the Markdown/Blade view

    /**
     * Create a new notification instance.
     *
     * @param string $subjectKey Translatable key for the email subject.
     * @param string $greetingKey Translatable key for the email greeting.
     * @param array $lines Array of translatable strings or plain strings for the email body.
     * @param string|null $actionUrl URL for the action button.
     * @param string $actionTextKey Translatable key for the action button text.
     * @param array $additionalData Extra data for 'database' channel or to pass to MailMessage.
     */
    public function __construct(
        string $subjectKey = 'Notifikasi Baru', // Default translatable key
        string $greetingKey = 'Salam Sejahtera', // Default translatable key
        array $lines = ['Anda mempunyai notifikasi baharu.'], // Default line (can be a key)
        ?string $actionUrl = null,
        string $actionTextKey = 'Lihat Butiran', // Default translatable key
        array $additionalData = []
    ) {
        $this->subjectKey = $subjectKey;
        $this->greetingKey = $greetingKey;
        $this->lines = $lines;
        $this->actionUrl = $actionUrl;
        $this->actionTextKey = $actionTextKey;
        $this->mailData = $additionalData; // Store additional data for mail view
    }

    public function via(User $notifiable): array
    {
        $channels = ['database'];
        // Example: Only send email if user has an email and notifications enabled
        if ($notifiable->email && ($this->mailData['send_email'] ?? true)) { // Assuming a flag or user preference
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(User $notifiable): MailMessage
    {
        // Prepare data for the email view
        $dataForView = array_merge([
            'greeting' => __($this->greetingKey),
            'notifiableName' => $notifiable->name,
            'lines' => array_map(fn ($line) => __($line), $this->lines), // Translate each line
            'actionUrl' => ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) ? $this->actionUrl : null,
            'actionText' => __($this->actionTextKey),
            'subject' => __($this->subjectKey) // For use within the template if needed
        ], $this->mailData); // Merge any other custom data

        // System Design: Section 9.5 for email notifications.
        return (new MailMessage())
            ->subject(__($this->subjectKey)) // Subject is translated
            ->markdown('emails.notifications.motac_default_notification', $dataForView); // Use the Blade template
    }

    public function toArray(User $notifiable): array
    {
        // Data for database notifications (System Design 4.4, 9.5)
        $translatedLines = array_map(fn ($line) => __($line), $this->lines);

        return array_merge([
            'subject' => __($this->subjectKey),
            'greeting' => __($this->greetingKey),
            'message' => implode("\n", $translatedLines), // Combine lines for simple display
            'action_url' => ($this->actionUrl && filter_var($this->actionUrl, FILTER_VALIDATE_URL)) ? $this->actionUrl : null,
            'action_text' => __($this->actionTextKey),
            'icon' => $this->mailData['icon'] ?? 'ti ti-bell', // Default icon from template
            'notification_type' => $this->mailData['type'] ?? 'user_specific', // e.g., 'loan_application'
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name, // For context in admin notification views
        ], $this->mailData);
    }
}
