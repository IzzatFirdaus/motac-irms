<?php

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public HelpdeskTicket $ticket;

    public string $ticketUrl;

    public function __construct(HelpdeskTicket $ticket, string $ticketUrl)
    {
        $this->ticket    = $ticket->loadMissing('user');
        $this->ticketUrl = $ticketUrl;
    }

    public function envelope(): Envelope
    {
        $applicantName = $this->ticket->user->name ?? 'Pengguna';
        $subject       = __('Tiket Sokongan IT Anda Berjaya Dicipta (#:id - :subject)', [
            'id'      => $this->ticket->id,
            'subject' => $this->ticket->subject,
        ]);

        return new Envelope(
            to: [new Address($this->ticket->user->email, $applicantName)],
            subject: $subject,
            tags: ['helpdesk', 'ticket-created'],
            metadata: [
                'ticket_id'    => (string) $this->ticket->id,
                'applicant_id' => (string) $this->ticket->user_id,
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.helpdesk.ticket-created',
            with: [
                'ticket'            => $this->ticket,
                'ticketCreatorName' => $this->ticket->user->name ?? 'Pengguna',
                'ticketUrl'         => $this->ticketUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
