<?php

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable for notifying next approver that a new loan application is submitted.
 */
class ApplicationSubmittedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LoanApplication $application;
    public User $approver;
    public ?string $reviewUrl;

    public function __construct(LoanApplication $application, User $approver, ?string $reviewUrl = null)
    {
        $this->application = $application->loadMissing('user', 'loanApplicationItems');
        $this->approver = $approver;
        $this->reviewUrl = $reviewUrl;

        Log::debug('ApplicationSubmittedNotification: Mailable instance created.', [
            'application_id' => $this->application->id ?? 'N/A',
            'approver_id' => $this->approver->id,
        ]);
    }

    public function envelope(): Envelope
    {
        $subject = __('Tindakan Diperlukan: Permohonan Pinjaman Peralatan ICT Baru Dihantar') . ' (#' . $this->application->id . ')';

        return new Envelope(
            subject: $subject,
            to: [$this->approver->email]
        );
    }

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

    public function attachments(): array
    {
        return [];
    }
}
