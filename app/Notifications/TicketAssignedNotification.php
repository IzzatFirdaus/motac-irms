<?php

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected HelpdeskTicket $ticket;

    protected User $assigner;

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpdeskTicket $ticket, User $assigner)
    {
        $this->ticket   = $ticket;
        $this->assigner = $assigner;
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
        return (new MailMessage)
            ->subject(sprintf('Helpdesk Ticket #%d Assigned to You', $this->ticket->id))
            ->greeting(sprintf('Dear %s,', $notifiable->name))
            ->line(sprintf('Helpdesk ticket **#%d** (`%s`) has been assigned to you by `%s`.', $this->ticket->id, $this->ticket->title, $this->assigner->name))
            ->line('Applicant: '.$this->ticket->applicant->name)
            ->line('Current Status: '.$this->ticket->status)
            ->action('View Ticket', url('/helpdesk/'.$this->ticket->id))
            ->line('Please review the ticket and take necessary action.');
    }

    /**
     * Get the array representation of the notification.
     *
    * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'   => $this->ticket->id,
            'title'       => $this->ticket->title,
            'assigned_by' => $this->assigner->name,
            'message'     => sprintf('Ticket #%d assigned to you.', $this->ticket->id),
            'url'         => url('/helpdesk/'.$this->ticket->id),
        ];
    }
}
