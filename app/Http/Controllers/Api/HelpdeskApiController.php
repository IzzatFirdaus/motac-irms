<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// Assuming these request classes are causing "Undefined type" errors because the files do not exist or are not found.
// If they exist and are correctly defined, ensure your autoloader is configured properly.
// use App\Http\Requests\Api\CloseHelpdeskTicketRequest;
// use App\Http\Requests\Api\UpdateHelpdeskTicketRequest;
use App\Models\HelpdeskTicket;
use App\Services\HelpdeskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Use generic Request for validation within the controller
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class HelpdeskApiController extends Controller
{
    protected HelpdeskService $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
        // Apply API authentication middleware if necessary (e.g., 'auth:sanctum')
        // $this->middleware('auth:sanctum');
    }

    /**
     * Store a newly created ticket via API.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data directly in the controller
            $validatedData = $request->validate([
                'category_id' => 'required|exists:helpdesk_categories,id',
                'priority_id' => 'required|exists:helpdesk_priorities,id',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'attachments.*' => 'nullable|file|max:5120', // Max 5MB per file
            ]);

            // Ensure there's an authenticated user to act as the applicant
            $applicant = Auth::user();
            if (!$applicant) {
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
     * Update the specified ticket via API.
     */
    public function update(Request $request, HelpdeskTicket $ticket): JsonResponse // Changed type hint to Request
    {
        try {
            // Add authorization check if needed for API access
            // $this->authorize('update', $ticket);

            // Validate the incoming request data directly in the controller
            $validatedData = $request->validate([
                'category_id' => 'sometimes|required|exists:helpdesk_categories,id',
                'priority_id' => 'sometimes|required|exists:helpdesk_priorities,id',
                'subject' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'status' => 'sometimes|required|string|in:open,in_progress,on_hold,resolved,closed,reopened',
                'assigned_to_user_id' => 'nullable|exists:users,id',
                'resolution_details' => 'nullable|string',
                'attachments.*' => 'nullable|file|max:5120', // Max 5MB per file for new attachments
            ]);

            // Ensure there's an authenticated user to act as the updater
            $updater = Auth::user();
            if (!$updater) {
                return response()->json(['message' => 'Unauthenticated user.', 'error' => 'Authentication required.'], 401);
            }

            // Pass the authenticated user as the updater and attachments
            $this->helpdeskService->updateTicket($ticket, $validatedData, $updater, $request->file('attachments', []));

            return response()->json(['message' => 'Ticket updated successfully', 'ticket_id' => $ticket->id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning(sprintf('API Ticket Update Validation Error for ID %d: ', $ticket->id) . $e->getMessage(), ['errors' => $e->errors(), 'request' => $request->all()]);

            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error(sprintf('API Ticket Update Error for ID %d: ', $ticket->id) . $e->getMessage(), ['request' => $request->all()]);

            return response()->json(['message' => 'Failed to update ticket', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Close the specified ticket via API.
     */
    public function close(Request $request, HelpdeskTicket $ticket): JsonResponse // Changed type hint to Request
    {
        try {
            // Add authorization check if needed for API access
            // $this->authorize('close', $ticket);

            // Validate the incoming request data directly in the controller
            $validatedData = $request->validate([
                'resolution_details' => 'required|string',
                // 'closed_by_id' => 'required|exists:users,id', // This should be handled by the service using Auth::user()
                'status' => 'required|string|in:closed', // Ensure status is explicitly 'closed'
            ]);

            // Ensure there's an authenticated user to act as the closer
            $closer = Auth::user();
            if (!$closer) {
                return response()->json(['message' => 'Unauthenticated user.', 'error' => 'Authentication required.'], 401);
            }

            // Pass the authenticated user as the closer
            $this->helpdeskService->closeTicket($ticket, $validatedData, $closer); // Pass validated data and the closer

            return response()->json(['message' => 'Ticket closed successfully', 'ticket_id' => $ticket->id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning(sprintf('API Ticket Close Validation Error for ID %d: ', $ticket->id) . $e->getMessage(), ['errors' => $e->errors(), 'request' => $request->all()]);

            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error(sprintf('API Ticket Close Error for ID %d: ', $ticket->id) . $e->getMessage(), ['request' => $request->all()]);

            return response()->json(['message' => 'Failed to close ticket', 'error' => $e->getMessage()], 500);
        }
    }
}
