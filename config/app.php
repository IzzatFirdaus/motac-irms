<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name & Branding
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'Sistem Pengurusan Sumber MOTAC'),

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
    | Application URL & Asset URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    */
    'locale' => env('APP_LOCALE', 'ms'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    | List of available locales with metadata for language switching and UI.
    */
    'available_locales' => [
        'ms' => [
            'name' => 'Bahasa Melayu',
            'script' => 'Latn',
            'native' => 'Bahasa Melayu',
            'regional' => 'ms_MY',
            'flag' => 'my',
            'flag_code' => 'my',
            'direction' => 'ltr',
            'key' => 'ms'
        ],
        'en' => [
            'name' => 'English',
            'script' => 'Latn',
            'native' => 'English',
            'regional' => 'en_US',
            'flag' => 'us',
            'flag_code' => 'us',
            'direction' => 'ltr',
            'key' => 'en'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    */
    'faker_locale' => env('APP_FAKER_LOCALE', 'ms_MY'),

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
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    */
    'providers' => ServiceProvider::defaultProviders()->merge([
        Livewire\LivewireServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\MenuServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,
        App\Providers\QueryLogServiceProvider::class,
        App\Providers\TranslationServiceProvider::class,
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
    | Date & Datetime Display Formats
    |--------------------------------------------------------------------------
    */
    'date_formats' => [
        'date_format_my_short' => 'd M Y',
        'date_format_my_long' => 'j F Y, l',
        'datetime_format_my' => 'd M Y, h:i A',
        'time_format_24h' => 'H:i',
        'time_format_12h' => 'h:i A',
        'full_datetime' => 'd M Y \a\t h:i A',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Application Settings (MOTAC IRMS Specific)
    |--------------------------------------------------------------------------
    */
    'custom_settings' => [
        'organization_name' => 'Kementerian Pelancongan, Seni dan Budaya Malaysia',
        'organization_short_name' => 'MOTAC',
        'division_name' => 'Bahagian Pengurusan Maklumat',
        'division_short_name' => 'BPM',
        'system_description' => 'Sistem Pengurusan Sumber Bersepadu MOTAC untuk pengurusan pinjaman peralatan ICT dan sistem meja bantuan.',
        'system_version' => env('APP_VERSION', '4.0.0'),
        'default_language' => 'ms',
        'branding' => [
            'primary_color' => '#0047AB',
            'secondary_color' => '#FFD700',
            'accent_color' => '#28a745',
            'warning_color' => '#ffc107',
            'danger_color' => '#dc3545',
            'logo_url' => env('APP_LOGO_URL', '/assets/img/motac-logo.png'),
            'favicon_url' => env('APP_FAVICON_URL', '/assets/img/favicon.ico'),
            'theme' => env('APP_THEME', 'theme-motac'),
        ],
        'contact' => [
            'email' => env('CONTACT_EMAIL', 'bpm@motac.gov.my'),
            'phone' => env('CONTACT_PHONE', '+603-8891 7200'),
            'address' => 'Tingkat 10, Blok D, Kompleks Kerja Raya, Jalan Sultan Salahuddin, 50580 Kuala Lumpur',
            'office_hours' => 'Isnin - Jumaat: 8:00 AM - 5:00 PM',
        ],
        'limits' => [
            'max_loan_duration_days' => 90,
            'max_file_upload_size_mb' => 2,
            'session_timeout_minutes' => 120,
            'max_equipment_per_loan' => 10,
        ],
        // Helpdesk-specific settings
        'helpdesk' => [
            'default_category' => env('HELPDESK_DEFAULT_CATEGORY', 'General'),
            'default_priority' => env('HELPDESK_DEFAULT_PRIORITY', 'Medium'),
            'support_email' => env('HELPDESK_SUPPORT_EMAIL', 'helpdesk@motac.gov.my'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation System Configuration
    |--------------------------------------------------------------------------
    */
    'translation' => [
        'use_suffixed_files' => true,
        'cache_translations' => env('CACHE_TRANSLATIONS', true),
        'fallback_behavior' => 'graceful',
        'log_missing_keys' => env('LOG_MISSING_TRANSLATIONS', true),
    ],
];
