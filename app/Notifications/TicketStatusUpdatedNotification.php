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
        $subject     = sprintf("Helpdesk Ticket #%d Status Updated to '%s'", $this->ticket->id, $this->ticket->status);
        $greeting    = sprintf('Dear %s,', $notifiable->name);
        $updaterName = $this->updater->name;

        $message = '';
        if ($this->recipientType === 'applicant') {
            $message = sprintf("The status of your helpdesk ticket **#%d** (`%s`) has been updated to **'%s'** by `%s`.", $this->ticket->id, $this->ticket->title, $this->ticket->status, $updaterName);
            if ($this->ticket->status === 'closed' && $this->ticket->resolution_notes) {
                $message .= '

Resolution Notes: '.$this->ticket->resolution_notes;
            }
        } elseif ($this->recipientType === 'agent') {
            $message = sprintf("The status of ticket **#%d** (`%s`) has been updated to **'%s'** by `%s`.", $this->ticket->id, $this->ticket->title, $this->ticket->status, $updaterName);
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
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'  => $this->ticket->id,
            'title'      => $this->ticket->title,
            'status'     => $this->ticket->status,
            'updated_by' => $this->updater->name,
            'message'    => sprintf("Ticket #%d status updated to '%s'.", $this->ticket->id, $this->ticket->status),
            'url'        => url('/helpdesk/'.$this->ticket->id),
        ];
    }
}
