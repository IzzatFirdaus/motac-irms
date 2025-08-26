<?php

namespace App\Http\Middleware;

use App\Models\User; // Assuming your User model is App\Models\User
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Corrected import
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckGradeLevel
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user's grade level meets the specified minimum.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param string|int                                                                       $minRequiredGradeLevelNumeric The minimum numeric grade level required.
     */
    public function handle(Request $request, Closure $next, $minRequiredGradeLevelNumeric): Response
    {
        if (! Auth::check()) {
            Log::warning('CheckGradeLevel: Unauthenticated user attempted to access grade-restricted route.', [ // [cite: 3]
                'route_name' => $request->route()?->getName(),
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('login')->with('error', 'Sila log masuk untuk mengakses halaman ini.'); // [cite: 3]
        }

        /** @var User $user */
        $user = Auth::user();

        // Ensure user has a grade and the grade has a numeric level property
        if (! $user->grade || ! isset($user->grade->level) || ! is_numeric($user->grade->level)) {
            Log::warning('CheckGradeLevel: User does not have a valid or numeric grade level configured.', [ // [cite: 3]
                'user_id'    => $user->id,
                'user_email' => $user->email,
                'route_name' => $request->route()?->getName(),
            ]);
            abort(403, 'Akses Ditolak. Akaun anda tidak mempunyai Gred yang sah atau tahap Gred tidak ditetapkan.'); // [cite: 3]
        }

        $userGradeLevelNumeric = (int) $user->grade->level; // [cite: 3]
        $requiredGradeLevel    = (int) $minRequiredGradeLevelNumeric; // [cite: 3]

        if ($userGradeLevelNumeric >= $requiredGradeLevel) { // [cite: 3]
            return $next($request);
        }

        Log::warning('CheckGradeLevel: User grade level insufficient.', [ // [cite: 3]
            'user_id'              => $user->id,
            'user_email'           => $user->email,
            'user_grade_level'     => $userGradeLevelNumeric,
            'required_grade_level' => $requiredGradeLevel,
            'route_name'           => $request->route()?->getName(),
        ]);
        abort(403, 'Akses Ditolak. Tahap Gred anda tidak mencukupi untuk mengakses sumber ini.'); // [cite: 3]
    }
}
