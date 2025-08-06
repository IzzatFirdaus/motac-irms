<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    protected HelpdeskService $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        $this->middleware(['auth', 'verified']);
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Display a listing of the user's tickets.
     */
    public function index(): View
    {
        // This will primarily be handled by Livewire, but a fallback view might exist.
        return view('helpdesk.index');
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create(): View
    {
        return view('helpdesk.create');
    }

    /**
     * Display the specified ticket.
     */
    public function show(HelpdeskTicket $ticket): View
    {
        $this->authorize('view', $ticket); // Ensure policy is applied
        return view('helpdesk.show', compact('ticket'));
    }

    // Add other methods like store, update, destroy if not fully Livewire driven
}
