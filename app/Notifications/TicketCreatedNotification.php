<?php

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected HelpdeskTicket $ticket;

    protected string $recipientType; // 'applicant' or 'admin'

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpdeskTicket $ticket, string $recipientType)
    {
        $this->ticket        = $ticket;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Send via email and store in database
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = ($this->recipientType === 'applicant')
            ? sprintf('Your Helpdesk Ticket #%d Has Been Created', $this->ticket->id)
            : sprintf('New Helpdesk Ticket #%d Created', $this->ticket->id);

        $greeting = ($this->recipientType === 'applicant')
            ? sprintf('Dear %s,', $notifiable->name)
            : 'Hello,';

        $introLine = ($this->recipientType === 'applicant')
            ? sprintf('Your helpdesk ticket **#%d** with the subject `%s` has been successfully created.', $this->ticket->id, $this->ticket->title)
            : sprintf('A new helpdesk ticket **#%d** has been submitted by `%s` with the subject `%s`.', $this->ticket->id, $this->ticket->applicant->name, $this->ticket->title);

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($introLine)
            ->line('Description: '.$this->ticket->description)
            ->action('View Ticket', url('/helpdesk/'.$this->ticket->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title'     => $this->ticket->title,
            'status'    => $this->ticket->status,
            'message'   => ($this->recipientType === 'applicant')
                ? sprintf('Your helpdesk ticket #%d has been created.', $this->ticket->id)
                : sprintf('New helpdesk ticket #%d submitted.', $this->ticket->id),
            'url' => url('/helpdesk/'.$this->ticket->id),
        ];
    }
}
