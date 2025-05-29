<?php

namespace App\Providers;

use App\Helpers\Helpers;
use App\Services\ApprovalService;
use App\Services\EmailApplicationService;
use App\Services\EmailProvisioningService;
use App\Services\EquipmentService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService; // Assuming this is your helper class
use App\Services\NotificationService; // Import the App facade
// MOTAC Core Services
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade; // Correctly imported
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApprovalService::class);
        $this->app->singleton(EmailApplicationService::class);
        $this->app->singleton(EmailProvisioningService::class);
        $this->app->singleton(EquipmentService::class);
        $this->app->singleton(LoanApplicationService::class);
        $this->app->singleton(LoanTransactionService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(UserService::class);
    }

    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->environment('production'));

        // Register Blade component aliases
        // This tells Laravel that when <x-app-layout> is used,
        // it should render the resources/views/layouts/app.blade.php view.
        Blade::component('layouts.app', 'app-layout');

        // Register the 'alert' component alias to use the 'alert-manager.blade.php' view
        // This means <x-alert /> will render resources/views/components/alert-manager.blade.php
        Blade::component('components.alert-manager', 'alert'); // <<< THIS LINE IS ADDED/ENSURED

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            $logMessage = "Missing translation key detected: [{$key}] for locale [{$locale}].";
            Log::warning($logMessage, ['replacements' => $replacements]);
            return $key; // Return the key itself to avoid breaking the UI
        });

        try {
            $currentAppLocale = app()->getLocale();
            Carbon::setLocale($currentAppLocale);
        } catch (\Exception $e) {
            Log::error("AppServiceProvider: Failed to set Carbon locale to '" . app()->getLocale() . "'. Error: " . $e->getMessage());
            Carbon::setLocale(config('app.fallback_locale', 'en')); // Fallback to default
        }

        // ✅ ONLY register view composers in HTTP context
        if (!$this->app->runningInConsole()) {
            View::composer('*', function ($view) {
                $configData = [];

                try {
                    $configData = class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')
                        ? Helpers::appClasses()
                        : throw new \Exception('Helpers::appClasses() not found or class not loaded.');
                } catch (\Exception $e) {
                    Log::critical('AppServiceProvider View Composer error: ' . $e->getMessage());
                    // Provide a sensible default configData array if Helpers fails
                    $configData = [
                        'templateName' => config('variables.templateName', __('Sistem MOTAC')),
                        'textDirection' => 'ltr', // default
                        'style' => 'light', // default
                        'theme' => 'theme-motac', // default (example)
                        'layout' => 'vertical', // default
                        'assetsPath' => asset('/assets') . '/',
                        'baseUrl' => url('/'),
                        'locale' => config('app.locale', 'ms'),
                        'bsTheme' => 'light', // Assuming Bootstrap 5+
                        'isMenu' => true,
                        'isNavbar' => true,
                        'isFooter' => true,
                        'contentNavbar' => true, // Default based on your app.blade.php
                        'menuFixed' => true,
                        'menuCollapsed' => false,
                        'navbarFixed' => true, // default
                        'navbarDetached' => true, // Default to detached as per your app.blade.php logic
                        'footerFixed' => false,
                        'customizerHidden' => true, // As per your app.blade.php
                        'displayCustomizer' => false, // Usually false by default
                        'rtlSupport' => '', // Or determine based on locale
                        'primaryColor' => '#0050A0', // Example color
                        'isFlex' => false, // As per your app.blade.php
                        'container' => 'container-fluid', // As per your app.blade.php
                        'containerNav' => 'container-fluid', // As per your app.blade.php
                        'showMenu' => true, // default
                        'contentLayout' => 'wide', // default
                    ];
                }

                // Merge appClasses for compatibility with your snippet
                $appClasses = $configData;

                $view->with('configData', $configData);
                $view->with('appClasses', $appClasses); // Share $appClasses as well
            });

            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        } else {
            // Optional: Log that view composers are skipped in console
            Log::info('View composer registration skipped: application is running in console.');
        }
    }
}
