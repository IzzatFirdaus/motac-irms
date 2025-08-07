<?php

namespace App\Mail;

use App\Models\Approval;
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
 * Mailable for notifying an approver about a pending approval action
 * for a LoanApplication.
 */
class ApplicationNeedsAction extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Approval $approvalTask;
    public User $approver;

    /**
     * Create a new message instance.
     */
    public function __construct(Approval $approvalTask, User $approver)
    {
        $this->approvalTask = $approvalTask->loadMissing(['approvable.user']);
        $this->approver = $approver;

        Log::info('ApplicationNeedsAction Mailable: Instance created.', [
            'approval_id' => $this->approvalTask->id,
            'approver_id' => $this->approver->id,
            'approvable_type' => $this->approvalTask->approvable_type,
            'approvable_id' => $this->approvalTask->approvable_id,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $application = $this->approvalTask->approvable;
        $subject = __('Tindakan Diperlukan: Permohonan Pinjaman ICT #') . ($application->id ?? 'N/A');

        return new Envelope(
            subject: $subject,
            to: [$this->approver->email],
            tags: ['approval', 'loan-application'],
            metadata: [
                'approval_id' => (string)($this->approvalTask->id ?? 'unknown'),
                'approvable_type' => $this->approvalTask->approvable_type ?? 'unknown',
                'approvable_id' => (string)($this->approvalTask->approvable_id ?? 'unknown'),
                'approver_id' => (string)($this->approver->id ?? 'unknown'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $application = $this->approvalTask->approvable;

        $reviewUrl = route('approvals.show', $this->approvalTask->id);

        return new Content(
            view: 'emails.application-needs-action',
            with: [
                'approvalTask' => $this->approvalTask,
                'approver' => $this->approver,
                'application' => $application,
                'reviewUrl' => $reviewUrl,
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
