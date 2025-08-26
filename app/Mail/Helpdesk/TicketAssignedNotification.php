<?php

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public HelpdeskTicket $ticket;

    public User $assignedTo;

    public string $ticketUrl;

    public function __construct(HelpdeskTicket $ticket, User $assignedTo, string $ticketUrl)
    {
        $this->ticket     = $ticket->loadMissing(['user', 'category', 'priority']);
        $this->assignedTo = $assignedTo;
        $this->ticketUrl  = $ticketUrl;
    }

    public function envelope(): Envelope
    {
        $subject = __('Tiket Sokongan IT Ditugaskan Kepada Anda (#:id - :subject)', [
            'id'      => $this->ticket->id,
            'subject' => $this->ticket->subject,
        ]);

        return new Envelope(
            to: [new Address($this->assignedTo->email, $this->assignedTo->name)],
            subject: $subject,
            tags: ['helpdesk', 'ticket-assigned'],
            metadata: [
                'ticket_id'           => (string) $this->ticket->id,
                'assigned_to_user_id' => (string) $this->assignedTo->id,
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.helpdesk.ticket-assigned',
            with: [
                'ticket'         => $this->ticket,
                'assignedToName' => $this->assignedTo->name,
                'ticketUrl'      => $this->ticketUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
