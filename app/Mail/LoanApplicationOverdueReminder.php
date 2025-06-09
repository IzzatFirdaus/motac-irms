<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Mailable class for sending a reminder when loaned ICT equipment is overdue.
 */
final class LoanApplicationOverdueReminder extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public LoanApplication $loanApplication;

    public Collection $overdueItems; // Collection of LoanTransactionItem models that are overdue

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\LoanApplication  $loanApplication  The overdue loan application model instance.
     */
    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication->loadMissing([
            'user',
            'transactions.loanTransactionItems.equipment', // For equipment details for issued items
            'transactions.loanTransactionItems.transaction', // For accessing parent transaction from item
        ]);

        $this->overdueItems = $this->calculateOverdueItems();

        Log::info(
            'LoanApplicationOverdueReminder Mailable: Instance created.',
            [
                'loan_application_id' => $this->loanApplication->id,
                'overdue_items_count' => $this->overdueItems->count(),
            ]
        );
    }

    /**
     * Calculate the items that are currently issued but not yet returned for this loan application.
     */
    private function calculateOverdueItems(): Collection
    {
        $issuedItemsCollection = new EloquentCollection; // Will hold LoanTransactionItem models from issue transactions
        $returnedEquipmentIds = collect(); // Will hold equipment_ids that have been returned

        if ($this->loanApplication->relationLoaded('transactions')) {
            foreach ($this->loanApplication->transactions as $transaction) {
                if ($transaction->type === LoanTransaction::TYPE_ISSUE) {
                    if ($transaction->relationLoaded('loanTransactionItems')) {
                        foreach ($transaction->loanTransactionItems as $item) {
                            // Ensure equipment relationship on item is loaded if not already
                            $item->loadMissing('equipment');
                            $issuedItemsCollection->add($item);
                        }
                    }
                } elseif ($transaction->type === LoanTransaction::TYPE_RETURN) {
                    if ($transaction->relationLoaded('loanTransactionItems')) {
                        foreach ($transaction->loanTransactionItems as $item) {
                            if ($item->equipment_id) {
                                $returnedEquipmentIds->push($item->equipment_id);
                            }
                        }
                    }
                }
            }
        }

        // Filter the issued items to find those not in the returned list
        return $issuedItemsCollection->filter(function ($issuedItem) use ($returnedEquipmentIds) {
            return $issuedItem->equipment_id && ! $returnedEquipmentIds->contains($issuedItem->equipment_id);
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicationId = $this->loanApplication->id ?? 'N/A';
        /** @phpstan-ignore-next-line nullCoalesce.expr, nullsafe.neverNull */
        $applicantName = $this->loanApplication->user?->full_name ??
                         ($this->loanApplication->user?->name ?? 'Pemohon');

        /** @phpstan-ignore-next-line nullsafe.neverNull */
        $recipientEmail = $this->loanApplication->user?->email;
        $toAddresses = [];

        if ($recipientEmail) {
            $toAddresses[] = new Address($recipientEmail, $applicantName);
            Log::info(
                "LoanApplicationOverdueReminder Mailable: Recipient identified for Loan Application ID: {$applicationId}.",
                ['recipient_email' => $recipientEmail]
            );
        } else {
            Log::error(
                "LoanApplicationOverdueReminder Mailable: Recipient email not found for Loan Application ID: {$applicationId}. Notification cannot be sent.",
                [
                    'loan_application_id' => $applicationId,
                    'applicant_user_id' => $this->loanApplication->user_id ?? 'N/A',
                ]
            );
        }

        $subject = "Tindakan Diperlukan: Peringatan Peralatan Pinjaman ICT Lewat Dipulangkan (Permohonan #{$applicationId} - {$applicantName})";

        Log::info(
            'LoanApplicationOverdueReminder Mailable: Preparing envelope.',
            [
                'loan_application_id' => $applicationId,
                'subject' => $subject,
                'to_recipients' => count($toAddresses) > 0 ? $toAddresses[0]->address : 'N/A',
            ]
        );

        return new Envelope(
            to: $toAddresses,
            subject: $subject,
            metadata: [
                'loan_application_id' => (string) ($this->loanApplication->id ?? 'unknown'),
                'applicant_id' => (string) ($this->loanApplication->user_id ?? 'unknown'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info(
            'LoanApplicationOverdueReminder Mailable: Preparing content.',
            [
                'loan_application_id' => $this->loanApplication->id ?? 'N/A',
                'view' => 'emails.loan-application-overdue-reminder',
            ]
        );

        return new Content(
            view: 'emails.loan-application-overdue-reminder',
            with: [
                'loanApplication' => $this->loanApplication,
                'overdueItems' => $this->overdueItems,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
