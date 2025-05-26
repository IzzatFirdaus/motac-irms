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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ApplicationNeedsAction extends Notification implements ShouldQueue
{
    use Queueable;

    public Approval $approvalTask;
    public Model $approvableItem; // e.g., EmailApplication or LoanApplication

    public function __construct(Approval $approvalTask, Model $approvableItem)
    {
        $this->approvalTask = $approvalTask;
        $this->approvableItem = $approvableItem->loadMissing('user'); // Eager load applicant
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function getItemTypeDisplayName(): string
    {
        if ($this->approvableItem instanceof EmailApplication) {
            return __('Permohonan E-mel/ID Pengguna');
        } elseif ($this->approvableItem instanceof LoanApplication) {
            return __('Permohonan Pinjaman Peralatan ICT');
        }
        return __('Permohonan Umum'); // Fallback
    }

    public function toMail(User $notifiable): MailMessage
    {
        $itemTypeDisplayName = $this->getItemTypeDisplayName();
        $applicationId = $this->approvableItem->id ?? 'N/A';
        $stageName = Approval::getStageDisplayName($this->approvalTask->stage);
        $applicantName = $this->approvableItem->user?->name ?? __('Pemohon Tidak Dikenali');

        $subject = __('Tindakan Diperlukan: :itemType #:applicationId - Peringkat :stageName', [
            'itemType' => $itemTypeDisplayName,
            'applicationId' => $applicationId,
            'stageName' => $stageName,
        ]);

        $viewUrl = '#';
        if ($this->approvalTask->id && Route::has('approvals.show')) { // Check if route exists
            try {
                $viewUrl = route('approvals.show', $this->approvalTask->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationNeedsAction mail: '.$e->getMessage(), ['approval_task_id' => $this->approvalTask->id]);
                $viewUrl = '#'; // Fallback
            }
        }

        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->greeting(__('Salam :name,', ['name' => $notifiable->name]))
            ->line(__('Satu :itemType memerlukan perhatian anda untuk peringkat kelulusan ":stageName".', ['itemType' => strtolower($itemTypeDisplayName), 'stageName' => $stageName]))
            ->line(__('Butiran Permohonan:'))
            ->line(__('- Jenis: :itemType', ['itemType' => $itemTypeDisplayName]))
            ->line(__('- ID Permohonan: #:applicationId', ['applicationId' => $applicationId]))
            ->line(__('- Pemohon: :applicantName', ['applicantName' => $applicantName]));

        $purposeOrNotes = null;
        if ($this->approvableItem instanceof LoanApplication) {
            $purposeOrNotes = $this->approvableItem->purpose;
            if ($purposeOrNotes) $mailMessage->line(__('- Tujuan: :purpose', ['purpose' => $purposeOrNotes]));
        } elseif ($this->approvableItem instanceof EmailApplication) {
            $purposeOrNotes = $this->approvableItem->application_reason_notes;
             if ($purposeOrNotes) $mailMessage->line(__('- Tujuan/Catatan: :notes', ['notes' => $purposeOrNotes]));
        }


        if ($viewUrl !== '#') {
            $mailMessage->action(__('Lihat Tugasan Kelulusan'), $viewUrl);
        }

        $mailMessage->line(__('Sila log masuk ke sistem untuk mengambil tindakan selanjutnya.'))
            ->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $itemTypeDisplayName = $this->getItemTypeDisplayName();
        $applicationId = $this->approvableItem->id ?? null;
        $stageName = Approval::getStageDisplayName($this->approvalTask->stage);
        $applicantName = $this->approvableItem->user?->name ?? __('Pemohon Tidak Dikenali');

        $viewUrl = '#';
        if ($this->approvalTask->id && Route::has('approvals.show')) { // Check if route exists
            try {
                $viewUrl = route('approvals.show', $this->approvalTask->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationNeedsAction array: '.$e->getMessage(), ['approval_task_id' => $this->approvalTask->id]);
                $viewUrl = '#'; // Fallback
            }
        }

        return [
            'approval_task_id' => $this->approvalTask->id,
            'approvable_item_id' => $applicationId,
            'approvable_item_type_display' => $itemTypeDisplayName,
            'approvable_item_morph_class' => $this->approvableItem->getMorphClass(),
            'applicant_name' => $applicantName,
            'stage_key' => $this->approvalTask->stage,
            'stage_display_name' => $stageName,
            'subject' => __('Tindakan Diperlukan untuk :itemType #:applicationId', ['itemType' => $itemTypeDisplayName, 'applicationId' => $applicationId ?? 'N/A']),
            'message' => __('Permohonan :itemType #:applicationId oleh :applicantName memerlukan tindakan anda di peringkat ":stageName".', [
                'itemType' => strtolower($itemTypeDisplayName),
                'applicationId' => $applicationId ?? 'N/A',
                'applicantName' => $applicantName,
                'stageName' => $stageName,
            ]),
            'url' => ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) ? $viewUrl : null,
            'icon' => 'ti ti-bell-ringing',
        ];
    }
}
