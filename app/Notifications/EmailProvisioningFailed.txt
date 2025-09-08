<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User; // For $notifiable type hint
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

final class EmailProvisioningFailed extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication $application;
    private ?string $failureReason;

    /**
     * Create a new notification instance.
     * @param EmailApplication $application The application for which provisioning failed.
     * @param string|null $failureReason Optional reason for the failure.
     */
    public function __construct(EmailApplication $application, ?string $failureReason = null)
    {
        $this->application = $application->loadMissing('user'); // Eager load applicant
        $this->failureReason = $failureReason;
    }

    public function via(User $notifiable): array // $notifiable here would be an IT Admin or BPM staff
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationId = $this->application->id ?? 'N/A';
        $applicantName = $this->application->user?->name ?? __('Pemohon Tidak Dikenali');
        $adminName = $notifiable->name ?? __('Pentadbir ICT');

        $mailMessage = (new MailMessage())
            ->subject(__("Amaran: Penyediaan E-mel/ID Gagal untuk Permohonan #:id", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $adminName]))
            ->error() // Mark as important/error
            ->line(__("Proses penyediaan E-mel/ID Pengguna untuk permohonan #:id bagi pihak :applicantName telah GAGAL.", ['id' => $applicationId, 'applicantName' => $applicantName]));

        if ($this->failureReason) {
            $mailMessage->line(__("Sebab Kegagalan (jika ada): :reason", ['reason' => $this->failureReason]));
        } else {
            $mailMessage->line(__("Tiada sebab khusus diberikan. Sila semak log sistem untuk butiran lanjut."));
        }

        $mailMessage->line(__("Sila siasat isu ini dan cuba proses semula permohonan atau maklumkan kepada pemohon."));

        $adminApplicationUrl = '#';
        // Assuming a generic admin view route for email applications
        $adminRouteName = 'admin.resource-management.email-applications.show';
        if ($this->application->id && Route::has($adminRouteName)) {
            try {
                $adminApplicationUrl = route($adminRouteName, $this->application->id);
            } catch (\Throwable $e) {
                Log::error("Error generating admin URL for EmailProvisioningFailed mail: {$e->getMessage()}", [
                    'application_id' => $this->application->id,
                    'route_name' => $adminRouteName,
                ]);
                $adminApplicationUrl = '#'; // Fallback
            }
        }

        if ($adminApplicationUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan'), $adminApplicationUrl);
        }

        return $mailMessage->salutation(__('Harap maklum.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? __('Pemohon Tidak Dikenali');

        $adminApplicationUrl = '#';
        $adminRouteName = 'admin.resource-management.email-applications.show';
        if ($applicationId && Route::has($adminRouteName)) {
            try {
                $adminApplicationUrl = route($adminRouteName, $applicationId);
            } catch (\Throwable $e) {
                Log::error("Error generating admin URL for EmailProvisioningFailed toArray: " . $e->getMessage(), ['application_id' => $applicationId]);
                $adminApplicationUrl = '#'; // Fallback
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'applicant_name' => $applicantName,
            'subject' => __("Penyediaan E-mel/ID Gagal (#:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Penyediaan E-mel/ID untuk permohonan #:id (:name) GAGAL. Sila siasat.", ['id' => $applicationId ?? 'N/A', 'name' => $applicantName]),
            'failure_reason' => $this->failureReason,
            'url' => ($adminApplicationUrl !== '#') ? $adminApplicationUrl : null,
            'icon' => 'ti ti-alert-octagon', // Example icon for failure
        ];
    }
}
