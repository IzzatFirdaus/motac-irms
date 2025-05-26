<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

final class ApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication|LoanApplication $application;
    private User $rejecter;
    private ?string $rejectionReason;

    public function __construct(
        EmailApplication|LoanApplication $application,
        User $rejecter,
        ?string $rejectionReason
    ) {
        $this->application = $application;
        $this->rejecter = $rejecter;
        $this->rejectionReason = $rejectionReason;
        Log::info('ApplicationRejected notification created for application ID '.($application->id ?? 'N/A').'.');
    }

    public function getApplication(): EmailApplication|LoanApplication
    {
        return $this->application;
    }

    public function getRejecter(): User
    {
        return $this->rejecter;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof User) {
            return ['mail', 'database'];
        }
        Log::warning('ApplicationRejected notification via() called for non-User notifiable: '.$notifiable::class);
        return [];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicationId = $this->application->id ?? 'N/A';
        $applicationTypeDisplay = $this->application instanceof EmailApplication
            ? __('Permohonan Akaun E-mel/ID Pengguna')
            : __('Permohonan Pinjaman Peralatan ICT');

        $applicantName = $notifiable->name ?? __('Pemohon');
        $rejecterName = $this->rejecter->name ?? __('Pegawai Pelulus');

        $mailMessage = (new MailMessage())
            ->subject(__(':appType Ditolak (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__('Dukacita dimaklumkan bahawa :appType anda (ID: #:appId) telah **ditolak**.', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]));

        if ($this->rejectionReason !== null && $this->rejectionReason !== '') {
            $mailMessage->line(__('Sebab penolakan oleh :rejecterName:', ['rejecterName' => $rejecterName]));
            $mailMessage->line($this->rejectionReason);
        } else {
            $mailMessage->line(__('Tiada sebab khusus dinyatakan atas penolakan ini.'));
        }

        $viewApplicationUrl = '#';
        $routeParameters = [];
        $routeName = null;

        if ($this->application instanceof EmailApplication && $this->application->id) {
            $routeName = 'resource-management.my-applications.email.show';
            $routeParameters = ['email_application' => $this->application->id];
        } elseif ($this->application instanceof LoanApplication && $this->application->id) {
            $routeName = 'resource-management.my-applications.loan.show';
            $routeParameters = ['loan_application' => $this->application->id];
        }

        if ($routeName && Route::has($routeName)) {
            try {
                $viewApplicationUrl = route($routeName, $routeParameters);
            } catch (\Exception $e) {
                 Log::error('Error generating URL for ApplicationRejected mail: '.$e->getMessage(), [
                    'application_id' => $this->application->id ?? null,
                    'route_name' => $routeName,
                ]);
                $viewApplicationUrl = '#'; // Fallback
            }
        }


        if ($viewApplicationUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Permohonan'), $viewApplicationUrl);
        }
        $mailMessage->line(__('Jika anda mempunyai sebarang pertanyaan, sila hubungi Bahagian Pengurusan Maklumat (BPM) atau pegawai yang bertanggungjawab.'));
        $mailMessage->salutation(__('Sekian, harap maklum.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $applicationId = $this->application->id ?? null;
        $applicationTypeDisplay = $this->application instanceof EmailApplication
            ? __('Permohonan Akaun E-mel/ID Pengguna')
            : __('Permohonan Pinjaman Peralatan ICT');
        $applicationMorphClass = $this->application->getMorphClass();

        $applicationUrl = null;
        $routeParameters = [];
        $routeName = null;

         if ($applicationId !== null) {
            if ($this->application instanceof EmailApplication) {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $applicationId];
            } elseif ($this->application instanceof LoanApplication) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $applicationId];
            }

            if ($routeName && Route::has($routeName)) {
                try {
                    $generatedUrl = route($routeName, $routeParameters);
                    if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
                        $applicationUrl = $generatedUrl;
                    }
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationRejected toArray: '.$e->getMessage(), [
                        'application_id' => $applicationId,
                        'route_name' => $routeName,
                    ]);
                }
            }
        }

        $rejectedBy = $this->rejecter->name ?? 'N/A';

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'subject' => __(':appType Ditolak', ['appType' => $applicationTypeDisplay]),
            'message' => __(':appType anda (ID: #:appId) telah ditolak oleh :rejecterName.', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId ?? 'N/A', 'rejecterName' => $rejectedBy]),
            'rejection_reason' => $this->rejectionReason,
            'rejected_by_name' => $rejectedBy,
            'rejected_by_id' => $this->rejecter->id,
            'url' => $applicationUrl,
            'icon' => 'ti ti-circle-x', // Example icon
        ];
    }
}
