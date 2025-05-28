<?php
// TEMPORARY DEBUGGING - REMOVE AFTER CHECKING
var_dump('APP_URL from config: ' . env('APP_URL', 'http://localhost'));
var_dump('ASSET_URL from config: ' . env('ASSET_URL'));
// You can also try to see the fully resolved config if the app boots far enough
// var_dump(config('app.url'), config('app.asset_url'));
// die('Checking config values from config/app.php'); 


use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

  /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    | Design Language: Prominent MOTAC Branding
    */
  'name' => env('APP_NAME', 'Sistem Pengurusan Sumber MOTAC'), // MOTAC specific name [cite: 9]

  /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */
  'env' => env('APP_ENV', 'production'),

  /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    */
  'debug' => (bool) env('APP_DEBUG', false),

  /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
  'url' => env('APP_URL', 'http://localhost'),
  'asset_url' => env('ASSET_URL'),

  /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    | System Design Reference: 3.3 AppServiceProvider sets 'Asia/Kuala_Lumpur'.
    */
  'timezone' => env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'), // MOTAC specific timezone [cite: 9]

  /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    | Design Language: Bahasa Melayu as Primary Language
    */
  'locale' => env('APP_LOCALE', 'my'), // MOTAC default: Bahasa Melayu [cite: 9]

  /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    */
  'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'), // English as fallback [cite: 9]

  /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    | Used by LanguageController and LocaleMiddleware.
    | 'my' for Bahasa Melayu, 'en' for English. 'ar' for Arabic (optional).
    | Added 'display' flag for UI control.
    | System Design Reference: LanguageController.php, LocaleMiddleware.php
    */
  'available_locales' => [
    'my' => ['name' => 'Bahasa Melayu', 'script' => 'Latn', 'native' => 'Bahasa Melayu', 'regional' => 'ms_MY', 'direction' => 'ltr', 'display' => true],
    'en' => ['name' => 'English',       'script' => 'Latn', 'native' => 'English',       'regional' => 'en_US', 'direction' => 'ltr', 'display' => true],
    // 'ar' => ['name' => 'العربية',       'script' => 'Arab', 'native' => 'العربية',       'regional' => 'ar_AE', 'direction' => 'rtl', 'display' => true], // Example for Arabic
  ],

  /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    */
  'faker_locale' => env('APP_FAKER_LOCALE', 'ms_MY'), // For Malaysian context data

  /*
    |--------------------------------------------------------------------------
    | Encryption Key & Cipher
    |--------------------------------------------------------------------------
    */
  'key' => env('APP_KEY'),
  'cipher' => 'AES-256-CBC',

  /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    */
  'maintenance' => [
    'driver' => env('MAINTENANCE_DRIVER', 'file'),
    // 'store'  => 'redis', // Example if using cache driver
  ],

  /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    | System Design Reference: 3.3 Core Providers and Configuration, 9.7 Shared Components
    */
  'providers' => ServiceProvider::defaultProviders()->merge([
    /*
     * Package Service Providers Below
     */
    // Illuminate\Auth\AuthServiceProvider::class, // Example of a Laravel Framework Provider (KEEP ENABLED INITIALLY)
    // Illuminate\Broadcasting\BroadcastServiceProvider::class, // KEEP ENABLED INITIALLY
    // ... other Illuminate providers ... // KEEP ENABLED INITIALLY

    // Spatie\Permission\PermissionServiceProvider::class, // ==> TEMPORARILY COMMENT OUT

    /*
     * Application Service Providers...
     */
    // App\Providers\AppServiceProvider::class,          // ==> TEMPORARILY COMMENT OUT
    // App\Providers\AuthServiceProvider::class,         // ==> TEMPORARILY COMMENT OUT
    // App\Providers\BroadcastServiceProvider::class,    // This is your app's one, if different from Illuminate's
    // App\Providers\EventServiceProvider::class,        // ==> TEMPORARILY COMMENT OUT
    // App\Providers\FortifyServiceProvider::class,      // ==> TEMPORARILY COMMENT OUT (if listed and not part of defaultProviders)
    // App\Providers\JetstreamServiceProvider::class,  // ==> TEMPORARILY COMMENT OUT (if listed and not part of defaultProviders)
    // App\Providers\MenuServiceProvider::class,         // ==> TEMPORARILY COMMENT OUT
    // App\Providers\RouteServiceProvider::class,        // ==> TEMPORARILY COMMENT OUT
    // App\Providers\QueryLogServiceProvider::class,     // ==> TEMPORARILY COMMENT OUT (even the modified one)
    // App\Providers\TelescopeServiceProvider::class,    // Already commented in your file

  ])->toArray(),

  /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    | System Design Reference: 9.7 Shared Components (Helper alias).
    */
  'aliases' => Facade::defaultAliases()->merge([
    'Helper' => App\Helpers\Helpers::class,
    // 'Excel' => Maatwebsite\Excel\Facades\Excel::class, // Example if using Laravel Excel
  ])->toArray(),

  /*
    |--------------------------------------------------------------------------
    | Date & Datetime Display Formats (Custom for MOTAC)
    |--------------------------------------------------------------------------
    | System Design (Section 3.3) for consistent date display.
    | Use with Carbon's translatedFormat() e.g., $date->translatedFormat(config('app.date_format_my_short'))
    */
  'date_format_my_short' => 'd M Y',           // Example: 28 Mei 2025
  'date_format_my_long' => 'j F Y, l',        // Example: 28 Mei 2025, Rabu
  'datetime_format_my' => 'd M Y, h:i A',     // Example: 28 Mei 2025, 10:30 PG
];
