<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class AuthorizeLogViewer
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and has the 'Admin' role
        // Assumes you are using Spatie's laravel-permission package or similar
        // If using a different role system, adjust the Auth::user()->hasRole('Admin') check accordingly.
        if (Auth::check() && Auth::user()->hasRole('Admin')) {
            return $next($request);
        }

        // If not authorized, you can redirect, show a 403 error, or handle as needed.
        // For this example, we'll abort with a 403 Forbidden error.
        abort(403, 'Unauthorized action. You do not have permission to view system logs.');
    }
}
