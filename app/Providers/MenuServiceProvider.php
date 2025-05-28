<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Support\Facades\View; // For View::share
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // No specific services to register for this provider.
    }

    /**
     * Bootstrap services.
     * Loads menu data from a JSON file and shares it with all views.
     * System Design Reference: 3.3 MenuServiceProvider loads and shares navigation menu data.
     * The menu data is used by resources/views/livewire/sections/menu/vertical-menu.blade.php.
     */
    public function boot(): void
    {
        $menuJsonPath = base_path('resources/menu/verticalMenu.json'); // Path to the menu configuration file
        $verticalMenuData = null;

        if (file_exists($menuJsonPath)) {
            $verticalMenuJson = file_get_contents($menuJsonPath);
            if ($verticalMenuJson === false) {
                Log::error('MenuServiceProvider: Could not read verticalMenu.json file.');
            } else {
                $decodedData = json_decode($verticalMenuJson);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $verticalMenuData = $decodedData;
                } else {
                    Log::error('MenuServiceProvider: Error decoding verticalMenu.json. JSON Error: ' . json_last_error_msg());
                }
            }
        } else {
            Log::warning('MenuServiceProvider: verticalMenu.json file not found at ' . $menuJsonPath);
        }

        // Share menuData with all views.
        // If $verticalMenuData is null (e.g., file not found or invalid JSON),
        // the views should handle this gracefully (e.g., display "Menu not available").
        View::share('menuData', $verticalMenuData);
    }
}
