<?php

namespace App\Mail;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationNeedsAction extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Approval $approvalTask;
    public User $approver;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Approval $approvalTask The approval task requiring action.
     * @param \App\Models\User $approver The officer being notified.
     */
    public function __construct(Approval $approvalTask, User $approver)
    {
        $this->approvalTask = $approvalTask->loadMissing([
            'approvable.user', // Eager load the applicant
        ]);
        $this->approver = $approver;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $application = $this->approvalTask->approvable;
        $itemType = $application instanceof \App\Models\LoanApplication ? 'Pinjaman ICT' : 'E-mel/ID';
        $subject = "Tindakan Diperlukan: Permohonan {$itemType} #{$application->id}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // This will render the 'emails.application-needs-action' view.
        return new Content(
            view: 'emails.application-needs-action',
            with: [
                'approverName' => $this->approver->name,
                'application' => $this->approvalTask->approvable,
                'reviewUrl' => route('approvals.show', $this->approvalTask->id),
            ],
        );
    }
}
