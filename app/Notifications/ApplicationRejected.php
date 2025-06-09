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
use Illuminate\Support\Facades\Route;

final class ApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public EmailApplication|LoanApplication $application;

    public User $rejecter;

    public ?string $rejectionReason;

    public function __construct(EmailApplication|LoanApplication $application, User $rejecter, ?string $rejectionReason)
    {
        $this->application = $application->loadMissing('user');
        $this->rejecter = $rejecter;
        $this->rejectionReason = $rejectionReason;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function getActionUrl(): string
    {
        $viewUrl = '#';
        $isLoanApp = $this->application instanceof LoanApplication;

        if ($this->application->id) {
            $routeName = $isLoanApp
              ? 'resource-management.my-applications.loan.show'
              : 'resource-management.my-applications.email.show';
            $routeParameters = $isLoanApp
              ? ['loan_application' => $this->application->id]
              : ['email_application' => $this->application->id];

            if (Route::has($routeName)) {
                try {
                    $viewUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationRejected mail: '.$e->getMessage(), ['application_id' => $this->application->id]);

                    return '#';
                }
            }
        }

        return $viewUrl;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
          ? __('Permohonan Pinjaman Peralatan ICT')
          : __('Permohonan Akaun E-mel/ID Pengguna');
        $applicationId = $this->application->id ?? 'N/A';

        $subject = __(':appType Ditolak (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.application-rejected', ['notification' => $this, 'notifiable' => $notifiable]);
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
            'applicant_user_id' => $this->application->user_id ?? $notifiable->id, // Use loaded user_id
            'status_key' => 'rejected',
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
                    $applicationUrl = '#';
                }
            }
        }
        $data['url'] = ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null;

        return $data;
    }
}
