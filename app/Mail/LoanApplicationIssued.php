<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable notification sent to the applicant when their loan application equipment has been issued.
 * This email is intended to be queued for better performance.
 */
final class LoanApplicationIssued extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public EloquentCollection $issueTransactions; // Property to hold issue transactions

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\LoanApplication  $loanApplication  The loan application model instance.
     */
    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication->loadMissing([
            'user', // For applicant details
            'transactions' => function ($query) {
                $query->where('type', LoanTransaction::TYPE_ISSUE)
                    ->with('loanTransactionItems.equipment'); // Eager load items and their equipment
            },
        ]);

        // Assign the filtered and loaded issue transactions to the public property
        $this->issueTransactions = $this->loanApplication->transactions
            ->where('type', LoanTransaction::TYPE_ISSUE);

        $this->onQueue('emails'); // Specify a queue name

        Log::info(
            'LoanApplicationIssued Mailable: Instance created.',
            [
                'loan_application_id' => $this->loanApplication->id,
                'issue_transactions_count' => $this->issueTransactions->count(),
            ]
        );
    }

    /**
     * Get the message envelope definition.
     */
    public function envelope(): Envelope
    {
        $applicationId = $this->loanApplication->id ?? 'N/A';
        /** @phpstan-ignore-next-line nullsafe.neverNull, nullCoalesce.expr */
        $applicantName = $this->loanApplication->user?->full_name ??
                         ($this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui');

        /** @phpstan-ignore-next-line nullsafe.neverNull */
        $recipientEmail = $this->loanApplication->user?->email;
        $toAddresses = [];

        if ($recipientEmail) {
            $toAddresses[] = new Address($recipientEmail, $applicantName);
            Log::info(
                "LoanApplicationIssued Mailable: Recipient identified for Loan Application ID: {$applicationId}.",
                ['recipient_email' => $recipientEmail]
            );
        } else {
            Log::warning(
                "LoanApplicationIssued Mailable: Recipient email not found for Loan Application ID: {$applicationId}. Notification cannot be sent.",
                [
                    'loan_application_id' => $applicationId,
                    'applicant_user_id' => $this->loanApplication->user_id ?? 'N/A',
                ]
            );
        }

        $subject = "Notifikasi Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #{$applicationId} - {$applicantName})";

        Log::info('LoanApplicationIssued Mailable: Preparing envelope.', [
            'loan_application_id' => $applicationId,
            'subject' => $subject,
            'to_recipients' => count($toAddresses) > 0 ? $toAddresses[0]->address : 'N/A',
        ]);

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            metadata: [
                'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
                'applicant_user_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('LoanApplicationIssued Mailable: Preparing content.', [
            'loan_application_id' => $this->loanApplication->id ?? 'N/A',
            'view' => 'emails.loan-application-issued',
        ]);

        return new Content(
            view: 'emails.loan-application-issued',
            with: [
                'loanApplication' => $this->loanApplication,
                'issueTransactions' => $this->issueTransactions,
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
