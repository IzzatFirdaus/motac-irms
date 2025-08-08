<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Generic Webhook Controller for handling incoming webhook calls.
 *
 * NOTE: This controller is now simplified to store the webhook call into the `webhook_calls` table,
 * as per the migration in 2024_05_27_100007_create_webhook_calls_table.php.
 * Signature validation, event-specific logic, and downstream processing should be handled via jobs or listeners.
 */
class WebhookController extends Controller
{
    /**
     * Handle incoming webhook call and persist it.
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Basic info about the incoming webhook
        $name = $request->input('name') ?? 'generic'; // Optionally set by the sender/client
        $url = $request->fullUrl();
        $headers = $request->headers->all();
        $payload = $request->all();

        // Attempt to persist the call to the database (webhook_calls table)
        try {
            $callId = \DB::table('webhook_calls')->insertGetId([
                'name' => $name,
                'url' => $url,
                'headers' => json_encode($headers), // Store all headers as JSON
                'payload' => json_encode($payload), // Store the payload as JSON
                'exception' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info('Webhook call stored in webhook_calls table.', [
                'webhook_call_id' => $callId,
                'name' => $name,
                'url' => $url,
            ]);
            return response()->json(['message' => 'Webhook call stored.', 'id' => $callId], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store webhook call.', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
                'payload' => $payload,
            ]);
            return response()->json(['message' => 'Failed to store webhook call.'], 500);
        }
    }
}
