<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

/**
 * TicketDetails Livewire component.
 *
 * Shows details for a specific ticket and allows comments/attachments.
 */
class TicketDetails extends Component
{
    use WithFileUploads;

    public HelpdeskTicket $ticket;
    public $newComment;
    public $commentAttachments = [];
    public $isInternalComment = false; // For agents to add internal notes

    protected HelpdeskService $helpdeskService;

    protected $rules = [
        'newComment' => 'required|string|min:3',
        'commentAttachments.*' => 'nullable|file|max:2048|mimes:jpg,png,pdf,docx,txt,xlsx',
    ];

    /**
     * Mount with the ticket model.
     */
    public function mount(HelpdeskTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->authorize('view', $this->ticket);
    }

    /**
     * Inject HelpdeskService for comment actions.
     */
    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Add a comment to the ticket.
     */
    public function addComment()
    {
        $this->validate();

        try {
            $this->helpdeskService->addComment(
                $this->ticket,
                $this->newComment,
                Auth::user(),
                $this->commentAttachments,
                $this->isInternalComment && Auth::user()->hasRole('IT Admin') // Only allow internal if user is IT Admin
            );

            $this->reset(['newComment', 'commentAttachments', 'isInternalComment']);
            $this->ticket->refresh(); // Reload ticket to show new comment
            session()->flash('message', 'Comment added successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    /**
     * Render the ticket details view.
     */
    public function render()
    {
        $this->ticket->load(['comments.user', 'comments.attachments', 'attachments']);
        return view('livewire.helpdesk.ticket-details', [
            'ticket' => $this->ticket,
        ]);
    }
}
