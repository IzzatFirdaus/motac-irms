<?php

declare(strict_types=1);

namespace App\Validator;

// Removed Spatie specific imports:
// use Illuminate\Http\Request;
// use Spatie\WebhookClient\Exceptions\InvalidConfig;
// use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
// use Spatie\WebhookClient\WebhookConfig;

final class CustomSignatureValidator // No longer implements Spatie's SignatureValidator
{
    /**
     * Determine if the GitHub webhook signature is valid.
     *
     * @param  string  $gitHubSignatureHeader  The value of the 'X-Hub-Signature-256' header.
     * @param  string  $payload  The raw request payload (body).
     * @param  string  $secret  The configured webhook secret.
     * @return bool True if the signature is valid, false otherwise.
     */
    public static function isValid(string $gitHubSignatureHeader, string $payload, string $secret): bool
    {
        if (empty($gitHubSignatureHeader)) {
            return false; // Signature header is missing
        }

        // The secret should not be empty for validation to be meaningful
        if ($secret === '') {
            // Depending on policy, you might throw an exception or log an error.
            // For now, returning false as an empty secret cannot validate anything.
            // throw new \RuntimeException('Webhook signing secret is not set.'); // Or log and return false
            return false;
        }

        // GitHub signatures are prefixed with the algorithm, e.g., "sha256=..."
        // We need to extract the hash part.
        $signatureParts = explode('=', $gitHubSignatureHeader, 2);
        if (count($signatureParts) !== 2) {
            return false; // Invalid signature format
        }

        $algorithm = $signatureParts[0];
        $knownSignature = $signatureParts[1];

        if ($algorithm !== 'sha256') {
            // GitHub currently uses sha256. If they change, this needs an update.
            // Or, support multiple algorithms if necessary.
            return false; // Unsupported or unknown algorithm
        }

        $calculatedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($knownSignature, $calculatedSignature);
    }
}
