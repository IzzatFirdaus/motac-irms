<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment; // For attachments() method signature
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

  /**
   * The user model instance receiving the email (applicant).
   * Public for automatic availability in the Blade view.
   */
  public User $user;

  /**
   * The provisioned MOTAC email address for the user.
   * Public for automatic availability in the Blade view.
   */
  public string $motacEmail;

  /**
   * The initial password for the new MOTAC email account.
   * Public for automatic availability in the Blade view.
   * IMPORTANT: The recipient should be strongly advised to change this password immediately.
   */
  public string $password;

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
    // $this->onQueue('emails'); // Consider adding if a specific queue is desired

    Log::info('WelcomeEmail Mailable: Instance created.', [
      'user_id' => $this->user->id,
      'motac_email' => $this->motacEmail, // Password is intentionally not logged here for security
    ]);
  }

  /**
   * Get the message envelope.
   * Defines the sender, recipient, and subject of the email.
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
      // Consider how to handle this failure, e.g., notify admin
    }

    $subject = 'Selamat Datang ke MOTAC ICT - Akaun E-mel Anda Disediakan';

    // Security Note for metadata: Including the password in metadata might expose it in logs
    // depending on the mail driver and logging setup. The original code had it,
    // and the design doc mentions it. Retained as per original, but with caution.
    // Ideally, avoid logging/storing plaintext passwords anywhere beyond the email body itself.
    $metadata = [
      'user_id' => (string) ($this->user->id ?? 'unknown'),
      'motac_email' => $this->motacEmail,
    ];
    if (!empty($this->password)) { // Only add if password exists
      $metadata['password_included'] = 'true'; // Indicate password was sent, not the password itself
      // Original metadata: 'password' => $this->password (Consider risks)
    }


    Log::info('WelcomeEmail Mailable: Preparing envelope.', [
      'user_id' => $this->user->id,
      'subject' => $subject,
      'to_recipients' => $toAddresses,
    ]);

    return new Envelope(
      to: $toAddresses,
      subject: $subject,
      tags: ['welcome-email', 'email-provisioning'], // As per original
      metadata: $metadata
    );
  }

  /**
   * Get the message content definition.
   * Defines the Blade view that will render the email body and passes data to it.
   */
  public function content(): Content
  {
    Log::info('WelcomeEmail Mailable: Preparing content.', [
      'user_id' => $this->user->id ?? 'N/A',
      'view' => 'emails.welcome',
    ]);

    return new Content(
      view: 'emails.welcome'
      // 'user', 'motacEmail', and 'password' are automatically available due to public properties
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment> An array of Attachment objects.
   */
  public function attachments(): array
  {
    return []; // No attachments by default
  }
}
