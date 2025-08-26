<?php

declare(strict_types=1);

namespace App\Validator;

/**
 * Validates GitHub webhook signatures.
 * No references to legacy email logic.
 */
final class CustomSignatureValidator
{
    /**
     * Validate GitHub webhook signature (X-Hub-Signature-256).
     *
     * @param  string  $gitHubSignatureHeader
     * @param  string  $payload
     * @param  string  $secret
     * @return bool
     */
    public static function isValid(string $gitHubSignatureHeader, string $payload, string $secret): bool
    {
        if ($gitHubSignatureHeader === '' || $gitHubSignatureHeader === '0') {
            return false;
        }
        if ($secret === '') {
            return false;
        }
        $signatureParts = explode('=', $gitHubSignatureHeader, 2);
        if (count($signatureParts) !== 2) {
            return false;
        }
        $algorithm = $signatureParts[0];
        $knownSignature = $signatureParts[1];
        if ($algorithm !== 'sha256' || $knownSignature === '' || $knownSignature === '0') {
            return false;
        }
        $calculatedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($knownSignature, $calculatedSignature);
    }
}
