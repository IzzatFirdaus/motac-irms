<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
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
 * Mailable class for sending a reminder when loaned ICT equipment is overdue.
 * This email is intended to be queued for better performance.
 */
final class LoanApplicationOverdueReminder extends Mailable implements ShouldQueue
{
  use Queueable;
  use SerializesModels;

  /**
   * The loan application instance that is overdue.
   * Public for automatic availability in the Blade view.
   */
  public LoanApplication $loanApplication;

  /**
   * Create a new message instance.
   *
   * @param  \App\Models\LoanApplication  $loanApplication  The overdue loan application model instance.
   */
  public function __construct(LoanApplication $loanApplication)
  {
    $this->loanApplication = $loanApplication->loadMissing('user'); // Eager load user for envelope
    // $this->onQueue('emails'); // Kept commented as per original, uses default queue

    Log::info(
      'LoanApplicationOverdueReminder Mailable: Instance created.',
      ['loan_application_id' => $this->loanApplication->id]
    );
  }

  /**
   * Get the message envelope.
   * Defines the subject, sender, and recipients of the email.
   */
  public function envelope(): Envelope
  {
    $applicationId = $this->loanApplication->id ?? 'N/A';
    /** @phpstan-ignore-next-line nullCoalesce.expr, nullsafe.neverNull */
    $applicantName = $this->loanApplication->user?->full_name ??
      ($this->loanApplication->user?->name ?? 'Pemohon');

    /** @phpstan-ignore-next-line nullsafe.neverNull */
    $recipientEmail = $this->loanApplication->user?->email;
    $toAddresses = [];

    if ($recipientEmail) {
      $toAddresses[] = new Address($recipientEmail, $applicantName);
      Log::info(
        "LoanApplicationOverdueReminder Mailable: Recipient identified for Loan Application ID: {$applicationId}.",
        ['recipient_email' => $recipientEmail]
      );
    } else {
      Log::error(
        "LoanApplicationOverdueReminder Mailable: Recipient email not found for Loan Application ID: {$applicationId}. Notification cannot be sent.",
        [
          'loan_application_id' => $applicationId,
          'applicant_user_id' => $this->loanApplication->user_id ?? 'N/A',
        ]
      );
    }

    $subject = "Tindakan Diperlukan: Peringatan Peralatan Pinjaman ICT Lewat Dipulangkan (Permohonan #{$applicationId} - {$applicantName})";

    Log::info(
      'LoanApplicationOverdueReminder Mailable: Preparing envelope.',
      [
        'loan_application_id' => $applicationId,
        'subject' => $subject,
        'to_recipients' => $toAddresses,
      ]
    );

    return new Envelope(
      to: $toAddresses,
      subject: $subject,
      // from: new Address(config('mail.from.address'), config('mail.from.name')), // Example from original
      // tags: ['loan-application', 'overdue-reminder'], // Example from original
      metadata: [
        'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
        'applicant_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
      ]
    );
  }

  /**
   * Get the message content definition.
   * Defines the Blade view and data passed to the view.
   */
  public function content(): Content
  {
    Log::info(
      'LoanApplicationOverdueReminder Mailable: Preparing content.',
      [
        'loan_application_id' => $this->loanApplication->id ?? 'N/A',
        'view' => 'emails.loan-application-overdue-reminder',
      ]
    );

    return new Content(
      view: 'emails.loan-application-overdue-reminder'
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
