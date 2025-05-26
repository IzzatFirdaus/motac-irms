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
use Illuminate\Support\HtmlString; // For rendering HTML in email if necessary (like preformatted error)
use Illuminate\Support\Str;

final class ProvisioningFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $application;
    protected string $errorMessage;
    protected ?User $adminUser; // Admin who might have triggered or is being informed

    public function __construct(
        EmailApplication $application,
        string $errorMessage,
        ?User $adminUser = null // User instance (e.g., the admin performing the action or a generic system user)
    ) {
        $this->application = $application->loadMissing('user'); // Ensure applicant is loaded
        $this->errorMessage = $errorMessage;
        $this->adminUser = $adminUser; // This might be the notifiable if sent to specific admin

        if ($this->application->user === null) {
            Log::warning("ProvisioningFailedNotification: Applicant (user) relationship is null for EmailApplication ID: " . ($this->application->id ?? 'N/A'));
        }
    }

    public function via(User $notifiable): array // $notifiable is the IT Admin/BPM staff
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // $notifiable is IT Admin/BPM Staff
    {
        $adminRecipientName = $notifiable->name ?? __('Pentadbir ICT');
        $applicantName = $this->application->user?->name ?? __('Tidak Diketahui');
        $applicationId = $this->application->id ?? 'N/A';

        $viewUrl = '#';
        $adminRouteName = 'admin.resource-management.email-applications.show'; // Specific admin route
        $genericRouteName = 'resource-management.email-applications.show'; // More generic one

        if ($this->application->id) {
            if (Route::has($adminRouteName)) {
                try {
                    $viewUrl = route($adminRouteName, $this->application->id);
                } catch (\Exception $e) {
                    Log::error("Error generating admin route for ProvisioningFailedNotification mail: {$e->getMessage()}", ['app_id' => $applicationId]);
                    $viewUrl = '#'; // Reset to try generic
                }
            }
            if ($viewUrl === '#' && Route::has($genericRouteName)) { // Fallback to generic show if admin not found
                 try {
                    $viewUrl = route($genericRouteName, $this->application->id);
                } catch (\Exception $e) {
                    Log::error("Error generating generic route for ProvisioningFailedNotification mail: {$e->getMessage()}", ['app_id' => $applicationId]);
                    $viewUrl = '#';
                }
            }
        }

        $mailMessage = (new MailMessage())
            ->subject(__("Amaran: Penyediaan E-mel/ID Gagal - Permohonan #:id", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $adminRecipientName]))
            ->error() // Mark as important
            ->line(__('Proses penyediaan E-mel/ID Pengguna untuk permohonan berikut telah **GAGAL**:'))
            ->line(__('ID Permohonan: **#:id**', ['id' => $applicationId]))
            ->line(__('Pemohon: **:name**', ['name' => $applicantName]))
            ->line('');

        if ($this->adminUser) {
             $mailMessage->line(__("Proses dicetuskan oleh: :adminName (ID: :adminId)", ['adminName' => $this->adminUser->name, 'adminId' => $this->adminUser->id]));
        }

        $mailMessage->line(__('Ralat yang dilaporkan:'))
                    // Using HtmlString to allow pre-formatted error or simple text
                    ->line(new HtmlString("<pre style='background-color:#f8f9fa; padding:10px; border:1px solid #dee2e6; white-space:pre-wrap; word-wrap:break-word;'>".e($this->errorMessage)."</pre>"))
                    ->line('');

        $mailMessage->line(__('Tindakan manual mungkin diperlukan. Sila semak log sistem dan permohonan berkaitan untuk maklumat lanjut.'));

        if ($viewUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan'), $viewUrl);
        }

        return $mailMessage->salutation(__('Harap maklum dan tindakan segera.'));
    }

    public function toArray(User $notifiable): array // $notifiable is IT Admin/BPM Staff
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? __('Tidak Diketahui');
        $triggeredByAdminName = $this->adminUser?->name ?? __('Sistem Automasi');

        $viewUrl = '#';
        $adminRouteName = 'admin.resource-management.email-applications.show';
        if ($applicationId && Route::has($adminRouteName)) {
            try {
                $viewUrl = route($adminRouteName, $applicationId);
            } catch (\Exception $e) {
                 Log::error("Error generating admin URL for ProvisioningFailedNotification toArray: " . $e->getMessage(), ['application_id' => $applicationId]);
                 $viewUrl = '#';
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'applicant_name' => $applicantName,
            'error_message' => Str::limit($this->errorMessage, 250), // Store a summary of the error
            'subject' => __("Penyediaan E-mel/ID Gagal (#:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Penyediaan E-mel/ID untuk :applicantName (Permohonan #:id) GAGAL. Punca: :reason", ['applicantName' => $applicantName, 'id' => $applicationId ?? 'N/A', 'reason' => Str::limit($this->errorMessage, 100)]),
            'url' => ($viewUrl !== '#') ? $viewUrl : null,
            'triggered_by_admin_name' => $triggeredByAdminName,
            'requires_manual_intervention' => true,
            'icon' => 'ti ti-alert-triangle-filled',
        ];
    }
}
