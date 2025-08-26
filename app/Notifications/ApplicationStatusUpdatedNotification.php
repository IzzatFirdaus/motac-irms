<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ApplicationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $application;

    private User $user;

    private string $newStatus;

    public function __construct(User $user, LoanApplication $application, string $newStatus)
    {
        $this->user        = $user;
        $this->application = $application->loadMissing('user');
        $this->newStatus   = $newStatus;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName          = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId          = $this->application->id ?? 'N/A';
        $newStatusDisplay       = $this->getStatusLabel($this->application, $this->newStatus);

        $subject = __('Status :appType Anda Dikemaskini (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]);

        $introLines = [
            __('Status :appType anda dengan nombor rujukan **#:id** telah dikemaskini dalam sistem.', [
                'appType' => $applicationTypeDisplay,
                'id'      => $applicationId,
            ]),
            __('Status terkini: **:newStatus**', ['newStatus' => $newStatusDisplay]),
        ];

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.motac_default_notification', [
                'greeting'       => __('Salam Sejahtera'),
                'notifiableName' => $applicantName,
                'introLines'     => $introLines,
                'actionText'     => __('Lihat Permohonan'),
                'actionUrl'      => $this->getActionUrl(),
            ]);
    }

    public function toArray(User $notifiable): array
    {
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationMorphClass  = $this->application->getMorphClass();
        $applicationId          = $this->application->id      ?? null;
        $applicantId            = $this->application->user_id ?? ($notifiable->id ?? null);
        $newStatusDisplay       = $this->getStatusLabel($this->application, $this->newStatus);
        $applicationUrl         = $this->getActionUrl();

        return [
            'application_type_morph'   => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'application_id'           => $applicationId,
            'applicant_id'             => $applicantId,
            'subject'                  => __('Status :appType Dikemaskini (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message'                  => __('Status :appType anda (#:id) telah dikemaskini ke **:newStatus**.', [
                'appType'   => $applicationTypeDisplay,
                'id'        => $applicationId ?? 'N/A',
                'newStatus' => $newStatusDisplay,
            ]),
            'url'                => ($applicationUrl !== '#') ? $applicationUrl : null,
            'new_status_key'     => $this->newStatus,
            'new_status_display' => $newStatusDisplay,
            'icon'               => 'ti ti-refresh-alert',
        ];
    }

    private function getActionUrl(): string
    {
        $routeName       = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $this->application->id];

        if (Route::has($routeName)) {
            try {
                return route($routeName, $routeParameters);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationStatusUpdatedNotification: '.$e->getMessage());
            }
        }

        return '#';
    }

    private function getStatusLabel(LoanApplication $application, string $statusKey): string
    {
        if (method_exists($application, 'getStatusOptions')) {
            $options = $application::getStatusOptions();

            return $options[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
        }

        return ucfirst(str_replace('_', ' ', $statusKey));
    }
}
