<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use stdClass;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // No service bindings needed for this provider.
    }

    /**
     * Bootstrap services.
     * Loads vertical menu structure from JSON and shares with all views.
     */
    public function boot(): void
    {
        $menuPath = base_path('resources/menu/verticalMenu.json');
        $menuData = new stdClass(); // Default object with no menu

        if (file_exists($menuPath)) {
            try {
                $jsonContent = file_get_contents($menuPath);

                if ($jsonContent === false) {
                    Log::error('[MenuServiceProvider] Failed to read verticalMenu.json.');
                } else {
                    $decoded = json_decode($jsonContent);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $menuData = $decoded;
                    } else {
                        Log::error('[MenuServiceProvider] JSON decoding failed: ' . json_last_error_msg());
                    }
                }
            } catch (\Throwable $e) {
                Log::critical('[MenuServiceProvider] Exception occurred: ' . $e->getMessage());
            }
        } else {
            Log::warning('[MenuServiceProvider] Menu file not found: ' . $menuPath);
        }

        // Ensure it always has a "menu" property to prevent Blade exceptions
        if (!property_exists($menuData, 'menu')) {
            $menuData->menu = [];
        }

        View::share('menuData', $menuData);
    }
}
