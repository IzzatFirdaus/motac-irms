<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User; // For $notifiable type hint
use App\Models\Equipment; // For instanceof check
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class EquipmentIssued extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;
    /**
     * The collection of LoanTransaction models for the issued items.
     * @var EloquentCollection<int, LoanTransaction>
     */
    private EloquentCollection $issuedTransactions;

    /**
     * Create a new notification instance.
     * @param LoanApplication $loanApplication The loan application.
     * @param EloquentCollection<int, LoanTransaction> $issuedTransactions Collection of issue transactions.
     */
    public function __construct(
        LoanApplication $loanApplication,
        EloquentCollection $issuedTransactions
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']);
        // Assuming 'equipment' is a direct relationship on LoanTransaction for this version.
        // If items are linked via LoanTransactionItem, this loading might need to be 'loanTransactionItems.equipment'.
        $this->issuedTransactions = $issuedTransactions->loadMissing(['equipment']);
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->loanApplication->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->loanApplication->id ?? 'N/A';

        $mailMessage = (new MailMessage())
            ->subject(__("Peralatan Pinjaman ICT Telah Dikeluarkan (Permohonan #:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__("Peralatan pinjaman ICT untuk Permohonan **#:id** anda telah dikeluarkan:", ['id' => $applicationId]))
            ->line('---');

        if ($this->issuedTransactions->isEmpty()) {
            $mailMessage->line(__('Tiada butiran peralatan khusus diberikan dalam transaksi ini. Sila semak dengan Unit ICT.'));
        } else {
            foreach ($this->issuedTransactions as $transaction) {
                /** @var LoanTransaction $transaction */
                $equipment = $transaction->equipment; // Assuming direct relationship

                if ($equipment instanceof Equipment) {
                    $assetTypeDisplay = $equipment->assetTypeDisplay ?? __('Peralatan'); // Assuming assetTypeDisplay accessor
                    $brandAndModel = trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? ''));
                    $serialNumber = $equipment->serial_number ?? __('Tidak Dinyatakan');
                    $tagId = $equipment->tag_id ?? __('Tidak Dinyatakan');
                    $issueNotes = $transaction->issue_notes ?? null;

                    $mailMessage->line("- **{$assetTypeDisplay}**" . ($brandAndModel ? " ({$brandAndModel})" : ""));
                    $mailMessage->line("  ".__('Nombor Siri').": {$serialNumber}");
                    $mailMessage->line("  ".__('ID Tag MOTAC').": {$tagId}");
                    if ($issueNotes) {
                        $mailMessage->line("  ".__('Catatan Pengeluaran').": {$issueNotes}");
                    }
                    $mailMessage->line('---');
                } else {
                    // This case implies LoanTransaction might not always have a direct 'equipment' or it could be null.
                    // Or, if equipment is through loanTransactionItems, this logic needs revision.
                    $transactionId = $transaction->id ?? 'N/A';
                    $mailMessage->line(__("- Peralatan (Butiran tidak tersedia) — Transaksi ID: :id", ['id' => $transactionId]));
                    Log::warning("EquipmentIssued Notification: Equipment details missing or not directly linked for LoanTransaction ID {$transactionId}.", [
                        'transaction_id' => $transactionId,
                        'loan_application_id' => $applicationId
                    ]);
                    $mailMessage->line('---');
                }
            }
        }

        $mailMessage->line(__('Sila pastikan anda menjaga peralatan dengan baik dan memulangkannya sebelum atau pada tarikh tamat tempoh pinjaman.'));

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show'; // Standardized route
        if ($this->loanApplication->id && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $this->loanApplication->id);
            } catch (\Throwable $e) {
                Log::error("Error generating URL for EquipmentIssued mail action: {$e->getMessage()}", [
                    'loan_application_id' => $this->loanApplication->id,
                    'exception' => $e,
                ]);
            }
        }

        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Pinjaman'), $applicationUrl);
        }

        return $mailMessage->salutation(__('Sekian, terima kasih.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;

        $itemsSummary = $this->issuedTransactions->map(function (LoanTransaction $transaction) {
            $equipment = $transaction->equipment;
            if ($equipment instanceof Equipment) {
                return "{$equipment->assetTypeDisplay} ({$equipment->brand} {$equipment->model}, Tag: {$equipment->tag_id})";
            }
            return __("Item dari transaksi :id (butiran tidak lengkap)", ['id' => $transaction->id ?? 'N/A']);
        })->implode('; ');

        $applicationUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $applicationUrl = route($routeName, $applicationId);
            } catch (\Throwable $e) {
                Log::error("Error generating URL for EquipmentIssued toArray: {$e->getMessage()}", ['loan_application_id' => $applicationId]);
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_name' => $this->loanApplication->user?->name ?? __('Pemohon'),
            'subject' => __("Peralatan Dikeluarkan (Permohonan #:id)", ['id' => $applicationId ?? 'N/A']),
            'message' => __("Peralatan untuk permohonan pinjaman #:id anda telah dikeluarkan. Item utama: :summary", ['id' => $applicationId ?? 'N/A', 'summary' => $itemsSummary]),
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'icon' => 'ti ti-archive',
            'transaction_ids' => $this->issuedTransactions->pluck('id')->toArray(),
        ];
    }
}
