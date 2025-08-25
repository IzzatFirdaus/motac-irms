<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LoanApplicationReadyForIssuanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication;
        $this->loanApplication->loadMissing(['user', 'responsibleOfficer', 'loanApplicationItems']);
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $loanApplicationId = $this->loanApplication->id ?? 'N/A';

        // --- EDITED CODE: START ---
        // The MailMessage object now uses the ->view() method to render the custom Blade template.
        // We pass the $loanApplication object to the view so it has access to all the necessary data.
        return (new MailMessage)
            ->subject(__('Tindakan Diperlukan: Permohonan Pinjaman Sedia Untuk Pengeluaran (#:id)', ['id' => $loanApplicationId]))
            ->view('emails.loan_application_ready_for_issuance', ['loanApplication' => $this->loanApplication]);
        // --- EDITED CODE: END ---
    }

    /**
     * Get the array representation of the notification.
     * (No changes needed here, the database notification remains the same)
     */
    public function toArray(User $notifiable): array
    {
        $loanApp = $this->loanApplication;
        $loanApplicationId = $loanApp->id ?? null;
        $applicantName = $loanApp->user?->name ?? 'Pemohon Tidak Dikenali';

        $applicationUrl = '#';
        if ($loanApplicationId) {
            try {
                $applicationUrl = route('loan-applications.show', $loanApplicationId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for LoanApplicationReadyForIssuanceNotification array: '.$e->getMessage(), ['loan_application_id' => $loanApplicationId]);
            }
        }

        $itemDetails = $loanApp->loanApplicationItems->map(function ($item) {
            return "{$item->equipment_type} (Kuantiti: ".($item->quantity_approved ?? $item->quantity_requested).')';
        })->toArray();

        return [
            'loan_application_id' => $loanApplicationId,
            'applicant_name' => $applicantName,
            'status_application' => $loanApp->status,
            'subject' => __('Permohonan Pinjaman #:id Sedia Untuk Pengeluaran', ['id' => $loanApplicationId ?? 'N/A']),
            'message' => __('Permohonan pinjaman #:id oleh :applicantName sedia untuk pengeluaran peralatan. Item: :items', [
                'id' => $loanApplicationId ?? 'N/A',
                'applicantName' => $applicantName,
                'items' => implode(', ', $itemDetails),
            ]),
            'url' => $applicationUrl,
            'icon' => 'ti ti-package',
            'action_required' => true,
        ];
    }
}
