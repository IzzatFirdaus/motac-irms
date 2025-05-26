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

class EmailProvisionedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $emailApplication;

    public function __construct(EmailApplication $emailApplication)
    {
        $this->emailApplication = $emailApplication->loadMissing('user'); // Eager load user
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicantName = $this->emailApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->emailApplication->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Makluman Penyediaan Akaun E-mel/ID Pengguna ICT MOTAC (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]));

        $assignedEmail = $this->emailApplication->final_assigned_email;
        $assignedUserId = $this->emailApplication->final_assigned_user_id;

        if ($assignedEmail) {
            $mailMessage->line(__('Akaun e-mel ICT MOTAC anda telah berjaya disediakan.'));
            $mailMessage->line(__('Alamat e-mel anda ialah: **:email**', ['email' => $assignedEmail]));
            $mailMessage->line(__('Anda akan menerima arahan berasingan melalui emel peribadi anda mengenai cara untuk menetapkan kata laluan dan mengakses e-mel.'));
        } elseif ($assignedUserId) {
            $mailMessage->line(__('ID pengguna ICT MOTAC anda telah berjaya disediakan.'));
            $mailMessage->line(__('ID Pengguna yang diberikan kepada anda ialah: **:userId**', ['userId' => $assignedUserId]));
            $mailMessage->line(__('Anda akan menerima arahan berasingan mengenai cara menggunakan ID Pengguna anda.'));
        } else {
            $mailMessage->line(__('Proses penyediaan permohonan akaun e-mel/ID pengguna ICT anda (Ruj: #:id) telah selesai.', ['id' => $applicationId]));
            $mailMessage->line(__('Sila semak sistem untuk butiran akaun anda atau nantikan maklumat lanjut daripada BPM.'));
        }

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.email.show'; // Standardized route
        if ($this->emailApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->emailApplication->id);
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

        $mailMessage->line(__('Terima kasih kerana menggunakan perkhidmatan kami!'))
                      ->salutation(__('Sekian, harap maklum.'));
        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $applicationId = $this->emailApplication->id ?? null;
        $applicantName = $this->emailApplication->user?->name ?? __('Pemohon');
        $assignedEmail = $this->emailApplication->final_assigned_email ?? null;
        $assignedUserId = $this->emailApplication->final_assigned_user_id ?? null;

        $messageText = __('Proses penyediaan akaun/ID pengguna ICT anda telah selesai.');
        if ($assignedEmail) {
            $messageText = __('Akaun e-mel ICT anda (:email) telah disediakan.', ['email' => $assignedEmail]);
        } elseif ($assignedUserId) {
            $messageText = __('ID pengguna ICT anda (:userId) telah disediakan.', ['userId' => $assignedUserId]);
        }

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.email.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $applicationId);
            } catch (\Throwable $e) {
                Log::error("Error generating URL for EmailProvisionedNotification toArray: " . $e->getMessage(), ['application_id' => $applicationId]);
                $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->emailApplication->getMorphClass(),
            'applicant_name' => $applicantName,
            'subject' => __('Akaun E-mel/ID Telah Disediakan'),
            'message' => $messageText,
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'status_key' => $this->emailApplication->status ?? 'provisioned',
            'assigned_email' => $assignedEmail,
            'assigned_user_id' => $assignedUserId,
            'icon' => 'ti ti-user-check',
        ];
    }
}
