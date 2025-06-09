<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProvisionEmailRequest; // Using the new FormRequest
use App\Models\EmailApplication;
use App\Services\EmailProvisioningService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmailProvisioningController extends Controller
{
    protected EmailProvisioningService $emailProvisioningService;

    public function __construct(EmailProvisioningService $emailProvisioningService)
    {
        $this->emailProvisioningService = $emailProvisioningService;
        // Middleware 'auth:sanctum' applied at route level in api.php
    }

    /**
     * Handle the API request to provision an email account.
     * SDD Ref: 3.1
     */
    public function provisionEmailAccount(ProvisionEmailRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $actingUser = $request->user(); // User from Sanctum token

        Log::info('API: Email provisioning request received.', [
            'application_id' => $validatedData['application_id'],
            'final_assigned_email' => $validatedData['final_assigned_email'] ?? null,
            'user_id_assigned' => $validatedData['user_id_assigned'] ?? null,
            'triggered_by_user_id' => $actingUser?->id,
            'ip_address' => $request->ip(),
        ]);

        try {
            /** @var EmailApplication $application */
            $application = EmailApplication::with('user')->findOrFail($validatedData['application_id']);

            // Authorization can be handled in ProvisionEmailRequest or here via policy
            // Example: $this->authorize('provisionViaApi', $application);

            // Ensure EmailApplication model has an isApproved() method.
            if (! $application->isApproved()) {
                Log::warning('API: Provisioning attempted for non-approved EmailApplication.', [
                    'application_id' => $application->id,
                    'current_status' => $application->status,
                    'acting_user_id' => $actingUser?->id,
                ]);

                return response()->json([
                    'message' => 'Email application is not in an approved state for provisioning.',
                    'application_status' => $application->status,
                    'code' => 'INVALID_STATUS',
                ], 409); // HTTP 409 Conflict
            }

            // Call the service that performs the actual provisioning
            // The service method might update the EmailApplication status itself or return data for this controller to do so.
            // Current SDD implies EmailApplicationService::processProvisioning does the update after calling this.
            $result = $this->emailProvisioningService->provisionEmailAccount(
                $application,
                $validatedData['final_assigned_email'] ?? '', // Pass empty string if null to satisfy type hint if service expects string
                $validatedData['user_id_assigned'] ?? null
            );

            // The SDD notes that EmailApplicationService::processProvisioning (which might call this service)
            // handles updating the EmailApplication model status.
            // This API endpoint's main role is to trigger the provisioning and report its direct outcome.
            if ($result['success']) {
                Log::info('API: Email provisioning service reported success.', ['application_id' => $application->id, 'result' => $result]);

                return response()->json([
                    'message' => $result['message'] ?? 'Email provisioning successful.',
                    'data' => [
                        'application_id' => $application->id,
                        'assigned_email' => $result['assigned_email'] ?? null,
                        'assigned_user_id' => $result['assigned_user_id'] ?? null,
                    ],
                    'code' => 'SUCCESS',
                ], 200);
            } else {
                Log::error('API: Email provisioning service reported failure.', ['application_id' => $application->id, 'result' => $result, 'acting_user_id' => $actingUser?->id]);

                return response()->json([
                    'message' => $result['message'] ?? 'Email provisioning failed.',
                    'error_code' => $result['error_code'] ?? 'PROVISIONING_FAILED',
                    'data' => ['application_id' => $application->id],
                ], isset($result['status_code']) && is_int($result['status_code']) ? $result['status_code'] : 500);
            }
        } catch (ModelNotFoundException $e) {
            Log::warning('API: Email application not found.', ['application_id' => $validatedData['application_id'] ?? 'N/A']);

            return response()->json(['message' => 'Email application not found.', 'code' => 'NOT_FOUND'], 404);
        } catch (\Throwable $e) {
            Log::critical('API: Unexpected error during email provisioning.', [
                'application_id' => $validatedData['application_id'] ?? 'N/A', 'exception' => $e->getMessage(),
                'trace_snippet' => substr($e->getTraceAsString(), 0, 500), 'acting_user_id' => $actingUser?->id,
            ]);

            return response()->json(['message' => 'An internal server error occurred.', 'code' => 'INTERNAL_ERROR'], 500);
        }
    }
}
