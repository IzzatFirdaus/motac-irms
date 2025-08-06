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
            'transactions' => function ($query): void {
                $query->where('type', LoanTransaction::TYPE_ISSUE)
                    ->with('loanTransactionItems.equipment'); // Eager load items and their equipment
            },
        ]);

        // Filter out only issue transactions if there are multiple transaction types
        $this->issueTransactions = $this->loanApplication->transactions->where('type', LoanTransaction::TYPE_ISSUE);

        Log::info('LoanApplicationIssued Mailable: Instance created.', [
            'loan_application_id' => $this->loanApplication->id,
            'issue_transactions_count' => $this->issueTransactions->count(),
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id ?? 'N/A';

        $toAddresses = [
            new Address($this->loanApplication->user->email, $applicantName),
        ];

        $subject = sprintf('Notifikasi Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #%s - %s)', $applicationId, $applicantName);

        Log::info('LoanApplicationIssued Mailable: Preparing envelope.', [
            'loan_application_id' => $applicationId,
            'subject' => $subject,
            'to_recipients' => $toAddresses !== [] ? $toAddresses[0]->address : 'N/A',
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
