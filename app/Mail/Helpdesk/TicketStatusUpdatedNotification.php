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

class TicketStatusUpdatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public HelpdeskTicket $ticket;

    public string $oldStatus;

    public string $newStatus;

    public ?string $comment = null;

    public string $ticketUrl;

    public function __construct(HelpdeskTicket $ticket, string $oldStatus, string $newStatus, ?string $comment = null)
    {
        $this->ticket    = $ticket->loadMissing(['user', 'category', 'priority']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->comment   = $comment;
        $this->ticketUrl = route('helpdesk.view', $this->ticket->id);
    }

    public function envelope(): Envelope
    {
        $toAddresses = [];
        if ($this->ticket->user) {
            $toAddresses[] = new Address($this->ticket->user->email, $this->ticket->user->name);
        }

        $subject = __('Sokongan IT: Status Tiket #:id Dikemas Kini Kepada :status - :subject', [
            'id'      => $this->ticket->id,
            'status'  => $this->newStatus,
            'subject' => $this->ticket->subject,
        ]);

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            tags: ['helpdesk', 'ticket-status-update'],
            metadata: [
                'ticket_id'  => (string) $this->ticket->id,
                'user_id'    => (string) $this->ticket->user_id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'ticket_url' => $this->ticketUrl,
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.helpdesk.ticket-status-updated',
            with: [
                'ticket'    => $this->ticket,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'comment'   => $this->comment,
                'ticketUrl' => $this->ticketUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
