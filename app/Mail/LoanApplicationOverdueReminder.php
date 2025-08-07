<?php

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

class LoanApplicationOverdueReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LoanApplication $loanApplication;
    public Collection $overdueItems;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication->loadMissing([
            'user',
            'transactions.loanTransactionItems.equipment',
            'transactions.loanTransactionItems.transaction',
        ]);
        $this->overdueItems = $this->calculateOverdueItems();
    }

    private function calculateOverdueItems(): Collection
    {
        $overdue = new Collection();
        foreach ($this->loanApplication->transactions as $transaction) {
            if ($transaction->type === LoanTransaction::TYPE_ISSUE) {
                foreach ($transaction->loanTransactionItems as $item) {
                    if (
                        (property_exists($item, 'is_returned') && !$item->is_returned)
                        && property_exists($item, 'due_date') && $item->due_date && $item->due_date->isPast()
                    ) {
                        $overdue->push($item);
                    }
                }
            }
        }
        return $overdue;
    }

    public function envelope(): Envelope
    {
        $applicantName = $this->loanApplication->user?->name ?? 'Pemohon Tidak Diketahui';
        $applicationId = $this->loanApplication->id ?? 'N/A';

        return new Envelope(
            to: [new Address($this->loanApplication->user->email, $applicantName)],
            subject: __('Peringatan: Peralatan Pinjaman ICT Telah Lewat Dipulangkan (Permohonan #:id)', ['id' => $applicationId])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-application-overdue-reminder',
            with: [
                'loanApplication' => $this->loanApplication,
                'overdueItems' => $this->overdueItems,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
