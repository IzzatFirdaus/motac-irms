<?php

namespace App\Notifications;

// EDITED: Added the Mailable class import
use App\Mail\EquipmentReturnedNotification as EquipmentReturnedMailable;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class EquipmentReturnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // These properties are now public to be accessed by the Mailable
    public LoanApplication $loanApplication;

    public ?LoanTransaction $returnTransaction; // Made nullable

    public ?User $returnAcceptingOfficer; // Made nullable

    public function __construct(
        LoanApplication $loanApplication,
        ?LoanTransaction $returnTransaction = null, // Added default null
        ?User $returnAcceptingOfficer = null // Added default null
    ) {
        $this->loanApplication        = $loanApplication->loadMissing('user');
        $this->returnTransaction      = $returnTransaction?->loadMissing(['loanTransactionItems.equipment', 'returnAcceptingOfficer:id,name']); // Handle nullable
        $this->returnAcceptingOfficer = $returnAcceptingOfficer;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // EDITED: The entire toMail method is refactored.
    // It no longer builds the message here. Instead, it returns an instance
    // of our new Mailable class, which is responsible for building the email.
    public function toMail(User $notifiable): EquipmentReturnedMailable
    {
        return new EquipmentReturnedMailable($this->loanApplication, $this->returnTransaction, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     */
    // EDITED: Made minor corrections to ensure data consistency.
    public function toArray(User $notifiable): array
    {
        $itemsDetails = $this->returnTransaction?->loanTransactionItems->map(function (LoanTransactionItem $txItem): array {
            // Handle nullable
            $equipment = $txItem->equipment;
            if ($equipment instanceof Equipment) {
                return [
                    'transaction_item_id' => $txItem->id,
                    'equipment_id'        => $equipment->id,
                    // CORRECTED: Uses the asset_type_label accessor from the Equipment model.
                    'asset_type'        => $equipment->asset_type_label ?? __('Peralatan Tidak Dikenali'),
                    'brand_model'       => trim(($equipment->brand ?? '').' '.($equipment->model ?? '')),
                    'tag_id'            => $equipment->tag_id,
                    'serial_number'     => $equipment->serial_number,
                    'quantity_returned' => $txItem->quantity_transacted,
                    // CORRECTED: Uses the condition_on_return_translated accessor for consistency.
                    'condition_on_return' => $txItem->condition_on_return_translated ?? __('Tidak dinyatakan'),
                    'item_notes'          => $txItem->item_notes,
                ];
            }

            return ['transaction_item_id' => $txItem->id, 'error' => __('Butiran peralatan tidak lengkap.')];
        })->toArray() ?? []; // Handle nullable

        $loanAppId     = $this->loanApplication->id;
        $applicantName = $this->loanApplication->user?->name ?? __('Pemohon');

        $applicationUrl = '#';
        $routeName      = 'resource-management.my-applications.loan.show';
        if ($loanAppId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, ['loan_application' => $loanAppId]);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentReturnedNotification toArray: '.$e->getMessage(), ['loan_application_id' => $loanAppId]);
                $applicationUrl = '#';
            }
        }

        $transactionDate = $this->returnTransaction?->transaction_date?->format(config('app.date_format_my', 'd/m/Y')); // Handle nullable

        return [
            'loan_application_id'      => $loanAppId,
            'applicant_name'           => $applicantName,
            'return_transaction_id'    => $this->returnTransaction->id                       ?? null, // Handle nullable
            'returned_by_name'         => $this->returnTransaction?->returningOfficer?->name ?? __('Tidak direkodkan'), // Handle nullable
            'accepted_by_officer_name' => $this->returnAcceptingOfficer->name                ?? __('Tidak diketahui'), // Handle nullable
            'transaction_date'         => $transactionDate,
            'subject'                  => __('Peralatan Dipulangkan (Permohonan #:id)', ['id' => $loanAppId]),
            'message'                  => __('Peralatan bagi Permohonan Pinjaman #:id oleh :name telah dipulangkan.', ['id' => $loanAppId, 'name' => $applicantName]),
            'url'                      => ($applicationUrl !== '#') ? $applicationUrl : null,
            'returned_items'           => $itemsDetails,
            'overall_status'           => $this->loanApplication->status === LoanApplication::STATUS_RETURNED ? __('Selesai') : __('Sebahagian Dipulangkan'),
            'icon'                     => 'ti ti-transfer-in',
        ];
    }
}
