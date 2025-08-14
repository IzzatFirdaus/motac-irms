<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ApplicationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication|LoanApplication|Model $application;

    private string $oldStatus;

    private string $newStatus;

    public function __construct(
        EmailApplication|LoanApplication|Model $application,
        string $oldStatus,
        string $newStatus
    ) {
        $this->application = $application;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->application->loadMissing('user');
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
        $applicationTypeDisplay = $this->getApplicationTypeDisplay();
        $applicationId = $this->application->id ?? 'N/A';
        $oldStatusDisplay = $this->getStatusLabel($this->application, $this->oldStatus);
        $newStatusDisplay = $this->getStatusLabel($this->application, $this->newStatus);

        $subject = __('Status :appType Anda Dikemaskini (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]);

        $introLines = [
            __('Status :appType anda dengan nombor rujukan **#:id** telah dikemaskini dalam sistem.', [
                'appType' => $applicationTypeDisplay,
                'id' => $applicationId,
            ]),
            __('Status terdahulu: **:oldStatus**', ['oldStatus' => $oldStatusDisplay]),
            __('Status terkini: **:newStatus**', ['newStatus' => $newStatusDisplay]),
        ];

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.motac_default_notification', [
                'greeting' => __('Salam Sejahtera'),
                'notifiableName' => $applicantName,
                'introLines' => $introLines,
                'actionText' => __('Lihat Permohonan'),
                'actionUrl' => $this->getActionUrl(),
            ]);
        // --- EDITED CODE: END ---
    }

    public function toArray(User $notifiable): array
    {
        $applicationTypeDisplay = $this->getApplicationTypeDisplay();
        $applicationMorphClass = $this->application->getMorphClass();
        $applicationId = $this->application->id ?? null;
        $applicantId = $this->application->user_id ?? ($notifiable->id ?? null);
        $oldStatusDisplay = $this->getStatusLabel($this->application, $this->oldStatus);
        $newStatusDisplay = $this->getStatusLabel($this->application, $this->newStatus);

        $applicationUrl = $this->getActionUrl();

        return [
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'application_id' => $applicationId,
            'applicant_id' => $applicantId,
            'subject' => __('Status :appType Dikemaskini (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message' => __('Status :appType anda (#:id) telah dikemaskini dari **:oldStatus** ke **:newStatus**.', [
                'appType' => $applicationTypeDisplay,
                'id' => $applicationId ?? 'N/A',
                'oldStatus' => $oldStatusDisplay,
                'newStatus' => $newStatusDisplay,
            ]),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'old_status_key' => $this->oldStatus,
            'new_status_key' => $this->newStatus,
            'old_status_display' => $oldStatusDisplay,
            'new_status_display' => $newStatusDisplay,
            'icon' => 'ti ti-refresh-alert',
        ];
    }

    private function getApplicationTypeDisplay(): string
    {
        if ($this->application instanceof EmailApplication) {
            return __('Permohonan Akaun E-mel/ID Pengguna');
        }

        if ($this->application instanceof LoanApplication) {
            return __('Permohonan Pinjaman Peralatan ICT');
        }

        return 'Permohonan';
    }

    private function getActionUrl(): string
    {
        $routeName = null;
        if ($this->application instanceof EmailApplication) {
            $routeName = 'resource-management.my-applications.email.show';
            $routeParameters = ['email_application' => $this->application->id];
        } elseif ($this->application instanceof LoanApplication) {
            $routeName = 'resource-management.my-applications.loan.show';
            $routeParameters = ['loan_application' => $this->application->id];
        }

        if ($routeName && Route::has($routeName)) {
            try {
                return route($routeName, $routeParameters);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationStatusUpdatedNotification: '.$e->getMessage());
            }
        }

        return '#';
    }

    private function getStatusLabel(Model $application, string $statusKey): string
    {
        if (method_exists($application, 'getStatusOptions')) {
            $options = $application::getStatusOptions();

            return $options[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
        }

        return ucfirst(str_replace('_', ' ', $statusKey));
    }
}
