<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

  /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    | Design Language: Prominent MOTAC Branding
    */
  'name' => env('APP_NAME', 'Sistem Pengurusan Sumber MOTAC'), // MOTAC specific name

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
  'timezone' => env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'), // MOTAC specific timezone

  /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    | Design Language: Bahasa Melayu as Primary Language
    */
  'locale' => env('APP_LOCALE', 'my'), // MOTAC default: Bahasa Melayu

  /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    */
  'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'), // English as fallback

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
    // Illuminate\Auth\AuthServiceProvider::class,
    // Illuminate\Broadcasting\BroadcastServiceProvider::class,
    // ... other Illuminate providers ...

    // Spatie\Permission\PermissionServiceProvider::class,

    // Add Livewire Service Provider here
    Livewire\LivewireServiceProvider::class, // UNCOMMENTED/ADDED THIS LINE

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    // App\Providers\QueryLogServiceProvider::class,
    // App\Providers\TelescopeServiceProvider::class,
    // Add any other custom application service providers that were commented out
    // based on your project's needs. For example:
    // App\Providers\FortifyServiceProvider::class,
    // App\Providers\JetstreamServiceProvider::class,
    // App\Providers\MenuServiceProvider::class,

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
