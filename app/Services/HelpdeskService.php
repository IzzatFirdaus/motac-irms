<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Added Log for debugging/info

class HelpdeskService
{
    protected NotificationService $notificationService; // Changed from TicketNotificationService

    public function __construct(NotificationService $notificationService) // Changed from TicketNotificationService
    {
        $this->notificationService = $notificationService;
    }

    public function createTicket(array $data, User $applicant, array $attachments = []): HelpdeskTicket
    {
        return DB::transaction(function () use ($data, $applicant, $attachments) {
            $ticket = new HelpdeskTicket();
            $ticket->fill($data);
            $ticket->user_id = $applicant->id;
            $ticket->status = 'open';

            $slaHours = config('motac.helpdesk.default_sla_hours', 48);
            $ticket->sla_due_at = Carbon::now()->addHours($slaHours);

            $ticket->save();

            $this->handleAttachments($ticket, $attachments);
            // Assuming notifyTicketCreated expects (User $recipient, HelpdeskTicket $ticket, string $recipientType)
            // For ticket creation, the recipient is the applicant, and recipientType is 'applicant'
            $this->notificationService->notifyTicketCreated($applicant, $ticket, 'applicant');

            return $ticket;
        });
    }

    public function addComment(HelpdeskTicket $ticket, string $commentText, User $user, array $attachments = [], bool $isInternal = false): HelpdeskComment
    {
        return DB::transaction(function () use ($ticket, $commentText, $user, $attachments, $isInternal) {
            $comment = new HelpdeskComment();
            $comment->helpdesk_ticket_id = $ticket->id;
            $comment->user_id = $user->id;
            $comment->comment = $commentText;
            $comment->is_internal = $isInternal;
            $comment->save();

            $this->handleAttachments($comment, $attachments);
            // Notify involved parties about the new comment
            // Assuming notifyTicketCommentAdded expects (User $recipient, HelpdeskComment $comment, User $commenter, string $recipientType)
            $this->notificationService->notifyTicketCommentAdded($ticket->user, $comment, $user, 'applicant'); // Notify applicant
            if ($ticket->assignedTo && $ticket->assignedTo->id !== $user->id) {
                $this->notificationService->notifyTicketCommentAdded($ticket->assignedTo, $comment, $user, 'assignee'); // Notify assignee
            }

            return $comment;
        });
    }

    /**
     * Update an existing helpdesk ticket.
     *
     * @param HelpdeskTicket $ticket The ticket to update.
     * @param array $data The data to update the ticket with.
     * @param User $updater The user performing the update.
     * @param array $attachments Optional array of uploaded files for attachments.
     * @return HelpdeskTicket
     */
    public function updateTicket(HelpdeskTicket $ticket, array $data, User $updater, array $attachments = []): HelpdeskTicket
    {
        return DB::transaction(function () use ($ticket, $data, $updater, $attachments) {
            $oldStatus = $ticket->status;
            $oldAssignedToUserId = $ticket->assigned_to_user_id;

            // Fill the ticket with validated data, excluding attachments.
            // Ensure only fillable attributes are passed to fill to prevent mass assignment issues.
            $fillableData = $data;
            unset($fillableData['attachments']); // Attachments are handled separately

            $ticket->fill($fillableData);

            // Handle status changes, especially for 'closed' status
            if (isset($data['status'])) {
                if ($data['status'] === 'closed' && is_null($ticket->closed_at)) {
                    $ticket->closed_at = Carbon::now();
                } elseif ($data['status'] !== 'closed' && !is_null($ticket->closed_at)) {
                    $ticket->closed_at = null; // Re-open the ticket if status changes from closed
                }
            }

            $ticket->save();

            // Handle new attachments
            $this->handleAttachments($ticket, $attachments);

            // Send notifications based on changes
            if ($oldStatus !== $ticket->status) {
                // Notify applicant and assigned staff about status change
                $this->notificationService->notifyTicketStatusUpdated($ticket->user, $ticket, $updater, 'applicant');
                if ($ticket->assignedTo) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->assignedTo, $ticket, $updater, 'assignee');
                }
            }

            if ($oldAssignedToUserId !== $ticket->assigned_to_user_id) {
                // Notify the newly assigned person
                if ($ticket->assignedTo) {
                    $this->notificationService->notifyTicketAssigned($ticket->assignedTo, $ticket, $updater);
                }
                // Optionally, notify the previously assigned person that they are no longer assigned
                // This would require fetching the old assigned user and checking if they are different from the new one.
            }

            Log::info(sprintf('Helpdesk Ticket ID %d updated by User ID %d.', $ticket->id, $updater->id), ['ticket_id' => $ticket->id, 'updated_by' => $updater->id, 'changes' => $ticket->getChanges()]);

            return $ticket;
        });
    }

    /**
     * Close a helpdesk ticket.
     *
     * @param HelpdeskTicket $ticket The ticket to close.
     * @param array $data Contains resolution_details and closed_by_id.
     * @param User $closer The user performing the close action.
     * @return HelpdeskTicket
     */
    public function closeTicket(HelpdeskTicket $ticket, array $data, User $closer): HelpdeskTicket
    {
        return DB::transaction(function () use ($ticket, $data, $closer) {
            $oldStatus = $ticket->status;

            $ticket->status = 'closed';
            $ticket->resolution_details = $data['resolution_details'] ?? null;
            $ticket->closed_by_id = $closer->id;
            $ticket->closed_at = Carbon::now();
            $ticket->save();

            // Notify relevant parties about the closure
            if ($oldStatus !== $ticket->status) {
                $this->notificationService->notifyTicketStatusUpdated($ticket->user, $ticket, $closer, 'applicant');
                if ($ticket->assignedTo) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->assignedTo, $ticket, $closer, 'assignee');
                }
            }

            Log::info(sprintf('Helpdesk Ticket ID %d closed by User ID %d.', $ticket->id, $closer->id), ['ticket_id' => $ticket->id, 'closed_by' => $closer->id]);

            return $ticket;
        });
    }

    protected function handleAttachments(Model $attachable, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if ($attachment instanceof UploadedFile && $attachment->isValid()) {
                $path = $attachment->store('helpdesk_attachments', 'public');

                $attachable->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_size' => $attachment->getSize(),
                    'file_type' => $attachment->getMimeType(),
                ]);
            }
        }
    }
}
