<?php

namespace App\Livewire\Helpdesk\Admin;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\HelpdeskService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TicketManagement extends Component
{
    use WithPagination;

    protected HelpdeskService $helpdeskService;

    // Properties for filtering and searching
    #[Url]
    public string $search = '';

    #[Url]
    public ?string $status = null;

    #[Url]
    public ?int $category_id = null;

    #[Url]
    public ?int $priority_id = null;

    #[Url]
    public ?int $assigned_to_user_id = null;

    // Properties for modals and selected ticket
    public HelpdeskTicket $selectedTicket;
    public bool $showTicketDetailsModal = false;
    public bool $showAssignTicketModal = false;
    public bool $showChangeStatusModal = false;
    public bool $showAddCommentModal = false;
    public bool $showCloseTicketModal = false;

    public string $newStatus = '';
    public ?int $assigneeId = null;
    public string $commentText = '';
    public bool $isInternalComment = false;
    public ?string $resolutionDetails = null;

    protected $listeners = ['ticketUpdated' => '$refresh']; // Listen for an event to refresh the list

    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    #[Computed()]
    public function tickets()
    {
        return HelpdeskTicket::query()
            ->with(['user', 'category', 'priority', 'assignedTo'])
            ->when($this->search, function (Builder $query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->status, fn(Builder $query) => $query->where('status', $this->status))
            ->when($this->category_id, fn(Builder $query) => $query->where('category_id', $this->category_id))
            ->when($this->priority_id, fn(Builder $query) => $query->where('priority_id', $this->priority_id))
            ->when($this->assigned_to_user_id, fn(Builder $query) => $query->where('assigned_to_user_id', $this->assigned_to_user_id))
            ->latest()
            ->paginate(10);
    }

    #[Computed()]
    public function categories()
    {
        return HelpdeskCategory::all();
    }

    #[Computed()]
    public function priorities()
    {
        return HelpdeskPriority::all();
    }

    #[Computed()]
    public function staffUsers()
    {
        // Assuming 'staff' role or similar for users who can be assigned tickets
        return User::role('BPM Staff')->orderBy('name')->get();
    }

    public function viewTicketDetails(HelpdeskTicket $ticket)
    {
        $this->selectedTicket = $ticket;
        $this->showTicketDetailsModal = true;
    }

    public function openAssignTicketModal(HelpdeskTicket $ticket)
    {
        $this->selectedTicket = $ticket;
        $this->assigneeId = $ticket->assigned_to_user_id; // Pre-fill if already assigned
        $this->showAssignTicketModal = true;
    }

    public function assignTicket()
    {
        $this->validate([
            'assigneeId' => 'nullable|exists:users,id',
        ]);

        try {
            $updater = Auth::user();
            if (!$updater) {
                session()->flash('error', 'Authentication required to assign ticket.');
                return;
            }

            // Use the updateTicket method from HelpdeskService
            $this->helpdeskService->updateTicket(
                $this->selectedTicket,
                ['assigned_to_user_id' => $this->assigneeId],
                $updater // Pass the updater (authenticated user)
            );

            session()->flash('success', 'Ticket assigned successfully.');
            $this->showAssignTicketModal = false;
            $this->dispatch('ticketUpdated'); // Refresh the list
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to assign ticket: ' . $e->getMessage());
        }
    }

    public function openChangeStatusModal(HelpdeskTicket $ticket)
    {
        $this->selectedTicket = $ticket;
        $this->newStatus = $ticket->status; // Pre-fill with current status
        $this->showChangeStatusModal = true;
    }

    public function changeTicketStatus()
    {
        $this->validate([
            'newStatus' => 'required|string|in:open,in_progress,on_hold,resolved,closed,reopened,pending_user_feedback',
        ]);

        try {
            $updater = Auth::user();
            if (!$updater) {
                session()->flash('error', 'Authentication required to change ticket status.');
                return;
            }

            // Corrected: Use updateTicket method from HelpdeskService
            // Pass the selected ticket, an array with the new status, and the updater user
            $this->helpdeskService->updateTicket(
                $this->selectedTicket,
                ['status' => $this->newStatus],
                $updater
            );

            session()->flash('success', 'Ticket status updated successfully.');
            $this->showChangeStatusModal = false;
            $this->dispatch('ticketUpdated'); // Refresh the list
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to change ticket status: ' . $e->getMessage());
        }
    }

    public function openAddCommentModal(HelpdeskTicket $ticket)
    {
        $this->selectedTicket = $ticket;
        $this->commentText = ''; // Clear previous comment
        $this->isInternalComment = false; // Reset
        $this->showAddCommentModal = true;
    }

    public function addComment()
    {
        $this->validate([
            'commentText' => 'required|string|min:5',
            'isInternalComment' => 'boolean',
        ]);

        try {
            $commenter = Auth::user();
            if (!$commenter) {
                session()->flash('error', 'Authentication required to add comment.');
                return;
            }

            $this->helpdeskService->addComment(
                $this->selectedTicket,
                $this->commentText,
                $commenter,
                [], // No attachments for now, can be extended
                $this->isInternalComment
            );

            session()->flash('success', 'Comment added successfully.');
            $this->showAddCommentModal = false;
            $this->dispatch('ticketUpdated'); // Refresh the list
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    public function openCloseTicketModal(HelpdeskTicket $ticket)
    {
        $this->selectedTicket = $ticket;
        $this->resolutionDetails = $ticket->resolution_details; // Pre-fill if exists
        $this->showCloseTicketModal = true;
    }

    public function closeTicket()
    {
        $this->validate([
            'resolutionDetails' => 'required|string|min:10',
        ]);

        try {
            $closer = Auth::user();
            if (!$closer) {
                session()->flash('error', 'Authentication required to close ticket.');
                return;
            }

            // Use the closeTicket method from HelpdeskService
            $this->helpdeskService->closeTicket(
                $this->selectedTicket,
                ['resolution_details' => $this->resolutionDetails],
                $closer
            );

            session()->flash('success', 'Ticket closed successfully.');
            $this->showCloseTicketModal = false;
            $this->dispatch('ticketUpdated'); // Refresh the list
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to close ticket: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.helpdesk.admin.ticket-management');
    }
}
