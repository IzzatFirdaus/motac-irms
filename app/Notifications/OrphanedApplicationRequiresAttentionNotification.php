<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrphanedApplicationRequiresAttentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $application;

    public string $applicationTypeDisplay;

    public string $reason;

    /**
     * Create a new notification instance.
     *
     * @param Model  $application The orphaned application instance (e.g., LoanApplication)
     * @param string $reason      A brief reason why it's orphaned (e.g., "No approver found")
     */
    public function __construct(Model $application, string $reason = 'No suitable approver could be automatically assigned.')
    {
        $this->application = $application;
        $this->reason      = $reason;

        if ($this->application instanceof LoanApplication) {
            $this->applicationTypeDisplay = __('Permohonan Pinjaman ICT');
        } else {
            $this->applicationTypeDisplay = __('Permohonan Tidak Diketahui');
            Log::warning('OrphanedApplicationRequiresAttentionNotification: Unknown application type encountered.', ['application_class' => get_class($application)]);
        }
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $applicationId = $this->application->id ?? 'N/A';
        $viewUrl       = '#';

        if ($this->application instanceof LoanApplication && $this->application->id) {
            $viewUrl = route('loan-applications.show', $this->application->id);
        }

        return (new MailMessage)
            ->subject(__('Tindakan Diperlukan: :appType ID #:id Memerlukan Penetapan Pelulus', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]))
            ->greeting(__('Salam Sejahtera,'))
            ->line(__('Sistem tidak dapat menetapkan pelulus secara automatik untuk :appType ID #:id. Alasan: :reason. Sila ambil tindakan.', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId, 'reason' => $this->reason]))
            ->action(__('Lihat Permohonan', ['appType' => $this->applicationTypeDisplay]), $viewUrl !== '#' ? $viewUrl : url('/'))
            ->line(__('Terima Kasih.'))
            ->salutation(__('Kementerian Pelancongan, Seni dan Budaya (MOTAC).'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $applicationId = $this->application->id ?? 'N/A';
        $viewUrl       = '#';
        if ($this->application instanceof LoanApplication && $this->application->id) {
            $viewUrl = route('loan-applications.show', $this->application->id);
        }

        // Add route for other application types

        return [
            'application_id'           => $applicationId,
            'application_type_morph'   => $this->application->getMorphClass(),
            'application_type_display' => $this->applicationTypeDisplay,
            'applicant_name'           => $this->application->user?->name ?? __('Tidak diketahui'),
            'status_key'               => $this->application->status      ?? 'orphaned_attention',
            'title'                    => __(':appType ID #:id Memerlukan Penetapan Pelulus', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]),
            'message'                  => __('Sistem tidak dapat menetapkan pelulus secara automatik untuk :appType ID #:id. Alasan: :reason. Sila ambil tindakan.', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId, 'reason' => $this->reason]),
            'icon'                     => 'ti ti-alert-triangle', // Alert icon
            'url'                      => $viewUrl,
            'notification_type'        => 'system_attention',
        ];
    }
}
