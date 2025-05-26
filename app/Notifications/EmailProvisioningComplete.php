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

final class EmailProvisioningComplete extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication $application;

    public function __construct(EmailApplication $application)
    {
        $this->application = $application->loadMissing('user');
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->application->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Akaun E-mel / ID Pengguna MOTAC Anda Telah Sedia Digunakan (#:id)", ['id' => $applicationId]))
            ->greeting(__('Tahniah, :name!', ['name' => $applicantName]))
            ->line(__('Permohonan anda untuk Akaun E-mel / ID Pengguna MOTAC (#:id) telah berjaya diproses dan akaun anda kini aktif.', ['id' => $applicationId]));

        if (!empty($this->application->final_assigned_email)) {
            $mailMessage->line(__('Alamat E-mel MOTAC rasmi anda ialah: **:email**', ['email' => $this->application->final_assigned_email]));
        }
        if (!empty($this->application->final_assigned_user_id)) {
            $mailMessage->line(__('ID Pengguna anda ialah: **:idPengguna**', ['idPengguna' => $this->application->final_assigned_user_id]));
        }

        if (empty($this->application->final_assigned_email) && empty($this->application->final_assigned_user_id)) {
            $mailMessage->line(__('Sila semak sistem untuk butiran lanjut atau nantikan maklumat tambahan.'));
        }

        $mailMessage->line(__('Untuk mendapatkan kata laluan awal (jika berkenaan) dan arahan log masuk, sila rujuk maklumat yang mungkin dihantar secara berasingan atau hubungi Bahagian Pengurusan Maklumat (BPM) jika anda mempunyai sebarang pertanyaan.'));

        $applicationShowUrl = '#';
        $routeName = 'resource-management.my-applications.email.show'; // Standardized route
        if ($this->application->id && Route::has($routeName)) {
            try {
                $applicationShowUrl = route($routeName, $this->application->id);
            } catch (\Exception $e) {
                Log::error('Error generating route for EmailProvisioningComplete mail: ' . $e->getMessage(), ['application_id' => $this->application->id, 'route_name' => $routeName]);
                $applicationShowUrl = '#'; // Fallback
            }
        }

        if ($applicationShowUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Permohonan'), $applicationShowUrl);
        }

        $mailMessage->line(__('Sila jaga kerahsiaan kata laluan anda.'))
            ->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? __('Pemohon Tidak Dikenali');
        $assignedEmail = $this->application->final_assigned_email;
        $assignedUserId = $this->application->final_assigned_user_id;

        $applicationShowUrl = '#';
        $routeName = 'resource-management.my-applications.email.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationShowUrl = route($routeName, $applicationId);
            } catch (\Exception $e) {
                Log::error('Error generating route for EmailProvisioningComplete array: ' . $e->getMessage(), ['application_id' => $applicationId, 'route_name' => $routeName]);
                $applicationShowUrl = '#'; // Fallback
            }
        }

        $message = __('Akaun e-mel/ID pengguna MOTAC anda (#:appId) untuk :name kini aktif.', [
            'appId' => $applicationId ?? 'N/A',
            'name' => $applicantName,
        ]);
        if (!empty($assignedEmail)) {
            $message .= ' ' . __('E-mel: :email.', ['email' => $assignedEmail]);
        }
        if (!empty($assignedUserId)) {
            $message .= ' ' . __('ID Pengguna: :userId.', ['userId' => $assignedUserId]);
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'applicant_name' => $applicantName,
            'status' => $this->application->status ?? EmailApplication::STATUS_COMPLETED,
            'assigned_email' => $assignedEmail,
            'assigned_user_id' => $assignedUserId,
            'subject' => __('Akaun E-mel/ID Pengguna Telah Aktif'),
            'message' => $message,
            'url' => ($applicationShowUrl !== '#') ? $applicationShowUrl : null,
            'icon' => 'ti ti-circle-check-filled',
        ];
    }
}
