<?php

declare(strict_types=1);

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket; // Assuming your new Ticket model is named HelpdeskTicket
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketCreatedNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public HelpdeskTicket $ticket; // Changed to HelpdeskTicket based on the plan's naming
    public string $ticketUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\HelpdeskTicket  $ticket  The newly created ticket instance.
     * @param  string  $ticketUrl  The URL to view the ticket.
     */
    public function __construct(HelpdeskTicket $ticket, string $ticketUrl)
    {
        $this->ticket = $ticket->loadMissing('user'); // Load the user who created the ticket
        $this->ticketUrl = $ticketUrl;

        Log::info('TicketCreatedNotification: Mailable instance created.', [
            'ticket_id' => $this->ticket->id,
            'applicant_email' => $this->ticket->user->email ?? 'N/A',
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicantName = $this->ticket->user->name ?? 'Pengguna';
        $subject = sprintf('Tiket Sokongan IT Anda Berjaya Dicipta (#%s - %s)', $this->ticket->id, $this->ticket->subject);

        return new Envelope(
            to: [new Address($this->ticket->user->email, $applicantName)],
            subject: $subject,
            tags: ['helpdesk', 'ticket-created'],
            metadata: [
                'ticket_id' => (string) $this->ticket->id,
                'applicant_id' => (string) $this->ticket->user_id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.helpdesk.ticket-created',
            with: [
                'ticket' => $this->ticket,
                'ticketCreatorName' => $this->ticket->user->name ?? 'Pengguna',
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
