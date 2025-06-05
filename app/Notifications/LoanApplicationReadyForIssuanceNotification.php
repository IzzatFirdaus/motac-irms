<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\LoanApplicationItem; // Added
use App\Models\User; // Added for type hint
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

class LoanApplicationReadyForIssuanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication;
        // Eager load the applicant (user) and the application items
        $this->loanApplication->loadMissing(['user', 'responsibleOfficer', 'loanApplicationItems']); // Corrected: load applicationItems
        // $this->onQueue('notifications'); // This is fine if you want to queue it
    }

    public function via(User $notifiable): array // Type hinted $notifiable as User
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable as User
    {
        $loanApp = $this->loanApplication; // Use a shorter alias
        $loanApplicationId = $loanApp->id ?? 'N/A';
        $applicantName = $loanApp->user?->name ?? 'Pemohon Tidak Dikenali';
        $responsibleOfficerName = $loanApp->responsibleOfficer?->name ?? 'Tidak Ditetapkan';

        $startDate = $loanApp->loan_start_date instanceof Carbon ? $loanApp->loan_start_date->format('d/m/Y') : ($loanApp->loan_start_date ?? 'N/A'); //
        $endDate = $loanApp->loan_end_date instanceof Carbon ? $loanApp->loan_end_date->format('d/m/Y') : ($loanApp->loan_end_date ?? 'N/A'); //

        $mailMessage = (new MailMessage())
            ->subject(__('Tindakan Diperlukan: Permohonan Pinjaman Sedia Untuk Pengeluaran (#:id)', ['id' => $loanApplicationId]))
            ->greeting(__('Salam Sejahtera, Staf BPM,')) // Greeting to BPM staff
            ->line(__('Permohonan Pinjaman Peralatan ICT berikut oleh :applicantName (#:id) telah diluluskan dan sedia untuk proses pengeluaran peralatan.', ['applicantName' => $applicantName, 'id' => $loanApplicationId]))
            ->line(__('Tujuan Pinjaman: :purpose', ['purpose' => $loanApp->purpose ?? __('Tidak dinyatakan')]))
            ->line(__('Tempoh Pinjaman: Dari :startDate hingga :endDate', ['startDate' => $startDate, 'endDate' => $endDate]))
            ->line(__('Pegawai Bertanggungjawab Asal (Pemohon): :responsibleOfficerName', ['responsibleOfficerName' => $responsibleOfficerName]));

        // List items from loan_application_items (applicationItems relationship)
        if ($loanApp->loanApplicationItems && $loanApp->loanApplicationItems->isNotEmpty()) {
            $mailMessage->line(__('Butiran peralatan yang diluluskan untuk dikeluarkan:'));
            foreach ($loanApp->loanApplicationItems as $item) {
                /** @var LoanApplicationItem $item */
                // Assuming LoanApplicationItem has quantity_approved or defaults to quantity_requested
                $quantityToIssue = $item->quantity_approved ?? $item->quantity_requested;
                $mailMessage->line("- Jenis Peralatan: **{$item->equipment_type}**, Kuantiti: **{$quantityToIssue}**".($item->notes ? " (Catatan: {$item->notes})" : ''));
            }
        } else {
            $mailMessage->line(__('Tiada butiran item peralatan yang diluluskan ditemui dalam permohonan ini. Sila semak semula permohonan.'));
            Log::warning('LoanApplicationReadyForIssuanceNotification: No applicationItems found or loaded for email.', ['loan_application_id' => $loanApplicationId]);
        }

        $mailMessage->line(__('Sila semak permohonan untuk butiran lanjut dan uruskan pengeluaran peralatan kepada pemohon.'));

        $viewUrl = '#';
        if ($loanApplicationId) {
            try {
                // Link to a specific BPM/Admin view for processing issuance if available, else the standard show view.
                $viewUrl = route('admin.loan-applications.show', $loanApplicationId); // Example admin route
                // Or route('loan-applications.show', $loanApplicationId) if BPM staff use the same view page with different actions.
            } catch (\Exception $e) {
                Log::error('Error generating URL for LoanApplicationReadyForIssuanceNotification mail: '.$e->getMessage(), ['loan_application_id' => $loanApplicationId]);
            }
        }
        if ($viewUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan & Proses Pengeluaran'), $viewUrl);
        }

        $mailMessage->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable as User
    {
        $loanApp = $this->loanApplication;
        $loanApplicationId = $loanApp->id ?? null;
        $applicantName = $loanApp->user?->name ?? 'Pemohon Tidak Dikenali';

        $applicationUrl = '#';
        if ($loanApplicationId) {
            try {
                $applicationUrl = route('admin.loan-applications.show', $loanApplicationId); // Example admin route
            } catch (\Exception $e) {
                Log::error('Error generating URL for LoanApplicationReadyForIssuanceNotification array: '.$e->getMessage(), ['loan_application_id' => $loanApplicationId]);
            }
        }

        $itemDetails = $loanApp->loanApplicationItems->map(function ($item) {
            /** @var LoanApplicationItem $item */
            return "{$item->equipment_type} (Kuantiti: ".($item->quantity_approved ?? $item->quantity_requested).')';
        })->toArray();

        return [
            'loan_application_id' => $loanApplicationId,
            'applicant_name' => $applicantName,
            'status_application' => $loanApp->status, // Current status of the application
            'subject' => __('Permohonan Pinjaman #:id Sedia Untuk Pengeluaran', ['id' => $loanApplicationId ?? 'N/A']),
            'message' => __('Permohonan pinjaman #:id oleh :applicantName sedia untuk pengeluaran peralatan. Item: :items', [
                'id' => $loanApplicationId ?? 'N/A',
                'applicantName' => $applicantName,
                'items' => implode(', ', $itemDetails),
            ]),
            'url' => $applicationUrl,
            'icon' => 'ti ti-package', // Example icon
            'action_required' => true,
        ];
    }

    // Getter was removed as property made protected from private.
    // Direct access $this->loanApplication is fine within the class.
}
