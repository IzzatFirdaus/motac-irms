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
use App\Translation\SuffixedTranslator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

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

        /**
         * Register the SuffixedTranslator as the default translator.
         * This enables support for language files like forms_en.php, forms_ms.php, etc.
         */
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            // Use SuffixedTranslator instead of Laravel's default Translator
            return new SuffixedTranslator($loader, $locale);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent N+1 query issues in non-production environments.
        Model::preventLazyLoading(! $this->app->environment('production'));

        // Use Bootstrap pagination views.
        Paginator::useBootstrapFour();

        // Register custom Blade component alias.
        Blade::component('components.alert-manager', 'alert-manager');

        // Log missing translation keys for maintenance.
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

        // Share global variables with all views, except during console commands.
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
