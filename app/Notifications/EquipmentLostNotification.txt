<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem; // Added
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class EquipmentLostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;
    /** @var EloquentCollection<int, LoanTransaction> */
    private EloquentCollection $lostTransactions;

    /**
     * @param EloquentCollection<int, LoanTransaction> $lostTransactions
     */
    public function __construct(
        LoanApplication $loanApplication,
        EloquentCollection $lostTransactions
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']);
        $this->lostTransactions = $lostTransactions->loadMissing(['loanTransactionItems.equipment']);
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $recipientName = $notifiable->name ?? __('Pengguna');
        $applicationId = $this->loanApplication->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Pemberitahuan Kehilangan Peralatan ICT - Permohonan #:appId", ['appId' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $recipientName]))
            ->line(__('Kami ingin memaklumkan bahawa peralatan ICT yang berkaitan dengan Permohonan Pinjaman Peralatan ICT anda dengan Nombor Rujukan **#:appId** telah dilaporkan *hilang*.', ['appId' => $applicationId]))
            ->line(__('Berikut adalah butiran peralatan yang dilaporkan hilang:'));

        if ($this->lostTransactions->isEmpty()) {
            $mailMessage->line(__('- Tiada butiran spesifik mengenai item hilang diterima.'));
        } else {
            foreach ($this->lostTransactions as $transaction) {
                /** @var LoanTransaction $transaction */
                if ($transaction->loanTransactionItems->isEmpty()) {
                    $mailMessage->line(__('- Tiada item disenaraikan untuk transaksi kehilangan ID: :txId.', ['txId' => $transaction->id ?? 'N/A']));
                    continue;
                }
                foreach ($transaction->loanTransactionItems as $transactionItem) {
                    /** @var LoanTransactionItem $transactionItem */
                    if ($transactionItem->equipment instanceof Equipment) {
                        $equipment = $transactionItem->equipment;
                        $assetTypeDisplay = $equipment->assetTypeDisplay ?? __('Peralatan');
                        $brandAndModel = trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? ''));
                        $mailMessage->line(
                            "- **{$assetTypeDisplay}**" . ($brandAndModel ? " ({$brandAndModel})" : "") .
                            " (ID Tag: ".($equipment->tag_id ?? '-').", No. Siri: ".($equipment->serial_number ?? '-').")." .
                            ($transactionItem->item_notes ? " ".__('Catatan').": {$transactionItem->item_notes}" : '')
                        );
                    } else {
                        $mailMessage->line(__('- Butiran peralatan tidak tersedia untuk item transaksi ID: :id', ['id' => $transactionItem->id ?? 'N/A']));
                        Log::warning("EquipmentLostNotification: Equipment details missing for LoanTransactionItem ID {$transactionItem->id}.", ['transaction_item_id' => $transactionItem->id, 'loan_application_id' => $applicationId]);
                    }
                }
                 $mailMessage->line('---');
            }
        }

        $mailMessage->line(__('Sila hubungi Unit ICT atau pegawai bertanggungjawab untuk maklumat lanjut atau tindakan yang diperlukan.'));

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Standardized route
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentLostNotification mail: ' . $e->getMessage(), ['loan_application_id' => $this->loanApplication->id]);
                $applicationUrl = '#'; // Fallback
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Permohonan'), $applicationUrl);
        }

        $mailMessage->salutation(__('Sekian, harap maklum.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $lostItemsDetails = [];

        foreach ($this->lostTransactions as $transaction) {
            /** @var LoanTransaction $transaction */
            foreach ($transaction->loanTransactionItems as $transactionItem) {
                /** @var LoanTransactionItem $transactionItem */
                $equipment = $transactionItem->equipment;
                if ($equipment instanceof Equipment) {
                    $lostItemsDetails[] = [
                        'equipment_id' => $equipment->id,
                        'tag_id' => $equipment->tag_id,
                        'asset_type' => $equipment->assetTypeDisplay ?? __('Peralatan'),
                        'brand_model' => trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')),
                        'serial_number' => $equipment->serial_number,
                        'item_notes' => $transactionItem->item_notes,
                        'transaction_item_id' => $transactionItem->id,
                        'transaction_id' => $transaction->id,
                    ];
                } else {
                     $lostItemsDetails[] = ['transaction_item_id' => $transactionItem->id, 'transaction_id' => $transaction->id, 'error' => __('Butiran peralatan hilang')];
                }
            }
        }


        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $applicationId);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentLostNotification array: ' . $e->getMessage(), ['application_id' => $applicationId]);
                $applicationUrl = '#'; // Fallback
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_name' => $this->loanApplication->user?->name ?? 'N/A',
            'subject' => __("Peralatan Dilaporkan Hilang (Permohonan #:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Beberapa peralatan bagi permohonan #:id telah dilaporkan hilang.", ['id' => $applicationId ?? 'N/A']),
            'lost_items' => $lostItemsDetails,
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'icon' => 'ti ti-mood-empty',
        ];
    }
}
