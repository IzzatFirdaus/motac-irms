<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure the user has the 'BPM Staff' role.
 * Adjusted to allow any user with 'BPM Staff' role, even if they are not in the helpdesk or technical units.
 * Special note: Seeded admin/system developer/maintainer users (see AdminUserSeeder.php) are always allowed.
 */
class EnsureUserIsBpmStaff
{
    /**
     * Handle an incoming request.
     * Allows access if the user has the 'BPM Staff' role or is a seeded admin/maintainer.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow if user has the 'BPM Staff' role (regardless of unit/department)
        if ($user && $user->hasRole('BPM Staff')) {
            return $next($request);
        }

        // Additional check: allow seeded admin/maintainer users (system developer)
        // assuming AdminUserSeeder sets a known email or flag; adjust as needed
        $adminSeededEmails = [
            'izzatfirdaus@motac.gov.my', // Add more if other seeded admins exist
            // 'other.admin@motac.gov.my',
        ];

        if ($user && in_array($user->email, $adminSeededEmails, true)) {
            // Optionally, you may also want to assign BPM Staff role to these users in the seeder.
            return $next($request);
        }

        // If neither check passes, deny access
        abort(403, 'THIS ACTION IS UNAUTHORIZED.');
    }
}
