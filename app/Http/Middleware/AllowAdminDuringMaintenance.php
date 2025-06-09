<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response; // For 503 status
use Symfony\Component\HttpKernel\Exception\HttpException; // For type hinting

class AllowAdminDuringMaintenance
{
    /**
     * Handle an incoming request.
     * Allows users with specific roles to access the application during maintenance mode.
     * System Design Reference: 3.1 Custom Middleware
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isDownForMaintenance()) {
            if (! Auth::check()) {
                // If not logged in during maintenance, redirect to login
                // Ensure your login route is accessible or explicitly allowed.
                return redirect()->route('login'); // Assuming 'login' is your named login route
            }

            /** @var User $user */
            $user = Auth::user();

            // Allow users with 'Admin' role or other designated roles/permissions
            // System Design 8.1 (Standardized role names)
            // The check for 'HR Payroll' by name is fragile; prefer roles or permissions.
            // For MOTAC, 'Admin' should suffice unless other specific roles need bypass access.
            if ($user && $user->hasRole('Admin')) { // Adjust roles as per MOTAC requirements
                return $next($request);
            }

            // If user is logged in but not authorized, throw 503
            throw new HttpException(503, __('Perkhidmatan Tidak Tersedia. Sistem sedang dalam penyelenggaraan.'));
        }

        return $next($request);
    }
}
