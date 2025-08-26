<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $permission
     */
    public function handle($request, Closure $next, $permission)
    {
        if (! Auth::user() || ! Auth::user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
