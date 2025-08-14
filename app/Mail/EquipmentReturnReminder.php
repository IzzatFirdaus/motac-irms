<?php

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentReturnReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The loan application instance.
     */
    public LoanApplication $loanApplication;

    /**
     * The number of days until the return is due.
     * Can be positive (due in X days), zero (due today), or negative (overdue by X days).
     */
    public int $daysUntilReturn;

    /**
     * The user receiving the notification.
     */
    public User $notifiable;

    /**
     * Create a new message instance.
     */
    public function __construct(LoanApplication $loanApplication, int $daysUntilReturn, User $notifiable)
    {
        $this->loanApplication = $loanApplication;
        $this->daysUntilReturn = $daysUntilReturn;
        $this->notifiable = $notifiable;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $subject = '';

        if ($this->daysUntilReturn > 0) {
            $subject = __('Peringatan: Pulangan Peralatan Dalam :days Hari Lagi - Permohonan #:id', ['days' => $this->daysUntilReturn, 'id' => $applicationId]);
        } elseif ($this->daysUntilReturn === 0) {
            $subject = __('Peringatan: Pulangan Peralatan Hari Ini - Permohonan #:id', ['id' => $applicationId]);
        } else {
            $daysOverdue = abs($this->daysUntilReturn);
            $subject = __('PERHATIAN: Peralatan Pinjaman LEWAT Dipulangkan (:days Hari) - Permohonan #:id', ['days' => $daysOverdue, 'id' => $applicationId]);
        }

        return new Envelope(
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // This points to the Blade view that renders the email's HTML
        return new Content(
            view: 'emails.equipment-return-reminder',
            // Pass the public properties of this Mailable to the view
            with: [
                'loanApplication' => $this->loanApplication,
                'daysUntilReturn' => $this->daysUntilReturn,
                'notifiable' => $this->notifiable,
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
