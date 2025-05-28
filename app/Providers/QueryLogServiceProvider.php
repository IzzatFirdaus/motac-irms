<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App; // Import the App facade

class QueryLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            DB::enableQueryLog();

            DB::whenQueryingForLongerThan(1000, function ($connection) {
                // Only attempt to get the URL if not running in console
                $url = 'N/A in console';
                if (!App::runningInConsole()) {
                    // Ensure request() is not null and fullUrl() can be safely called
                    if (request() && method_exists(request(), 'fullUrl')) {
                        $url = request()->fullUrl();
                    } else {
                        $url = 'Request object not available';
                    }
                }

                Log::warning(
                    'Long running queries detected.', [
                        'queries' => $connection->getQueryLog(),
                        'url' => $url,
                    ]);
            });
        }
    }
}
