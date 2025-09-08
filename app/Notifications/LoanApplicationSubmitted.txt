<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class LoanApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer', 'items']); // Added items
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = $this->loanApplication->user?->name ?? $notifiable->name ?? 'Pemohon'; // Prioritize applicant from loan application
        $applicationId = $this->loanApplication->id ?? 'N/A';

        // Use consistent date properties: loan_start_date and loan_end_date
        $startDate = $this->formatDate($this->loanApplication->loan_start_date);
        $endDate = $this->formatDate($this->loanApplication->loan_end_date);

        $mailMessage = (new MailMessage)
            ->subject("Permohonan Pinjaman Peralatan ICT Dihantar (#{$applicationId})")
            ->greeting("Assalamualaikum / Salam Sejahtera, {$applicantName},")
            ->line('Permohonan Pinjaman Peralatan ICT anda dengan butiran berikut telah berjaya dihantar:')
            ->line("ID Permohonan: **#{$applicationId}**")
            ->line('Tujuan: '.($this->loanApplication->purpose ?? __('Tidak dinyatakan')))
            ->line("Tarikh Pinjaman: {$startDate} hingga {$endDate}")
            ->line('');

        if ($this->loanApplication->items && $this->loanApplication->items->count() > 0) {
            $mailMessage->line('Butiran Peralatan Dimohon:');
            foreach ($this->loanApplication->items as $item) {
                $mailMessage->line("- {$item->equipment_type} (Kuantiti: {$item->quantity_requested})");
            }
            $mailMessage->line('');
        }

        $mailMessage->line('Permohonan anda sedang dalam proses semakan. Anda akan dimaklumkan mengenai status permohonan ini dari semasa ke semasa.')
            ->line('Sila semak status permohonan anda di dalam sistem.');

        $viewUrl = '#';
        if (isset($this->loanApplication->id) && Route::has('loan-applications.show')) {
            try {
                $viewUrl = route('loan-applications.show', $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for LoanApplicationSubmitted notification (toMail): '.$e->getMessage(), ['application_id' => $this->loanApplication->id]);
            }
        }
        if ($viewUrl !== '#') {
            $mailMessage->action('Lihat Permohonan', $viewUrl);
        }

        return $mailMessage->salutation('Sekian, terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $applicantId = $this->loanApplication->user_id ?? null; // user_id directly from application

        $startDate = $this->formatDate($this->loanApplication->loan_start_date);
        $endDate = $this->formatDate($this->loanApplication->loan_end_date);

        $applicationUrl = '#';
        if ($applicationId !== null && Route::has('loan-applications.show')) {
            try {
                $applicationUrl = route('loan-applications.show', $applicationId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for LoanApplicationSubmitted notification (toArray): '.$e->getMessage(), ['application_id' => $applicationId]);
            }
        }

        // Ensure LoanApplication::STATUS_PENDING constant exists in App\Models\LoanApplication model
        return [
            'application_type' => 'Permohonan Pinjaman',
            'loan_application_id' => $applicationId,
            'applicant_id' => $applicantId,
            'applicant_name' => $this->loanApplication->user?->name ?? null,
            'status' => LoanApplication::STATUS_PENDING, // Requires STATUS_PENDING in LoanApplication model
            'subject' => __('Permohonan Pinjaman Dihantar').($applicationId !== null ? " (#{$applicationId})" : ''),
            'message' => __('Permohonan Pinjaman Peralatan ICT anda').($applicationId !== null ? " (#{$applicationId})" : '').__(' telah berjaya dihantar.'),
            'url' => $applicationUrl,
            'purpose' => $this->loanApplication->purpose ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    private function formatDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('d/m/Y');
        }
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format('d/m/Y');
            } catch (\Exception $e) {
                return __('Tidak dinyatakan');
            }
        }

        return __('Tidak dinyatakan');
    }
}
