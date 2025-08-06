<?php

namespace App\Providers;

use App\Helpers\Helpers;
use App\Services\ApprovalService;
use App\Services\EquipmentService;
use App\Services\HelpdeskService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use App\Services\NotificationService;
use App\Services\TicketNotificationService;
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
        $this->app->singleton(EquipmentService::class);
        $this->app->singleton(HelpdeskService::class);
        $this->app->singleton(LoanApplicationService::class);
        $this->app->singleton(LoanTransactionService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(TicketNotificationService::class);
        $this->app->singleton(UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure that lazy loading is not allowed in production to prevent N+1 issues.
        // Changed from isProduction() to environment('production')
        Model::preventLazyLoading(! $this->app->environment('production'));

        // Use Bootstrap pagination views.
        Paginator::useBootstrapFour();

        // Register custom Blade component alias.
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
