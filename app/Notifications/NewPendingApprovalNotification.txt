<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model; // For type hinting $approvable
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class NewPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Approval $approval;
    protected EmailApplication|LoanApplication|Model $approvable; // Ensure it's one of these or a generic Model

    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
        $this->approval->loadMissing(['approvable.user']);

        /** @var EmailApplication|LoanApplication|Model|null $approvableModel */
        $approvableModel = $this->approval->approvable;

        if (!$approvableModel) {
            Log::critical('NewPendingApprovalNotification: Approval record is missing its approvable model!', ['approval_id' => $this->approval->id]);
            // This is a critical issue, consider throwing an exception or handling it gracefully.
            // For now, we'll proceed, but notifications might be incomplete.
            // If $approvableModel must be set, an exception here is better.
            throw new \LogicException("Tugasan Kelulusan ID {$this->approval->id} tidak mempunyai item berkaitan yang sah.");
        }
        $this->approvable = $approvableModel;

        Log::info('NewPendingApprovalNotification created.', [
            'approval_id' => $this->approval->id,
            'approvable_type' => $this->approvable::class,
            'approvable_id' => $this->approvable->id,
        ]);
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function getApprovableTypeDisplayName(): string
    {
        if ($this->approvable instanceof EmailApplication) return __('Permohonan E-mel/ID Pengguna');
        if ($this->approvable instanceof LoanApplication) return __('Permohonan Pinjaman Peralatan ICT');
        return __('Permohonan Umum');
    }

    private function getApprovablePurposeOrNotes(): string
    {
        if ($this->approvable instanceof LoanApplication) return $this->approvable->purpose ?? __('Tiada Tujuan Dinyatakan');
        if ($this->approvable instanceof EmailApplication) return $this->approvable->application_reason_notes ?? __('Tiada Catatan');
        return __('Tiada Butiran Lanjut');
    }

    public function toMail(User $notifiable): MailMessage
    {
        $approvableTypeDisplay = $this->getApprovableTypeDisplayName();
        $applicantName = $this->approvable->user?->name ?? __('Pengguna Tidak Dikenali');
        $approvableId = $this->approvable->id ?? 'N/A';
        $approverName = $notifiable->name ?? __('Pegawai Pelulus');
        $stageName = $this->approval->stageTranslated ?? Approval::getStageDisplayName($this->approval->stage);
        $purposeOrNotes = $this->getApprovablePurposeOrNotes();

        $actionUrl = '#';
        $approvalRouteName = 'approvals.show'; // Preferred route for approvers

        if ($this->approval->id && Route::has($approvalRouteName)) {
            try {
                $actionUrl = route($approvalRouteName, $this->approval->id);
            } catch (\Throwable $e) {
                Log::error("Failed to generate approval task URL for NewPendingApprovalNotification (toMail): {$e->getMessage()}", ['approval_id' => $this->approval->id]);
                $actionUrl = '#'; // Fallback below if this fails
            }
        }

        // Fallback to application show page if approval task URL fails or is not desired
        if ($actionUrl === '#') {
            $appRouteName = null;
            if ($this->approvable instanceof EmailApplication) $appRouteName = 'resource-management.admin.email-applications.show';
            elseif ($this->approvable instanceof LoanApplication) $appRouteName = 'resource-management.admin.loan-applications.show'; // Assuming admin view

            if ($appRouteName && $this->approvable->id && Route::has($appRouteName)) {
                try {
                    $actionUrl = route($appRouteName, $this->approvable->id);
                } catch (\Throwable $e) {
                    Log::error("Failed to generate application URL for NewPendingApprovalNotification (toMail): {$e->getMessage()}", ['approvable_id' => $this->approvable->id]);
                    $actionUrl = '#';
                }
            }
        }


        $mailMessage = (new MailMessage())
            ->subject(__("Tindakan Diperlukan: :type (#:id) - Peringkat :stage", ['type' => $approvableTypeDisplay, 'id' => $approvableId, 'stage' => $stageName]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $approverName]))
            ->line(__("Satu :type memerlukan semakan dan tindakan anda pada peringkat ':stage'.", ['type' => strtolower($approvableTypeDisplay), 'stage' => $stageName]))
            ->line(__('Butiran Permohonan:'))
            ->line(__('- Jenis: :type', ['type' => $approvableTypeDisplay]))
            ->line(__('- ID Rujukan: #:id', ['id' => $approvableId]))
            ->line(__('- Pemohon: :name', ['name' => $applicantName]))
            ->line(__('- Tujuan/Catatan: :purpose', ['purpose' => $purposeOrNotes]));

        if ($actionUrl !== '#') {
            $mailMessage->action(__('Semak Tugasan Kelulusan'), $actionUrl);
        }

        $mailMessage->line(__('Sila log masuk ke sistem untuk mengambil tindakan sewajarnya.'))
                      ->salutation(__('Sekian, terima kasih.'));
        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $approvableTypeDisplay = $this->getApprovableTypeDisplayName();
        $approvableId = $this->approvable->id ?? null;
        $applicantName = $this->approvable->user?->name ?? __('Pengguna Tidak Dikenali');
        $stageName = $this->approval->stageTranslated ?? Approval::getStageDisplayName($this->approval->stage);

        $actionUrl = null;
        $approvalRouteName = 'approvals.show';
        if ($this->approval->id && Route::has($approvalRouteName)) {
            try {
                $generatedUrl = route($approvalRouteName, $this->approval->id);
                if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) $actionUrl = $generatedUrl;
            } catch (\Throwable $e) { /* Logged in toMail */ }
        }
        // Fallback URL generation can be added here if needed, similar to toMail()

        return [
            'approval_id' => $this->approval->id,
            'approvable_id' => $approvableId,
            'approvable_type_morph' => $this->approvable->getMorphClass(),
            'approvable_type_display' => $approvableTypeDisplay,
            'applicant_name' => $applicantName,
            'stage_key' => $this->approval->stage,
            'stage_display' => $stageName,
            'subject' => __("Tindakan Diperlukan: :type #:id", ['type' => $approvableTypeDisplay, 'id' => $approvableId ?? 'N/A']),
            'message' => __(":type #:id oleh :name memerlukan tindakan anda di peringkat ':stage'.", ['type' => $approvableTypeDisplay, 'id' => $approvableId ?? 'N/A', 'name' => $applicantName, 'stage' => $stageName]),
            'url' => $actionUrl,
            'requires_action' => true,
            'icon' => 'ti ti-file-check',
        ];
    }
}
