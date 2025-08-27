<?php

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying the applicant when equipment is returned and recorded.
 */
class EquipmentReturnedNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public LoanTransaction $returnTransaction;

    public User $notifiable;

    public function __construct(LoanApplication $loanApplication, LoanTransaction $returnTransaction, User $notifiable)
    {
        $this->loanApplication   = $loanApplication->loadMissing('user');
        $this->returnTransaction = $returnTransaction->loadMissing(['loanTransactionItems.equipment', 'returnAcceptingOfficer']);
        $this->notifiable        = $notifiable;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Peralatan Pinjaman Dipulangkan (Permohonan #:id)', ['id' => $this->loanApplication->id ?? 'N/A']),
            to: [$this->notifiable->email]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.equipment-returned',
            with: [
                'loanApplication'   => $this->loanApplication,
                'returnTransaction' => $this->returnTransaction,
                'actionUrl'         => route('loan-applications.show', $this->loanApplication->id),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
