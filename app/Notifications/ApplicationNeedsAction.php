<?php

namespace App\Notifications;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationNeedsAction extends Notification implements ShouldQueue
{
    use Queueable;

    public Approval $approvalTask;

    public Model $approvableItem;

    /**
     * Create a new notification instance.
     * The constructor now only requires the Approval task.
     */
    public function __construct(Approval $approvalTask)
    {
        $this->approvalTask = $approvalTask;
        // The related application is derived directly from the approval task
        $this->approvableItem = $approvalTask->approvable()->with('user')->firstOrFail();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $itemTypeDisplayName = $this->getItemTypeDisplayName();
        $applicationId = $this->approvableItem->id;

        $subject = "Tindakan Diperlukan: {$itemTypeDisplayName} #{$applicationId}";
        $url = route('approvals.dashboard'); // Link to the approver's dashboard

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Salam Sejahtera, '.$notifiable->name.',')
            ->line("Satu {$itemTypeDisplayName} baru telah diserahkan dan memerlukan tindakan kelulusan daripada anda.")
            ->line('**Pemohon:** '.$this->approvableItem->user->name)
            ->line('**Peringkat:** '.Approval::getStageDisplayName($this->approvalTask->stage))
            ->action('Lihat Tugasan', $url)
            ->line('Sila log masuk ke dalam sistem untuk menyemak butiran permohonan.');
    }

    /**
     * Get the array representation for the database notification.
     */
    public function toArray(User $notifiable): array
    {
        return [
            'title' => 'Tindakan Kelulusan Diperlukan',
            'message' => 'Permohonan #'.$this->approvableItem->id.' oleh '.$this->approvableItem->user->name.' menunggu tindakan anda.',
            'action_url' => route('approvals.dashboard'),
            'related_model' => $this->approvableItem->getMorphClass(),
            'related_id' => $this->approvableItem->id,
        ];
    }

    /**
     * Helper to get a user-friendly name for the application type.
     */
    private function getItemTypeDisplayName(): string
    {
        if ($this->approvableItem instanceof LoanApplication) {
            return __('Permohonan Pinjaman Peralatan ICT');
        }
        if ($this->approvableItem instanceof EmailApplication) {
            return __('Permohonan E-mel/ID Pengguna');
        }

        return __('Permohonan Umum');
    }
}
