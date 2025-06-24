<?php

namespace App\Notifications;

use App\Models\LoanApplication; // Assuming it's for LoanApplication, can be made more generic
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log; // <--- ADD THIS LINE

class OrphanedApplicationRequiresAttentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $application; // Using generic Model type

    public string $applicationTypeDisplay;

    public string $reason;

    /**
     * Create a new notification instance.
     *
     * @param  Model  $application  The orphaned application instance (e.g., LoanApplication)
     * @param  string  $reason  A brief reason why it's orphaned (e.g., "No approver found")
     */
    public function __construct(Model $application, string $reason = 'No suitable approver could be automatically assigned.') //
    {
        $this->application = $application;
        $this->reason = $reason;

        if ($this->application instanceof LoanApplication) {
            $this->applicationTypeDisplay = __('Permohonan Pinjaman ICT');
        }
        // Add other application types if needed
        // elseif ($this->application instanceof EmailApplication) {
        //     $this->applicationTypeDisplay = __('Permohonan E-mel/ID Pengguna');
        // }
        else {
            // Fallback for any other model type if this notification is used more broadly
            $this->applicationTypeDisplay = __('Permohonan Sistem');
        }

        Log::info('OrphanedApplicationRequiresAttentionNotification created for '.get_class($application).(' ID: '.$application->id));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['mail', 'database']; // Send via email and store in DB for admin dashboard
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $applicantName = $this->application->user?->name ?? __('Tidak diketahui');
        $applicationId = $this->application->id ?? 'N/A';
        $subject = __('[TINDAKAN DIPERLUKAN] :appType ID #:id Memerlukan Penetapan Pelulus Segera', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]);

        $viewUrl = '#';
        if ($this->application instanceof LoanApplication && $this->application->id) {
            $viewUrl = route('loan-applications.show', $this->application->id);
        }

        // Add route for other application types if necessary

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('Amaran Sistem Pentadbiran,'))
            ->line(__(':appType dengan ID #:id yang dimohon oleh :applicantName memerlukan perhatian segera.', [
                'appType' => $this->applicationTypeDisplay,
                'id' => $applicationId,
                'applicantName' => $applicantName,
            ]))
            ->line(__('Sebab: :reason', ['reason' => $this->reason]))
            ->line(__("Status semasa permohonan ialah ':status'. Sila semak permohonan ini dan tetapkan pegawai pelulus yang bersesuaian dengan kadar segera untuk mengelakkan kelewatan proses.", ['status' => $this->application->status_label ?? $this->application->status]))
            ->action(__('Lihat Permohonan'), $viewUrl)
            ->line(__('Ini adalah notifikasi automatik dari Sistem Pengurusan Sumber MOTAC.'))
            ->salutation(__('Terima Kasih.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        $applicationId = $this->application->id ?? 'N/A';
        $viewUrl = '#';
        if ($this->application instanceof LoanApplication && $this->application->id) {
            $viewUrl = route('loan-applications.show', $this->application->id);
        }

        // Add route for other application types

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'application_type_display' => $this->applicationTypeDisplay,
            'applicant_name' => $this->application->user?->name ?? __('Tidak diketahui'),
            'status_key' => $this->application->status ?? 'orphaned_attention',
            'title' => __(':appType ID #:id Memerlukan Penetapan Pelulus', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]),
            'message' => __('Sistem tidak dapat menetapkan pelulus secara automatik untuk :appType ID #:id. Alasan: :reason. Sila ambil tindakan.', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId, 'reason' => $this->reason]),
            'icon' => 'ti ti-alert-triangle', // Alert icon
            'url' => $viewUrl,
            'notification_type' => 'system_alert', // For categorizing
        ];
    }
}
