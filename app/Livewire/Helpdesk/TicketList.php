<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * TicketList.
 *
 * Displays a paginated, filterable list of the current user's helpdesk tickets.
 */
class TicketList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $priorityFilter = '';

    public $categoryFilter = '';

    // Persist filters/search in the query string for shareable/filterable URLs
    protected $queryString = [
        'search'         => ['except' => ''],
        'statusFilter'   => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    // Reset pagination when filters/search changes
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
            ->where('user_id', Auth::id())
            ->with(['category', 'priority', 'assignedTo'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priority_id', $this->priorityFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.helpdesk.ticket-list', [
            'tickets'    => $tickets,
            'categories' => HelpdeskCategory::active()->get(),
            'priorities' => HelpdeskPriority::all(),
        ]);
    }
}
