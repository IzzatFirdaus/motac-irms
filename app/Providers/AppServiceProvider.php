<?php

namespace App\Providers;

use App\Helpers\Helpers;
use App\Services\ApprovalService;
use App\Services\EmailApplicationService;
use App\Services\EmailProvisioningService;
use App\Services\EquipmentService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use App\Services\NotificationService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registering application services as singletons for efficiency.
        $this->app->singleton(ApprovalService::class);
        $this->app->singleton(EmailApplicationService::class);
        $this->app->singleton(EmailProvisioningService::class);
        $this->app->singleton(EquipmentService::class);
        $this->app->singleton(LoanApplicationService::class);
        $this->app->singleton(LoanTransactionService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- REVISED: Locale setting logic is now handled here. ---
        // This is more robust than using middleware for this specific case.
        $sessionLocale = Session::get('locale');
        $fallbackLocale = Config::get('app.fallback_locale', 'en');
        $finalLocale = $fallbackLocale; // Default to fallback

        $configuredLocales = Config::get('app.available_locales');

        if (is_array($configuredLocales) && $configuredLocales !== []) {
            $allowedLocaleKeys = array_keys($configuredLocales);
            if ($sessionLocale && in_array($sessionLocale, $allowedLocaleKeys, true)) {
                $finalLocale = $sessionLocale;
            }
        }

        App::setLocale($finalLocale);
        // --- End of Locale Logic ---

        // Enforce strict model behavior (no lazy loading, etc.) in non-production environments.
        Model::shouldBeStrict(! $this->app->environment('production'));

        // Set pagination to use Bootstrap 5 styles.
        Paginator::useBootstrapFive();

        // Register a custom Blade component alias.
        Blade::component('components.alert-manager', 'alert-manager');

        // Provide a handler to log missing translation keys for easier maintenance.
        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale): string {
            Log::warning(sprintf('Missing translation key detected: [%s] for locale [%s].', $key, $locale), ['replacements' => $replacements]);

            return $key;
        });

        // Set the global locale for the Carbon date library.
        try {
            Carbon::setLocale(App::getLocale());
        } catch (\Exception $exception) {
            Log::error('AppServiceProvider: Failed to set Carbon locale: '.$exception->getMessage());
            Carbon::setLocale(config('app.fallback_locale', 'en'));
        }

        // Share global variables with all views, but not during console commands.
        if (! $this->app->runningInConsole()) {
            View::composer('*', function (\Illuminate\View\View $view): void {
                try {
                    $configData = class_exists(Helpers::class) ? Helpers::appClasses() : [];
                } catch (\Exception $exception) {
                    $configData = [];
                }

                $view->with('configData', $configData);
                $view->with('appClasses', $configData);
            });
            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        }
    }
}
