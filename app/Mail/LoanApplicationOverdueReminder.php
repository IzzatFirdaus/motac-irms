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

        Log::info('LoanApplicationOverdueReminder Mailable: Instance created.', [
            'loan_application_id' => $this->loanApplication->id,
            'overdue_items_count' => $this->overdueItems->count(),
        ]);
    }

    /**
     * Determine which items are overdue.
     */
    private function calculateOverdueItems(): Collection
    {
        $overdue = new Collection();
        foreach ($this->loanApplication->transactions as $transaction) {
            // Only consider 'issue' transactions for overdue checks
            if ($transaction->type === LoanTransaction::TYPE_ISSUE) {
                foreach ($transaction->loanTransactionItems as $item) {
                    // An item is overdue if it was issued, has a due date,
                    // and has not been returned (no return transaction item linked)
                    // and its due date is in the past.
                    if (
                        ! $item->is_returned &&
                        $item->due_date &&
                        $item->due_date->isPast()
                    ) {
                        $overdue->push($item);
                    }
                }
            }
        }

        return $overdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id ?? 'N/A';

        $toAddresses = [
            new Address($this->loanApplication->user->email, $applicantName),
        ];

        // Subject will indicate if there are overdue items and for which application
        $subject = __('Peringatan: Peralatan Pinjaman ICT Telah Lewat Dipulangkan (Permohonan #:id)', ['id' => $applicationId]);

        Log::info(
            'LoanApplicationOverdueReminder Mailable: Preparing envelope.',
            [
                'loan_application_id' => $applicationId,
                'subject' => $subject,
                'to_recipients' => $toAddresses !== [] ? $toAddresses[0]->address : 'N/A',
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
