<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

final class EmailApplicationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication $emailApplication;

    public function __construct(EmailApplication $emailApplication)
    {
        $this->emailApplication = $emailApplication;
        $this->emailApplication->loadMissing('user'); // Eager load user
        Log::info("EmailApplicationApprovedNotification created for EmailApplication ID: {$emailApplication->id}.");
    }

    public function getEmailApplication(): EmailApplication
    {
        return $this->emailApplication;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database']; // Added 'database' channel
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->emailApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->emailApplication->id ?? 'N/A';

        $mailMessage = new MailMessage(); // Standard instantiation
        $mailMessage
            ->subject(__("Permohonan E-mel ICT MOTAC Diluluskan (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__('Sukacita dimaklumkan bahawa permohonan akaun e-mel ICT MOTAC anda telah diluluskan.'))
            ->line(__('Nombor Rujukan Permohonan: #:id', ['id' => $applicationId]))
            ->line(__('Status Permohonan: Diluluskan'))
            ->line(__('Proses penyediaan akaun e-mel anda akan dilaksanakan oleh Bahagian Pengurusan Maklumat (BPM). Anda akan dimaklumkan melalui e-mel berasingan setelah akaun e-mel anda berjaya disediakan, termasuk maklumat akaun dan kata laluan sementara.'))
            ->line(__('Sekiranya anda memerlukan maklumat lanjut, sila hubungi Bahagian Pengurusan Maklumat (BPM) MOTAC.'));

        $applicationUrl = '#';
        if (isset($this->emailApplication->id) && Route::has('resource-management.my-applications.email.show')) {
            try {
                $applicationUrl = route('resource-management.my-applications.email.show', $this->emailApplication->id);
            } catch (\Throwable $e) {
                Log::error("Failed to generate route for EmailApplicationApprovedNotification: {$e->getMessage()}", [
                    'application_id' => $this->emailApplication->id,
                    'route_name' => 'resource-management.my-applications.email.show',
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action(__('Lihat Permohonan Anda'), $applicationUrl);
        }

        return $mailMessage->salutation(__('Sekian, terima kasih.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->emailApplication->id ?? null;
        $applicantId = $this->emailApplication->user_id ?? ($notifiable->id ?? null);

        $applicationUrl = null;
        if ($applicationId !== null && Route::has('resource-management.my-applications.email.show')) {
            try {
                $generatedUrl = route('resource-management.my-applications.email.show', $applicationId);
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
                    $applicationUrl = $generatedUrl;
                }
            } catch (\Throwable $e) {
                Log::error("Failed to generate route for EmailApplicationApprovedNotification (toArray): {$e->getMessage()}", [
                    'application_id' => $applicationId,
                    'route_name' => 'resource-management.my-applications.email.show',
                ]);
            }
        }

        return [
            'application_type_morph' => $this->emailApplication->getMorphClass(),
            'application_id' => $applicationId,
            'applicant_id' => $applicantId,
            'subject' => __("Permohonan E-mel Diluluskan (#:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Permohonan akaun e-mel ICT MOTAC anda (#:id) telah diluluskan.", ['id' => $applicationId ?? 'N/A']),
            'url' => $applicationUrl,
            'status' => 'approved',
            'icon' => 'ti ti-mail-check', // Example icon
        ];
    }
}
