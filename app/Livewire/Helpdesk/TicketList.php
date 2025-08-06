<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class TicketList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // open, in_progress, resolved, closed
    public $priorityFilter = '';
    public $categoryFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tickets = HelpdeskTicket::query()
            ->where('user_id', Auth::id()) // Only show user's own tickets
            ->with(['category', 'priority', 'assignedTo'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->whereHas('priority', function ($q) {
                    $q->where('id', $this->priorityFilter);
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('id', $this->categoryFilter);
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.helpdesk.ticket-list', [
            'tickets' => $tickets,
            'categories' => \App\Models\HelpdeskCategory::all(),
            'priorities' => \App\Models\HelpdeskPriority::all(),
        ]);
    }
}
