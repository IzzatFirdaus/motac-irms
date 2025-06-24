<?php

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public EmailApplication|LoanApplication $application;

    public string $applicationTypeDisplay;

    public function __construct(EmailApplication|LoanApplication $application)
    {
        $this->application = $application;
        if ($this->application instanceof LoanApplication) {
            $this->application->loadMissing(['user:id,name,email', 'loanApplicationItems:id,loan_application_id,equipment_type,quantity_requested']);
            $this->applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        } elseif ($this->application instanceof EmailApplication) {
            $this->application->loadMissing(['user:id,name,email']);
            $this->applicationTypeDisplay = __('Permohonan Akaun E-mel/ID Pengguna');
        } else {
            $this->applicationTypeDisplay = __('Permohonan Umum');
        }

        Log::info('ApplicationSubmitted notification INSTANTIATED for '.$this->application::class.sprintf(' ID: %d. Notifying user ID: %d', $this->application->id, $this->application->user_id));
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        // --- EDITED CODE: START ---
        // The method now uses the generic motac_default_notification view for consistency.
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->application->id ?? 'N/A';
        $subject = __(':appType Anda Telah Dihantar (ID: #:id)', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]);

        $introLines = [
            __(':appType anda dengan nombor rujukan **#:id** telah berjaya dihantar dan diterima untuk semakan.', [
                'appType' => $this->applicationTypeDisplay,
                'id' => $applicationId,
            ]),
        ];
        $outroLines = [
            __('Permohonan anda kini sedang dalam proses semakan oleh pegawai yang bertanggungjawab. Anda akan dimaklumkan melalui e-mel dan notifikasi sistem mengenai sebarang perkembangan status permohonan ini.'),
        ];

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.motac_default_notification', [
                'greeting' => __('Salam Sejahtera'),
                'notifiableName' => $applicantName,
                'introLines' => $introLines,
                'outroLines' => $outroLines,
                'actionText' => __('Lihat Status Permohonan'),
                'actionUrl' => $this->getActionUrl(),
            ]);
        // --- EDITED CODE: END ---
    }

    public function getActionUrl(): string
    {
        $viewUrl = '#';
        if ($this->application->id) {
            if ($this->application instanceof LoanApplication) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $this->application->id];
            } elseif ($this->application instanceof EmailApplication) {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $this->application->id];
            }

            if (Route::has($routeName)) {
                try {
                    return route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationSubmitted mail notification: '.$e->getMessage());
                }
            }
        }

        return $viewUrl;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationMorphClass = $this->application->getMorphClass();
        $viewUrl = $this->getActionUrl();

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $this->applicationTypeDisplay,
            'applicant_name' => $applicantName,
            'status_key' => $this->application->status ?? 'submitted',
            'title' => __(':appType Dihantar (ID: #:id)', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message' => __(':appType anda (ID: #:id) telah berjaya dihantar dan sedang diproses.', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'icon' => ($this->application instanceof LoanApplication) ? 'ti ti-file-invoice' : 'ti ti-mail-forward',
            'url' => ($viewUrl !== '#') ? $viewUrl : null,
            'notification_type' => 'application_event',
        ];
    }
}
