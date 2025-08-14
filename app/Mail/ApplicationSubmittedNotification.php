<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
     */
    public EmailApplication|LoanApplication $application;

    /**
     * EDITED: The User model instance for the approver receiving the email.
     */
    public User $approver;

    /**
     * Optional URL to review the application (e.g., approval detail page).
     */
    public ?string $reviewUrl;

    /**
     * EDITED: Create a new message instance.
     * The constructor now accepts the approver's User model to pass their name to the view.
     *
     * @param  EmailApplication|LoanApplication  $application  The submitted application model.
     * @param  User  $approver  The officer who needs to approve the application.
     * @param  string|null  $reviewUrl  Optional URL for direct review.
     */
    public function __construct(EmailApplication|LoanApplication $application, User $approver, ?string $reviewUrl = null)
    {
        if (! $application instanceof EmailApplication && ! $application instanceof LoanApplication) {
            $errorMessage = 'ApplicationSubmittedNotification: Received invalid application type.';
            Log::error($errorMessage, ['type' => is_object($application) ? $application::class : gettype($application)]);
            throw new InvalidArgumentException($errorMessage.' Must be EmailApplication or LoanApplication.');
        }

        $this->application = $application->loadMissing('user');
        $this->approver = $approver;
        $this->reviewUrl = $reviewUrl;

        Log::debug('ApplicationSubmittedNotification: Mailable instance created.', [
            'application_id' => $this->application->id ?? 'N/A',
            'approver_id' => $this->approver->id,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->application instanceof EmailApplication
          ? __('Tindakan Diperlukan: Permohonan E-mel ICT Baru Dihantar')
          : __('Tindakan Diperlukan: Permohonan Pinjaman Peralatan ICT Baru Dihantar');

        return new Envelope(
            subject: $subject.' (#'.$this->application->id.')'
        );
    }

    /**
     * EDITED: Get the message content definition.
     * It now correctly passes the approver's name to the view.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-submitted-notification',
            with: [
                'application' => $this->application,
                'approverName' => $this->approver->name,
                'reviewUrl' => $this->reviewUrl,
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
