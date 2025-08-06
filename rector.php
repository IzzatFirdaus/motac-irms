<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
//use Rector\Laravel\Set\LaravelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
// Ensure the package is installed; otherwise, comment this line
use Driftingly\RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Define paths to refactor
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        // __DIR__ . '/src', // Keep this commented unless you actually have a 'src' directory
        __DIR__.'/tests',
    ]);

    // Apply sets of rules
    $rectorConfig->sets([
        // PHP upgrades - keep these active
        SetList::PHP_82, // Your composer.json requires ^8.2
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        SetList::STRICT_BOOLEANS,
        SetList::INSTANCEOF,
        SetList::CODING_STYLE,

        // Laravel upgrades: UNCOMMENT THIS LINE for Laravel 11
        Driftingly\RectorLaravel\Set\LaravelSetList::LARAVEL_11, // Adjust this as needed for your Laravel version

        // If you are using Symfony components, you can include their sets too
        // SymfonySetList::SYMFONY_60,
        // SymfonySetList::SYMFONY_Casts_ATTRIBUTES,
        // SymfonySetList::SYMFONY_CODE_QUALITY,
    ]);

    // Optional: Exclude specific files or directories
    $rectorConfig->skip([
        // Paths that Rector should not touch
        __DIR__.'/vendor', // Always exclude vendor!
        __DIR__.'/storage', // Typically exclude storage
        __DIR__.'/bootstrap/cache', // Exclude cached files
        __DIR__.'/public/build', // Exclude build assets

        // !!! IMPORTANT: Try to re-enable this file.
        // Comment out this line to allow Rector to process it again,
        // especially after clearing Rector's cache.
        // __DIR__.'/app/Livewire/ResourceManagement/EmailAccount/ApplicationForm.php',
    ]);

    // Optional: Configure caching for faster subsequent runs
    $rectorConfig->cacheDirectory(__DIR__.'/.rector_cache');

    // Optional: Turn off reporting rule names for less verbose output
    // $rectorConfig->withShortName();
};
