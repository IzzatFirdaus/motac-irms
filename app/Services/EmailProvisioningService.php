<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config; // Added from suggestion
use RuntimeException;
use Throwable;

final class EmailProvisioningService
{
    private const LOG_AREA = 'EmailProvisioningService:';

    protected ?string $apiKey;
    protected ?string $apiBaseUrl;
    protected string $defaultDomain;

    public function __construct()
    {
        $this->apiKey = Config::get('motac.email_provisioning.api_key'); // Using Facade or global helper config()
        $this->apiBaseUrl = Config::get('motac.email_provisioning.api_endpoint');
        $this->defaultDomain = Config::get('motac.email_provisioning.default_domain', 'motac.gov.my');

        if (empty($this->apiBaseUrl) && !app()->environment('testing')) {
            Log::warning(self::LOG_AREA.'API Base URL is not configured. Provisioning will be simulated.');
        }
    }

    public function provisionEmailAccount(EmailApplication $application, string $targetEmail, ?string $targetUserId = null): array
    {
        Log::info(self::LOG_AREA.'Attempting to provision email account.', [
            'application_id' => $application->id,
            'applicant_user_id' => $application->user_id,
            'target_email' => $targetEmail,
            'target_user_id' => $targetUserId,
        ]);

        /** @var User $applicant */
        $applicant = $application->user;

        if (empty($this->apiBaseUrl) || empty($this->apiKey)) {
            Log::warning(self::LOG_AREA.'API endpoint or key not configured. Simulating provisioning SUCCESS.');
            if (app()->environment(['local', 'development', 'testing'])) {
                return [
                    'success' => true,
                    'message' => 'Simulated: Akaun e-mel berjaya disediakan.',
                    'assigned_email' => $targetEmail,
                    'assigned_user_id' => $targetUserId ?? explode('@', $targetEmail)[0],
                    'error_code' => null,
                ];
            }
            return [
                'success' => false,
                'message' => 'Konfigurasi API Penyediaan E-mel tidak lengkap.',
                'assigned_email' => null, 'assigned_user_id' => null, 'error_code' => 'CONFIG_ERROR',
            ];
        }

        $payload = [
            'applicant_name' => $applicant->name,
            'applicant_nric' => $applicant->identification_number,
            'applicant_department' => $applicant->department?->name,
            'requested_email' => $targetEmail,
            'requested_user_id' => $targetUserId ?? explode('@', $targetEmail)[0],
            'service_type' => $applicant->service_status,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->apiBaseUrl . '/create-account', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info(self::LOG_AREA.'Provisioning API call successful.', ['application_id' => $application->id, 'response' => $responseData]);
                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Akaun e-mel berjaya disediakan.',
                    'assigned_email' => $responseData['data']['email'] ?? $targetEmail,
                    'assigned_user_id' => $responseData['data']['user_id'] ?? ($targetUserId ?? explode('@', $targetEmail)[0]),
                    'error_code' => null,
                ];
            }

            $errorMessage = 'Panggilan API penyediaan gagal: ' . $response->status();
            $errorCode = 'API_HTTP_ERROR';
            if ($response->json('message')) {
                $errorMessage .= ' - ' . $response->json('message');
            }
            if ($response->json('errors')) {
                 $errorMessage .= ' Details: ' . json_encode($response->json('errors'));
            }
             if ($response->json('error_code')) {
                $errorCode = $response->json('error_code');
            }

            Log::error(self::LOG_AREA.'Provisioning API call failed.', [
                'application_id' => $application->id, 'status_code' => $response->status(), 'response_body' => $response->body()
            ]);
            return [
                'success' => false, 'message' => $errorMessage,
                'assigned_email' => null, 'assigned_user_id' => null, 'error_code' => $errorCode,
            ];

        } catch (Throwable $e) {
            Log::critical(self::LOG_AREA.'Exception during provisioning API call.', [
                'application_id' => $application->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false, 'message' => 'Ralat kritikal semasa menghubungi servis penyediaan e-mel: '.$e->getMessage(),
                'assigned_email' => null, 'assigned_user_id' => null, 'error_code' => 'EXCEPTION_ERROR',
            ];
        }
    }
}
