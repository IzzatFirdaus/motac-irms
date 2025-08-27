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
     * NOTE: This method points to a custom Blade view. The link inside that view also needs to be updated.
     * See step 2 below for the change required in the 'emails.loan_application_ready_for_issuance' view.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $loanApplicationId = $this->loanApplication->id ?? 'N/A';

        return (new MailMessage())
            ->subject(__('Tindakan Diperlukan: Permohonan Pinjaman Sedia Untuk Pengeluaran (#:id)', ['id' => $loanApplicationId]))
            ->view('emails.loan_application_ready_for_issuance', ['loanApplication' => $this->loanApplication]);
    }

    /**
     * Get the array representation of the notification.
     * This is sent to the 'database' channel for in-app notifications.
     */
    public function toArray(User $notifiable): array
    {
        $loanApp           = $this->loanApplication;
        $loanApplicationId = $loanApp->id          ?? null;
        $applicantName     = $loanApp->user?->name ?? 'Pemohon Tidak Dikenali';

        $applicationUrl = '#';
        if ($loanApplicationId) {
            try {
                // --- THIS IS THE FIX ---
                // The route has been changed from 'loan-applications.show' to 'loan-applications.issue.form'.
                // This ensures the link for the in-app notification takes the user directly to the issuance form.
                $applicationUrl = route('loan-applications.issue.form', $loanApplicationId);
            } catch (\Exception $e) {
                // The original error logging is good practice and remains unchanged.
                Log::error('Error generating URL for LoanApplicationReadyForIssuanceNotification array: ' . $e->getMessage(), ['loan_application_id' => $loanApplicationId]);
            }
        }

        $itemDetails = $loanApp->loanApplicationItems->map(function ($item): string {
            return $item->equipment_type . ' (Kuantiti: ' . ($item->quantity_approved ?? $item->quantity_requested) . ')';
        })->toArray();

        // The returned array now includes the corrected URL.
        return [
            'loan_application_id' => $loanApplicationId,
            'applicant_name'      => $applicantName,
            'status_application'  => $loanApp->status,
            'subject'             => __('Permohonan Pinjaman #:id Sedia Untuk Pengeluaran', ['id' => $loanApplicationId ?? 'N/A']),
            'message'             => __('Permohonan pinjaman #:id oleh :applicantName sedia untuk pengeluaran peralatan. Item: :items', [
                'id'            => $loanApplicationId ?? 'N/A',
                'applicantName' => $applicantName,
                'items'         => implode(', ', $itemDetails),
            ]),
            'url'             => $applicationUrl,
            'icon'            => 'ti ti-package',
            'action_required' => true,
        ];
    }
}
