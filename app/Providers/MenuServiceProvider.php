<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config; // Import Config facade
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use stdClass;

// Ensures stdClass can be used for default empty object

class MenuServiceProvider extends ServiceProvider
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
     * Loads MOTAC vertical menu structure from config/menu.php and shares it with all views.
     * System Design Reference: Assumes config/menu.php is now the primary source,
     * updating behavior from original design doc mention of verticalMenu.json.
     */
    public function boot(): void
    {
        $menuDataObject       = new stdClass; // Default to an empty object
        $menuDataObject->menu = [];   // Ensure 'menu' property exists as an array by default

        try {
            // Attempt to load menu data from config/menu.php
            // The config/menu.php file should return an array with a 'menu' key,
            // which itself is an array of menu items.
            $menuConfigArray = Config::get('menu.menu');

            if (is_array($menuConfigArray)) {
                $menuDataObject->menu = $menuConfigArray;
                Log::debug('[MenuServiceProvider] Successfully loaded MOTAC menu data from config/menu.php.');
            } elseif ($menuConfigArray === null) {
                Log::warning('[MenuServiceProvider] Menu configuration key "menu.menu" not found or is null in config/menu.php. Using default empty menu structure.');
                // $menuDataObject is already set to default empty menu
            } else {
                Log::error('[MenuServiceProvider] Menu data from config/menu.php is not in the expected array format. Structure type: '.gettype($menuConfigArray).'. Using default empty menu structure.');
                // $menuDataObject remains default empty menu
            }
        } catch (\Throwable $throwable) { // Catch any generic error during config access
            Log::critical('[MenuServiceProvider] Exception occurred while processing MOTAC menu configuration: '.$throwable->getMessage(), ['exception' => $throwable]);
            // $menuDataObject remains default empty menu
        }

        // Share the $menuDataObject (which is guaranteed to have a ->menu property as an array)
        // This $menuData will be globally available in all Blade views.
        View::share('menuData', $menuDataObject);
        // Avoid logging potentially large menu structure in production if not needed for debugging,
        // or consider logging only a count or a summary.
        // Log::debug('[MenuServiceProvider] MOTAC menuData shared with views: ' . json_encode($menuDataObject));
    }
}
