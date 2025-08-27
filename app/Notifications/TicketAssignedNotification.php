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
        return (new MailMessage())
            ->subject("Helpdesk Ticket #{$this->ticket->id} Assigned to You")
            ->greeting("Dear {$notifiable->name},")
            ->line("Helpdesk ticket **#{$this->ticket->id}** (`{$this->ticket->title}`) has been assigned to you by `{$this->assigner->name}`.")
            ->line("Applicant: {$this->ticket->applicant->name}")
            ->line("Current Status: {$this->ticket->status}")
            ->action('View Ticket', url('/helpdesk/' . $this->ticket->id))
            ->line('Please review the ticket and take necessary action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'   => $this->ticket->id,
            'title'       => $this->ticket->title,
            'assigned_by' => $this->assigner->name,
            'message'     => "Ticket #{$this->ticket->id} assigned to you.",
            'url'         => url('/helpdesk/' . $this->ticket->id),
        ];
    }
}
