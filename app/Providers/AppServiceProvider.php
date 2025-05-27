<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model; // For Model::shouldBeStrict()
use App\Helpers\Helpers;

// MOTAC Core Services - As per System Design 3.1, 3.3, 9
use App\Services\ApprovalService;
use App\Services\EmailApplicationService;
use App\Services\EmailProvisioningService; // Added as per System Design 9.2
use App\Services\EquipmentService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService; // Added as per System Design 9.3
use App\Services\NotificationService;
use App\Services\UserService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * System Design Reference: 3.3 AppServiceProvider registers core services.
     */
    public function register(): void
    {
        // Register MOTAC specific services as singletons
        $this->app->singleton(ApprovalService::class);
        $this->app->singleton(EmailApplicationService::class);
        $this->app->singleton(EmailProvisioningService::class); // For handling email creation logic
        $this->app->singleton(EquipmentService::class); // System Design 9.3 mentions ResourceService/EquipmentService
        $this->app->singleton(LoanApplicationService::class);
        $this->app->singleton(LoanTransactionService::class); // For handling loan issue/return logic
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(UserService::class);
    }

    /**
     * Bootstrap any application services.
     * System Design Reference: 3.3 Handles view composers, Carbon localization, missing translation logging.
     */
    public function boot(): void
    {
        // Enable Eloquent strict mode in non-production environments to catch common issues.
        // Helps prevent N+1 queries, unfillable assignments, and lazy loading violations.
        Model::shouldBeStrict(! $this->app->environment('production')); // Changed isProduction() to environment('production')

        // Custom handler for missing translation keys
        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            $logMessage = "Missing translation key detected: [{$key}] for locale [{$locale}].";
            if (!empty($replacements)) {
                $logMessage .= " Replacements: " . json_encode($replacements);
            }
            Log::warning($logMessage);
            return $key; // Return the key itself so UI doesn't break, but issue is logged
        });

        // Set Carbon's locale based on the application's current locale
        try {
            $appLocale = config('app.locale', 'en'); // Default to 'en' if config is missing
            Carbon::setLocale($appLocale);
            Log::debug("AppServiceProvider: Carbon locale set to '{$appLocale}'.");
        } catch (\Exception $e) {
            Log::error("AppServiceProvider: Failed to set Carbon locale to '" . config('app.locale') . "'. Error: " . $e->getMessage() . ". Defaulting to 'en'.");
            Carbon::setLocale('en'); // Fallback to English
        }

        // Share common configuration data with specific layouts or all views.
        // This ensures $configData from Helpers::appClasses() is available.
        // System Design Reference: 3.3 View composers share global UI config.
        View::composer(['layouts.commonMaster', 'layouts.app'], function ($view) {
            $configData = [];
            if (class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')) {
                try {
                    $configData = Helpers::appClasses();
                } catch (\Exception $e) {
                    Log::critical('AppServiceProvider: CRITICAL ERROR calling Helpers::appClasses() in View Composer. ' . $e->getMessage(), ['exception' => $e]);
                    $configData = [
                        'templateName' => config('app.name', 'MOTAC RMS'),
                        'textDirection' => 'ltr',
                        'style' => 'light',
                        'theme' => 'theme-default',
                        'layout' => 'vertical',
                        'assetsPath' => asset('/assets') . '/',
                        'baseUrl' => url('/'),
                        'locale' => config('app.locale', 'en'),
                        'navbarFixed' => false, 'menuFixed' => false, 'menuCollapsed' => false,
                        'footerFixed' => false, 'customizerHidden' => true, 'rtlSupport' => '',
                        'primaryColor' => '#696cff', 'displayCustomizer' => false,
                    ];
                }
            } else {
                Log::error('AppServiceProvider: App\Helpers\Helpers::appClasses() not found. View composer will use minimal default configData.');
                $configData = ['templateName' => config('app.name', 'MOTAC RMS'), 'textDirection' => 'ltr', 'style' => 'light', 'layout' => 'vertical', 'assetsPath' => asset('/assets') . '/', 'baseUrl' => url('/')];
            }
            $view->with('configData', $configData);
        });

        View::share('appName', config('app.name', 'MOTAC Resource Management System'));
    }
}
