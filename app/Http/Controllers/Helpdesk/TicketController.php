<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Helpdesk\StoreHelpdeskTicketRequest;
use App\Http\Requests\Helpdesk\UpdateHelpdeskTicketRequest;
use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * TicketController.
 *
 * Handles the main Helpdesk ticket web interface (non-API).
 * Most business logic is delegated to the HelpdeskService or Livewire components.
 * Provides endpoints for listing, creating, viewing, editing, updating, and deleting tickets.
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
        // Typically, Livewire handles the data. This view is the container.
        return view('helpdesk.index');
    }

    /**
     * Show the form for creating a new ticket.
     * Typically handled by Livewire.
     */
    public function create(): View
    {
        // Shows the helpdesk ticket creation form/container.
        return view('helpdesk.create');
    }

    /**
     * Store a newly created ticket in storage (web interface).
     * Delegates logic to HelpdeskService. For Livewire, this may not be used.
     */
    public function store(StoreHelpdeskTicketRequest $request): RedirectResponse
    {
        // The request is already validated by StoreHelpdeskTicketRequest
        $validated = $request->validated();

        $user = Auth::user();

        try {
            $ticket = $this->helpdeskService->createTicket(
                $validated,
                $user,
                $request->file('attachments', [])
            );

            Log::info('Helpdesk Web: Ticket created successfully.', [
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
            ]);

            return redirect()->route('helpdesk.tickets.show', $ticket)
                ->with('success', __('Tiket bantuan berjaya dihantar.'));
        } catch (\Exception $e) {
            Log::error('Helpdesk Web: Failed to create ticket.', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('error', __('Gagal menghantar tiket bantuan: ') . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified ticket details page.
     * Applies policy to ensure only authorized users can view the ticket.
     */
    public function show(HelpdeskTicket $ticket): View
    {
        $this->authorize('view', $ticket);
        // Optionally eager load relations for display, e.g. category, priority, comments
        $ticket->load(['category', 'priority', 'applicant', 'assignedTo', 'comments', 'attachments']);

        return view('helpdesk.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified ticket.
     * Only allow if user has permission.
     */
    public function edit(HelpdeskTicket $ticket): View
    {
        $this->authorize('update', $ticket);
        // Optionally eager load for form display
        $ticket->load(['category', 'priority', 'assignedTo']);

        return view('helpdesk.edit', compact('ticket'));
    }

    /**
     * Update the specified ticket in storage.
     * Delegates update logic to HelpdeskService.
     */
    public function update(UpdateHelpdeskTicketRequest $request, HelpdeskTicket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validated();

        $user = Auth::user();

        try {
            $this->helpdeskService->updateTicket(
                $ticket,
                $validated,
                $user,
                $request->file('attachments', [])
            );

            Log::info('Helpdesk Web: Ticket updated successfully.', [
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
            ]);

            return redirect()->route('helpdesk.tickets.show', $ticket)
                ->with('success', __('Tiket berjaya dikemaskini.'));
        } catch (\Exception $e) {
            Log::error('Helpdesk Web: Failed to update ticket.', [
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->with('error', __('Gagal mengemaskini tiket: ') . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified ticket from storage (soft delete).
     * Only allow if user has permission.
     */
    public function destroy(HelpdeskTicket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        try {
            $ticket->delete();

            Log::info('Helpdesk Web: Ticket deleted (soft) successfully.', [
                'ticket_id' => $ticket->id,
                'user_id'   => Auth::id(),
            ]);

            return redirect()->route('helpdesk.tickets.index')
                ->with('success', __('Tiket berjaya dipadam.'));
        } catch (\Exception $e) {
            Log::error('Helpdesk Web: Failed to delete ticket.', [
                'ticket_id' => $ticket->id,
                'user_id'   => Auth::id(),
                'error'     => $e->getMessage(),
            ]);

            return back()->with('error', __('Gagal memadam tiket: ') . $e->getMessage());
        }
    }
}
