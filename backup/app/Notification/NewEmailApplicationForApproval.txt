<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

final class NewEmailApplicationForApproval extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $application;
    protected Approval $approval;

    public function __construct(EmailApplication $application, Approval $approval)
    {
        $this->application = $application->loadMissing('user'); // Eager load applicant
        $this->approval = $approval;
        Log::info("NewEmailApplicationForApproval notification created for EmailApplication ID: {$this->application->id}, Approval ID: {$this->approval->id}");
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationId = $this->application->id ?? 'N/A';
        $applicantName = $this->application->user?->name ?? __('Pengguna Tidak Dikenali');
        $applicationPurposeOrNotes = $this->application->application_reason_notes ?? $this->application->purpose ?? __('Tiada Tujuan Dinyatakan');
        $approverName = $notifiable->name ?? __('Pegawai Pelulus');
        $stageName = $this->approval->stageTranslated ?? Approval::getStageDisplayName($this->approval->stage);

        $actionUrl = '#';
        $approvalRouteName = 'approvals.show'; // Prefer link to the approval task
        $appRouteName = 'resource-management.admin.email-applications.show'; // Fallback to admin view of application

        if ($this->approval->id && Route::has($approvalRouteName)) {
            try {
                $actionUrl = route($approvalRouteName, $this->approval->id);
            } catch (\Throwable $e) {
                Log::error("Failed to generate approval route for NewEmailApplicationForApproval (toMail): {$e->getMessage()}", ['approval_id' => $this->approval->id]);
                $actionUrl = '#'; // Clear to try next
            }
        }

        if ($actionUrl === '#' && $this->application->id && Route::has($appRouteName)) {
            try {
                $actionUrl = route($appRouteName, $this->application->id);
            } catch (\Throwable $e) {
                Log::error("Failed to generate application route for NewEmailApplicationForApproval (toMail): {$e->getMessage()}", ['application_id' => $this->application->id]);
                $actionUrl = '#'; // Final fallback
            }
        }

        $mailMessage = (new MailMessage())
            ->subject(__("Tindakan Diperlukan: Permohonan E-mel/ID (#:appId) - Peringkat :stage", ['appId' => $applicationId, 'stage' => $stageName]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $approverName]))
            ->line(__('Satu Permohonan Akaun E-mel / ID Pengguna MOTAC memerlukan semakan dan tindakan anda pada peringkat ":stage".', ['stage' => $stageName]))
            ->line(__('Butiran Permohonan:'))
            ->line(__('- ID Permohonan: #:id', ['id' => $applicationId]))
            ->line(__('- Pemohon: :name', ['name' => $applicantName]))
            ->line(__('- Tujuan/Catatan: :purpose', ['purpose' => $applicationPurposeOrNotes]));

        if ($actionUrl !== '#') {
            $mailMessage->action(__('Semak Permohonan'), $actionUrl);
        }

        $mailMessage->line(__('Sila log masuk ke sistem untuk mengambil tindakan sewajarnya (luluskan atau tolak).'))
                      ->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? __('Pengguna Tidak Dikenali');
        $stageName = $this->approval->stageTranslated ?? Approval::getStageDisplayName($this->approval->stage);

        $actionUrl = null;
        $approvalRouteName = 'approvals.show';
        $appRouteName = 'resource-management.admin.email-applications.show';

        if ($this->approval->id && Route::has($approvalRouteName)) {
            try {
                $generatedUrl = route($approvalRouteName, $this->approval->id);
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) $actionUrl = $generatedUrl;
            } catch (\Throwable $e) { /* Logged in toMail */ }
        }

        if (!$actionUrl && $applicationId && Route::has($appRouteName)) {
             try {
                $generatedUrl = route($appRouteName, $applicationId);
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) $actionUrl = $generatedUrl;
            } catch (\Throwable $e) { /* Logged in toMail */ }
        }

        return [
            'approval_id' => $this->approval->id,
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'applicant_name' => $applicantName,
            'stage_key' => $this->approval->stage,
            'stage_display' => $stageName,
            'subject' => __("Tindakan Kelulusan: Permohonan E-mel #:id", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Permohonan E-mel/ID #:id oleh :name menunggu tindakan anda di peringkat ':stage'.", ['id' => $applicationId ?? 'N/A', 'name' => $applicantName, 'stage' => $stageName]),
            'url' => $actionUrl,
            'requires_action' => true,
            'icon' => 'ti ti-mail-fast',
        ];
    }
}
