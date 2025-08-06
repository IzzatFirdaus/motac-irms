<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mailable class for notifying the applicant when their loaned ICT equipment has been returned.
 * This email is intended to be queued for better performance.
 */
final class LoanApplicationReturned extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public LoanTransaction $loanTransaction;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\LoanApplication  $loanApplication  The loan application model instance.
     * @param  \App\Models\LoanTransaction  $loanTransaction  The loan transaction model instance for the returned item.
     */
    public function __construct(LoanApplication $loanApplication, LoanTransaction $loanTransaction)
    {
        $this->loanApplication = $loanApplication->loadMissing('user');
        // Eager load relationships needed for the email content
        $this->loanTransaction = $loanTransaction->loadMissing([
            'loanTransactionItems.equipment', // For equipment details
            'returnAcceptingOfficer', // If you have a relationship for the officer who accepted the return
        ]);

        Log::info('LoanApplicationReturned Mailable: Instance created.', [
            'loan_application_id' => $this->loanApplication->id,
            'loan_transaction_id' => $this->loanTransaction->id,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $transactionId = $this->loanTransaction->id ?? 'N/A';

        $toAddresses = [
            new Address($this->loanApplication->user->email, $applicantName),
        ];

        $subject = sprintf('Notifikasi Peralatan Pinjaman ICT Telah Dipulangkan (Permohonan #%s - Transaksi #%s)', $applicationId, $transactionId);

        Log::info('LoanApplicationReturned Mailable: Preparing envelope.', [
            'loan_application_id' => $applicationId,
            'subject' => $subject,
            'to_recipients' => $toAddresses !== [] ? $toAddresses[0]->address : 'N/A',
        ]);

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            tags: ['loan-application', 'returned-notification'],
            metadata: [
                'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
                'loan_transaction_id' => (string) ($this->loanTransaction->id ?? 'unknown'),
                'applicant_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('LoanApplicationReturned Mailable: Preparing email content.', [
            'loan_application_id' => $this->loanApplication->id ?? 'N/A',
            'loan_transaction_id' => $this->loanTransaction->id ?? 'N/A',
            'view' => 'emails.loan-application-returned',
        ]);

        return new Content(
            view: 'emails.loan-application-returned',
            with: [
                'loanApplication' => $this->loanApplication,
                'loanTransaction' => $this->loanTransaction,
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
        return []; // No attachments by default
    }
}
