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

final class ApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public LoanApplication $application;
    public User $rejecter;
    public ?string $rejectionReason;

    public function __construct(LoanApplication $application, User $rejecter, ?string $rejectionReason)
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
        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $this->application->id];

        if (Route::has($routeName)) {
            try {
                $viewUrl = route($routeName, $routeParameters);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationRejected mail: '.$e->getMessage(), [
                    'application_id' => $this->application->id,
                ]);
                return '#';
            }
        }

        return $viewUrl;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $this->application->id ?? 'N/A';

        $subject = __(':appType Ditolak (#:appId)', [
            'appType' => $applicationTypeDisplay,
            'appId' => $applicationId
        ]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.application-rejected', [
                'notification' => $this,
                'notifiable' => $notifiable
            ]);
    }

    public function toArray(User $notifiable): array
    {
        $app = $this->application;
        $applicationId = $app->id ?? null;
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationMorphClass = $app->getMorphClass();
        $rejectedBy = $this->rejecter->name ?? 'N/A';

        $data = [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'applicant_user_id' => $app->user_id ?? $notifiable->id,
            'status_key' => 'rejected',
            'subject' => __(':appType Ditolak (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message' => __(':appType anda (ID: #:appId) telah ditolak oleh :rejecterName.', [
                'appType' => $applicationTypeDisplay,
                'appId' => $applicationId ?? 'N/A',
                'rejecterName' => $rejectedBy
            ]),
            'rejection_reason' => $this->rejectionReason,
            'rejected_by_id' => $this->rejecter->id,
            'rejected_by_name' => $rejectedBy,
            'icon' => 'ti ti-circle-x',
        ];

        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $applicationId];

        if ($applicationId !== null && Route::has($routeName)) {
            try {
                $generatedUrl = route($routeName, $routeParameters);
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
                    $data['url'] = $generatedUrl;
                }
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationRejected toArray: '.$e->getMessage(), [
                    'application_id' => $applicationId,
                    'application_type' => $applicationMorphClass,
                    'route_name' => $routeName,
                ]);
                $data['url'] = null;
            }
        }

        return $data;
    }
}
