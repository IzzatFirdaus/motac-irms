<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable class for sending a welcome email with newly provisioned MOTAC email credentials.
 * This email is intended to be queued for better performance.
 */
final class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $motacEmail;
    public string $password; // Initial password

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user  The user receiving the email (applicant).
     * @param  string  $motacEmail  The provisioned MOTAC email address for the user.
     * @param  string  $password  The initial password for the new MOTAC email account.
     */
    public function __construct(User $user, string $motacEmail, string $password)
    {
        $this->user = $user;
        $this->motacEmail = $motacEmail;
        $this->password = $password;

        Log::info('WelcomeEmail Mailable: Instance created.', [
            'user_id' => $this->user->id,
            'motac_email' => $this->motacEmail, // Password is intentionally not logged here for security
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        /** @phpstan-ignore-next-line nullsafe.neverNull, nullCoalesce.expr */
        $recipientEmail = $this->user->personal_email ?? $this->user->email;
        /** @phpstan-ignore-next-line nullCoalesce.expr, nullsafe.neverNull */
        $recipientName = $this->user->full_name ?? ($this->user->name ?? $this->user->email);

        $toAddresses = [];
        if ($recipientEmail) {
            $toAddresses[] = new Address($recipientEmail, $recipientName);
            Log::info(
                "WelcomeEmail Mailable: Recipient identified for User ID: {$this->user->id}.",
                ['recipient_email' => $recipientEmail]
            );
        } else {
            Log::error(
                "WelcomeEmail Mailable: Recipient email (personal or primary) not found for User ID: {$this->user->id}. Welcome email cannot be sent.",
                ['user_id' => $this->user->id]
            );
        }

        $subject = 'Selamat Datang ke MOTAC ICT - Akaun E-mel Anda Disediakan';

        // Security Note: Avoid putting the actual password in metadata to prevent accidental logging.
        $metadata = [
            'user_id' => (string) ($this->user->id ?? 'unknown'),
            'motac_email' => $this->motacEmail,
            'password_included_in_body' => !empty($this->password) ? 'true' : 'false',
        ];

        Log::info('WelcomeEmail Mailable: Preparing envelope.', [
            'user_id' => $this->user->id,
            'subject' => $subject,
            'to_recipients' => count($toAddresses) > 0 ? $toAddresses[0]->address : 'N/A',
        ]);

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            tags: ['welcome-email', 'email-provisioning'],
            metadata: $metadata
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('WelcomeEmail Mailable: Preparing content.', [
            'user_id' => $this->user->id ?? 'N/A',
            'view' => 'emails.welcome',
        ]);

        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'motacEmail' => $this->motacEmail,
                'password' => $this->password, // Password is sent to the view for the email body
            ]
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
