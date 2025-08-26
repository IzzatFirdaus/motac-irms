<?php

declare(strict_types=1);

/**
 * MOTAC IRMS - Rector Configuration
 * ---------------------------------
 * This Rector config is tailored for the MOTAC Integrated Resource Management System (MOTAC_IRMS).
 * It enforces consistent code quality, modern PHP practices, and Laravel upgrade rules.
 * Adjust paths, sets, and skips as your system evolves.
 */

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

// If you use Rector Laravel sets, require and use the correct package and class.
// Example: use Rector\Laravel\Set\LaravelSetList;
// You can use Symfony sets if you use Symfony components
// use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    // Specify the project directories to refactor
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        // __DIR__ . '/src', // Uncomment if you have a 'src' directory
        __DIR__.'/tests',
    ]);

    // Apply relevant Rector rule sets for MOTAC IRMS
    $rectorConfig->sets([
        // PHP upgrades for compatibility with PHP 8.2+
        SetList::PHP_82,
        SetList::CODE_QUALITY,         // Improve overall code quality
        SetList::DEAD_CODE,            // Remove unused code
        SetList::TYPE_DECLARATION,     // Add or improve type declarations
        SetList::PRIVATIZATION,        // Make things private where possible
        SetList::EARLY_RETURN,         // Use early return where beneficial
        SetList::STRICT_BOOLEANS,      // Use strict boolean checks
        SetList::INSTANCEOF,           // Modernize instanceof usage
        SetList::CODING_STYLE,         // Enforce consistent coding style

        // Laravel upgrade and modernization rules
        // Add Laravel Rector sets here if you have the correct package installed.
        // Example: LaravelSetList::LARAVEL_110,

        // If you use Symfony, you may enable these:
        // SymfonySetList::SYMFONY_60,
        // SymfonySetList::SYMFONY_Casts_ATTRIBUTES,
        // SymfonySetList::SYMFONY_CODE_QUALITY,
    ]);

    // Exclude files/directories that should not be refactored
    $rectorConfig->skip([
        __DIR__.'/vendor',            // Always exclude vendor!
        __DIR__.'/storage',           // Exclude storage (runtime data)
        __DIR__.'/bootstrap/cache',   // Exclude cached files
        __DIR__.'/public/build',      // Exclude frontend build output

        // If you want to temporarily exclude a file from Rector, add it here.
        // Example:
        // __DIR__ . '/app/Livewire/ResourceManagement/EmailAccount/ApplicationForm.php',
    ]);

    // Use a local cache directory for faster repeated runs
    $rectorConfig->cacheDirectory(__DIR__.'/.rector_cache');

    // Optionally, reduce output verbosity by showing only short rule names
    // $rectorConfig->withShortName();
};
