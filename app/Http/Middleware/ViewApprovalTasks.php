<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if a user has permission to view approval tasks.
 * This middleware ensures only users with the appropriate permission
 * (e.g. 'view approval tasks') can access approval dashboard routes.
 */
class ViewApprovalTasks
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Uses Spatie Permission package's can() to check permission.
        // Adjust permission name as needed according to your policy/seeder.
        if (! $request->user() || ! $request->user()->can('view approval tasks')) {
            abort(403, 'You do not have permission to view approval tasks.');
        }

        return $next($request);
    }
}
