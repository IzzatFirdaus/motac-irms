<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * MyTicketsIndex.
 *
 * User's own helpdesk tickets (paginated, filterable).
 */
/**
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $tickets
 */
class MyTicketsIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $status = '';

    public $priority = '';

    public $category = '';

    protected $queryString = [
        'search'   => ['except' => ''],
        'status'   => ['except' => ''],
        'priority' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    /**
     * Get tickets belonging to the authenticated user.
     */
    public function getTicketsProperty()
    {
        return HelpdeskTicket::query()
            ->where('user_id', Auth::id())
            ->with(['category', 'priority', 'assignedTo'])
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->status, function ($query): void {
                $query->where('status', $this->status);
            })
            ->when($this->priority, function ($query): void {
                $query->where('priority_id', $this->priority);
            })
            ->when($this->category, function ($query): void {
                $query->where('category_id', $this->category);
            })
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.helpdesk.my-tickets-index', [
            'tickets'    => $this->tickets,
            'categories' => \App\Models\HelpdeskCategory::all(),
            'priorities' => \App\Models\HelpdeskPriority::all(),
        ]);
    }
}
