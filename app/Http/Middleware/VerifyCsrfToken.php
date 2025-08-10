<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware to handle CSRF verification for requests.
 *
 * Excludes specific URIs (like webhooks) from CSRF protection.
 *
 * To avoid CSRF token mismatch errors, make sure to only add endpoints here that
 * are genuinely external (e.g., webhooks from third-party services). For normal
 * form submissions, always include @csrf in your Blade templates or send the
 * X-CSRF-TOKEN header in your AJAX requests.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * Add webhook endpoints and other exceptions here. Example:
     * - 'webhooks/deploy' allows Github/CI/CD deployment webhook calls without CSRF.
     * - You can use wildcards for URI patterns, e.g. 'api/external/*'
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/deploy', // Allow Github/CI/CD deployment webhook
        // Add other external API callbacks or webhooks as needed
        // 'api/external/*', // Example: exclude all routes under api/external/
    ];
}
