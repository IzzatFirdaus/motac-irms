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
            ? "Your Helpdesk Ticket #{$this->ticket->id} Has Been Created"
            : "New Helpdesk Ticket #{$this->ticket->id} Created";

        $greeting = ($this->recipientType === 'applicant')
            ? "Dear {$notifiable->name},"
            : 'Hello,';

        $introLine = ($this->recipientType === 'applicant')
            ? "Your helpdesk ticket **#{$this->ticket->id}** with the subject `{$this->ticket->title}` has been successfully created."
            : "A new helpdesk ticket **#{$this->ticket->id}** has been submitted by `{$this->ticket->applicant->name}` with the subject `{$this->ticket->title}`.";

        return (new MailMessage())
            ->subject($subject)
            ->greeting($greeting)
            ->line($introLine)
            ->line("Description: {$this->ticket->description}")
            ->action('View Ticket', url('/helpdesk/' . $this->ticket->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title'     => $this->ticket->title,
            'status'    => $this->ticket->status,
            'message'   => ($this->recipientType === 'applicant')
                ? "Your helpdesk ticket #{$this->ticket->id} has been created."
                : "New helpdesk ticket #{$this->ticket->id} submitted.",
            'url' => url('/helpdesk/' . $this->ticket->id),
        ];
    }
}
