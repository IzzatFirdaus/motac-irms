<?php

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEscalatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public HelpdeskTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Helpdesk Ticket Escalated'))
            ->line(__('A helpdesk ticket has been escalated due to SLA breach.'))
            ->line(__('Ticket ID: :id', ['id' => $this->ticket->id]))
            ->line(__('Title: :title', ['title' => $this->ticket->title]))
            ->action(__('View Ticket'), route('helpdesk.tickets.show', $this->ticket->id));
    }
}
