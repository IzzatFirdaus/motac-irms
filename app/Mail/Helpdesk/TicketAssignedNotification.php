<?php

declare(strict_types=1);

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket; // Assuming your new Ticket model is named HelpdeskTicket
use App\Models\User; // For the assigned agent
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketAssignedNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public HelpdeskTicket $ticket;
    public User $assignedTo; // The user (agent) to whom the ticket is assigned
    public string $ticketUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\HelpdeskTicket  $ticket  The ticket that was assigned.
     * @param  \App\Models\User  $assignedTo  The user (agent) who was assigned the ticket.
     * @param  string  $ticketUrl  The URL to view the ticket.
     */
    public function __construct(HelpdeskTicket $ticket, User $assignedTo, string $ticketUrl)
    {
        $this->ticket = $ticket->loadMissing(['user', 'category', 'priority']); // Load related data for the email
        $this->assignedTo = $assignedTo;
        $this->ticketUrl = $ticketUrl;

        Log::info('TicketAssignedNotification: Mailable instance created.', [
            'ticket_id' => $this->ticket->id,
            'assigned_to_user_id' => $this->assignedTo->id,
            'assigned_to_email' => $this->assignedTo->email,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = sprintf('Tiket Sokongan IT Ditugaskan Kepada Anda (#%s - %s)', $this->ticket->id, $this->ticket->subject);

        return new Envelope(
            to: [new Address($this->assignedTo->email, $this->assignedTo->name)],
            subject: $subject,
            tags: ['helpdesk', 'ticket-assigned'],
            metadata: [
                'ticket_id' => (string) $this->ticket->id,
                'assigned_to_user_id' => (string) $this->assignedTo->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.helpdesk.ticket-assigned',
            with: [
                'ticket' => $this->ticket,
                'assignedToName' => $this->assignedTo->name,
                'ticketUrl' => $this->ticketUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
