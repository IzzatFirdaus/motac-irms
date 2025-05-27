<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProvisionEmailRequest; // Assuming this Form Request is created
use App\Models\EmailApplication;
use App\Services\EmailProvisioningService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Still needed for IP if not passed through FormRequest property
use Illuminate\Support\Facades\Auth; // For getting acting user if needed for logs
use Illuminate\Support\Facades\Log;

class EmailProvisioningController extends Controller
{
    protected EmailProvisioningService $emailProvisioningService;

    public function __construct(EmailProvisioningService $emailProvisioningService)
    {
        $this->emailProvisioningService = $emailProvisioningService;
        // Middleware 'auth:sanctum' is applied at the route level in api.php
    }

    /**
     * Handle the API request to provision an email account.
     * Aligns with the route definition in api.php.
     *
     * @param  \App\Http\Requests\Api\ProvisionEmailRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function provisionEmailAccount(ProvisionEmailRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $actingUser = $request->user(); // User from Sanctum token

        Log::info('API: Email provisioning request received.', [
            'application_id' => $validatedData['application_id'],
            'final_assigned_email' => $validatedData['final_assigned_email'],
            'user_id_assigned' => $validatedData['user_id_assigned'] ?? null,
            'triggered_by_user_id' => $actingUser?->id,
            'ip_address' => $request->ip(),
        ]);

        try {
            /** @var EmailApplication $application */
            $application = EmailApplication::with('user')->findOrFail($validatedData['application_id']);

            // Policy check could be here: $this->authorize('provisionViaApi', $application);
            // Or within the FormRequest's authorize() method.

            if (!$application->isApproved()) { // Method from EmailApplication model
                Log::warning("API: Provisioning attempted for non-approved EmailApplication.", [
                    'application_id' => $application->id,
                    'current_status' => $application->status,
                    'acting_user_id' => $actingUser?->id,
                ]);
                return response()->json([
                    'message' => 'Email application is not approved for provisioning.',
                    'application_status' => $application->status,
                    'code' => 'INVALID_STATUS'
                ], 409); // Conflict
            }

            $result = $this->emailProvisioningService->provisionEmailAccount(
                $application,
                $validatedData['final_assigned_email'],
                $validatedData['user_id_assigned'] ?? null
            ); //

            // Note: The EmailProvisioningService returns a result array.
            // The actual update of the EmailApplication model's status (e.g., to 'completed' or 'provision_failed')
            // and saving final_assigned_email/user_id is handled by the calling service that uses EmailProvisioningService,
            // typically EmailApplicationService::processProvisioning.
            // If this API endpoint is a direct interface that should also update the application model,
            // that logic would need to be added here or, preferably, by calling a method in EmailApplicationService.

            if ($result['success']) {
                Log::info('API: Email provisioning service reported success.', [
                    'application_id' => $application->id,
                    'result' => $result,
                ]);
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
                Log::error('API: Email provisioning service reported failure.', [
                    'application_id' => $application->id,
                    'result' => $result,
                    'acting_user_id' => $actingUser?->id,
                ]);
                return response()->json([
                    'message' => $result['message'] ?? 'Email provisioning failed.',
                    'error_code' => $result['error_code'] ?? 'PROVISIONING_FAILED',
                    'data' => [
                        'application_id' => $application->id,
                    ]
                ], 500); // Or a more specific error if available from $result['error_code']
            }
        } catch (ModelNotFoundException $e) {
            Log::warning('API: Email application not found during provisioning attempt.', [
                'application_id' => $validatedData['application_id'] ?? 'N/A',
                'exception_message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Email application not found.', 'code' => 'NOT_FOUND'], 404);
        } catch (\Throwable $e) { // Catching generic Throwable for unexpected errors
            Log::critical('API: Unexpected error during email provisioning.', [
                'application_id' => $validatedData['application_id'] ?? 'N/A',
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'acting_user_id' => $actingUser?->id,
            ]);
            return response()->json([
                'message' => 'An internal server error occurred during provisioning.',
                'code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }
}
