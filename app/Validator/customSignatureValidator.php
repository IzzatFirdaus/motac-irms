<?php

declare(strict_types=1);

namespace App\Validator;

// No external dependencies needed for this standalone validator.

/**
 * Validates GitHub webhook signatures.
 * System Design Reference: [cite: 51, 196, 335, 480, 525, 619, 765, 809] (Webhook Security)
 */
final class CustomSignatureValidator
{
    /**
     * Determine if the GitHub webhook signature (X-Hub-Signature-256) is valid.
     *
     * @param  string  $gitHubSignatureHeader  The value of the 'X-Hub-Signature-256' header.
     * @param  string  $payload  The raw request payload (JSON body).
     * @param  string  $secret  The configured webhook signing secret.
     * @return bool True if the signature is valid, false otherwise.
     */
    public static function isValid(string $gitHubSignatureHeader, string $payload, string $secret): bool
    {
        if ($gitHubSignatureHeader === '' || $gitHubSignatureHeader === '0') {
            // \Illuminate\Support\Facades\Log::warning('GitHub webhook signature header is missing.');
            return false;
        }

        // The secret should not be empty for validation to be meaningful.
        if ($secret === '') {
            // Depending on policy, you might throw an exception or log a critical error.
            // \Illuminate\Support\Facades\Log::error('Webhook signing secret is not configured.');
            // throw new \RuntimeException('Webhook signing secret is not set.');
            return false; // Cannot validate anything with an empty secret.
        }

        // GitHub signatures are prefixed with the algorithm, e.g., "sha256=THE_ACTUAL_HASH"
        // We need to extract the hash part.
        $signatureParts = explode('=', $gitHubSignatureHeader, 2);
        if (count($signatureParts) !== 2) {
            // \Illuminate\Support\Facades\Log::warning('Invalid GitHub webhook signature format.', ['header' => $gitHubSignatureHeader]);
            return false; // Invalid signature format (missing '=')
        }

        $algorithm = $signatureParts[0];
        $knownSignature = $signatureParts[1];

        if ($algorithm !== 'sha256') {
            // GitHub currently uses sha256. If they change, or if you need to support other algorithms,
            // this logic would need to be updated.
            // \Illuminate\Support\Facades\Log::warning('Unsupported GitHub webhook signature algorithm.', ['algorithm' => $algorithm]);
            return false; // Unsupported or unknown algorithm
        }

        if ($knownSignature === '' || $knownSignature === '0') {
            // \Illuminate\Support\Facades\Log::warning('GitHub webhook signature hash is empty after parsing.', ['header' => $gitHubSignatureHeader]);
            return false; // Hash part is empty
        }

        // Calculate the expected signature based on the payload and secret
        $calculatedSignature = hash_hmac('sha256', $payload, $secret);

        // Use hash_equals for a timing attack safe string comparison
        return hash_equals($knownSignature, $calculatedSignature);
    }
}
