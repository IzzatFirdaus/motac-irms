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
use Illuminate\Support\Facades\Route;

final class EmailApplicationReadyForProcessingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication $emailApplication;

    public function __construct(EmailApplication $emailApplication)
    {
        $this->emailApplication = $emailApplication;
        $this->emailApplication->loadMissing('user');
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->emailApplication->user?->name ?? __('Pemohon Tidak Diketahui');
        // Assuming 'identification_number' is the correct field for NRIC/IC
        $applicantIdentifier = $this->emailApplication->user?->identification_number ?? 'N/A';
        $proposedEmail = $this->emailApplication->proposed_email ?? __('Tiada Cadangan');
        $purposeOrNotes = $this->emailApplication->application_reason_notes ?? __('Tiada Tujuan Dinyatakan');
        $applicationId = $this->emailApplication->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Permohonan E-mel Baru Diluluskan & Sedia Untuk Penyediaan (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Pentadbir ICT,')) // Changed greeting to be more specific
            ->line(__('Terdapat permohonan akaun e-mel ICT MOTAC baru yang telah diluluskan dan sedia untuk proses penyediaan (provisioning).'))
            ->line(__('**Nombor Rujukan Permohonan:** #:id', ['id' => $applicationId]))
            ->line(__('**Pemohon:** :name (No. KP/Pasport: :identifier)', ['name' => $applicantName, 'identifier' => $applicantIdentifier]))
            ->line(__('**Cadangan E-mel/ID:** :email', ['email' => $proposedEmail]))
            ->line(__('**Tujuan/Catatan Permohonan:** :purpose', ['purpose' => $purposeOrNotes]))
            ->line(__('Sila log masuk ke sistem untuk melihat butiran penuh permohonan dan melaksanakan proses penyediaan akaun e-mel.'));

        $adminApplicationUrl = '#';
        // Standardized admin route name pattern
        $adminRouteName = 'admin.email-applications.show';
        if (isset($this->emailApplication->id) && Route::has($adminRouteName)) {
            try {
                $adminApplicationUrl = route($adminRouteName, ['email_application' => $this->emailApplication->id]);
            } catch (\Throwable $e) {
                Log::error("Error generating admin URL for EmailApplicationReadyForProcessingNotification: {$e->getMessage()}", [
                    'application_id' => $this->emailApplication->id,
                    'route_name' => $adminRouteName,
                    'exception' => $e,
                ]);
                $adminApplicationUrl = '#';
            }
        }


        if ($adminApplicationUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan'), $adminApplicationUrl);
        }

        return $mailMessage->salutation(__('Terima kasih.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->emailApplication->id ?? null;
        $applicantId = $this->emailApplication->user_id ?? null;
        $applicantName = $this->emailApplication->user?->name ?? __('Pemohon');

        $adminApplicationUrl = null;
        $adminRouteName = 'admin.email-applications.show'; // Standardized admin route
        if ($applicationId !== null && Route::has($adminRouteName)) {
            try {
                 $generatedUrl = route($adminRouteName, ['email_application' => $applicationId]); // Pass correct param name
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
                    $adminApplicationUrl = $generatedUrl;
                }
            } catch (\Throwable $e) {
                Log::error("Error generating admin URL for EmailApplicationReadyForProcessingNotification (toArray): {$e->getMessage()}", [
                    'application_id' => $applicationId,
                    'route_name' => $adminRouteName,
                    'exception' => $e,
                ]);
            }
        }

        return [
            'application_type_morph' => $this->emailApplication->getMorphClass(),
            'application_id' => $applicationId,
            'applicant_id' => $applicantId,
            'applicant_name' => $applicantName,
            'subject' => __("Permohonan E-mel Sedia Untuk Penyediaan (#:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Permohonan E-mel ICT (#:id) oleh :name sedia untuk penyediaan.", ['id' => $applicationId ?? 'N/A', 'name' => $applicantName]),
            'url' => $adminApplicationUrl,
            'status_key' => 'ready_for_processing', // Changed 'status' to 'status_key' for consistency
            'icon' => 'ti ti-mail-forward',
            'action_required' => true, // Added for UI hints
        ];
    }
}
