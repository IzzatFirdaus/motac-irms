<?php

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

/**
 * Mailable for notifying applicant when their equipment is returned.
 */
class LoanApplicationReturned extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public LoanTransaction $loanTransaction;

    public function __construct(LoanApplication $loanApplication, LoanTransaction $loanTransaction)
    {
        $this->loanApplication = $loanApplication->loadMissing('user');
        $this->loanTransaction = $loanTransaction->loadMissing(['loanTransactionItems.equipment', 'returnAcceptingOfficer', 'issuingOfficer']);
    }

    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id          ?? 'N/A';
        $transactionId = $this->loanTransaction->id          ?? 'N/A';

        return new Envelope(
            to: [new Address($this->loanApplication->user->email, $applicantName)],
            subject: __('Notifikasi Peralatan Pinjaman ICT Telah Dipulangkan (Permohonan #:id - Transaksi #:trx)', [
                'id'  => $applicationId,
                'trx' => $transactionId,
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-application-returned',
            with: [
                'loanApplication' => $this->loanApplication,
                'loanTransaction' => $this->loanTransaction,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
