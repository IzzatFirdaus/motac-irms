<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model; // Kept for general Model type hint if ever needed, though property is now more specific.
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable notification sent when a new application is submitted.
 * Intended to be sent to the next required approver or relevant staff.
 */
final class ApplicationSubmittedNotification extends Mailable implements ShouldQueue
{
  use Queueable;
  use SerializesModels;

  /**
   * The application instance (EmailApplication or LoanApplication) that was submitted.
   * This property is public so it's automatically available in the Blade view.
   */
  public EmailApplication|LoanApplication $application;

  /**
   * Optional URL to review the application (e.g., approval detail page).
   * This property is public so it's automatically available in the Blade view.
   */
  public ?string $reviewUrl;

  /**
   * Create a new message instance.
   *
   * @param  \App\Models\EmailApplication|\App\Models\LoanApplication  $application  The submitted application model instance.
   * @param  string|null  $reviewUrl  Optional URL for direct review (e.g., link to the approval task).
   */
  public function __construct(EmailApplication|LoanApplication $application, ?string $reviewUrl = null)
  {
    if (! ($application instanceof EmailApplication || $application instanceof LoanApplication)) {
      $errorMessage = 'ApplicationSubmittedNotification: Received invalid application type.';
      Log::error($errorMessage, [
        'type' => is_object($application) ? $application::class : gettype($application),
        'application_id' => $application->id ?? 'N/A',
      ]);
      throw new \InvalidArgumentException($errorMessage . ' Must be EmailApplication or LoanApplication.');
    }

    // Eager load necessary relationships
    $application->loadMissing(['user']);

    if ($application instanceof EmailApplication) {
      $application->loadMissing(['groupMembers']); // As per original
    } elseif ($application instanceof LoanApplication) {
      $application->loadMissing(['applicationItems.equipment']); // As per original
    }

    $this->application = $application;
    $this->reviewUrl = $reviewUrl;

    Log::debug('ApplicationSubmittedNotification: Mailable instance created.', [
      'application_id' => $this->application->id ?? 'N/A',
      'application_type' => $this->application::class,
      'review_url' => $this->reviewUrl ?? 'N/A',
    ]);
  }

  /**
   * Get the message envelope.
   * Defines the subject and other envelope properties.
   */
  public function envelope(): Envelope
  {
    $subject = $this->application instanceof EmailApplication
      ? __('Tindakan Diperlukan: Permohonan Emel ICT Baru Dihantar')
      : __('Tindakan Diperlukan: Permohonan Pinjaman Peralatan ICT Baru Dihantar');

    if ($this->application->user) {
      $subject .= ' oleh ' . $this->application->user->name;
    }
    if ($this->application->id) {
      $subject .= ' (#' . $this->application->id . ')';
    }

    return new Envelope(
      subject: $subject
      // from: new \Illuminate\Mail\Mailables\Address('noreply@yourdomain.com', 'HRMS System'), // Example from original
    );
  }

  /**
   * Get the message content definition.
   * Specifies the Blade view for the email.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.application-submitted-notification'
      // with: Pass data to view if not using public properties
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return []; // No attachments by default
  }

  /**
   * Get the headers for the message.
   * Note: System Design Rev. 3 indicated this Mailable's headers() return type
   * was changed to a standard array.
   *
   * @return array<int|string, string|array<string, string>>
   */
  public function headers(): array
  {
    return []; // No custom headers by default
  }
}
