<?php

namespace App\Mail;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class EquipmentReturnedNotification extends Mailable
{
  use Queueable, SerializesModels;

  public LoanApplication $loanApplication;
  public LoanTransaction $returnTransaction;
  public User $notifiable;

  /**
   * Create a new message instance.
   *
   * @param \App\Models\LoanApplication $loanApplication
   * @param \App\Models\LoanTransaction $returnTransaction
   * @param \App\Models\User $notifiable
   */
  public function __construct(LoanApplication $loanApplication, LoanTransaction $returnTransaction, User $notifiable)
  {
    $this->loanApplication = $loanApplication->loadMissing('user');
    $this->returnTransaction = $returnTransaction->loadMissing(['loanTransactionItems.equipment']);
    $this->notifiable = $notifiable;
  }

  /**
   * Get the message envelope.
   *
   * @return \Illuminate\Mail\Mailables\Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: __('Peralatan Pinjaman Dipulangkan (Permohonan #:id)', ['id' => $this->loanApplication->id ?? 'N/A'])
    );
  }

  /**
   * Get the message content definition.
   *
   * @return \Illuminate\Mail\Mailables\Content
   */
  public function content(): Content
  {
    // We will create this Blade view next
    return new Content(
      view: 'emails.equipment-returned',
      with: [
        'applicantName' => $this->loanApplication->user?->name ?? $this->notifiable->name,
        'applicationId' => $this->loanApplication->id ?? 'N/A',
        'transactionDate' => $this->returnTransaction->transaction_date instanceof Carbon
          ? $this->returnTransaction->transaction_date->format(config('app.datetime_format_my', 'd/m/Y H:i A'))
          : __('tarikh tidak direkodkan'),
        'returnedItems' => $this->returnTransaction->loanTransactionItems,
        'returnNotes' => $this->returnTransaction->return_notes,
        'applicationStatus' => $this->loanApplication->status,
        'applicationUrl' => route('resource-management.my-applications.loan.show', ['loan_application' => $this->loanApplication->id]),
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
