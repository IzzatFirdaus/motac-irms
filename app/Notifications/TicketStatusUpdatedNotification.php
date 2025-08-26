<?php

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected HelpdeskTicket $ticket;

    protected User $updater;

    protected string $recipientType; // 'applicant' or 'agent'

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpdeskTicket $ticket, User $updater, string $recipientType)
    {
        $this->ticket        = $ticket;
        $this->updater       = $updater;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject     = "Helpdesk Ticket #{$this->ticket->id} Status Updated to '{$this->ticket->status}'";
        $greeting    = "Dear {$notifiable->name},";
        $updaterName = $this->updater->name;

        $message = '';
        if ($this->recipientType === 'applicant') {
            $message = "The status of your helpdesk ticket **#{$this->ticket->id}** (`{$this->ticket->title}`) has been updated to **'{$this->ticket->status}'** by `{$updaterName}`.";
            if ($this->ticket->status === 'closed' && $this->ticket->resolution_notes) {
                $message .= "\n\nResolution Notes: {$this->ticket->resolution_notes}";
            }
        } elseif ($this->recipientType === 'agent') {
            $message = "The status of ticket **#{$this->ticket->id}** (`{$this->ticket->title}`) has been updated to **'{$this->ticket->status}'** by `{$updaterName}`.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->action('View Ticket', url('/helpdesk/'.$this->ticket->id))
            ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'  => $this->ticket->id,
            'title'      => $this->ticket->title,
            'status'     => $this->ticket->status,
            'updated_by' => $this->updater->name,
            'message'    => "Ticket #{$this->ticket->id} status updated to '{$this->ticket->status}'.",
            'url'        => url('/helpdesk/'.$this->ticket->id),
        ];
    }
}
