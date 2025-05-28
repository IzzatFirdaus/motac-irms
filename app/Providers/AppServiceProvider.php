<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helpers; // Assuming this is your helper class
use Illuminate\Support\Facades\App; // Import the App facade

// MOTAC Core Services
use App\Services\ApprovalService;
use App\Services\EmailApplicationService;
use App\Services\EmailProvisioningService;
use App\Services\EquipmentService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use App\Services\NotificationService;
use App\Services\UserService;

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
        Model::shouldBeStrict(! $this->app->environment('production'));

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            $logMessage = "Missing translation key detected: [{$key}] for locale [{$locale}].";
            Log::warning($logMessage, ['replacements' => $replacements]);
            return $key;
        });

        try {
            $currentAppLocale = app()->getLocale();
            Carbon::setLocale($currentAppLocale);
        } catch (\Exception $e) {
            Log::error("AppServiceProvider: Failed to set Carbon locale to '" . app()->getLocale() . "'. Error: " . $e->getMessage() . ". Defaulting to fallback '" . config('app.fallback_locale', 'en') . "'.");
            Carbon::setLocale(config('app.fallback_locale', 'en'));
        }

        // Defer View Composers if running in console
        if (!App::runningInConsole()) { // Using the App facade here
            View::composer('*', function ($view) {
                $configData = [];
                $isConsole = App::runningInConsole(); // Check again, just in case, though this block shouldn't run in console

                try {
                    if (class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')) {
                        $configData = Helpers::appClasses();
                    } else {
                        Log::warning('AppServiceProvider: Helpers::appClasses() not found or class not loaded. Using fallback configData.');
                        throw new \Exception('Helpers::appClasses() not found or class not loaded.');
                    }
                } catch (\Exception $e) {
                    Log::critical('AppServiceProvider: CRITICAL ERROR calling Helpers::appClasses() in View Composer. ' . $e->getMessage(), ['exception' => $e]);
                    // Fallback configuration - AVOID asset() and url() here if they are problematic in console
                    $configData = [
                        'templateName' => config('variables.templateName', __('Sistem MOTAC')),
                        'textDirection' => 'ltr', 'style' => 'light', 'theme' => 'theme-motac', 'layout' => 'vertical',
                        // Provide console-safe fallbacks or omit if not strictly needed by all views
                        'assetsPath' => $isConsole ? '/assets/' : asset('/assets') . '/', // Conditional asset path
                        'baseUrl' => $isConsole ? config('app.url', '/') : url('/'),      // Conditional base URL
                        'locale' => config('app.locale', 'ms'), 'bsTheme' => 'light',
                        'isMenu' => true, 'isNavbar' => true, 'isFooter' => true, 'contentNavbar' => true,
                        'menuFixed' => true, 'menuCollapsed' => false, 'navbarFixed' => true, 'navbarDetached' => true,
                        'footerFixed' => false, 'customizerHidden' => true, 'displayCustomizer' => false,
                        'rtlSupport' => '', 'primaryColor' => '#0050A0', 'isFlex' => false,
                        'container' => 'container-fluid', 'containerNav' => 'container-fluid',
                        'showMenu' => true, 'contentLayout' => 'wide',
                    ];
                }
                $view->with('configData', $configData);
            });

            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        }
    }
}
