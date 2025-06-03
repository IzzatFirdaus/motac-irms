<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

// Conventionally, controller names end with "Controller"
class MiscErrorController extends Controller
{
    /**
     * Display a miscellaneous or generic error page.
     * SDD Ref:
     * @param  \Illuminate\Http\Request  $request
     * @param  int $statusCode The HTTP status code for the error
     * @param  string|null $message A custom message for the error
     * @return \Illuminate\View\View
     */
    public function index(Request $request, int $statusCode = 500, ?string $message = null): View
    {
        $pageConfigs = ['myLayout' => 'blank']; // Example theme config

        Log::warning('MiscErrorController page accessed.', [
            'status_code' => $statusCode, 'custom_message' => $message,
            'url' => $request->fullUrl(), 'method' => $request->method(),
            'user_id' => Auth::id(), 'ip_address' => $request->ip(),
        ]);

        return view('content.pages-misc-error', [ // View path from web.php fallback
            'pageConfigs' => $pageConfigs,
            'statusCode' => $statusCode,
            'message' => $message ?? __('An unexpected error occurred.'),
        ]);
    }
}
