<?php

namespace App\Providers;

use App\Helpers\Helpers; // Assuming this helper class exists and provides appClasses()
use App\Services\ApprovalService;
use App\Services\EmailApplicationService;
use App\Services\EmailProvisioningService;
use App\Services\EquipmentService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService; // Standard service import
use App\Services\NotificationService;    // Standard service import
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;      // Correct facade for app() helper
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <--- ADD THIS LINE

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registering application services as singletons
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
        // Enforce strict Eloquent mode in non-production environments
        Model::shouldBeStrict(!$this->app->environment('production'));

        // Configure Laravel Paginator to use Bootstrap 5 <--- ADD THESE LINES
        Paginator::useBootstrapFive();


        // Register Blade component aliases for convenience
        // Blade::component('layouts.app', 'app-layout'); // Example: <x-app-layout /> maps to resources/views/layouts/app.blade.php
        // Blade::component('components.alert', 'alert');     // Example: <x-alert /> maps to resources/views/components/alert.blade.php
        Blade::component('components.alert-manager', 'alert-manager'); // Your existing alias for alert-manager

        // Custom handler for missing translation keys
        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            $logMessage = "Missing translation key detected: [{$key}] for locale [{$locale}].";
            Log::warning($logMessage, ['replacements' => $replacements]);
            return $key; // Return the key itself to avoid breaking UI, makes missing keys noticeable
        });

        // Set Carbon's locale based on the application's current locale
        try {
            $currentAppLocale = App::getLocale(); // Use App facade
            Carbon::setLocale($currentAppLocale);
        } catch (\Exception $e) {
            Log::error("AppServiceProvider: Failed to set Carbon locale to '" . App::getLocale() . "'. Error: " . $e->getMessage(), ['exception_class' => get_class($e)]);
            Carbon::setLocale(config('app.fallback_locale', 'en')); // Fallback to default if error
        }

        // Register view composers only in HTTP context (not console)
        if (!$this->app->runningInConsole()) {
            View::composer('*', function (\Illuminate\View\View $view) { // Type hint $view
                $configData = [];
                try {
                    // Attempt to load UI configuration from Helpers class
                    if (class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')) {
                        $configData = Helpers::appClasses();
                    } else {
                        // This will be caught by the catch block if Helpers::appClasses() is unavailable
                        throw new \Exception('Helpers::appClasses() method not found or Helpers class not loaded.');
                    }
                } catch (\Exception $e) {
                    Log::critical('AppServiceProvider View Composer (Helpers::appClasses) error: ' . $e->getMessage(), ['exception_class' => get_class($e)]);
                    // Provide a sensible default configData array if Helpers fails, matching your example
                    $configData = [
                        'templateName' => config('variables.templateName', __('Sistem MOTAC')),
                        'textDirection' => config('variables.textDirection', 'ltr'),
                        'style' => config('variables.style', 'light'), // theme-default or theme-bordered or theme-semi-dark
                        'theme' => config('variables.theme', 'theme-motac'), // Example default from your app
                        'layout' => config('variables.layout', 'vertical'),
                        'assetsPath' => asset(config('variables.assetsPath', 'assets')) . '/',
                        'baseUrl' => url('/'),
                        'locale' => App::getLocale(), // Use current app locale
                        'bsTheme' => config('variables.bsTheme', 'light'), // Bootstrap theme: light or dark
                        'isMenu' => config('variables.isMenu', true),
                        'isNavbar' => config('variables.isNavbar', true),
                        'isFooter' => config('variables.isFooter', true),
                        'contentNavbar' => config('variables.contentNavbar', true),
                        'menuFixed' => config('variables.menuFixed', true),
                        'menuCollapsed' => config('variables.menuCollapsed', false),
                        'navbarFixed' => config('variables.navbarFixed', true),
                        'navbarDetached' => config('variables.navbarDetached', true),
                        'footerFixed' => config('variables.footerFixed', false),
                        'customizerHidden' => config('variables.customizerHidden', true),
                        'displayCustomizer' => config('variables.displayCustomizer', false),
                        'rtlSupport' => config('variables.rtlSupport', (App::getLocale() === 'ar' || App::getLocale() === 'fa')), // Basic RTL detection
                        'primaryColor' => config('variables.primaryColor', '#0050A0'), // Default MOTAC blue
                        'isFlex' => config('variables.isFlex', false),
                        'container' => config('variables.container', 'container-fluid'),
                        'containerNav' => config('variables.containerNav', 'container-fluid'),
                        'showMenu' => config('variables.showMenu', true),
                        'contentLayout' => config('variables.contentLayout', 'wide'),
                    ];
                }

                // Share $configData (and $appClasses if your views specifically use it)
                $view->with('configData', $configData);
                $view->with('appClasses', $configData); // If appClasses is just an alias for configData
            });

            // Share application name with all views
            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        } else {
            Log::info('AppServiceProvider: View composer registration skipped (running in console).');
        }
    }
}
