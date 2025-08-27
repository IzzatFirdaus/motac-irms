<?php

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for reminding borrower about upcoming/overdue equipment return.
 */
class EquipmentReturnReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public int $daysUntilReturn;

    public User $notifiable;

    public function __construct(LoanApplication $loanApplication, int $daysUntilReturn, User $notifiable)
    {
        $this->loanApplication = $loanApplication;
        $this->daysUntilReturn = $daysUntilReturn;
        $this->notifiable      = $notifiable;
    }

    public function envelope(): Envelope
    {
        $applicationId = $this->loanApplication->id ?? 'N/A';
        if ($this->daysUntilReturn > 0) {
            $subject = __('Peringatan: Pulangan Peralatan Dalam :days Hari Lagi - Permohonan #:id', ['days' => $this->daysUntilReturn, 'id' => $applicationId]);
        } elseif ($this->daysUntilReturn === 0) {
            $subject = __('Peringatan: Pulangan Peralatan Hari Ini - Permohonan #:id', ['id' => $applicationId]);
        } else {
            $subject = __('PERHATIAN: Peralatan Pinjaman LEWAT Dipulangkan (:days Hari) - Permohonan #:id', ['days' => abs($this->daysUntilReturn), 'id' => $applicationId]);
        }

        return new Envelope(
            subject: $subject,
            to: [$this->notifiable->email]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.equipment-return-reminder',
            with: [
                'loanApplication' => $this->loanApplication,
                'daysUntilReturn' => $this->daysUntilReturn,
                'notifiable'      => $this->notifiable,
                'actionUrl'       => route('loan-applications.show', $this->loanApplication->id),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
