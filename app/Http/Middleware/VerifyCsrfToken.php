<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // This should match the URI of your deployment webhook route
        // As defined in your web.php: Route::post('/webhooks/deploy', ...)
        'webhooks/deploy',

        // Add any other URIs that should be excluded from CSRF verification
        // (e.g., other external API callbacks or webhooks).
    ];
}
