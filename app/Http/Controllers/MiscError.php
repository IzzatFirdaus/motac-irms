<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For consistency in getting user ID
use Illuminate\View\View;

class MiscError extends Controller
{
    /**
     * Display a miscellaneous or generic error page.
     *
     * This method is typically called by the application's exception handler
     * to render a custom error view when specific error views (e.g., 404, 503)
     * are not found or for other general errors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $statusCode The HTTP status code for the error (optional, can be passed by handler)
     * @param  string|null $message A custom message for the error (optional)
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $statusCode = 500, ?string $message = null): View
    {
        // Page configurations, useful if your error page uses a master layout
        // that expects these. Otherwise, it can be simplified.
        $pageConfigs = ['myLayout' => 'blank']; //

        Log::warning('MiscError page accessed.', [
            'status_code' => $statusCode,
            'custom_message' => $message,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(), // Use Auth::id() for consistency
            'ip_address' => $request->ip(),
        ]);

        // Ensure the view 'content.pages-misc-error' exists.
        // You might want to pass $statusCode and $message to the view
        // to display more dynamic error information.
        return view('content.pages-misc-error', [
            'pageConfigs' => $pageConfigs,
            'statusCode' => $statusCode,
            'message' => $message ?? __('An unexpected error occurred.'), // Provide a default message
        ]);
    }
}
