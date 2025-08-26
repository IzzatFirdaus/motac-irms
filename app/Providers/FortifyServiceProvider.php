<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fortify's own service provider handles its core registration.
        // You can bind your own implementations of Fortify contracts here if needed.
    }

    /**
     * Bootstrap any application services.
     * Configures Fortify actions and features.
     * System Design Reference: [cite: 58, 342].
     */
    public function boot(): void
    {
        // Registering custom actions for Fortify processes
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Configure views for Fortify if not using default (usually handled by Jetstream or custom routes)
        // Fortify::loginView(fn () => view('auth.login'));
        // Fortify::registerView(fn () => view('auth.register'));
        // ... other view configurations ...

        // Rate limiting for login attempts
        RateLimiter::for('login', function (Request $request) {
            $email       = (string) $request->input(Fortify::username()); // Get username input (usually email)
            $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey); // Example: 5 attempts per minute per email/IP
        });

        // Rate limiting for two-factor authentication attempts
        RateLimiter::for('two-factor', function (Request $request) {
            // Uses session data set during the login process
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
