<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Handles rendering of custom MOTAC error pages matching the resources/views/errors directory.
 * This controller provides a clear mapping for standard HTTP error codes to their respective MOTAC-branded Blade views.
 */
class MiscErrorController extends Controller
{
    /**
     * Display a MOTAC-styled error page for a given HTTP status code.
     *
     * @param int         $statusCode The HTTP status code for the error (e.g. 401, 403, 404, 422, 429, 500, 503)
     * @param string|null $message    Optional custom message to display
     */
    public function show(Request $request, int $statusCode = 500, ?string $message = null): View
    {
        // Prepare config data for themed error illustrations (light/dark)
        $configData = [
            'myStyle' => config('motac.theme_style', 'light'), // Example usage; adjust as needed
        ];

        // Log error page access for auditing/troubleshooting
        Log::warning('Error page rendered via MiscErrorController.', [
            'status_code'    => $statusCode,
            'custom_message' => $message,
            'url'            => $request->fullUrl(),
            'method'         => $request->method(),
            'user_id'        => Auth::id(),
            'ip_address'     => $request->ip(),
        ]);

        // Map the status code to a matching Blade error view if it exists
        $errorViews = [
            401 => 'errors.401',
            403 => 'errors.403',
            404 => 'errors.404',
            422 => 'errors.422',
            429 => 'errors.429',
            500 => 'errors.500',
            503 => 'errors.503',
        ];

        $view = $errorViews[$statusCode] ?? 'errors.500';

        // Optionally allow a custom message to override the view's default message
        return view($view, [
            'configData' => $configData,
            'message'    => $message,
        ]);
    }
}
