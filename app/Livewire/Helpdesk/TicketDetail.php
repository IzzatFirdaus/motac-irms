<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * TicketDetail.
 *
 * Displays a single ticket (for user/agent) and allows adding comments.
 */
class TicketDetail extends Component
{
    use WithFileUploads;

    public HelpdeskTicket $ticket;

    public $newComment;

    public $commentAttachments = [];

    public $isInternalComment = false; // For IT agents only

    protected $rules = [
        'newComment'           => 'required|string|min:3',
        'commentAttachments.*' => 'nullable|file|max:2048|mimes:jpg,png,pdf,docx,txt,xlsx',
    ];

    protected HelpdeskService $helpdeskService;

    public function mount(HelpdeskTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->authorize('view', $this->ticket);
    }

    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    public function addComment()
    {
        $this->validate();

        try {
            $this->helpdeskService->addComment(
                $this->ticket,
                $this->newComment,
                Auth::user(),
                $this->commentAttachments,
                $this->isInternalComment && Auth::user()->hasRole('IT Admin')
            );

            $this->reset(['newComment', 'commentAttachments', 'isInternalComment']);
            $this->ticket->refresh();
            session()->flash('message', 'Comment added successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->ticket->load(['comments.user', 'comments.attachments', 'attachments']);

        return view('livewire.helpdesk.ticket-detail');
    }
}
