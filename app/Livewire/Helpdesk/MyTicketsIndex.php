<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * MyTicketsIndex
 *
 * User's own helpdesk tickets (paginated, filterable).
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }

    /**
     * Get tickets belonging to the authenticated user.
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

    public function render()
    {
        return view('livewire.helpdesk.my-tickets-index', [
            'tickets' => $this->tickets,
            'categories' => \App\Models\HelpdeskCategory::all(),
            'priorities' => \App\Models\HelpdeskPriority::all(),
        ]);
    }
}
