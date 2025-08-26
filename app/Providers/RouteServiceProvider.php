<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str; // Added import for the Str facade

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     * Typically, users are redirected here after authentication.
     * This should align with your MOTAC system's main dashboard.
     * System Design Reference: [cite: 60, 344] (HOME constant is /dashboard).
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     * This is often commented out in modern Laravel applications that use Fully Qualified Class Names (FQCN) for controllers.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers'; // Typically not needed with FQCN

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::middleware('api')
                ->prefix('api')
                // ->namespace($this->namespace) // Not needed if using FQCN for API controllers
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                // ->namespace($this->namespace) // Not needed if using FQCN for web controllers
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     * System Design Reference: [cite: 60, 344] (API rate limiting & Fortify rate limiting).
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            // Limit by authenticated user ID or by IP address for guests
            // Ensure your API guard is configured if 'api.throttle' is used.
            return Limit::perMinute(config('auth.guards.api.throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        // Custom rate limiter for login attempts.
        // Fortify also has built-in login throttling; this can complement or replace it.
        RateLimiter::for('login', function (Request $request) {
            // Throttle by a combination of the login identifier (e.g., email) and IP address
            $loginIdentifier = $request->input(config('fortify.username', 'email'), ''); // Get the configured username field (defaults to 'email')
            $throttleKey     = Str::transliterate(Str::lower($loginIdentifier).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey); // Corrected to use $throttleKey
        });
    }
}
