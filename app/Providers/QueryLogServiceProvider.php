<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider; // Import the QueryExecuted event

class QueryLogServiceProvider extends ServiceProvider // Corrected class name
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
     * Enables query logging in the local environment and logs queries exceeding a certain duration.
     */
    public function boot(): void
    {
        if (App::environment('local')) {
            // Optionally enable logging of all queries in local environment for debugging
            // DB::enableQueryLog();

            $longQueryThreshold = config('database.long_query_threshold', 1000); // Default to 1000ms (1s)

            DB::whenQueryingForLongerThan($longQueryThreshold, function ($connection, QueryExecuted $event) {
                $url = 'N/A (Console or no request context)';
                if (! App::runningInConsole()) {
                    /** @var \Illuminate\Http\Request|null $currentRequest */
                    $currentRequest = request(); // Global helper
                    if ($currentRequest && method_exists($currentRequest, 'fullUrl')) {
                        $url = $currentRequest->fullUrl();
                    } else {
                        $url = 'Request object or fullUrl() method not available.';
                    }
                }

                Log::warning(
                    'Long running query detected.',
                    [
                        'connection_name' => $connection->getName(),
                        'query' => $event->sql,          // SQL from the event
                        'bindings' => $event->bindings,   // Bindings from the event
                        'time_ms' => $event->time,        // Execution time in milliseconds from the event
                        'url' => $url,
                    ]
                );
            });
        }
    }
}
