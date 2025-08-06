<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * TicketController
 *
 * Handles the main Helpdesk ticket web interface (non-API).
 * Most business logic is delegated to the HelpdeskService or Livewire components.
 */
class TicketController extends Controller
{
    protected HelpdeskService $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        // Ensure only authenticated, verified users can access helpdesk pages
        $this->middleware(['auth', 'verified']);
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Display a listing of the user's tickets.
     * Typically rendered by a Livewire component.
     */
    public function index(): View
    {
        return view('helpdesk.index');
    }

    /**
     * Show the form for creating a new ticket.
     * Typically handled by Livewire.
     */
    public function create(): View
    {
        return view('helpdesk.create');
    }

    /**
     * Display the specified ticket details page.
     * Applies policy to ensure only authorized users can view the ticket.
     */
    public function show(HelpdeskTicket $ticket): View
    {
        $this->authorize('view', $ticket);
        return view('helpdesk.show', compact('ticket'));
    }

    // Optionally, you could add methods for update, destroy, etc., if not handled by Livewire.
}
