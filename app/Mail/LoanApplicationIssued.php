<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\User; // Potentially used for type hinting if user object is passed differently
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
 * Mailable notification sent to the applicant when their loan application equipment has been issued.
 * This email is intended to be queued for better performance.
 */
final class LoanApplicationIssued extends Mailable implements ShouldQueue
{
  use Queueable;
  use SerializesModels;

  /**
   * The loan application instance that has been issued.
   * Public for automatic availability in the Blade view.
   */
  public LoanApplication $loanApplication;

  /**
   * Create a new message instance.
   *
   * @param  \App\Models\LoanApplication  $loanApplication  The loan application model instance.
   */
  public function __construct(LoanApplication $loanApplication)
  {
    $this->loanApplication = $loanApplication->loadMissing('user'); // Eager load user for envelope
    $this->onQueue('emails'); // Specify a queue name as per original

    Log::info(
      'LoanApplicationIssued Mailable: Instance created.',
      ['loan_application_id' => $this->loanApplication->id]
    );
  }

  /**
   * Get the message envelope definition.
   * Defines the subject, sender, and recipients of the email.
   */
  public function envelope(): Envelope
  {
    $applicationId = $this->loanApplication->id ?? 'N/A';
    /** @phpstan-ignore-next-line nullsafe.neverNull, nullCoalesce.expr */
    $applicantName = $this->loanApplication->user?->full_name ??
      ($this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui');

    /** @phpstan-ignore-next-line nullsafe.neverNull */
    $recipientEmail = $this->loanApplication->user?->email;
    $toAddresses = [];

    if ($recipientEmail) {
      $toAddresses[] = new Address($recipientEmail, $applicantName);
      Log::info(
        "LoanApplicationIssued Mailable: Recipient identified for Loan Application ID: {$applicationId}.",
        ['recipient_email' => $recipientEmail]
      );
    } else {
      Log::warning(
        "LoanApplicationIssued Mailable: Recipient email not found for Loan Application ID: {$applicationId}. Notification cannot be sent.",
        [
          'loan_application_id' => $applicationId,
          'applicant_user_id' => $this->loanApplication->user_id ?? 'N/A',
        ]
      );
      // Consider throwing an exception or a more robust error handling if sending is critical
    }

    $subject = "Notifikasi Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #{$applicationId} - {$applicantName})";

    Log::info('LoanApplicationIssued Mailable: Preparing envelope.', [
      'loan_application_id' => $applicationId,
      'subject' => $subject,
      'to_recipients' => $toAddresses,
    ]);

    return new Envelope(
      to: $toAddresses,
      subject: $subject,
      // from: new Address(config('mail.from.address'), config('mail.from.name')), // Example from original
      // tags: ['loan-application', 'issued-notification'], // Example tags from original
      metadata: [
        'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
        'applicant_user_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
      ]
    );
  }

  /**
   * Get the message content definition.
   * Defines the Blade view and data passed to the view.
   */
  public function content(): Content
  {
    Log::info('LoanApplicationIssued Mailable: Preparing content.', [
      'loan_application_id' => $this->loanApplication->id ?? 'N/A',
      'view' => 'emails.loan-application-issued',
    ]);

    return new Content(
      view: 'emails.loan-application-issued'
      // 'loanApplication' is automatically available due to public property
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
