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

class TicketClosedNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public HelpdeskTicket $ticket;
    public string $ticketUrl;
    public ?string $resolutionNotes; // Optional notes provided upon closure

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\HelpdeskTicket  $ticket  The ticket that was closed.
     * @param  string  $ticketUrl  The URL to view the ticket.
     * @param  string|null  $resolutionNotes  Optional notes on how the ticket was resolved.
     */
    public function __construct(HelpdeskTicket $ticket, string $ticketUrl, ?string $resolutionNotes = null)
    {
        $this->ticket = $ticket->loadMissing('user'); // Load the user who created the ticket
        $this->ticketUrl = $ticketUrl;
        $this->resolutionNotes = $resolutionNotes;

        Log::info('TicketClosedNotification: Mailable instance created.', [
            'ticket_id' => $this->ticket->id,
            'applicant_email' => $this->ticket->user->email ?? 'N/A',
            'resolution_notes_present' => ! is_null($this->resolutionNotes),
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicantName = $this->ticket->user->name ?? 'Pengguna';
        $subject = sprintf('Tiket Sokongan IT Anda Telah Ditutup (#%s - %s)', $this->ticket->id, $this->ticket->subject);

        return new Envelope(
            to: [new Address($this->ticket->user->email, $applicantName)],
            subject: $subject,
            tags: ['helpdesk', 'ticket-closed'],
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
            view: 'emails.helpdesk.ticket-closed', // You will need to create this Blade view
            with: [
                'ticket' => $this->ticket,
                'ticketUrl' => $this->ticketUrl,
                'resolutionNotes' => $this->resolutionNotes,
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
