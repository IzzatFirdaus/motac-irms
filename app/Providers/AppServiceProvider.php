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
            Log::error("AppServiceProvider: Failed to set Carbon locale to '" . app()->getLocale() . "'. Error: " . $e->getMessage());
            Carbon::setLocale(config('app.fallback_locale', 'en'));
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
                    $configData = [
                      'templateName' => config('variables.templateName', __('Sistem MOTAC')),
                      'textDirection' => 'ltr',
                      'style' => 'light',
                      'theme' => 'theme-motac',
                      'layout' => 'vertical',
                      'assetsPath' => asset('/assets') . '/',
                      'baseUrl' => url('/'),
                      'locale' => config('app.locale', 'ms'),
                      'bsTheme' => 'light',
                      'isMenu' => true,
                      'isNavbar' => true,
                      'isFooter' => true,
                      'contentNavbar' => true,
                      'menuFixed' => true,
                      'menuCollapsed' => false,
                      'navbarFixed' => true,
                      'navbarDetached' => true,
                      'footerFixed' => false,
                      'customizerHidden' => true,
                      'displayCustomizer' => false,
                      'rtlSupport' => '',
                      'primaryColor' => '#0050A0',
                      'isFlex' => false,
                      'container' => 'container-fluid',
                      'containerNav' => 'container-fluid',
                      'showMenu' => true,
                      'contentLayout' => 'wide',
                    ];
                }

                // Merge appClasses for compatibility with your snippet
                $appClasses = $configData;

                $view->with('configData', $configData);
                $view->with('appClasses', $appClasses);
            });

            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        } else {
            Log::info('View composer skipped: running in console mode.');
        }
    }
}
