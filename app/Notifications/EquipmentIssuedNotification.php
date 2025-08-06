<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
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

    private ?LoanTransaction $issueTransaction; // Made nullable

    private ?User $issuedByOfficer; // Made nullable

    public function __construct(
        LoanApplication $loanApplication,
        ?LoanTransaction $issueTransaction = null, // Added default null
        ?User $issuedByOfficer = null // Added default null
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user']);
        $this->issueTransaction = $issueTransaction?->loadMissing(['loanTransactionItems.equipment']);
        $this->issuedByOfficer = $issuedByOfficer;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationId = $this->loanApplication->id ?? 'N/A';
        $subject = __('Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #:appId)', ['appId' => $applicationId]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.loan-application-issued', [
                'loanApplication' => $this->loanApplication,
                // The view expects a collection of transactions, so we wrap our single transaction.
                'issueTransactions' => $this->issueTransaction ? collect([$this->issueTransaction]) : collect([]), // Handle nullable
            ]);
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $applicantName = $this->loanApplication->user?->name ?? __('Pemohon');
        $transactionId = $this->issueTransaction->id ?? null;

        $itemsDetails = $this->issueTransaction?->loanTransactionItems->map(function ($item) { // Handle nullable
            $equipment = $item->equipment;
            if ($equipment) {
                $assetTypeDisplay = $equipment->asset_type_label ?? __('Peralatan');
                $brandAndModel = trim(($equipment->brand ?? '').' '.($equipment->model ?? ''));

                return $assetTypeDisplay.($brandAndModel !== '' && $brandAndModel !== '0' ? sprintf(' (%s)', $brandAndModel) : '').', Tag: '.($equipment->tag_id ?? '-').', Siri: '.($equipment->serial_number ?? '-').(' - Kuantiti: '.$item->quantity_transacted);
            }

            return __('Item ID: :id - Butiran peralatan tidak lengkap.', ['id' => $item->id]);
        })->toArray() ?? []; // Handle nullable

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, ['loan_application' => $applicationId]);
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentIssuedNotification array: '.$e->getMessage(), ['loan_application_id' => $applicationId]);
                $applicationUrl = '#';
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_name' => $applicantName,
            'transaction_id' => $transactionId,
            'issued_by_officer_id' => $this->issuedByOfficer->id ?? null, // Handle nullable
            'issued_by_officer_name' => $this->issuedByOfficer->name ?? __('Tidak diketahui'), // Handle nullable
            'subject' => __('Peralatan Dikeluarkan (Permohonan #:appId)', ['appId' => $applicationId ?? 'N/A']),
            'message' => __('Peralatan untuk permohonan pinjaman anda #:appId oleh :name telah dikeluarkan.', ['appId' => $applicationId ?? 'N/A', 'name' => $applicantName]),
            'items_summary' => implode('; ', $itemsDetails),
            'url' => ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null,
            'icon' => 'ti ti-transfer-out',
        ];
    }
}
