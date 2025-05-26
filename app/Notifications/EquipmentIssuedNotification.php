<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class EquipmentIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;
    private LoanTransaction $issueTransaction;
    private User $issuedByOfficer;

    public function __construct(
        LoanApplication $loanApplication,
        LoanTransaction $issueTransaction,
        User $issuedByOfficer
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user']);
        $this->issueTransaction = $issueTransaction->loadMissing(['loanTransactionItems.equipment']);
        $this->issuedByOfficer = $issuedByOfficer;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->loanApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $transactionId = $this->issueTransaction->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #:appId)", ['appId' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__("Peralatan untuk Permohonan Pinjaman ICT anda **#:appId** telah dikeluarkan (Transaksi Pengeluaran #:txId).", ['appId' => $applicationId, 'txId' => $transactionId]))
            ->line(__('Butiran peralatan yang dikeluarkan:'));

        if ($this->issueTransaction->loanTransactionItems->isNotEmpty()) {
            foreach ($this->issueTransaction->loanTransactionItems as $item) {
                /** @var LoanTransactionItem $item */
                $equipment = $item->equipment;
                if ($equipment instanceof Equipment) {
                    $assetTypeDisplay = $equipment->assetTypeDisplay ?? __('Peralatan'); // Assuming assetTypeDisplay accessor
                    $brandAndModel = trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? ''));

                    $mailMessage->line(
                        "- **{$assetTypeDisplay}**" . ($brandAndModel ? " ({$brandAndModel})" : "") .
                        " (ID Tag: ".($equipment->tag_id ?? '-').", No. Siri: ".($equipment->serial_number ?? '-')."). " .
                        __('Kuantiti').': '.$item->quantity_transacted."." .
                        ($item->item_notes ? " ".__('Catatan Item').": {$item->item_notes}" : '')
                    );
                } else {
                    $mailMessage->line(__("- Butiran peralatan tidak lengkap untuk item transaksi ID: :id", ['id' => $item->id]));
                }
            }
        } else {
            $mailMessage->line(__('Tiada butiran item ditemui untuk transaksi pengeluaran ini.'));
        }

        $mailMessage->line(__('Sila pastikan anda menjaga peralatan dengan baik dan memulangkannya sebelum atau pada tarikh tamat tempoh pinjaman.'));

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Standardized route
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentIssuedNotification mail: ' . $e->getMessage(), ['loan_application_id' => $this->loanApplication->id]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan Pinjaman'), $applicationUrl);
        }

        $mailMessage->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $applicantName = $this->loanApplication->user?->name ?? __('Pemohon');
        $transactionId = $this->issueTransaction->id ?? null;

        $itemsDetails = $this->issueTransaction->loanTransactionItems->map(function (LoanTransactionItem $item) {
            $equipment = $item->equipment;
            if ($equipment instanceof Equipment) {
                 $assetTypeDisplay = $equipment->assetTypeDisplay ?? __('Peralatan');
                 $brandAndModel = trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? ''));
                return "{$assetTypeDisplay}" . ($brandAndModel ? " ({$brandAndModel})" : "") . ", Tag: ".($equipment->tag_id ?? '-').", Siri: ".($equipment->serial_number ?? '-')." - Kuantiti: {$item->quantity_transacted}";
            }
            return __("Item ID: :id - Butiran peralatan tidak lengkap.", ['id' => $item->id]);
        })->toArray();

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $applicationId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentIssuedNotification array: ' . $e->getMessage(), ['loan_application_id' => $applicationId]);
                 $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_name' => $applicantName,
            'transaction_id' => $transactionId,
            'issued_by_officer_id' => $this->issuedByOfficer->id,
            'issued_by_officer_name' => $this->issuedByOfficer->name,
            'subject' => __("Peralatan Dikeluarkan (Permohonan #:appId)", ['appId' => $applicationId ?? 'N/A']),
            'message' => __("Peralatan untuk permohonan pinjaman anda #:appId oleh :name telah dikeluarkan.", ['appId' => $applicationId ?? 'N/A', 'name' => $applicantName]),
            'items_summary' => implode('; ', $itemsDetails),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'icon' => 'ti ti-transfer-out',
        ];
    }
}
