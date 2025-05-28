<?php

namespace App\Notifications;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction; // Added for type hinting
use App\Models\LoanTransactionItem; // Added for instanceof
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Added for consistency
use Illuminate\Support\Facades\Route; // Added for date formatting

class EquipmentReturnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected LoanApplication $loanApplication;
    protected LoanTransaction $returnTransaction;
    protected User $returnAcceptingOfficer;

    public function __construct(
        LoanApplication $loanApplication,
        LoanTransaction $returnTransaction,
        User $returnAcceptingOfficer
    ) {
        $this->loanApplication = $loanApplication->loadMissing('user'); // Load applicant
        $this->returnTransaction = $returnTransaction->loadMissing(['loanTransactionItems.equipment']);
        $this->returnAcceptingOfficer = $returnAcceptingOfficer;
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicantName = $this->loanApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $transactionDate = $this->returnTransaction->transaction_date instanceof Carbon
            ? $this->returnTransaction->transaction_date->format(config('app.datetime_format_my', 'd/m/Y H:i A'))
            : __('tarikh tidak direkodkan');

        $mailMessage = (new MailMessage())
            ->subject(__("Peralatan Pinjaman Dipulangkan (Permohonan #:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__("Peralatan yang anda pinjam bagi Permohonan Pinjaman ICT **#:id** telah berjaya dipulangkan pada :date.", ['id' => $applicationId, 'date' => $transactionDate]))
            ->line(__('Butiran peralatan yang dipulangkan:'));

        if ($this->returnTransaction->loanTransactionItems->isNotEmpty()) {
            foreach ($this->returnTransaction->loanTransactionItems as $item) {
                /** @var LoanTransactionItem $item */
                $equipment = $item->equipment;
                if ($equipment instanceof Equipment) {
                    $assetTypeDisplay = $equipment->assetTypeDisplay ?? __('Peralatan');
                    $brandAndModel = trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? ''));
                    $conditionDisplay = $item->condition_on_return ? (Equipment::getConditionStatusesList()[$item->condition_on_return] ?? ucfirst(str_replace('_', ' ', $item->condition_on_return))) : __('Tidak dinyatakan');

                    $mailMessage->line(
                        "- **{$assetTypeDisplay}**" . ($brandAndModel ? " ({$brandAndModel})" : "") .
                        " (ID Tag: ".($equipment->tag_id ?? '-').", No. Siri: ".($equipment->serial_number ?? '-')."). " .
                        __('Kuantiti Dipulang').': '.$item->quantity_transacted."." .
                        " ".__('Keadaan Semasa Pulang').": {$conditionDisplay}." .
                        ($item->item_notes ? " ".__('Catatan Item').": {$item->item_notes}" : '')
                    );
                } else {
                    $mailMessage->line(__("- Butiran peralatan tidak lengkap untuk item transaksi ID: :id", ['id' => $item->id]));
                }
            }
        } else {
            $mailMessage->line(__("Tiada butiran item spesifik untuk transaksi pemulangan ini."));
        }


        if ($this->returnTransaction->return_notes) {
            $mailMessage->line('');
            $mailMessage->line(__('Catatan Umum Pemulangan oleh pegawai: :notes', ['notes' => $this->returnTransaction->return_notes]));
        }

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Standardized route name
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentReturnedNotification mail action: ' . $e->getMessage(), [
                    'loan_application_id' => $this->loanApplication->id ?? null,
                    'exception' => $e,
                ]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Status Permohonan'), $applicationUrl);
        }

        if ($this->loanApplication->status === LoanApplication::STATUS_RETURNED) {
            $mailMessage->line(__('Semua peralatan untuk permohonan pinjaman ini telah dipulangkan. Permohonan anda kini telah selesai.'));
        } else {
            $mailMessage->line(__('Pemulangan anda telah direkodkan. Jika masih ada baki peralatan, sila pastikan semua dipulangkan untuk melengkapkan permohonan.'));
        }
        $mailMessage->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $itemsDetails = $this->returnTransaction->loanTransactionItems->map(function (LoanTransactionItem $txItem) {
            $equipment = $txItem->equipment;
            if ($equipment instanceof Equipment) {
                return [
                    'transaction_item_id' => $txItem->id,
                    'equipment_id' => $equipment->id,
                    'asset_type' => $equipment->assetTypeDisplay ?? __('Peralatan'),
                    'brand_model' => trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')),
                    'tag_id' => $equipment->tag_id,
                    'serial_number' => $equipment->serial_number,
                    'quantity_returned' => $txItem->quantity_transacted,
                    'condition_on_return' => $txItem->condition_on_return ? (Equipment::getConditionStatusesList()[$txItem->condition_on_return] ?? ucfirst(str_replace('_', ' ', $txItem->condition_on_return))) : __('Tidak dinyatakan'),
                    'item_notes' => $txItem->item_notes,
                ];
            }
            return ['transaction_item_id' => $txItem->id, 'error' => __('Butiran peralatan tidak lengkap.')];
        })->toArray();

        $loanAppId = $this->loanApplication->id ?? null;
        $applicantName = $this->loanApplication->user?->name ?? __('Pemohon');

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($loanAppId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $loanAppId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentReturnedNotification toArray: ' . $e->getMessage(), ['loan_application_id' => $loanAppId]);
                $applicationUrl = '#'; // Fallback
            }
        }
        $transactionDate = $this->returnTransaction->transaction_date instanceof Carbon
            ? $this->returnTransaction->transaction_date->format(config('app.date_format', 'Y-m-d'))
            : null;

        return [
            'loan_application_id' => $loanAppId,
            'applicant_name' => $applicantName,
            'return_transaction_id' => $this->returnTransaction->id ?? null,
            'returned_by_name' => $this->returnTransaction->returningOfficer?->name ?? __('Tidak direkodkan'), // Assuming returningOfficer relationship
            'accepted_by_officer_name' => $this->returnAcceptingOfficer->name,
            'transaction_date' => $transactionDate,
            'subject' => __("Peralatan Dipulangkan (Permohonan #:id)", ['id' => $loanAppId ?? 'N/A']),
            'message' => __("Peralatan bagi Permohonan Pinjaman #:id oleh :name telah dipulangkan.", ['id' => $loanAppId ?? 'N/A', 'name' => $applicantName]),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'returned_items' => $itemsDetails,
            'overall_status' => $this->loanApplication->status === LoanApplication::STATUS_RETURNED ? __('Selesai') : __('Sebahagian Dipulangkan'),
            'icon' => 'ti ti-transfer-in',
        ];
    }
}
