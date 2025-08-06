<?php

namespace App\Notifications;

use App\Models\HelpdeskComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected HelpdeskComment $comment;
    protected User $commenter;
    protected string $recipientType; // 'applicant', 'agent', 'internal_admin'

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpdeskComment $comment, User $commenter, string $recipientType)
    {
        $this->comment = $comment;
        $this->commenter = $commenter;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->comment->ticket;
        $commenterName = $this->commenter->name;

        $subject = "New Comment on Helpdesk Ticket #{$ticket->id}";
        $greeting = "Dear {$notifiable->name},";
        $messageLine = '';

        if ($this->recipientType === 'applicant') {
            $messageLine = "A new comment has been added to your helpdesk ticket **#{$ticket->id}** (`{$ticket->title}`) by `{$commenterName}`.";
        } elseif ($this->recipientType === 'agent') {
            $messageLine = "A new comment has been added to ticket **#{$ticket->id}** (`{$ticket->title}`) by `{$commenterName}`.";
        } elseif ($this->recipientType === 'internal_admin') {
            $subject = "Internal Comment on Helpdesk Ticket #{$ticket->id}";
            $messageLine = "An internal comment has been added to ticket **#{$ticket->id}** (`{$ticket->title}`) by `{$commenterName}`.";
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($messageLine)
                    ->line("Comment: \"{$this->comment->comment}\"")
                    ->action('View Ticket', url('/helpdesk/' . $ticket->id))
                    ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->comment->ticket_id,
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->commenter->name,
            'message' => "New comment on ticket #{$this->comment->ticket_id}.",
            'url' => url('/helpdesk/' . $this->comment->ticket_id),
        ];
    }
}
