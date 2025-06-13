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
    'locale' => env('APP_LOCALE', 'ms'),

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
    | EDITED: Added a 'flag' key to make the component logic simpler.
    | This is now the single source of truth for locale data.
    */
    'available_locales' => [
        'ms' => [
            'name' => 'Bahasa Melayu',
            'script' => 'Latn',
            'native' => 'Bahasa Melayu',
            'regional' => 'ms_MY',
            'flag' => 'my' // Flag code for Malaysia
        ],
        'en' => [
            'name' => 'English',
            'script' => 'Latn',
            'native' => 'English',
            'regional' => 'en_US',
            'flag' => 'us' // Flag code for USA
        ],
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
    */
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers Below
         */
        Livewire\LivewireServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\MenuServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,
        App\Providers\QueryLogServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    */
    'aliases' => Facade::defaultAliases()->merge([
        'Helper' => App\Helpers\Helpers::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Date & Datetime Display Formats (Custom for MOTAC)
    |--------------------------------------------------------------------------
    */
    'date_format_my_short' => 'd M Y',           // Example: 28 Mei 2025
    'date_format_my_long' => 'j F Y, l',        // Example: 28 Mei 2025, Rabu
    'datetime_format_my' => 'd M Y, h:i A',     // Example: 28 Mei 2025, 10:30 PG
];
