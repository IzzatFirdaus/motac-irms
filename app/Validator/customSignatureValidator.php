<?php

declare(strict_types=1);

namespace App\Validator;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

/**
 * Custom signature validator for incoming webhooks.
 * Validates HMAC-SHA256 using the configured signing secret against the configured header.
 */
class CustomSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $headerName        = $config->signatureHeaderName;
        $providedSignature = (string) $request->header($headerName, '');

        if ($providedSignature === '') {
            return false;
        }

        $computed = hash_hmac('sha256', (string) $request->getContent(), (string) $config->signingSecret);

        // Timing-safe comparison to prevent timing attacks
        return hash_equals($computed, $providedSignature);
    }
}
