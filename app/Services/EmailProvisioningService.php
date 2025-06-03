<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable; // Import Throwable for catching exceptions

/**
 * Service class for handling the provisioning of email accounts via an external API.
 * Interacts with the email provisioning API defined in the configuration.
 * System Design Reference: Sections 3.1 (Services), 5.1 (Email/User ID Workflow), 9.2
 */
final class EmailProvisioningService
{
    private const LOG_AREA = 'EmailProvisioningService:';
    private const API_CREATE_ACCOUNT_ENDPOINT_PATH = '/create-account'; // Example, make configurable if it changes often

    protected ?string $apiKey;
    protected ?string $apiBaseUrl;
    protected string $defaultDomain;

    public function __construct()
    {
        $this->apiKey = Config::get('motac.email_provisioning.api_key');
        $this->apiBaseUrl = Config::get('motac.email_provisioning.api_endpoint');
        $this->defaultDomain = Config::get('motac.email_provisioning.default_domain', 'motac.gov.my'); // [cite: 349]

        if (empty($this->apiBaseUrl) && !app()->environment('testing')) {
            Log::warning(self::LOG_AREA . 'API Base URL is not configured. Provisioning will be simulated for relevant environments.');
        }
        if (empty($this->apiKey) && !app()->environment('testing')) {
            Log::warning(self::LOG_AREA . 'API Key is not configured. Provisioning will be simulated for relevant environments.');
        }
    }

    /**
     * Attempts to provision an email account using an external API.
     *
     * @param  EmailApplication  $application The email application record.
     * @param  string  $targetEmail The final email address to be provisioned.
     * @param  string|null  $targetUserId The User ID to be provisioned (if applicable, might be derived from email).
     * @return array{
     * success: bool,
     * message: string,
     * assigned_email: string|null,
     * assigned_user_id: string|null,
     * error_code: string|null
     * }
     * An array containing the success status, a message, the actually assigned email and user ID (if successful),
     * and an error code if applicable.
     * Possible error_codes: 'CONFIG_ERROR', 'API_HTTP_ERROR', 'EXCEPTION_ERROR', or specific codes from the API.
     */
    public function provisionEmailAccount(EmailApplication $application, string $targetEmail, ?string $targetUserId = null): array
    {
        Log::info(self::LOG_AREA . 'Attempting to provision email account.', [
            'application_id' => $application->id,
            'applicant_user_id' => $application->user_id,
            'target_email' => $targetEmail,
            'target_user_id' => $targetUserId,
        ]);

        /** @var User $applicant */
        $applicant = $application->user; // Assuming user relationship is loaded or accessible

        // Simulation mode if API is not configured or for specific environments
        if (empty($this->apiBaseUrl) || empty($this->apiKey)) {
            Log::warning(self::LOG_AREA . 'API endpoint or key not configured. Simulating provisioning outcome.', [
                'application_id' => $application->id,
                'environment' => app()->environment(),
            ]);
            // Simulate success in local, development, or testing environments for easier workflow testing
            if (app()->environment(['local', 'development', 'testing'])) {
                return [
                    'success' => true,
                    'message' => __('Simulated: Akaun e-mel berjaya disediakan.'), // Translatable
                    'assigned_email' => $targetEmail,
                    'assigned_user_id' => $targetUserId ?? explode('@', $targetEmail)[0], // Derive UserID if not provided
                    'error_code' => null,
                ];
            }
            // Fail in other environments (e.g., staging, production) if config is missing
            return [
                'success' => false,
                'message' => __('Konfigurasi API Penyediaan E-mel tidak lengkap. Sila hubungi pentadbir sistem.'),
                'assigned_email' => null,
                'assigned_user_id' => null,
                'error_code' => 'CONFIG_ERROR',
            ];
        }

        $payload = [
            'applicant_name' => $applicant->name,
            'applicant_nric' => $applicant->identification_number, // Ensure this field exists and is relevant for API
            'applicant_department' => optional($applicant->department)->name, // Safely access department name
            'requested_email' => $targetEmail,
            'requested_user_id' => $targetUserId ?? explode('@', $targetEmail)[0], // Derive UserID if not provided
            'service_type' => $application->service_status, // Send the service_status from the application
            // Add any other fields required by the external provisioning API
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json', // Explicitly set Content-Type for POST
            ])->post($this->apiBaseUrl . self::API_CREATE_ACCOUNT_ENDPOINT_PATH, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info(self::LOG_AREA . 'Provisioning API call successful.', ['application_id' => $application->id, 'response_status' => $response->status(), 'response_data' => $responseData]);
                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? __('Akaun e-mel berjaya disediakan melalui API.'),
                    'assigned_email' => $responseData['data']['email'] ?? $targetEmail, // Fallback to target if API doesn't confirm
                    'assigned_user_id' => $responseData['data']['user_id'] ?? ($targetUserId ?? explode('@', $targetEmail)[0]),
                    'error_code' => null,
                ];
            }

            // Handle API errors (non-2xx responses)
            $errorMessage = __('Panggilan API penyediaan gagal: ') . $response->status();
            $errorCode = 'API_HTTP_ERROR';
            $responseBody = $response->json(); // Attempt to parse JSON error response

            if (is_array($responseBody)) {
                if (isset($responseBody['message'])) {
                    $errorMessage .= ' - ' . $responseBody['message'];
                }
                if (isset($responseBody['errors'])) {
                    $errorMessage .= ' ' . __('Butiran:') . ' ' . json_encode($responseBody['errors']);
                }
                if (isset($responseBody['error_code'])) {
                    $errorCode = $responseBody['error_code'];
                }
            } else {
                 $errorMessage .= ' - ' . $response->body(); // Fallback to raw body if not JSON
            }


            Log::error(self::LOG_AREA . 'Provisioning API call failed.', [
                'application_id' => $application->id,
                'status_code' => $response->status(),
                'response_body' => $response->body(), // Log raw body for debugging
            ]);
            return [
                'success' => false,
                'message' => $errorMessage,
                'assigned_email' => null,
                'assigned_user_id' => null,
                'error_code' => $errorCode,
            ];

        } catch (Throwable $e) { // Catch any connection exceptions or other Throwables
            Log::critical(self::LOG_AREA . 'Exception during provisioning API call.', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace_snippet' => substr($e->getTraceAsString(), 0, 1000) // Increased trace snippet
            ]);
            return [
                'success' => false,
                'message' => __('Ralat kritikal semasa menghubungi servis penyediaan e-mel: ') . $e->getMessage(),
                'assigned_email' => null,
                'assigned_user_id' => null,
                'error_code' => 'EXCEPTION_ERROR',
            ];
        }
    }
}
