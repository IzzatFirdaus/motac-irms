<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CloseHelpdeskTicketRequest;
use App\Http\Requests\Api\UpdateHelpdeskTicketRequest;
use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * HelpdeskApiController.
 *
 * Provides API endpoints for creating, updating, and closing helpdesk tickets.
 * Handles authentication, validation, and delegates business logic to HelpdeskService.
 */
class HelpdeskApiController extends Controller
{
    protected HelpdeskService $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
        $this->middleware('auth:sanctum'); // Ensure API authentication using Sanctum
    }

    /**
     * Store a newly created ticket via API.
     * Validates input, ensures authenticated user, and returns JSON response.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'category_id'   => 'required|exists:helpdesk_categories,id',
                'priority_id'   => 'required|exists:helpdesk_priorities,id',
                'subject'       => 'required|string|max:255',
                'description'   => 'required|string',
                'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
            ]);

            $applicant = Auth::user();
            if (! $applicant) {
                return response()->json(['message' => 'Unauthenticated user.', 'error' => 'Authentication required.'], 401);
            }

            $ticket = $this->helpdeskService->createTicket($validatedData, $applicant, $request->file('attachments', []));

            Log::info(sprintf('API Ticket Creation Success: Ticket ID %d created by User ID %d.', $ticket->id, $applicant->id), ['ticket_id' => $ticket->id]);

            return response()->json(['message' => 'Ticket created successfully', 'ticket_id' => $ticket->id], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('API Ticket Creation Validation Error: ' . $e->getMessage(), ['errors' => $e->errors(), 'request' => $request->all()]);

            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('API Ticket Creation Error: ' . $e->getMessage(), ['exception_class' => get_class($e), 'request' => $request->all()]);

            return response()->json(['message' => 'Failed to create ticket', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing ticket via API.
     * Validates input, ensures authenticated user, and returns JSON response.
     */
    public function update(UpdateHelpdeskTicketRequest $request, HelpdeskTicket $ticket): JsonResponse
    {
        try {
            $updater = Auth::user();
            if (! $updater) {
                return response()->json(['message' => 'Unauthenticated user.', 'error' => 'Authentication required.'], 401);
            }

            $this->helpdeskService->updateTicket($ticket, $request->validated(), $updater, $request->file('attachments', []));

            Log::info(sprintf('API Ticket Update Success: Ticket ID %d updated by User ID %d.', $ticket->id, $updater->id));

            return response()->json(['message' => 'Ticket updated successfully', 'ticket_id' => $ticket->id]);
        } catch (\Exception $e) {
            Log::error(sprintf('API Ticket Update Error for Ticket ID %d: ', $ticket->id) . $e->getMessage(), [
                'exception_class' => get_class($e),
                'request'         => $request->all(),
            ]);

            return response()->json(['message' => 'Failed to update ticket', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Close the specified ticket via API.
     * Validates input, ensures authenticated user, and updates ticket status.
     */
    public function close(CloseHelpdeskTicketRequest $request, HelpdeskTicket $ticket): JsonResponse
    {
        try {
            $closer = Auth::user();
            if (! $closer) {
                return response()->json(['message' => 'Unauthenticated user.', 'error' => 'Authentication required.'], 401);
            }

            $this->helpdeskService->closeTicket($ticket, $request->validated(), $closer);

            Log::info(sprintf('API Ticket Closure Success: Ticket ID %d closed by User ID %d.', $ticket->id, $closer->id));

            return response()->json(['message' => 'Ticket closed successfully', 'ticket_id' => $ticket->id]);
        } catch (\Exception $e) {
            Log::error(sprintf('API Ticket Closure Error for Ticket ID %d: ', $ticket->id) . $e->getMessage(), [
                'exception_class' => get_class($e),
                'request'         => $request->all(),
            ]);

            return response()->json(['message' => 'Failed to close ticket', 'error' => $e->getMessage()], 500);
        }
    }
}
