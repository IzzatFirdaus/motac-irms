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

    private EloquentCollection $incidentItems;

    private string $incidentType;

    public function __construct(
        LoanApplication $loanApplication,
        EloquentCollection $incidentItems,
        string $incidentType
    ) {
        $this->loanApplication = $loanApplication->loadMissing(['user', 'responsibleOfficer']); //
        $this->incidentItems   = $incidentItems->loadMissing(['equipment', 'loanTransaction']); //
        $this->incidentType    = $incidentType; //

        if (! in_array($incidentType, ['lost', 'damaged'])) { //
            throw new \InvalidArgumentException(sprintf("Invalid incident type: %s. Must be 'lost' or 'damaged'.", $incidentType)); //
        }
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database']; //
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicationId = $this->loanApplication->id ?? 'N/A'; //
        $subject       = ''; //
        $introLines    = []; //
        $outroLines    = []; //

        if ($this->incidentType === 'lost') { //
            $subject      = __('Pemberitahuan Kehilangan Peralatan ICT - Permohonan #:applicationId', ['applicationId' => $applicationId]); //
            $introLines[] = __('Kami ingin memaklumkan bahawa peralatan ICT yang berkaitan dengan Permohonan Pinjaman Peralatan ICT anda dengan Nombor Rujukan **#:applicationId** telah dilaporkan *hilang*.', ['applicationId' => $applicationId]); //
            $outroLines[] = __('Sila hubungi Unit ICT untuk maklumat lanjut atau tindakan yang diperlukan.'); //
        } elseif ($this->incidentType === 'damaged') { //
            $subject      = __('Makluman: Peralatan Pinjaman ICT Ditemui Rosak (Permohonan #:applicationId)', ['applicationId' => $applicationId]); //
            $introLines[] = __('Semasa penerimaan pulangan peralatan pinjaman ICT bagi Permohonan **#:applicationId**, item berikut ditemui rosak:', ['applicationId' => $applicationId]); //
            $outroLines[] = __('BPM akan menghubungi anda untuk tindakan lanjut jika perlu.'); //
        }

        if ($this->incidentItems->isNotEmpty()) { //
            $introLines[] = '---'; //
            foreach ($this->incidentItems as $item) { //
                if ($item->equipment instanceof Equipment) { //
                    // CORRECTED: Changed assetTypeDisplay to the correct accessor 'asset_type_label'
                    $details = sprintf('- **%s** (%s %s) - Tag: %s', $item->equipment->asset_type_label, $item->equipment->brand, $item->equipment->model, $item->equipment->tag_id); //
                    if ($item->item_notes) { //
                        $details .= sprintf(' | Catatan: *%s*', $item->item_notes); //
                    }

                    $introLines[] = $details; //
                }
            }

            $introLines[] = '---'; //
        }

        return (new MailMessage) //
            ->subject($subject) //
            ->level($this->incidentType === 'lost' ? 'error' : 'warning') //
            ->view('emails.notifications.motac_default_notification', [ //
                'greeting'       => __('Salam Sejahtera'), //
                'notifiableName' => $notifiable->name, //
                'introLines'     => $introLines, //
                'outroLines'     => $outroLines, //
                'actionText'     => __('Lihat Butiran Pinjaman'), //
                'actionUrl'      => $this->getActionUrl(), //
            ]);
    }

    public function getActionUrl(): string
    {
        if ($this->loanApplication->id && Route::has('loan-applications.show')) { //
            try {
                return route('loan-applications.show', ['loan_application' => $this->loanApplication->id]); //
            } catch (\Exception $e) {
                Log::error('Error generating URL for EquipmentIncidentNotification: '.$e->getMessage()); //
            }
        }

        return '#'; //
    }

    public function toArray(User $notifiable): array
    {
        $applicationId        = $this->loanApplication->id          ?? null; //
        $applicantName        = $this->loanApplication->user?->name ?? 'N/A'; //
        $incidentItemsDetails = $this->incidentItems->map(function (LoanTransactionItem $item): array {
            //
            $equipment = $item->equipment; //
            $details   = ['transaction_item_id' => $item->id, 'item_notes' => $item->item_notes]; //
            if ($equipment instanceof Equipment) { //
                // CORRECTED: Changed assetTypeDisplay to the correct accessor 'asset_type_label'
                $details = array_merge($details, ['equipment_id' => $equipment->id, 'tag_id' => $equipment->tag_id, 'asset_type' => $equipment->asset_type_label, 'brand_model' => sprintf('%s %s', $equipment->brand, $equipment->model), 'serial_number' => $equipment->serial_number]); //
            }

            if ($this->incidentType === 'damaged') { //
                $details['condition_on_return'] = $item->condition_on_return; //
            }

            return $details; //
        })->toArray();

        $subject = '';
        $message = '';
        $icon    = 'ti ti-alert-circle'; //
        if ($this->incidentType === 'lost') { //
            $subject = __('Peralatan Dilaporkan Hilang (Permohonan #:id)', ['id' => $applicationId ?? 'N/A']); //
            $message = __('Beberapa peralatan bagi permohonan #:id telah dilaporkan hilang.', ['id' => $applicationId ?? 'N/A']); //
            $icon    = 'ti ti-mood-empty'; //
        } elseif ($this->incidentType === 'damaged') { //
            $subject = __('Peralatan Ditemui Rosak (Permohonan #:id)', ['id' => $applicationId ?? 'N/A']); //
            $message = __('Beberapa peralatan bagi permohonan #:id ditemui rosak semasa pemulangan.', ['id' => $applicationId ?? 'N/A']); //
            $icon    = 'ti ti-alert-triangle'; //
        }

        $applicationUrl = $this->getActionUrl(); //

        return [ //
            'loan_application_id' => $applicationId, 'applicant_name' => $applicantName, 'incident_type' => $this->incidentType, //
            'subject'             => $subject, 'message' => $message, 'incident_items' => $incidentItemsDetails, //
            'url'                 => ($applicationUrl !== '#') ? $applicationUrl : null, 'icon' => $icon, //
        ];
    }
}
