<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use stdClass; // Ensures stdClass can be used for default empty object

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
     * Loads MOTAC vertical menu structure from JSON and shares with all views.
     */
    public function boot(): void
    {
        // Path to your MOTAC-specific verticalMenu.json
        $motacMenuPath = base_path('resources/menu/verticalMenu.json');
        $menuDataObject = new stdClass(); // Default to an object
        $menuDataObject->menu = [];   // Ensure 'menu' property exists as an array

        if (file_exists($motacMenuPath)) {
            try {
                $jsonContent = file_get_contents($motacMenuPath);

                if ($jsonContent === false) {
                    Log::error('[MenuServiceProvider] Failed to read MOTAC verticalMenu.json. Path: ' . $motacMenuPath);
                } else {
                    if (trim($jsonContent) === '') {
                        Log::warning('[MenuServiceProvider] MOTAC verticalMenu.json is empty. Using default empty menu structure.');
                    } else {
                        $decoded = json_decode($jsonContent); // Decodes to an object by default

                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (is_object($decoded) && property_exists($decoded, 'menu') && is_array($decoded->menu)) {
                                $menuDataObject = $decoded; // Use the fully decoded object if valid
                                Log::debug('[MenuServiceProvider] Successfully decoded MOTAC verticalMenu.json.');
                            } else {
                                Log::error('[MenuServiceProvider] MOTAC verticalMenu.json is valid JSON but not in the expected object format (e.g., {"menu": []}). Structure type: ' . gettype($decoded));
                            }
                        } else {
                            Log::error('[MenuServiceProvider] JSON decoding failed for MOTAC verticalMenu.json: ' . json_last_error_msg());
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::critical('[MenuServiceProvider] Exception occurred while processing MOTAC menu JSON: ' . $e->getMessage());
            }
        } else {
            Log::warning('[MenuServiceProvider] MOTAC menu file not found: ' . $motacMenuPath);
        }

        // Share the $menuDataObject (which has a ->menu property as an array of items)
        // This $menuData will be globally available in all Blade views.
        View::share('menuData', $menuDataObject);
        Log::debug('[MenuServiceProvider] MOTAC menuData shared with views: ' . json_encode($menuDataObject));
    }
}
