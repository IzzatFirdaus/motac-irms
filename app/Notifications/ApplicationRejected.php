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
        $this->application->loadMissing('user'); // Eager load user relationship
        $this->rejecter = $rejecter;
        $this->rejectionReason = $rejectionReason;
        Log::info('ApplicationRejected notification created for '.$application::class." ID: ".($application->id ?? 'N/A')." by User ID: {$rejecter->id}.");
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

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationId = $this->application->id ?? 'N/A';
        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
            ? __('Permohonan Pinjaman Peralatan ICT')
            : __('Permohonan Akaun E-mel/ID Pengguna');

        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $rejecterName = $this->rejecter->name ?? __('Pegawai Pelulus');

        $mailMessage = (new MailMessage())
            ->subject(__(':appType Ditolak (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->error() // Mark as important/error
            ->line(__('Dukacita dimaklumkan bahawa :appType anda (ID: #:appId) telah **ditolak**.', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]));

        if ($this->rejectionReason !== null && trim($this->rejectionReason) !== '') {
            $mailMessage->line(__('Sebab penolakan oleh :rejecterName:', ['rejecterName' => $rejecterName]));
            $mailMessage->line($this->rejectionReason);
        } else {
            $mailMessage->line(__('Permohonan anda ditolak oleh :rejecterName. Tiada sebab khusus dinyatakan.',['rejecterName' => $rejecterName]));
        }
        $mailMessage->line(''); // spacing

        $viewApplicationUrl = '#';
        $routeParameters = [];
        $routeName = null;

        if ($this->application->id) {
            if ($isLoanApp) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $this->application->id];
            } else {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $this->application->id];
            }

            if ($routeName && Route::has($routeName)) {
                try {
                    $viewApplicationUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                     Log::error('Error generating URL for ApplicationRejected mail: '.$e->getMessage(), [
                        'application_id' => $this->application->id ?? null,
                        'application_type' => $this->application::class,
                        'route_name' => $routeName,
                    ]);
                    $viewApplicationUrl = '#'; // Fallback
                }
            }
        }


        if ($viewApplicationUrl !== '#' && filter_var($viewApplicationUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action(__('Lihat Butiran Permohonan'), $viewApplicationUrl);
        }
        $mailMessage->line(__('Jika anda mempunyai sebarang pertanyaan, sila hubungi Bahagian Pengurusan Maklumat (BPM) atau pegawai yang bertanggungjawab.'));

        return $mailMessage->salutation(__('Sekian, harap maklum.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
            ? __('Permohonan Pinjaman Peralatan ICT')
            : __('Permohonan Akaun E-mel/ID Pengguna');
        $applicationMorphClass = $this->application->getMorphClass();

        $rejectedBy = $this->rejecter->name ?? 'N/A';

        $data = [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'applicant_user_id' => $this->application->user_id ?? $notifiable->id,
            'status_key' => 'rejected', // Generic rejected status
            'subject' => __(':appType Ditolak (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message' => __(':appType anda (ID: #:appId) telah ditolak oleh :rejecterName.', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId ?? 'N/A', 'rejecterName' => $rejectedBy]),
            'rejection_reason' => $this->rejectionReason,
            'rejected_by_id' => $this->rejecter->id,
            'rejected_by_name' => $rejectedBy,
            'icon' => 'ti ti-circle-x',
        ];

        $applicationUrl = '#';
        $routeParameters = [];
        $routeName = null;

         if ($applicationId !== null) {
            if ($isLoanApp) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $applicationId];
            } else {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $applicationId];
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
                        'application_type' => $applicationMorphClass,
                        'route_name' => $routeName,
                    ]);
                     $applicationUrl = '#'; // Fallback
                }
            }
        }
        $data['url'] = ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null;

        return $data;
    }
}
