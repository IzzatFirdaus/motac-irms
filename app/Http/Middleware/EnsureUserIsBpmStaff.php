<?php
// app/Http/Middleware/EnsureUserIsBpmStaff.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsBpmStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This uses the same logic that correctly identifies the role for the dashboard.
        // If the user does not have the 'BPM Staff' role, abort with a 403 error.
        if (! $request->user() || ! $request->user()->hasRole('BPM Staff')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        return $next($request);
    }
}
