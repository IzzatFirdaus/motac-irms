<?php

declare(strict_types=1);

namespace App\Validator;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

class CustomSignatureValidator implements SignatureValidator
{
    /**
     * Matches SignatureValidator::isValid(Request, WebhookConfig): bool
     * We avoid importing WebhookConfig here to keep static analysis simpler and
     * only rely on the request headers for signature presence checks.
     */
    public function isValid(Request $request, $config): bool
    {
        // Look for common signature headers; presence is considered valid for this lightweight validator.
        $signatureHeader = $request->header('X-Signature') ?? $request->header('X-Signature-256') ?? $request->header('Signature') ?? '';

        return ! empty($signatureHeader);
    }
}
