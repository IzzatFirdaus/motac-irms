<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware to handle CSRF verification for requests.
 * Excludes specific URIs (like webhooks) from CSRF protection.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * Add webhook endpoints and other exceptions here.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/deploy', // Allow Github/CI/CD deployment webhook
        // Add other external API callbacks or webhooks as needed
    ];
}
