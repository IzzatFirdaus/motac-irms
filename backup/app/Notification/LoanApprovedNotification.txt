<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\User; // For type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Class LoanApprovedNotification
 *
 * Notification sent to the applicant when their Loan Application has been approved.
 * This notification includes details about the approved loan, such as purpose, location, and loan period,
 * and provides instructions for picking up equipment.
 */
class LoanApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected LoanApplication $loanApplication;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication;
        // Eager load applicant and responsible officer if not already loaded
        $this->loanApplication->loadMissing(['user', 'responsibleOfficer', 'applicationItems']);
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database']; // Added 'database' channel
    }

    private function formatDateForDisplay(?Carbon $date): string
    {
        if ($date instanceof Carbon) {
            return $date->format(config('app.datetime_format_my', 'd/m/Y H:i A'));
        }
        return __('Tidak Dinyatakan');
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicantName = $this->loanApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $purpose = $this->loanApplication->purpose ?? __('Tidak Dinyatakan');
        $location = $this->loanApplication->location ?? __('Tidak Dinyatakan');

        $startDate = $this->formatDateForDisplay($this->loanApplication->loan_start_date);
        $endDate = $this->formatDateForDisplay($this->loanApplication->loan_end_date);

        $mailMessage = (new MailMessage())
            ->subject(__("Permohonan Pinjaman Peralatan ICT Anda Telah Diluluskan (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__('Sukacita dimaklumkan bahawa permohonan pinjaman peralatan ICT anda telah diluluskan.'))
            ->line(__('Butiran Pinjaman:'))
            ->line(__('- Nombor Rujukan: #:id', ['id' => $applicationId]))
            ->line(__('- Tujuan: :purpose', ['purpose' => $purpose]))
            ->line(__('- Lokasi Penggunaan: :location', ['location' => $location]))
            ->line(__('- Tempoh Pinjaman: :startDate hingga :endDate', ['startDate' => $startDate, 'endDate' => $endDate]))
            ->line(__("Status Permohonan: :status", ['status' => $this->loanApplication->statusTranslated ?? __('Diluluskan')])); // Use accessor

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Consistent route name
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error("Error generating URL for LoanApprovedNotification (toMail): {$e->getMessage()}", [
                    'application_id' => $this->loanApplication->id,
                    'route_name' => $routeName,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan Anda'), $applicationUrl);
        }

        $mailMessage
            ->line(__('Sila berhubung dengan Bahagian Pengurusan Maklumat (BPM) untuk urusan pengambilan peralatan.'))
            ->line(__('Jika anda mempunyai sebarang pertanyaan lanjut, sila hubungi BPM MOTAC.'))
            ->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $loanApplicationId = $this->loanApplication->id ?? null;
        $applicantName = $this->loanApplication->user?->name ?? __('Pemohon');

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($loanApplicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $loanApplicationId);
            } catch (\Exception $e) {
                Log::error("Error generating URL for LoanApprovedNotification (toArray): {$e->getMessage()}", ['application_id' => $loanApplicationId]);
                $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'loan_application_id' => $loanApplicationId,
            'application_type_morph' => $this->loanApplication->getMorphClass(),
            'applicant_name' => $applicantName,
            'applicant_id' => $this->loanApplication->user_id, // From your previous structure
            'responsible_officer_id' => $this->loanApplication->responsible_officer_id, // From your previous structure
            'status_key' => $this->loanApplication->status,
            'status_display' => $this->loanApplication->statusTranslated ?? __('Diluluskan'),
            'subject' => __("Permohonan Pinjaman Diluluskan (#:id)", ['id' => $loanApplicationId ?? 'N/A']),
            'message' => __("Permohonan Pinjaman Peralatan ICT anda (#:id) telah diluluskan. Sila hubungi BPM untuk pengambilan.", ['id' => $loanApplicationId ?? 'N/A']),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'purpose' => $this->loanApplication->purpose,
            'location' => $this->loanApplication->location,
            'loan_start_date' => $this->loanApplication->loan_start_date?->toDateTimeString(),
            'loan_end_date' => $this->loanApplication->loan_end_date?->toDateTimeString(),
            'icon' => 'ti ti-circle-check',
        ];
    }
}
