<?php

namespace App\Providers;

use App\Helpers\Helpers; // Assuming this helper class exists and is used
use App\Services\LoanApplicationService; // Specific to MOTAC system
// SMS Service is out of scope based on MOTAC design, so it's not registered.
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// Uncomment if using Model::shouldBeStrict() in development
// use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register MOTAC specific services
        $this->app->singleton(LoanApplicationService::class);

        // Example of how other services would be registered:
        // $this->app->singleton(EmailApplicationService::class);
        // $this->app->singleton(EquipmentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Recommended for development to catch mass assignment issues, etc.
        // Model::shouldBeStrict(! $this->app->isProduction());

        // Custom handler for missing translation keys
        Lang::handleMissingKeysUsing(function (
            string $key,
            array $replacements,
            string $locale
        ) {
            Log::warning("Missing translation key [{$key}] for locale [{$locale}].", $replacements);
            // Return the key itself or a default message
            return $key;
        });

        // Set Carbon's locale based on application's locale
        try {
            Carbon::setLocale(config('app.locale', 'en'));
        } catch (\Exception $e) {
            Log::error("Failed to set Carbon locale: " . $e->getMessage() . ". Defaulting to 'en'.");
            Carbon::setLocale('en');
        }


        // Share common configuration data with specific layouts or all views
        // This example targets 'layouts.commonMaster' as seen in deprecated file.
        // Adjust if your MOTAC layout name is different.
        View::composer('layouts.commonMaster', function ($view) {
            $configData = [];
            if (class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')) {
                try {
                    $configData = Helpers::appClasses();
                } catch (\Exception $e) {
                    Log::error('Error calling Helpers::appClasses(): ' . $e->getMessage());
                }
            } else {
                Log::warning('App\Helpers\Helpers::appClasses() not found. View composer for layouts.commonMaster will use empty configData.');
            }

            $view->with('configData', $configData)
                 ->with('navbarFixed', data_get($configData, 'navbarFixed', false))
                 ->with('menuFixed', data_get($configData, 'menuFixed', false))
                 ->with('menuCollapsed', data_get($configData, 'menuCollapsed', false))
                 ->with('footerFixed', data_get($configData, 'footerFixed', false))
                 ->with('customizerHidden', data_get($configData, 'customizerHidden', true)); // Default to hidden
        });

        // Share application name globally with all views
        View::share('appName', config('app.name', 'MOTAC Resource Management'));
    }
}
