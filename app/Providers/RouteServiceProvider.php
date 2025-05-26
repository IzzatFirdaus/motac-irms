<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     * Typically, users are redirected here after authentication.
     * This should align with your MOTAC system's main dashboard.
     */
    public const HOME = '/dashboard'; // As per MOTAC design (Section 3.3 / Fortify config)

    /**
     * The controller namespace for the application.
     * This is often commented out in modern Laravel applications that use FQCN for controllers.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                // ->namespace($this->namespace) // Not needed if using FQCN for controllers
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                // ->namespace($this->namespace) // Not needed if using FQCN for controllers
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Example for login rate limiting (if using Fortify, it has its own)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });
    }
}
