<?php
declare(strict_types=1);

namespace App\Validator;

use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Illuminate\Http\Request;

class CustomSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, string $signatureHeader): bool
    {
        return !empty($signatureHeader);
    }
}
