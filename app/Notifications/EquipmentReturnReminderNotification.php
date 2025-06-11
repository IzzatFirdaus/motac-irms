<?php

namespace App\Notifications;

// EDITED: Added the Mailable class import
use App\Mail\EquipmentReturnReminder;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class EquipmentReturnReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public LoanApplication $loanApplication;
    public int $daysUntilReturn;

    public function __construct(LoanApplication $loanApplication, int $daysUntilReturn)
    {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']);
        $this->daysUntilReturn = $daysUntilReturn;
    }

    public function getLoanApplication(): LoanApplication
    {
        return $this->loanApplication;
    }

    public function getDaysUntilReturn(): int
    {
        return $this->daysUntilReturn;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function getActionUrl(): string
    {
        $routeName = 'resource-management.my-applications.loan.show';
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                return route($routeName, ['loan_application' => $this->loanApplication->id]);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentReturnReminderNotification: '.$e->getMessage());
            }
        }
        return '#';
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param \App\Models\User $notifiable
     * @return \App\Mail\EquipmentReturnReminder
     */
    // EDITED: Refactored to return the new Mailable class.
    public function toMail(User $notifiable): EquipmentReturnReminder
    {
        return new EquipmentReturnReminder($this->loanApplication, $this->daysUntilReturn, $notifiable);
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id;
        $expectedReturnDate = $this->loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y'));
        $returnLocation = $this->loanApplication->return_location ?? __('Unit ICT, Bahagian Pengurusan Maklumat');

        $status = 'return_reminder';
        $subject = __('Peringatan Pulangan Peralatan');
        $message = '';
        $icon = 'ti ti-calendar-event';

        if ($this->daysUntilReturn > 0) {
            $subject = __('Peringatan: Peralatan Perlu Dipulangkan Dalam :days Hari', ['days' => $this->daysUntilReturn]);
            $message = __('Peralatan untuk Permohonan #:id perlu dipulangkan dalam masa :days hari lagi (:date).', ['id' => $applicationId, 'days' => $this->daysUntilReturn, 'date' => $expectedReturnDate]);
        } elseif ($this->daysUntilReturn === 0) {
            $subject = __('Peringatan: Peralatan Perlu Dipulangkan Hari Ini');
            $message = __('Peralatan untuk Permohonan #:id perlu dipulangkan hari ini (:date).', ['id' => $applicationId, 'date' => $expectedReturnDate]);
        } else {
            $daysOverdue = abs($this->daysUntilReturn);
            $status = 'overdue';
            $subject = __('PERHATIAN: Peralatan Lewat Dipulangkan (:days Hari)', ['days' => $daysOverdue]);
            $message = __('Peralatan untuk Permohonan #:id telah lewat dipulangkan :days hari. Tarikh pulang jangkaan: :date.', ['id' => $applicationId, 'days' => $daysOverdue, 'date' => $expectedReturnDate]);
            $icon = 'ti ti-alarm-snooze';
        }
        $subject .= __(' (#:id)', ['id' => $applicationId]);

        $applicationUrl = $this->getActionUrl();

        return [
            'loan_application_id' => $applicationId,
            'applicant_id' => $this->loanApplication->user_id,
            'responsible_officer_id' => $this->loanApplication->responsible_officer_id,
            'status_key' => $status,
            'days_value' => $this->daysUntilReturn,
            'subject' => $subject,
            'message' => $message,
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'expected_return_date' => $expectedReturnDate,
            'return_location' => $returnLocation,
            'icon' => $icon,
        ];
    }
}
