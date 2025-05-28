<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

/**
 * Class EmailProvisionedNotification
 *
 * Notifies the applicant that their Email/User ID application has been processed and the account is ready.
 * Consolidates previous EmailProvisionedNotification and EmailProvisioningComplete.
 */
class EmailProvisionedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $emailApplication;

    public function __construct(EmailApplication $emailApplication)
    {
        $this->emailApplication = $emailApplication->loadMissing('user'); // Eager load user
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->emailApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->emailApplication->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Makluman: Akaun E-mel/ID Pengguna ICT MOTAC Anda Telah Sedia (#:id)", ['id' => $applicationId]))
            ->greeting(__('Tahniah, :name!', ['name' => $applicantName]));

        $assignedEmail = $this->emailApplication->final_assigned_email;
        $assignedUserId = $this->emailApplication->final_assigned_user_id;

        if ($assignedEmail || $assignedUserId) {
            $mailMessage->line(__('Permohonan anda untuk Akaun E-mel / ID Pengguna MOTAC (#:id) telah berjaya diproses dan akaun anda kini aktif.', ['id' => $applicationId]));
            if ($assignedEmail) {
                $mailMessage->line(__('Alamat e-mel MOTAC rasmi anda ialah: **:email**', ['email' => $assignedEmail]));
            }
            if ($assignedUserId) {
                $mailMessage->line(__('ID Pengguna anda ialah: **:userId**', ['userId' => $assignedUserId]));
            }
            $mailMessage->line(__('Untuk mendapatkan kata laluan awal (jika berkenaan) dan arahan log masuk, sila rujuk maklumat yang mungkin dihantar secara berasingan atau hubungi Bahagian Pengurusan Maklumat (BPM) jika anda mempunyai sebarang pertanyaan.'));
        } else {
            // Fallback if somehow no email or ID is assigned but notification is triggered
            $mailMessage->line(__('Proses penyediaan permohonan akaun e-mel/ID pengguna ICT anda (Ruj: #:id) telah selesai.', ['id' => $applicationId]));
            $mailMessage->line(__('Sila semak sistem untuk butiran akaun anda atau nantikan maklumat lanjut daripada BPM.'));
        }


        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.email.show'; // Standardized route
        if ($this->emailApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, ['email_application' => $this->emailApplication->id]);
            } catch (\Throwable $e) {
                Log::error("Error generating URL for EmailProvisionedNotification mail: {$e->getMessage()}", [
                    'application_id' => $this->emailApplication->id,
                    'route_name' => $routeName,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Status Permohonan'), $applicationUrl);
        }

        $mailMessage->line(__('Sila jaga kerahsiaan kata laluan anda.'))
                      ->salutation(__('Sekian, terima kasih.'));
        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->emailApplication->id ?? null;
        $applicantName = $this->emailApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $assignedEmail = $this->emailApplication->final_assigned_email ?? null;
        $assignedUserId = $this->emailApplication->final_assigned_user_id ?? null;

        $messageText = __('Akaun e-mel/ID pengguna MOTAC anda (#:appId) kini aktif.', ['appId' => $applicationId ?? 'N/A']);
        if ($assignedEmail) {
            $messageText .= ' ' . __('E-mel: :email.', ['email' => $assignedEmail]);
        }
        if ($assignedUserId) {
            $messageText .= ' ' . __('ID Pengguna: :userId.', ['userId' => $assignedUserId]);
        }
        if (!$assignedEmail && !$assignedUserId) {
            $messageText = __('Proses penyediaan akaun/ID pengguna ICT anda (#:appId) telah selesai.', ['appId' => $applicationId ?? 'N/A']);
        }


        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.email.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, ['email_application' => $applicationId]);
            } catch (\Throwable $e) {
                Log::error("Error generating URL for EmailProvisionedNotification toArray: " . $e->getMessage(), ['application_id' => $applicationId]);
                $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->emailApplication->getMorphClass(),
            'applicant_name' => $applicantName,
            'status_key' => $this->emailApplication->status ?? EmailApplication::STATUS_COMPLETED, // Use constant or string
            'assigned_email' => $assignedEmail,
            'assigned_user_id' => $assignedUserId,
            'subject' => __('Akaun E-mel/ID Pengguna Anda Telah Aktif'),
            'message' => $messageText,
            'url' => ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null,
            'icon' => 'ti ti-user-check',
        ];
    }
}
