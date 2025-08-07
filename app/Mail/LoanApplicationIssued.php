<?php

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

/**
 * Mailable notification sent to applicant when their loan application equipment has been issued.
 */
class LoanApplicationIssued extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LoanApplication $loanApplication;
    public EloquentCollection $issueTransactions;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication->loadMissing([
            'user',
            'transactions' => function ($query) {
                $query->where('type', LoanTransaction::TYPE_ISSUE)
                    ->with('loanTransactionItems.equipment');
            },
        ]);
        $this->issueTransactions = $this->loanApplication->transactions->where('type', LoanTransaction::TYPE_ISSUE);
    }

    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id ?? 'N/A';

        return new Envelope(
            to: [new Address($this->loanApplication->user->email, $applicantName)],
            subject: __('Notifikasi Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #:id - :name)', [
                'id' => $applicationId,
                'name' => $applicantName
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-application-issued',
            with: [
                'loanApplication' => $this->loanApplication,
                'issueTransactions' => $this->issueTransactions,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
