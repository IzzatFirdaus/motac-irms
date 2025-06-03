<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable notification sent when an email provisioning process fails.
 * Intended for IT administrators or relevant support staff.
 */
final class ProvisioningFailedNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public EmailApplication $application;
    public string $reason;
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
        $this->application = $application->loadMissing('user'); // Eager load applicant user
        $this->reason = $reason;
        $this->adminUser = $adminUser;

        Log::info('ProvisioningFailedNotification Mailable: Instance created.', [
            'application_id' => $this->application->id,
            'reason' => $this->reason,
            'admin_user_id' => $this->adminUser?->id,
        ]);
    }

    /**
     * Get the message envelope.
     * Recipients should be set when sending this Mailable (e.g., to IT Admin group).
     */
    public function envelope(): Envelope
    {
        $applicationId = $this->application->id ?? 'N/A';
        $applicantName = $this->application->user?->name ?? 'Pemohon Tidak Diketahui'; // Relies on 'user' being loaded

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
            markdown: 'emails.provisioning-failed',
            with: [
                'application' => $this->application,
                'reason' => $this->reason,
                'adminUser' => $this->adminUser,
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
