<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Notifications\Notification;

class SupportPendingApprovalNotification extends Notification
{
    protected $loanApplication;

    public function __construct(LoanApplication $loanApplication)
    {
        $this->loanApplication = $loanApplication;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Loan Application Pending Support Review')
            ->line('A new loan application requires support officer review.')
            ->action('View Application', url('/loan-applications/'.$this->loanApplication->id));
    }
}
