<?php

namespace App\Mail;

use App\Models\Approval;
use App\Models\LoanApplication; // Ensure LoanApplication is imported
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Added for logging

class ApplicationNeedsAction extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public Approval $approvalTask;

    public User $approver;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Approval  $approvalTask  The approval task requiring action.
     * @param  \App\Models\User  $approver  The officer being notified.
     */
    public function __construct(Approval $approvalTask, User $approver)
    {
        // Eager load the approvable (which should now strictly be LoanApplication) and its user
        $this->approvalTask = $approvalTask->loadMissing([
            'approvable.user',
        ]);
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

        // Ensure the application is a LoanApplication before proceeding,
        // although with the refactoring, it should always be.
        if (!($application instanceof LoanApplication)) {
            Log::error('ApplicationNeedsAction Mailable: Non-LoanApplication approvable found.', [
                'approval_id' => $this->approvalTask->id,
                'approvable_type' => $this->approvalTask->approvable_type,
                'approvable_id' => $this->approvalTask->approvable_id,
            ]);
            // You might want to throw an exception or handle this more gracefully
            // depending on how strictly you want to enforce the type after refactoring.
            // For now, we'll proceed assuming it's a LoanApplication.
            $itemType = 'Permohonan Tidak Dikenali';
            $applicationId = 'N/A';
        } else {
            $itemType = 'Pinjaman ICT'; // Always 'Pinjaman ICT' as EmailApplication is removed
            $applicationId = $application->id;
        }

        $subject = sprintf('Tindakan Diperlukan: Permohonan %s #%s', $itemType, $applicationId);

        Log::info('ApplicationNeedsAction Mailable: Preparing envelope.', [
            'approval_id' => $this->approvalTask->id,
            'subject' => $subject,
            'to_recipient' => $this->approver->email,
        ]);

        return new Envelope(
            subject: $subject,
            to: [$this->approver->email], // Sending to the approver as an array
            tags: ['approval', 'loan-application'], // Added specific tags
            metadata: [
                'approval_id' => (string) ($this->approvalTask->id ?? 'unknown'),
                'approvable_type' => $this->approvalTask->approvable_type ?? 'unknown',
                'approvable_id' => (string) ($this->approvalTask->approvable_id ?? 'unknown'),
                'approver_id' => (string) ($this->approver->id ?? 'unknown'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $application = $this->approvalTask->approvable; // This should now be a LoanApplication

        // Ensure the correct route is used for LoanApplication approvals
        $reviewUrl = route('approvals.show', $this->approvalTask->id);

        Log::info('ApplicationNeedsAction Mailable: Preparing content.', [
            'approval_id' => $this->approvalTask->id,
            'view' => 'emails.application-needs-action',
            'review_url' => $reviewUrl,
        ]);

        return new Content(
            view: 'emails.application-needs-action',
            with: [
                'approverName' => $this->approver->name,
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
