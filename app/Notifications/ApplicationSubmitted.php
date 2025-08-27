<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to the applicant when their loan application is submitted.
 */
class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected LoanApplication $application;

    public function __construct(LoanApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Permohonan Pinjaman ICT Telah Dihantar')
            ->greeting('Assalamualaikum & Salam Sejahtera')
            ->line('Permohonan pinjaman ICT anda telah berjaya dihantar untuk semakan dan kelulusan.')
            ->line('Tujuan: ' . ($this->application->purpose ?? '-'))
            ->line('Lokasi: ' . ($this->application->location ?? '-'))
            ->line('Tarikh Mula: ' . ($this->application->loan_start_date ?? '-'))
            ->line('Tarikh Tamat: ' . ($this->application->loan_end_date ?? '-'))
            ->action('Lihat Permohonan', url('/loan-applications/' . $this->application->id))
            ->line('Terima kasih kerana menggunakan sistem pinjaman ICT MOTAC.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'purpose'        => $this->application->purpose,
            'status'         => $this->application->status,
        ];
    }
}
