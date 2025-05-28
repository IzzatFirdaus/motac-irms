<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

final class EquipmentIncidentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private LoanApplication $loanApplication;
    /** @var EloquentCollection<int, LoanTransactionItem> */
    private EloquentCollection $incidentItems;
    private string $incidentType; // 'lost' or 'damaged'

    /**
     * Create a new notification instance.
     *
     * @param EloquentCollection<int, LoanTransactionItem> $incidentItems
     */
    public function __construct(
        LoanApplication $loanApplication,
        EloquentCollection $incidentItems,
        string $incidentType // 'lost' or 'damaged'
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']);
        $this->incidentItems = $incidentItems->loadMissing(['equipment', 'loanTransaction']); // Ensure equipment details are loaded
        $this->incidentType = $incidentType;

        if (!in_array($incidentType, ['lost', 'damaged'])) {
            throw new \InvalidArgumentException("Invalid incident type: {$incidentType}. Must be 'lost' or 'damaged'.");
        }
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $recipientName = $notifiable->name ?? __('Pengguna');
        $applicationId = $this->loanApplication->id ?? 'N/A';

        $subject = '';
        $greetingLine = '';
        $introLine = '';
        $detailsHeader = '';

        if ($this->incidentType === 'lost') {
            $subject = __('Pemberitahuan Kehilangan Peralatan ICT - Permohonan #:applicationId', ['applicationId' => $applicationId]);
            $greetingLine = __('Salam Sejahtera, :name,', ['name' => $recipientName]);
            $introLine = __('Kami ingin memaklumkan bahawa peralatan ICT yang berkaitan dengan Permohonan Pinjaman Peralatan ICT anda dengan Nombor Rujukan **#:applicationId** telah dilaporkan *hilang*.', ['applicationId' => $applicationId]);
            $detailsHeader = __('Berikut adalah butiran peralatan yang dilaporkan hilang:');
        } elseif ($this->incidentType === 'damaged') {
            $subject = __('Makluman: Peralatan Pinjaman ICT Ditemui Rosak (Permohonan #:applicationId)', ['applicationId' => $applicationId]);
            $greetingLine = __('Salam :name,', ['name' => $recipientName]);
            $introLine = __('Semasa penerimaan pulangan peralatan pinjaman ICT bagi Permohonan **#:applicationId**, item berikut ditemui rosak:', ['applicationId' => $applicationId]);
            $detailsHeader = __('Butiran peralatan yang rosak:');
        }

        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->greeting($greetingLine)
            ->line($introLine)
            ->line($detailsHeader);

        if ($this->incidentItems->isNotEmpty()) {
            foreach ($this->incidentItems as $item) {
                /** @var LoanTransactionItem $item */
                $equipment = $item->equipment;

                if ($equipment instanceof Equipment) {
                    $mailMessage->line('---'); // Separator for each item
                    $mailMessage->line("- **{$equipment->assetTypeDisplay}** ({$equipment->brand} {$equipment->model})");
                    $mailMessage->line(__('  ID Tag MOTAC: :tagId', ['tagId' => $equipment->tag_id ?? __('Tidak Dinyatakan')]));
                    $mailMessage->line(__('  No. Siri: :serialNo', ['serialNo' => $equipment->serial_number ?? __('Tidak Dinyatakan')]));

                    if ($this->incidentType === 'damaged') {
                        $conditionDisplay = $item->condition_on_return ? (Equipment::getConditionStatusesList()[$item->condition_on_return] ?? ucfirst(str_replace('_', ' ', $item->condition_on_return))) : __('Tidak Dinyatakan');
                        $mailMessage->line(__('  Keadaan Dilaporkan Semasa Pulangan: :condition', ['condition' => $conditionDisplay]));
                    }
                    if ($item->item_notes) {
                        $mailMessage->line(__('  Catatan Item: :notes', ['notes' => $item->item_notes]));
                    }
                } else {
                    $itemId = $item->id ?? 'N/A';
                    $mailMessage->line(__('- Butiran peralatan tidak tersedia untuk item transaksi ID: :itemId', ['itemId' => $itemId]));
                    Log::warning("EquipmentIncidentNotification ({$this->incidentType}): Equipment details missing for LoanTransactionItem ID {$itemId}.", [
                        'item_id' => $itemId,
                        'loan_application_id' => $applicationId
                    ]);
                }
            }
            $mailMessage->line('---'); // Final separator
        } else {
            $mailMessage->line(__('Tiada butiran item spesifik dilaporkan untuk insiden ini.'));
        }


        if ($this->incidentType === 'lost') {
            $mailMessage->line(__('Sila hubungi Unit ICT untuk maklumat lanjut atau tindakan yang diperlukan.'));
        } elseif ($this->incidentType === 'damaged') {
            $mailMessage->line(__('BPM akan menghubungi anda untuk tindakan lanjut jika perlu.'));
            $mailMessage->line(__('Sekiranya ada pertanyaan, sila hubungi BPM MOTAC.'));
        }

        $applicationUrl = '#';
        if ($this->loanApplication->id && Route::has('loan-applications.show')) {
            try {
                $applicationUrl = route('loan-applications.show', ['loan_application' => $this->loanApplication->id]);
            } catch (\Exception $e) {
                Log::error("Error generating URL for EquipmentIncidentNotification ({$this->incidentType}) mail: ".$e->getMessage(), ['loan_application_id' => $this->loanApplication->id]);
            }
        }
        if ($applicationUrl !== '#') {
            $mailMessage->action(__('Lihat Butiran Pinjaman'), $applicationUrl);
        }

        $mailMessage->salutation(__('Sekian, terima kasih.'));

        return $mailMessage;
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->loanApplication->id ?? null;
        $applicantName = $this->loanApplication->user?->name ?? 'N/A';

        $incidentItemsDetails = $this->incidentItems->map(function (LoanTransactionItem $item) {
            $equipment = $item->equipment;
            $details = [
                'transaction_item_id' => $item->id,
                'item_notes' => $item->item_notes,
            ];
            if ($equipment instanceof Equipment) {
                $details = array_merge($details, [
                    'equipment_id' => $equipment->id,
                    'tag_id' => $equipment->tag_id,
                    'asset_type' => $equipment->assetTypeDisplay,
                    'brand_model' => "{$equipment->brand} {$equipment->model}",
                    'serial_number' => $equipment->serial_number,
                ]);
            } else {
                $details['error'] = 'Equipment details missing';
            }
            if ($this->incidentType === 'damaged') {
                $details['condition_on_return'] = $item->condition_on_return;
            }
            return $details;
        })->toArray();

        $subject = '';
        $message = '';
        $icon = 'ti ti-alert-circle'; // Default incident icon

        if ($this->incidentType === 'lost') {
            $subject = __('Peralatan Dilaporkan Hilang (Permohonan #:id)', ['id' => $applicationId ?? 'N/A']);
            $message = __('Beberapa peralatan bagi permohonan #:id telah dilaporkan hilang.', ['id' => $applicationId ?? 'N/A']);
            $icon = 'ti ti-mood-empty';
        } elseif ($this->incidentType === 'damaged') {
            $subject = __('Peralatan Ditemui Rosak (Permohonan #:id)', ['id' => $applicationId ?? 'N/A']);
            $message = __('Beberapa peralatan bagi permohonan #:id ditemui rosak semasa pemulangan.', ['id' => $applicationId ?? 'N/A']);
            $icon = 'ti ti-alert-triangle';
        }

        $applicationUrl = '#';
        if ($applicationId && Route::has('loan-applications.show')) {
            try {
                $applicationUrl = route('loan-applications.show', ['loan_application' => $applicationId]);
            } catch (\Exception $e) {
                Log::error("Error generating URL for EquipmentIncidentNotification ({$this->incidentType}) array: ".$e->getMessage(), ['application_id' => $applicationId]);
            }
        }

        return [
            'loan_application_id' => $applicationId,
            'applicant_name' => $applicantName,
            'incident_type' => $this->incidentType,
            'subject' => $subject,
            'message' => $message,
            'incident_items' => $incidentItemsDetails,
            'url' => ($applicationUrl !== '#') ? $applicationUrl : null,
            'icon' => $icon,
        ];
    }
}
