<?php

declare(strict_types=1);

namespace App\Validator;

/**
<<<<<<< HEAD
 * Standalone validator for GitHub webhook signatures.
 * System Design Reference: [cite: 51, 196, 335, 480, 525, 619, 765, 809] (Webhook Security)
=======
 * Validates GitHub webhook signatures.
 * No references to legacy email logic.
>>>>>>> release/v4.0
 */
final class CustomSignatureValidator
{
    /**
<<<<<<< HEAD
     * Validates the GitHub webhook signature (X-Hub-Signature-256).
     *
     * @param string $gitHubSignatureHeader Value of the 'X-Hub-Signature-256' header.
     * @param string $payload Raw request payload (JSON body).
     * @param string $secret Webhook signing secret.
     * @return bool True if signature is valid, false otherwise.
=======
     * Validate GitHub webhook signature (X-Hub-Signature-256).
     *
     * @param  string  $gitHubSignatureHeader
     * @param  string  $payload
     * @param  string  $secret
     * @return bool
>>>>>>> release/v4.0
     */
    public static function isValid(string $gitHubSignatureHeader, string $payload, string $secret): bool
    {
        if ($gitHubSignatureHeader === '' || $gitHubSignatureHeader === '0') {
<<<<<<< HEAD
            // Signature header is missing.
            return false;
        }

        if ($secret === '') {
            // Secret is missing; cannot validate signature.
            return false;
        }

        // GitHub signatures are in the format "sha256=THE_ACTUAL_HASH"
        $signatureParts = explode('=', $gitHubSignatureHeader, 2);
        if (count($signatureParts) !== 2) {
            // Invalid format (missing '=')
=======
            return false;
        }
        if ($secret === '') {
            return false;
        }
        $signatureParts = explode('=', $gitHubSignatureHeader, 2);
        if (count($signatureParts) !== 2) {
>>>>>>> release/v4.0
            return false;
        }
        $algorithm = $signatureParts[0];
        $knownSignature = $signatureParts[1];
<<<<<<< HEAD

        if ($algorithm !== 'sha256') {
            // Only sha256 is supported.
            return false;
        }

        if ($knownSignature === '' || $knownSignature === '0') {
            // Hash part is empty.
            return false;
        }

        // Calculate expected signature for verification.
        $calculatedSignature = hash_hmac('sha256', $payload, $secret);

        // Use hash_equals for timing-attack-safe comparison.
=======
        if ($algorithm !== 'sha256' || $knownSignature === '' || $knownSignature === '0') {
            return false;
        }
        $calculatedSignature = hash_hmac('sha256', $payload, $secret);
>>>>>>> release/v4.0
        return hash_equals($knownSignature, $calculatedSignature);
    }
}
