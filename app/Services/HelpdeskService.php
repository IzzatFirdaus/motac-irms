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
use Illuminate\Support\Facades\Log;

/**
 * HelpdeskService
 *
 * Handles core business logic for helpdesk tickets, including creation, updates,
 * closing, commenting, and attachment management.
 */
class HelpdeskService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new helpdesk ticket and handle file attachments.
     *
     * @param array $data Ticket data (validated fields).
     * @param User $applicant User creating the ticket.
     * @param array $attachments Optional file attachments.
     * @return HelpdeskTicket
     */
    public function createTicket(array $data, User $applicant, array $attachments = []): HelpdeskTicket
    {
        return DB::transaction(function () use ($data, $applicant, $attachments) {
            $ticket = new HelpdeskTicket();
            $ticket->fill($data);
            $ticket->user_id = $applicant->id;
            $ticket->status = HelpdeskTicket::STATUS_OPEN;

            // Set SLA due date (default 48h unless config override)
            $slaHours = config('motac.helpdesk.default_sla_hours', 48);
            $ticket->sla_due_at = Carbon::now()->addHours($slaHours);

            $ticket->save();

            $this->handleAttachments($ticket, $attachments);

            // Notify applicant about ticket creation
            $this->notificationService->notifyTicketCreated($applicant, $ticket, 'applicant');

            return $ticket;
        });
    }

    /**
     * Add a comment to a helpdesk ticket.
     *
     * @param HelpdeskTicket $ticket
     * @param string $commentText
     * @param User $user
     * @param array $attachments
     * @param bool $isInternal
     * @return HelpdeskComment
     */
    public function addComment(HelpdeskTicket $ticket, string $commentText, User $user, array $attachments = [], bool $isInternal = false): HelpdeskComment
    {
        return DB::transaction(function () use ($ticket, $commentText, $user, $attachments, $isInternal) {
            $comment = new HelpdeskComment();
            // Comment model uses 'ticket_id' as FK
            $comment->ticket_id = $ticket->id;
            $comment->user_id = $user->id;
            $comment->comment = $commentText;
            $comment->is_internal = $isInternal;
            $comment->save();

            // Attach files to the comment if the comment model supports attachments()
            if (method_exists($comment, 'attachments')) {
                $this->handleAttachments($comment, $attachments);
            }

            // Notify applicant and assignee of new comment
            if ($ticket->user instanceof User) {
                $this->notificationService->notifyTicketCommentAdded($ticket->user, $comment, $user, 'applicant');
            }
            if ($ticket->assignedTo instanceof User && $ticket->assignedTo->id !== $user->id) {
                $this->notificationService->notifyTicketCommentAdded($ticket->assignedTo, $comment, $user, 'assignee');
            }

            return $comment;
        });
    }

    /**
     * Update an existing helpdesk ticket (web or API).
     *
     * @param HelpdeskTicket $ticket
     * @param array $data Validated update data
     * @param User $updater
     * @param array $attachments
     * @return HelpdeskTicket
     */
    public function updateTicket(HelpdeskTicket $ticket, array $data, User $updater, array $attachments = []): HelpdeskTicket
    {
        return DB::transaction(function () use ($ticket, $data, $updater, $attachments) {
            $oldStatus = $ticket->status;
            $oldAssignedToUserId = $ticket->assigned_to_user_id;

            // Exclude attachments from fillable data
            $fillableData = $data;
            unset($fillableData['attachments']);

            // Map legacy/alternate fields for compatibility
            if (isset($fillableData['subject']) && !isset($fillableData['title'])) {
                $fillableData['title'] = $fillableData['subject'];
                unset($fillableData['subject']);
            }
            if (isset($fillableData['resolution_details']) && !isset($fillableData['resolution_notes'])) {
                $fillableData['resolution_notes'] = $fillableData['resolution_details'];
                unset($fillableData['resolution_details']);
            }

            $ticket->fill($fillableData);

            // Handle status transitions, especially for closing or reopening
            if (isset($data['status'])) {
                if ($data['status'] === HelpdeskTicket::STATUS_CLOSED && is_null($ticket->closed_at)) {
                    $ticket->closed_at = Carbon::now();
                } elseif ($data['status'] !== HelpdeskTicket::STATUS_CLOSED && !is_null($ticket->closed_at)) {
                    $ticket->closed_at = null; // Re-open the ticket if status changes from closed
                }
            }

            $ticket->save();

            if (method_exists($ticket, 'attachments')) {
                $this->handleAttachments($ticket, $attachments);
            }

            // Notify about status changes
            if ($oldStatus !== $ticket->status) {
                if ($ticket->user instanceof User) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->user, $ticket, $updater, 'applicant');
                }
                if ($ticket->assignedTo instanceof User) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->assignedTo, $ticket, $updater, 'assignee');
                }
            }

            // Notify about assignment change
            if ($oldAssignedToUserId !== $ticket->assigned_to_user_id && $ticket->assignedTo) {
                $this->notificationService->notifyTicketAssigned($ticket->assignedTo, $ticket, $updater);
            }

            Log::info(sprintf('Helpdesk Ticket ID %d updated by User ID %d.', $ticket->id, $updater->id), [
                'ticket_id' => $ticket->id,
                'updated_by' => $updater->id,
                'changes' => $ticket->getChanges()
            ]);

            return $ticket;
        });
    }

    /**
     * Close a helpdesk ticket.
     *
     * @param HelpdeskTicket $ticket
     * @param array $data ['resolution_notes' => ..., ...]
     * @param User $closer
     * @return HelpdeskTicket
     */
    public function closeTicket(HelpdeskTicket $ticket, array $data, User $closer): HelpdeskTicket
    {
        return DB::transaction(function () use ($ticket, $data, $closer) {
            $oldStatus = $ticket->status;

            $ticket->status = HelpdeskTicket::STATUS_CLOSED;
            // Use 'resolution_notes' as canonical; fallback to 'resolution_details' for legacy/compat.
            $ticket->resolution_notes = $data['resolution_notes'] ?? ($data['resolution_details'] ?? null);
            $ticket->closed_by_id = $closer->id;
            $ticket->closed_at = Carbon::now();
            $ticket->save();

            // Notify parties only if status actually changed to closed
            if ($oldStatus !== HelpdeskTicket::STATUS_CLOSED) {
                if ($ticket->user instanceof User) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->user, $ticket, $closer, 'applicant');
                }
                if ($ticket->assignedTo instanceof User) {
                    $this->notificationService->notifyTicketStatusUpdated($ticket->assignedTo, $ticket, $closer, 'assignee');
                }
            }

            Log::info(sprintf('Helpdesk Ticket ID %d closed by User ID %d.', $ticket->id, $closer->id), [
                'ticket_id' => $ticket->id,
                'closed_by' => $closer->id
            ]);

            return $ticket;
        });
    }

    /**
     * Handles file attachments for tickets or comments.
     *
    * @param mixed $attachable Accepts Eloquent models that implement attachments().
     * @param array $attachments
     * @return void
     */
    protected function handleAttachments($attachable, array $attachments): void
    {
        if (! method_exists($attachable, 'attachments')) {
            return;
        }

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
