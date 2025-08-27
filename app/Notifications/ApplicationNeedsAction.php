<?php

namespace App\Notifications;

use App\Models\Approval;
use App\Models\LoanApplication; // Keep this import
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model; // Keep Model for generic polymorphic relation
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationNeedsAction extends Notification implements ShouldQueue
{
    use Queueable;

    public Approval $approvalTask;

    /**
     * The approvable item, specifically a LoanApplication now that EmailApplications are removed.
     *
     * @var LoanApplication|Model // More specific, but Model is fine if other types will exist later
     */
    public Model $approvableItem;

    public function __construct(Approval $approvalTask)
    {
        $this->approvalTask = $approvalTask;
        // Ensure approvable and its user relationship are loaded
        $this->approvableItem = $approvalTask->approvable()->with('user')->firstOrFail();
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        // Asserting the type for better IDE support and clarity, though not strictly necessary if type is always LoanApplication
        /** @var LoanApplication $app */
        $app = $this->approvableItem;

        $subject = __('Tindakan Diperlukan: :itemType #:id', [
            'itemType' => $this->getItemTypeDisplayName($app), // Pass the application instance
            'id'       => $app->id,
        ]);

        return (new MailMessage())
            ->subject($subject)
            ->view('emails.application-needs-action', [
                'notification' => $this,
                'notifiable'   => $notifiable,
            ]);
    }

    public function getActionUrl(): string
    {
        // This route should lead to the approval dashboard or the specific loan application for approval
        return route('approvals.dashboard'); // Or route('loan-applications.show', $this->approvableItem->id); for specific view
    }

    public function toArray(User $notifiable): array
    {
        /** @var LoanApplication $app */
        $app = $this->approvableItem;

        return [
            'title'   => 'Tindakan Kelulusan Diperlukan',
            'message' => __('Permohonan #:id oleh :applicantName menunggu tindakan anda.', [
                'id'            => $app->id,
                'applicantName' => optional($app->user)->name,
            ]),
            'action_url'    => $this->getActionUrl(),
            'related_model' => $app->getMorphClass(),
            'related_id'    => $app->id,
            'icon'          => 'ti ti-alert-triangle', // Consider adding a default icon
        ];
    }

    /**
     * Get the display name for the approvable item type.
     * Updated to specifically handle LoanApplication.
     */
    public function getItemTypeDisplayName(Model $approvableItem): string
    {
        // With EmailApplication removed, we can assume it's a LoanApplication
        if ($approvableItem instanceof LoanApplication) {
            return __('Permohonan Pinjaman Peralatan ICT');
        }

        // Fallback for any other morphable types that might be introduced later
        return ucfirst(str_replace('_', ' ', class_basename($approvableItem)));
    }
}
