<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
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
 * Mailable class for notifying the applicant when their loaned ICT equipment has been returned.
 * This email is intended to be queued for better performance.
 */
final class LoanApplicationReturned extends Mailable implements ShouldQueue
{
  use Queueable;
  use SerializesModels;

  /**
   * The loan application instance.
   * Public for automatic availability in the Blade view.
   */
  public LoanApplication $loanApplication;

  /**
   * The loan transaction instance for the returned item(s).
   * Public for automatic availability in the Blade view.
   */
  public LoanTransaction $loanTransaction;

  /**
   * Create a new message instance.
   *
   * @param  \App\Models\LoanApplication  $loanApplication  The loan application model instance.
   * @param  \App\Models\LoanTransaction  $loanTransaction  The loan transaction model instance for the returned item.
   */
  public function __construct(LoanApplication $loanApplication, LoanTransaction $loanTransaction)
  {
    $this->loanApplication = $loanApplication->loadMissing('user'); // Eager load user for envelope
    $this->loanTransaction = $loanTransaction->loadMissing('equipment'); // Eager load equipment for envelope
    // $this->onQueue('emails'); // Consider adding if a specific queue is desired

    Log::info('LoanApplicationReturned Mailable: Instance created.', [
      'loan_application_id' => $this->loanApplication->id,
      'loan_transaction_id' => $this->loanTransaction->id,
    ]);
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
    $equipmentDetails = $this->loanTransaction->equipment?->model ??
      ($this->loanTransaction->equipment?->tag_id ?? 'Peralatan ICT');

    /** @phpstan-ignore-next-line nullsafe.neverNull */
    $recipientEmail = $this->loanApplication->user?->email;
    $toAddresses = [];

    if ($recipientEmail) {
      $toAddresses[] = new Address($recipientEmail, $applicantName);
      Log::info(
        "LoanApplicationReturned Mailable: Recipient identified for Loan Application ID: {$applicationId}.",
        ['recipient_email' => $recipientEmail]
      );
    } else {
      Log::error(
        "LoanApplicationReturned Mailable: Recipient email not found for Loan Application ID: {$applicationId}. Notification cannot be sent.",
        [
          'loan_application_id' => $applicationId,
          'applicant_user_id' => $this->loanApplication->user_id ?? 'N/A',
        ]
      );
    }

    $subject = "Notifikasi {$equipmentDetails} Telah Dipulangkan (Permohonan #{$applicationId} - {$applicantName})";

    Log::info('LoanApplicationReturned Mailable: Preparing email envelope.', [
      'loan_application_id' => $applicationId,
      'subject' => $subject,
      'to_recipients' => $toAddresses,
    ]);

    return new Envelope(
      to: $toAddresses,
      subject: $subject,
      tags: ['loan-application', 'returned-notification'], // As per original
      metadata: [
        'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
        'loan_transaction_id' => (string) ($this->loanTransaction->id ?? 'unknown'),
        'applicant_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
        'equipment_id' => (string) ($this->loanTransaction->equipment_id ?? 'unknown'),
      ]
      // from: new Address(config('mail.from.address'), config('mail.from.name')), // Example from original
    );
  }

  /**
   * Get the message content definition.
   * Defines the Blade view and data passed to the view.
   */
  public function content(): Content
  {
    Log::info('LoanApplicationReturned Mailable: Preparing email content.', [
      'loan_application_id' => $this->loanApplication->id ?? 'N/A',
      'loan_transaction_id' => $this->loanTransaction->id ?? 'N/A',
      'view' => 'emails.loan-application-returned',
    ]);

    return new Content(
      view: 'emails.loan-application-returned'
      // 'loanApplication' and 'loanTransaction' are automatically available due to public properties
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
