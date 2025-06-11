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
use Illuminate\Support\Facades\DB; // Ensure DB facade is imported
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // EDIT: This debugger is now more specific. It will only stop
        // on the INSERT query for a 'return' transaction, which is the one that fails.
        DB::listen(function ($query) {
            // Check if this is an insert into the correct table
            if (str_contains($query->sql, 'insert into `loan_transactions`')) {
                // Find the position of the `type` column in the SQL statement
                // to reliably check its value in the bindings array.
                preg_match('/`type`/', $query->sql, $matches, PREG_OFFSET_CAPTURE);
                if (isset($matches[0])) {
                    $typePosition = substr_count(substr($query->sql, 0, $matches[0][1]), '`');
                    // The binding index is one less than the column position
                    $typeBindingIndex = $typePosition - 1;

                    // Only dump and die if this is the 'return' transaction
                    if (isset($query->bindings[$typeBindingIndex]) && $query->bindings[$typeBindingIndex] === 'return') {
                        dd($query->sql, $query->bindings);
                    }
                }
            }
        });

        Model::shouldBeStrict(! $this->app->environment('production'));
        Paginator::useBootstrapFive();
        Blade::component('components.alert-manager', 'alert-manager');

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            Log::warning("Missing translation key detected: [{$key}] for locale [{$locale}].", ['replacements' => $replacements]);
            return $key;
        });

        try {
            Carbon::setLocale(App::getLocale());
        } catch (\Exception $e) {
            Log::error("AppServiceProvider: Failed to set Carbon locale: ".$e->getMessage());
            Carbon::setLocale(config('app.fallback_locale', 'en'));
        }

        if (! $this->app->runningInConsole()) {
            View::composer('*', function (\Illuminate\View\View $view) {
                try {
                    $configData = class_exists(Helpers::class) ? Helpers::appClasses() : [];
                } catch (\Exception $e) {
                    $configData = [];
                }
                $view->with('configData', $configData);
                $view->with('appClasses', $configData);
            });
            View::share('appName', config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')));
        }
    }
}
