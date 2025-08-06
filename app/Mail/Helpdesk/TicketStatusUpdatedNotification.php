<?php

declare(strict_types=1);

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket; // Updated: Assuming your new Ticket model is in App\Models\HelpdeskTicket
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class TicketStatusUpdatedNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public HelpdeskTicket $ticket; // Updated: Changed type hint to HelpdeskTicket
    public string $oldStatus;
    public string $newStatus;
    public ?string $comment = null; // Optional comment related to the status update
    public string $ticketUrl; // Added: To store the generated URL

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\HelpdeskTicket  $ticket  The ticket instance that had its status updated.
     * @param  string  $oldStatus  The previous status of the ticket.
     * @param  string  $newStatus  The new status of the ticket.
     * @param  string|null  $comment  Optional comment related to the status update.
     */
    public function __construct(HelpdeskTicket $ticket, string $oldStatus, string $newStatus, ?string $comment = null)
    {
        $this->ticket = $ticket->loadMissing(['user', 'category', 'priority']); // Eager load relationships
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->comment = $comment;
        // Generate the ticket URL here, as it's needed in the content and potentially envelope metadata
        $this->ticketUrl = route('helpdesk.view', $this->ticket->id); // Updated: Use the named route 'helpdesk.view'

        Log::info('TicketStatusUpdatedNotification Mailable: Instance created.', [
            'ticket_id' => $this->ticket->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'ticket_url' => $this->ticketUrl, // Log the generated URL
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Ensure user exists before trying to access properties
        $toAddresses = [];
        if ($this->ticket->user) {
            $toAddresses[] = new Address($this->ticket->user->email, $this->ticket->user->name);
        } else {
            Log::warning('TicketStatusUpdatedNotification: No user found for ticket.', ['ticket_id' => $this->ticket->id]);
        }


        $subject = sprintf('Sokongan IT: Status Tiket #%s Dikemas Kini Kepada %s - %s',
            $this->ticket->id,
            $this->newStatus,
            $this->ticket->subject
        );

        Log::info('TicketStatusUpdatedNotification Mailable: Preparing envelope.', [
            'ticket_id' => $this->ticket->id,
            'subject' => $subject,
            'to_recipients' => collect($toAddresses)->map(fn($address) => $address->address)->implode(', '),
        ]);

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            tags: ['helpdesk', 'ticket-status-update'],
            metadata: [
                'ticket_id' => (string) ($this->ticket->id ?? 'unknown'),
                'user_id' => (string) ($this->ticket->user_id ?? 'unknown'),
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'ticket_url' => $this->ticketUrl, // Include URL in metadata for tracking/debugging
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('TicketStatusUpdatedNotification Mailable: Preparing content.', [
            'ticket_id' => $this->ticket->id ?? 'N/A',
            'view' => 'emails.helpdesk.ticket-status-updated',
        ]);

        return new Content(
            view: 'emails.helpdesk.ticket-status-updated',
            with: [
                'ticket' => $this->ticket,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'comment' => $this->comment,
                'ticketUrl' => $this->ticketUrl, // Pass the generated URL
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
