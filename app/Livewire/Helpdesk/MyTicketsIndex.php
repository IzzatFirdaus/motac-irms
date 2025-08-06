<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * MyTicketsIndex Livewire component.
 *
 * Shows a paginated list of tickets created by the authenticated user.
 */
class MyTicketsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = '';
    public $category = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    /**
     * Reset pagination on filter updates.
     */
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }

    /**
     * Get user's own tickets with optional filtering.
     */
    public function getTicketsProperty()
    {
        return HelpdeskTicket::query()
            ->where('user_id', Auth::id())
            ->with(['category', 'priority', 'assignedTo'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->priority, function ($query) {
                $query->where('priority_id', $this->priority);
            })
            ->when($this->category, function ($query) {
                $query->where('category_id', $this->category);
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * Render the user's tickets view.
     */
    public function render()
    {
        return view('livewire.helpdesk.my-tickets-index', [
            'tickets' => $this->tickets,
            'categories' => \App\Models\HelpdeskCategory::all(),
            'priorities' => \App\Models\HelpdeskPriority::all(),
        ]);
    }
}
