<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment; // For attachments() method signature
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Ensure Log facade is imported

/**
 * Mailable notification sent when an email provisioning process fails.
 * Intended for IT administrators or relevant support staff.
 */
final class ProvisioningFailedNotification extends Mailable implements ShouldQueue
{
  use Queueable;
  use SerializesModels;

  /**
   * The email application for which provisioning failed.
   * Public for automatic availability in the Blade view.
   */
  public EmailApplication $application;

  /**
   * The reason for the provisioning failure.
   * Public for automatic availability in the Blade view.
   */
  public string $reason;

  /**
   * The admin user who might have attempted the provisioning or is relevant to the context.
   * Public for automatic availability in the Blade view.
   */
  public ?User $adminUser;

  /**
   * Create a new message instance.
   *
   * @param \App\Models\EmailApplication $application The email application instance.
   * @param string $reason The reason for the provisioning failure.
   * @param \App\Models\User|null $adminUser The admin user associated with the provisioning attempt (optional).
   */
  public function __construct(EmailApplication $application, string $reason, ?User $adminUser = null)
  {
    $this->application = $application->loadMissing('user'); // Eager load applicant user for envelope/view
    $this->reason = $reason;
    $this->adminUser = $adminUser; // Could also load relationships on adminUser if needed in view

    Log::info('ProvisioningFailedNotification Mailable: Instance created.', [
      'application_id' => $this->application->id,
      'reason' => $this->reason,
      'admin_user_id' => $this->adminUser?->id,
    ]);
  }

  /**
   * Get the message envelope.
   * Defines the subject and other envelope properties.
   * Recipients should be set when sending this Mailable (e.g., to IT Admin group).
   */
  public function envelope(): Envelope
  {
    $applicationId = $this->application->id ?? 'N/A';
    $applicantName = $this->application->user?->name ?? 'Pemohon Tidak Diketahui';

    $subject = __('Pemberitahuan Gagal Peruntukan E-mel: Permohonan #') .
      $applicationId .
      __(' oleh ') .
      $applicantName;

    Log::info('ProvisioningFailedNotification Mailable: Preparing envelope.', [
      'application_id' => $applicationId,
      'subject' => $subject,
    ]);

    return new Envelope(
      subject: $subject,
      // Note: Recipients (to, cc, bcc) are typically set when calling Mail::send() or Mail::to()->send()
      // e.g., Mail::to(config('motac.it_admin_emails'))->send(new ProvisioningFailedNotification(...));
      tags: [
        'email-provisioning',
        'failed',
        'application-' . $applicationId,
      ],
      metadata: [
        'application_id' => (string) $applicationId,
        'applicant_id' => (string) ($this->application->user_id ?? 'N/A'),
      ]
    );
  }

  /**
   * Get the message content definition.
   * Defines the Markdown Blade view and data passed to it.
   */
  public function content(): Content
  {
    Log::info('ProvisioningFailedNotification Mailable: Preparing content.', [
      'application_id' => $this->application->id ?? 'N/A',
      'view' => 'emails.provisioning-failed',
    ]);

    return new Content(
      markdown: 'emails.provisioning-failed', // Ensure this Markdown view exists
      // 'application', 'reason', and 'adminUser' are automatically available due to public properties
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
}
