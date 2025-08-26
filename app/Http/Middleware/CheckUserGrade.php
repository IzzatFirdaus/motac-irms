<?php

namespace App\Http\Middleware;

use App\Models\User; // Assuming your User model is App\Models\User
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserGrade
{
    /**
     * Handle an incoming request.
     * Checks if the user has a grade assigned and optionally if that grade
     * meets certain criteria (e.g., is an approver grade).
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param string|null                                                                      $requiredProperty Optional: A boolean property on the Grade model to check (e.g., 'is_approver_grade').
     */
    public function handle(Request $request, Closure $next, ?string $requiredProperty = null): Response
    {
        if (! Auth::check()) {
            Log::warning('CheckUserGrade: Unauthenticated user attempt.');

            return redirect()->route('login')->with('error', 'Sila log masuk.');
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->grade) {
            Log::warning('CheckUserGrade: User does not have a grade assigned.', [
                'user_id'    => $user->id,
                'route_name' => $request->route()?->getName(),
            ]);
            abort(403, 'Akses Ditolak. Tiada Gred ditetapkan untuk akaun anda.');
        }

        // If a specific grade property is required (e.g., is_approver_grade === true)
        if ($requiredProperty && (! isset($user->grade->{$requiredProperty}) || ! $user->grade->{$requiredProperty})) {
            Log::warning('CheckUserGrade: User grade does not meet required property.', [
                'user_id'           => $user->id,
                'grade_id'          => $user->grade->id,
                'required_property' => $requiredProperty,
                'property_value'    => $user->grade->{$requiredProperty} ?? 'not_set',
                'route_name'        => $request->route()?->getName(),
            ]);
            abort(403, sprintf('Akses Ditolak. Gred anda tidak memenuhi kriteria yang diperlukan (%s).', $requiredProperty));
        }

        // If the middleware is used just to check if a grade exists,
        // and we've passed the !$user->grade check, then proceed.
        return $next($request);
    }
}
