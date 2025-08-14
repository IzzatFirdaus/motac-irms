<?php

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

class EmailApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $application; // Made property type explicit
    protected ?string $rejectionReason;    // Made property type explicit

    public function __construct(
        EmailApplication $application,
        ?string $rejectionReason = null
    ) {
        $this->application = $application;
        $this->rejectionReason = $rejectionReason ?? $application->rejection_reason;
        $this->application->loadMissing('user'); // Eager load user
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pengguna');
        $applicationId = $this->application->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Permohonan Akaun E-mel/ID Pengguna Ditolak (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__('Dukacita dimaklumkan bahawa permohonan anda untuk Akaun E-mel / ID Pengguna MOTAC (#:id) telah ditolak.', ['id' => $applicationId]));

        if ($this->rejectionReason) {
            $mailMessage->line(__('Sebab Penolakan: :reason', ['reason' => $this->rejectionReason]));
        }

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.email.show';
        if ($this->application->id && Route::has($routeName)) {
             try {
                $applicationUrl = route($routeName, $this->application->id);
            } catch (\Throwable $e) {
                Log::error("Failed to generate route for EmailApplicationRejectedNotification: {$e->getMessage()}", [
                    'application_id' => $this->application->id,
                    'route_name' => $routeName,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Permohonan'), $applicationUrl);
        }

        $mailMessage->line(__('Jika anda mempunyai sebarang pertanyaan, sila hubungi Bahagian Pengurusan Maklumat.'));
        $mailMessage->salutation(__('Sekian, harap maklum.'));
        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $applicationId = $this->application->id ?? null;
        $applicantId = $this->application->user_id ?? ($notifiable->id ?? null);

        $applicationUrl = null;
        $routeName = 'resource-management.my-applications.email.show';
        if ($applicationId !== null && Route::has($routeName)) {
            try {
                $generatedUrl = route($routeName, $applicationId);
                 if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
                    $applicationUrl = $generatedUrl;
                }
            } catch (\Throwable $e) {
                 Log::error("Failed to generate route for EmailApplicationRejectedNotification (toArray): {$e->getMessage()}", [
                    'application_id' => $applicationId,
                    'route_name' => $routeName,
                ]);
            }
        }

        return [
            'application_type_morph' => $this->application->getMorphClass(),
            'application_id' => $applicationId,
            'applicant_id' => $applicantId,
            'status' => $this->application->status ?? 'rejected', // Ensure status is captured
            'subject' => __("Permohonan E-mel Ditolak (#:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __('Permohonan e-mel/ID pengguna anda (#:id) telah ditolak.', ['id' => $applicationId ?? 'N/A']),
            'rejection_reason' => $this->rejectionReason,
            'url' => $applicationUrl,
            'icon' => 'ti ti-mail-off', // Example icon
        ];
    }
}
