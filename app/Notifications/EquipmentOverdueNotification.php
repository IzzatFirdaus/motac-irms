<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class EquipmentOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;
    private int $daysOverdue;

    public function __construct(LoanApplication $loanApplication, int $daysOverdue) // Modified constructor
    {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']);
        $this->daysOverdue = $daysOverdue;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $notifiable->name ?? __('Pemohon'); // $notifiable is the direct recipient
        $applicationId = $this->loanApplication->id ?? 'N/A';
        // If the notification is also sent to the responsible officer, their name can be mentioned.
        // $responsibleOfficerName = $this->loanApplication->responsibleOfficer?->name ?? $this->loanApplication->user?->name ?? __('Pegawai Bertanggungjawab');

        $expectedReturnDate = $this->loanApplication->loan_end_date
            ? Carbon::parse($this->loanApplication->loan_end_date)->format(config('app.date_format', 'd/m/Y'))
            : __('Tidak dinyatakan');

        $mailMessage = (new MailMessage())
            ->subject(__("Peringatan: Peralatan Pinjaman Lewat Pulang (:days hari) - Permohonan #:id", ['days' => $this->daysOverdue, 'id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->error() // Marks the email as important/error level
            ->line(__('Peralatan yang dipinjam di bawah Permohonan Pinjaman Peralatan ICT **#:id** telah lewat dipulangkan sebanyak **:days hari**.', ['id' => $applicationId, 'days' => $this->daysOverdue]))
            ->line(__("Tarikh pemulangan yang dijangka adalah pada **:date**.", ['date' => $expectedReturnDate]))
            ->line('')
            ->line(__('Sila pulangkan peralatan tersebut dengan kadar **SEGERA** kepada Unit ICT atau pegawai yang bertanggungjawab.'));

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Standardized route
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentOverdueNotification: ' . $e->getMessage(), [
                    'exception' => $e,
                    'application_id' => $this->loanApplication->id ?? null,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan'), $applicationUrl);
        }

        $mailMessage
            ->line('')
            ->line(__('Kegagalan memulangkan peralatan dalam tempoh yang ditetapkan boleh menyebabkan tindakan selanjutnya diambil. Kerjasama anda amat dihargai.'));

        return $mailMessage->salutation(__('Sekian, harap maklum.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $expectedReturnDate = $this->loanApplication->loan_end_date
            ? Carbon::parse($this->loanApplication->loan_end_date)->format(config('app.date_format', 'd/m/Y'))
            : __('Tidak dinyatakan');

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $applicationId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentOverdueNotification toArray: ' . $e->getMessage(), [
                    'exception' => $e,
                    'application_id' => $applicationId,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_id' => $this->loanApplication->user_id ?? null,
            'responsible_officer_id' => $this->loanApplication->responsible_officer_id ?? null,
            'status' => 'overdue',
            'days_overdue' => $this->daysOverdue,
            'subject' => __("Peralatan Lewat Pulang (:days hari)", ['days' => $this->daysOverdue]) . ($applicationId !== null ? " (#{$applicationId})" : ''),
            'message' => __("Peralatan untuk Permohonan #:id telah lewat dipulangkan :days hari. Tarikh pulang jangkaan: :date.", ['id' => $applicationId ?? 'N/A', 'days' => $this->daysOverdue, 'date' => $expectedReturnDate]),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'expected_return_date' => $expectedReturnDate,
            'icon' => 'ti ti-alarm-snooze',
        ];
    }

    public function getLoanApplication(): LoanApplication
    {
        return $this->loanApplication;
    }

    public function getDaysOverdue(): int
    {
        return $this->daysOverdue;
    }
}
