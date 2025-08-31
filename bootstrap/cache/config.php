<?php return array (
  'concurrency' => 
  array (
    'default' => 'process',
  ),
  'activitylog' => 
  array (
    'enabled' => true,
    'delete_records_older_than_days' => 365,
    'default_log_name' => 'default',
    'default_auth_driver' => NULL,
    'subject_returns_soft_deleted_models' => false,
    'activity_model' => 'Spatie\\Activitylog\\Models\\Activity',
    'table_name' => 'activity_log',
    'database_connection' => NULL,
  ),
  'app' => 
  array (
    'name' => 'MOTAC ICT LOAN HRMS',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://127.0.0.1/',
    'frontend_url' => 'http://localhost:3000',
    'asset_url' => NULL,
    'timezone' => 'Asia/Kuala_Lumpur',
    'locale' => 'ms',
    'fallback_locale' => 'en',
    'faker_locale' => 'ms_MY',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:ha7uUjsalQOUhd5Ey5SrVC6BmNzr84Qa8AVrEl5AgVc=',
    'previous_keys' => 
    array (
    ),
    'maintenance' => 
    array (
      'driver' => 'file',
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Concurrency\\ConcurrencyServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      16 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      17 => 'Illuminate\\Queue\\QueueServiceProvider',
      18 => 'Illuminate\\Redis\\RedisServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Livewire\\LivewireServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
      24 => 'App\\Providers\\AuthServiceProvider',
      25 => 'App\\Providers\\BroadcastServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'App\\Providers\\MenuServiceProvider',
      29 => 'App\\Providers\\FortifyServiceProvider',
      30 => 'App\\Providers\\JetstreamServiceProvider',
      31 => 'App\\Providers\\QueryLogServiceProvider',
      32 => 'App\\Providers\\TranslationServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Concurrency' => 'Illuminate\\Support\\Facades\\Concurrency',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Context' => 'Illuminate\\Support\\Facades\\Context',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Js' => 'Illuminate\\Support\\Js',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Number' => 'Illuminate\\Support\\Number',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Process' => 'Illuminate\\Support\\Facades\\Process',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schedule' => 'Illuminate\\Support\\Facades\\Schedule',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Uri' => 'Illuminate\\Support\\Uri',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Vite' => 'Illuminate\\Support\\Facades\\Vite',
      'Helper' => 'App\\Helpers\\Helpers',
    ),
    'available_locales' => 
    array (
      'ms' => 
      array (
        'name' => 'Bahasa Melayu',
        'script' => 'Latn',
        'native' => 'Bahasa Melayu',
        'regional' => 'ms_MY',
        'flag' => 'my',
        'flag_code' => 'my',
        'direction' => 'ltr',
        'key' => 'ms',
      ),
      'en' => 
      array (
        'name' => 'English',
        'script' => 'Latn',
        'native' => 'English',
        'regional' => 'en_US',
        'flag' => 'us',
        'flag_code' => 'us',
        'direction' => 'ltr',
        'key' => 'en',
      ),
    ),
    'date_formats' => 
    array (
      'date_format_my_short' => 'd M Y',
      'date_format_my_long' => 'j F Y, l',
      'datetime_format_my' => 'd M Y, h:i A',
      'time_format_24h' => 'H:i',
      'time_format_12h' => 'h:i A',
      'full_datetime' => 'd M Y \\a\\t h:i A',
    ),
    'custom_settings' => 
    array (
      'organization_name' => 'Kementerian Pelancongan, Seni dan Budaya Malaysia',
      'organization_short_name' => 'MOTAC',
      'division_name' => 'Bahagian Pengurusan Maklumat',
      'division_short_name' => 'BPM',
      'system_description' => 'Sistem Pengurusan Sumber Bersepadu MOTAC untuk pengurusan pinjaman peralatan ICT dan sistem meja bantuan.',
      'system_version' => '4.0.0',
      'default_language' => 'ms',
      'branding' => 
      array (
        'primary_color' => '#0047AB',
        'secondary_color' => '#FFD700',
        'accent_color' => '#28a745',
        'warning_color' => '#ffc107',
        'danger_color' => '#dc3545',
        'logo_url' => '/assets/img/motac-logo.png',
        'favicon_url' => '/assets/img/favicon.ico',
        'theme' => 'theme-motac',
      ),
      'contact' => 
      array (
        'email' => 'bpm@motac.gov.my',
        'phone' => '+603-8891 7200',
        'address' => 'Tingkat 10, Blok D, Kompleks Kerja Raya, Jalan Sultan Salahuddin, 50580 Kuala Lumpur',
        'office_hours' => 'Isnin - Jumaat: 8:00 AM - 5:00 PM',
      ),
      'limits' => 
      array (
        'max_loan_duration_days' => 90,
        'max_file_upload_size_mb' => 2,
        'session_timeout_minutes' => 120,
        'max_equipment_per_loan' => 10,
      ),
      'helpdesk' => 
      array (
        'default_category' => 'General',
        'default_priority' => 'Medium',
        'support_email' => 'helpdesk@motac.gov.my',
      ),
    ),
    'translation' => 
    array (
      'use_suffixed_files' => true,
      'cache_translations' => true,
      'fallback_behavior' => 'graceful',
      'log_missing_keys' => true,
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'sanctum' => 
      array (
        'driver' => 'sanctum',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'reverb' => 
      array (
        'driver' => 'reverb',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => NULL,
          'port' => 443,
          'scheme' => 'https',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => 'api-mt1.pusher.com',
          'port' => 443,
          'scheme' => 'https',
          'encrypted' => true,
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => NULL,
        'secret' => NULL,
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      '|-microscope-|' => 
      array (
        'driver' => 'file',
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\framework\\cache\\microscope',
      ),
    ),
    'prefix' => 'motac_ict_loan_hrms_cache_',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'custom' => 
  array (
    'custom' => 
    array (
      'myLayout' => 'vertical',
      'myTheme' => 'theme-motac',
      'myStyle' => 'light',
      'myRTLSupport' => true,
      'myRTLMode' => false,
      'hasCustomizer' => false,
      'displayCustomizer' => false,
      'menuFixed' => true,
      'menuCollapsed' => false,
      'navbarFixed' => true,
      'navbarDetached' => false,
      'footerFixed' => false,
      'showDropdownOnHover' => true,
      'container' => 'container-fluid',
      'containerNav' => 'container-fluid',
      'contentNavbar' => true,
      'isMenu' => true,
      'isNavbar' => true,
      'isFooter' => true,
      'isFlex' => false,
      'primaryColor' => '#0055A4',
      'customizerControls' => 
      array (
        0 => 'style',
        1 => 'layoutType',
        2 => 'menuFixed',
        3 => 'menuCollapsed',
        4 => 'layoutNavbarFixed',
        5 => 'layoutFooterFixed',
      ),
    ),
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'motac_irms',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'motac_irms',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'mariadb' => 
      array (
        'driver' => 'mariadb',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'motac_irms',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'motac_irms',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'motac_irms',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
      'mysql_test' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'motac_irms_test',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'motac_ict_loan_hrms_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'hide_empty_tabs' => true,
    'except' => 
    array (
      0 => 'telescope*',
      1 => 'horizon*',
    ),
    'storage' => 
    array (
      'enabled' => true,
      'open' => false,
      'driver' => 'file',
      'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\debugbar',
      'connection' => NULL,
      'provider' => '',
      'hostname' => '127.0.0.1',
      'port' => 2304,
    ),
    'editor' => 'phpstorm',
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'ajax_handler_auto_show' => true,
    'ajax_handler_enable_tab' => true,
    'defer_datasets' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => false,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
      'cache' => false,
      'models' => true,
      'livewire' => true,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'backtrace_exclude_paths' => 
        array (
        ),
        'timeline' => false,
        'duration_background' => true,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => false,
        'show_copy' => false,
        'slow_threshold' => false,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'timeline' => false,
        'data' => false,
        'exclude_paths' => 
        array (
        ),
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
      'cache' => 
      array (
        'values' => true,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_middleware' => 
    array (
    ),
    'route_domain' => NULL,
    'theme' => 'auto',
    'debug_backtrace_limit' => 50,
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
        'output_encoding' => '',
        'test_auto_detect' => true,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => true,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => NULL,
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
      'default_ttl' => 10800,
    ),
    'transactions' => 
    array (
      'handler' => 'db',
      'db' => 
      array (
        'connection' => NULL,
      ),
    ),
    'temporary_files' => 
    array (
      'local_path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\framework/cache/laravel-excel',
      'local_permissions' => 
      array (
      ),
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'public',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\app',
        'throw' => false,
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\app/public',
        'url' => 'http://127.0.0.1//storage',
        'visibility' => 'public',
        'throw' => false,
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
        'throw' => false,
      ),
    ),
    'links' => 
    array (
      'C:\\XAMPP\\htdocs\\motac-irms\\public\\storage' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\app/public',
    ),
  ),
  'fortify-options' => 
  array (
    'two-factor-authentication' => 
    array (
      'confirm' => true,
      'confirmPassword' => true,
    ),
  ),
  'fortify' => 
  array (
    'guard' => 'web',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'auth_middleware' => 'auth',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'views' => true,
    'home' => '/dashboard',
    'prefix' => '',
    'domain' => NULL,
    'lowercase_usernames' => true,
    'limiters' => 
    array (
      'login' => 'login',
      'two-factor' => 'two-factor',
    ),
    'paths' => 
    array (
      'login' => NULL,
      'logout' => NULL,
      'password' => 
      array (
        'request' => NULL,
        'reset' => NULL,
        'email' => NULL,
        'update' => NULL,
        'confirm' => NULL,
        'confirmation' => NULL,
      ),
      'register' => NULL,
      'verification' => 
      array (
        'notice' => NULL,
        'verify' => NULL,
        'send' => NULL,
      ),
      'user-profile-information' => 
      array (
        'update' => NULL,
      ),
      'user-password' => 
      array (
        'update' => NULL,
      ),
      'two-factor' => 
      array (
        'login' => NULL,
        'enable' => NULL,
        'confirm' => NULL,
        'disable' => NULL,
        'qr-code' => NULL,
        'secret-key' => NULL,
        'recovery-codes' => NULL,
      ),
    ),
    'redirects' => 
    array (
      'login' => NULL,
      'logout' => NULL,
      'password-confirmation' => NULL,
      'register' => NULL,
      'email-verification' => NULL,
      'password-reset' => NULL,
    ),
    'features' => 
    array (
      0 => 'registration',
      1 => 'reset-passwords',
      2 => 'email-verification',
      3 => 'update-profile-information',
      4 => 'update-passwords',
      5 => 'two-factor-authentication',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 65536,
      'threads' => 1,
      'time' => 4,
    ),
    'rehash_on_login' => true,
  ),
  'ide-helper' => 
  array (
    'filename' => '_ide_helper.php',
    'models_filename' => '_ide_helper_models.php',
    'meta_filename' => '.phpstorm.meta.php',
    'include_fluent' => false,
    'include_factory_builders' => false,
    'write_model_magic_where' => true,
    'write_model_external_builder_methods' => true,
    'write_model_relation_count_properties' => true,
    'write_model_relation_exists_properties' => false,
    'write_eloquent_model_mixins' => false,
    'include_helpers' => false,
    'helper_files' => 
    array (
      0 => 'C:\\XAMPP\\htdocs\\motac-irms/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ),
    'model_locations' => 
    array (
      0 => 'app',
    ),
    'ignored_models' => 
    array (
    ),
    'model_hooks' => 
    array (
    ),
    'extra' => 
    array (
      'Eloquent' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'Illuminate\\Database\\Query\\Builder',
      ),
      'Session' => 
      array (
        0 => 'Illuminate\\Session\\Store',
      ),
    ),
    'magic' => 
    array (
    ),
    'interfaces' => 
    array (
    ),
    'model_camel_case_properties' => false,
    'type_overrides' => 
    array (
      'integer' => 'int',
      'boolean' => 'bool',
    ),
    'include_class_docblocks' => false,
    'force_fqn' => false,
    'use_generics_annotations' => true,
    'macro_default_return_types' => 
    array (
      'Illuminate\\Http\\Client\\Factory' => 'Illuminate\\Http\\Client\\PendingRequest',
    ),
    'additional_relation_types' => 
    array (
    ),
    'additional_relation_return_types' => 
    array (
    ),
    'enforce_nullable_relationships' => true,
    'post_migrate' => 
    array (
    ),
  ),
  'jetstream' => 
  array (
    'stack' => 'livewire',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'features' => 
    array (
      0 => 'profile-photos',
      1 => 'api',
    ),
    'profile_photo_disk' => 'public',
    'auth_session' => 'Laravel\\Jetstream\\Http\\Middleware\\AuthenticateSession',
    'guard' => 'sanctum',
  ),
  'livewire' => 
  array (
    'class_namespace' => 'App\\Livewire',
    'view_path' => 'C:\\XAMPP\\htdocs\\motac-irms\\resources\\views/livewire',
    'layout' => 'livewire/layouts/app',
    'lazy_placeholder' => NULL,
    'temporary_file_upload' => 
    array (
      'disk' => NULL,
      'rules' => NULL,
      'directory' => NULL,
      'middleware' => NULL,
      'preview_mimes' => 
      array (
        0 => 'png',
        1 => 'gif',
        2 => 'bmp',
        3 => 'svg',
        4 => 'wav',
        5 => 'mp4',
        6 => 'mov',
        7 => 'avi',
        8 => 'wmv',
        9 => 'mp3',
        10 => 'm4a',
        11 => 'jpg',
        12 => 'jpeg',
        13 => 'mpga',
        14 => 'webp',
        15 => 'wma',
      ),
      'max_upload_time' => 5,
    ),
    'render_on_redirect' => false,
    'legacy_model_binding' => false,
    'inject_assets' => true,
    'navigate' => 
    array (
      'show_progress_bar' => true,
      'progress_bar_color' => '#2299dd',
    ),
    'inject_morph_markers' => true,
    'pagination_theme' => 'bootstrap',
  ),
  'log-viewer' => 
  array (
    'enabled' => true,
    'api_only' => false,
    'require_auth_in_production' => true,
    'route_domain' => NULL,
    'route_path' => 'log-viewer',
    'assets_path' => 'vendor/log-viewer',
    'back_to_system_url' => 'http://127.0.0.1/',
    'back_to_system_label' => NULL,
    'timezone' => NULL,
    'datetime_format' => 'Y-m-d H:i:s',
    'middleware' => 
    array (
      0 => 'web',
      1 => 'auth:sanctum',
      2 => 'App\\Http\\Middleware\\ViewLogs',
      3 => 'Opcodes\\LogViewer\\Http\\Middleware\\AuthorizeLogViewer',
    ),
    'api_middleware' => 
    array (
      0 => 'Opcodes\\LogViewer\\Http\\Middleware\\EnsureFrontendRequestsAreStateful',
      1 => 'Opcodes\\LogViewer\\Http\\Middleware\\AuthorizeLogViewer',
    ),
    'api_stateful_domains' => NULL,
    'hosts' => 
    array (
      'local' => 
      array (
        'name' => 'Local',
      ),
    ),
    'include_files' => 
    array (
      0 => '*.log',
      1 => '**/*.log',
      2 => '/var/log/httpd/*',
      3 => '/var/log/nginx/*',
      4 => '/opt/homebrew/var/log/nginx/*',
      5 => '/opt/homebrew/var/log/httpd/*',
      6 => '/opt/homebrew/var/log/php-fpm.log',
      7 => '/opt/homebrew/var/log/postgres*log',
      8 => '/opt/homebrew/var/log/redis*log',
      9 => '/opt/homebrew/var/log/supervisor*log',
    ),
    'exclude_files' => 
    array (
    ),
    'hide_unknown_files' => true,
    'shorter_stack_trace_excludes' => 
    array (
      0 => '/vendor/symfony/',
      1 => '/vendor/laravel/framework/',
      2 => '/vendor/barryvdh/laravel-debugbar/',
    ),
    'cache_driver' => NULL,
    'cache_key_prefix' => 'lv',
    'lazy_scan_chunk_size_in_mb' => 50,
    'strip_extracted_context' => true,
    'per_page_options' => 
    array (
      0 => 10,
      1 => 25,
      2 => 50,
      3 => 100,
      4 => 250,
      5 => 500,
    ),
    'defaults' => 
    array (
      'use_local_storage' => true,
      'folder_sorting_method' => 'ModifiedTime',
      'folder_sorting_order' => 'desc',
      'log_sorting_order' => 'desc',
      'per_page' => 25,
      'theme' => 'System',
      'shorter_stack_traces' => false,
    ),
    'exclude_ip_from_identifiers' => false,
    'root_folder_prefix' => 'root',
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 
    array (
      'channel' => NULL,
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
          1 => 'daily',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\logs/laravel.log',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
        'replace_placeholders' => true,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
        'facility' => 8,
        'replace_placeholders' => true,
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\logs/laravel.log',
      ),
      'browser' => 
      array (
        'driver' => 'single',
        'path' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\logs/browser.log',
        'level' => 'debug',
        'days' => 14,
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => '127.0.0.1',
        'port' => '25',
        'encryption' => NULL,
        'username' => NULL,
        'password' => NULL,
        'timeout' => NULL,
        'local_domain' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'resend' => 
      array (
        'transport' => 'resend',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
      ),
      'roundrobin' => 
      array (
        'transport' => 'roundrobin',
        'mailers' => 
        array (
          0 => 'ses',
          1 => 'postmark',
        ),
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
    ),
    'from' => 
    array (
      'address' => 'noreply@motac.gov.my',
      'name' => 'MOTAC ICT LOAN HRMS',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'C:\\XAMPP\\htdocs\\motac-irms\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'menu' => 
  array (
    'menu' => 
    array (
      0 => 
      array (
        'menuHeader' => 'menu.section.public',
        'guestOnly' => true,
      ),
      1 => 
      array (
        'url' => '/',
        'name' => 'menu.home',
        'icon' => 'house',
        'guestOnly' => true,
      ),
      2 => 
      array (
        'url' => '/contact-us',
        'name' => 'menu.contact_us',
        'icon' => 'envelope',
        'guestOnly' => true,
      ),
      3 => 
      array (
        'url' => '/login',
        'name' => 'menu.login',
        'icon' => 'box-arrow-in-right',
        'guestOnly' => true,
      ),
      4 => 
      array (
        'url' => '/dashboard',
        'name' => 'menu.dashboard',
        'icon' => 'house-door-fill',
        'routeName' => 'dashboard',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'BPM Staff',
          2 => 'IT Admin',
          3 => 'User',
          4 => 'Approver',
          5 => 'HOD',
        ),
      ),
      5 => 
      array (
        'menuHeader' => 'menu.section.resource_management',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'BPM Staff',
          2 => 'IT Admin',
          3 => 'User',
          4 => 'Approver',
          5 => 'HOD',
        ),
      ),
      6 => 
      array (
        'name' => 'menu.my_applications.title',
        'icon' => 'folder-check',
        'routeNamePrefix' => 'loan-applications.index,helpdesk-tickets.index',
        'role' => 
        array (
          0 => 'User',
          1 => 'Admin',
          2 => 'BPM Staff',
          3 => 'IT Admin',
          4 => 'Approver',
          5 => 'HOD',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'url' => '/loan-applications',
            'name' => 'menu.my_applications.loan',
            'icon' => 'laptop-fill',
            'routeName' => 'loan-applications.index',
          ),
          1 => 
          array (
            'url' => '/helpdesk-tickets',
            'name' => 'menu.my_applications.helpdesk',
            'icon' => 'life-preserver',
            'routeName' => 'helpdesk-tickets.index',
          ),
        ),
      ),
      7 => 
      array (
        'name' => 'menu.apply_for_resources.title',
        'icon' => 'file-earmark-plus-fill',
        'role' => 
        array (
          0 => 'User',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'url' => '/loan-applications/create',
            'name' => 'menu.apply_for_resources.loan',
            'icon' => 'box-arrow-up-right',
            'routeName' => 'loan-applications.create',
          ),
          1 => 
          array (
            'url' => '/helpdesk-tickets/create',
            'name' => 'menu.apply_for_resources.helpdesk_ticket',
            'icon' => 'ticket-fill',
            'routeName' => 'helpdesk-tickets.create',
          ),
        ),
      ),
      8 => 
      array (
        'name' => 'menu.approvals.title',
        'icon' => 'clipboard-check-fill',
        'routeName' => 'approvals.dashboard',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'Approver',
          2 => 'HOD',
        ),
        'submenu' => 
        array (
        ),
      ),
      9 => 
      array (
        'name' => 'menu.resource_inventory.title',
        'icon' => 'boxes',
        'routeNamePrefix' => 'equipment.index,equipment.show,equipment.create,equipment.edit,loan-transactions.index,loan-transactions.show',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'BPM Staff',
          2 => 'IT Admin',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'url' => '/equipment',
            'name' => 'menu.resource_inventory.equipment',
            'icon' => 'tools',
            'routeName' => 'equipment.index',
          ),
          1 => 
          array (
            'url' => '/loan-transactions',
            'name' => 'menu.resource_inventory.loan_transactions',
            'icon' => 'exchange',
            'routeName' => 'loan-transactions.index',
          ),
        ),
      ),
      10 => 
      array (
        'menuHeader' => 'menu.section.reports_analytics',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'BPM Staff',
          2 => 'IT Admin',
        ),
      ),
      11 => 
      array (
        'name' => 'menu.reports.title',
        'icon' => 'file-earmark-bar-graph-fill',
        'routeNamePrefix' => 'reports.',
        'role' => 
        array (
          0 => 'Admin',
          1 => 'BPM Staff',
          2 => 'IT Admin',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'url' => '/reports/equipment-inventory',
            'name' => 'menu.reports.equipment_report',
            'icon' => 'card-list',
            'routeName' => 'reports.equipment-inventory',
          ),
          1 => 
          array (
            'url' => '/reports/loan-applications',
            'name' => 'menu.reports.loan_applications_report',
            'icon' => 'journal-text',
            'routeName' => 'reports.loan-applications',
          ),
          2 => 
          array (
            'url' => '/reports/helpdesk-tickets',
            'name' => 'menu.reports.helpdesk_report',
            'icon' => 'life-preserver',
            'routeName' => 'reports.helpdesk-tickets',
          ),
          3 => 
          array (
            'url' => '/reports/activity-log',
            'name' => 'menu.reports.user_activity_report',
            'icon' => 'person-check-fill',
            'routeName' => 'reports.activity-log',
          ),
          4 => 
          array (
            'url' => '/reports/loan-history',
            'name' => 'menu.reports.loan_history_report',
            'icon' => 'clock-history',
            'routeName' => 'reports.loan-history',
          ),
          5 => 
          array (
            'url' => '/reports/utilization-report',
            'name' => 'menu.reports.utilization_report',
            'icon' => 'graph-up',
            'routeName' => 'reports.utilization-report',
          ),
          6 => 
          array (
            'url' => '/reports/loan-status-summary',
            'name' => 'menu.reports.loan_status_summary_report',
            'icon' => 'pie-chart-fill',
            'routeName' => 'reports.loan-status-summary',
          ),
        ),
      ),
      12 => 
      array (
        'menuHeader' => 'menu.section.system_settings',
        'role' => 
        array (
          0 => 'Admin',
        ),
      ),
      13 => 
      array (
        'name' => 'menu.system_settings.title',
        'icon' => 'gear-fill',
        'routeNamePrefix' => 'settings.',
        'role' => 
        array (
          0 => 'Admin',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'url' => '/settings/users',
            'name' => 'menu.system_settings.users',
            'icon' => 'people-fill',
            'routeName' => 'settings.users.index',
          ),
          1 => 
          array (
            'url' => '/settings/roles',
            'name' => 'menu.system_settings.roles',
            'icon' => 'person-badge-fill',
            'routeName' => 'settings.roles.index',
          ),
          2 => 
          array (
            'url' => '/settings/permissions',
            'name' => 'menu.system_settings.permissions',
            'icon' => 'key-fill',
            'routeName' => 'settings.permissions.index',
          ),
          3 => 
          array (
            'url' => '/settings/grades',
            'name' => 'menu.system_settings.grades',
            'icon' => 'award-fill',
            'routeName' => 'settings.grades.index',
          ),
          4 => 
          array (
            'url' => '/settings/departments',
            'name' => 'menu.system_settings.departments',
            'icon' => 'building-fill',
            'routeName' => 'settings.departments.index',
          ),
          5 => 
          array (
            'url' => '/settings/positions',
            'name' => 'menu.system_settings.positions',
            'icon' => 'briefcase-fill',
            'routeName' => 'settings.positions.index',
          ),
        ),
      ),
      14 => 
      array (
        'url' => '/log-viewer',
        'name' => 'menu.system_logs',
        'icon' => 'file-text-fill',
        'routeName' => 'log-viewer.index',
        'target' => '_blank',
        'role' => 
        array (
          0 => 'Admin',
        ),
      ),
    ),
    'version' => '2025-08-06',
    'last_updated' => '2025-08-06T12:19:04Z',
  ),
  'motac' => 
  array (
    'organization_name' => 'MOTAC',
    'organization_full_name' => 'Ministry of Tourism, Arts and Culture Malaysia',
    'approval' => 
    array (
      'min_loan_support_grade_level' => 41,
      'min_general_view_approval_grade_level' => 9,
      'min_loan_general_approver_grade_level' => 44,
    ),
    'grade_options' => 
    array (
      '' => '- Select Grade -',
      1 => 'Minister',
      2 => 'Deputy Minister',
      3 => 'Turus III',
      4 => 'Jusa A',
      5 => 'Jusa B',
      6 => 'Jusa C',
      7 => 'Jusa A',
      8 => 'Jusa B',
      9 => 'Jusa C',
      10 => 'Jusa A',
      11 => 'Jusa B',
      12 => 'Jusa C',
      13 => '(14) 54',
      14 => '(13) 52',
      15 => '(12) 48',
      16 => '14 (54)',
      17 => '13 (52)',
      18 => '(12) 48',
      19 => '14 (54)',
      20 => '13 (52)',
      21 => '12 (48)',
      22 => '14 (54)',
      23 => '13 (52)',
      24 => '(12) 48',
      25 => '14 (54)',
      26 => '13 (52)',
      27 => '12 (48)',
      28 => '10 (44)',
      29 => '9 (41)',
      30 => '14 (54)',
      31 => '13 (52)',
      32 => '12 (48)',
      33 => '14 (54)',
      34 => '13 (52)',
      35 => '12 (48)',
      36 => '10 (44)',
      37 => '9 (41)',
      38 => '14 (54)',
      39 => '13 (52)',
      40 => '12 (48)',
      41 => '10 (44)',
      42 => '9 (41)',
      43 => '14 (54)',
      44 => '13 (52)',
      45 => '12 (48)',
      46 => '14 (54)',
      47 => '13 (52)',
      48 => '12 (48)',
      49 => '10 (44)',
      50 => '9 (41)',
      51 => '14 (54)',
      52 => '13 (52)',
      53 => '12 (48)',
      54 => '10 (44)',
      55 => '9 (41)',
      56 => '14 (54)',
      57 => '13 (52)',
      58 => '12 (48)',
      59 => '10 (44)',
      60 => '9 (41)',
      61 => '13 (52)',
      62 => '12 (48)',
      63 => '13 (52)',
      64 => '12 (48)',
      65 => '10 (44)',
      66 => '9 (41)',
      67 => '14 (54)',
      68 => '13 (52)',
      69 => '12 (48)',
      70 => '10 (44)',
      71 => '9 (41)',
      72 => '14 (53/54)',
      73 => '13 (51/52)',
      74 => '12 (47/48)',
      75 => '10 (43/44)',
      76 => '9 (41/42)',
      77 => '7 (37/38)',
      78 => '6 (31/32)',
      79 => '5 (29/30)',
      80 => '3 (25/26)',
      81 => '2 (21/22)',
      82 => '1 (19)',
      83 => '14 (54)',
      84 => '13 (52)',
      85 => '12 (48)',
      86 => '10 (44)',
      87 => '9 (41)',
      88 => '14 (54)',
      89 => '13 (52)',
      90 => '12 (48)',
      91 => '10 (44)',
      92 => '9 (41)',
      93 => '14 (54)',
      94 => '14 (53/54)',
      95 => '13 (51/52)',
      96 => '12 (47/48)',
      97 => '10 (44)',
      98 => '8 (40)',
      99 => '7 (38)',
      100 => '6 (32)',
      101 => '5 (29)',
      102 => '4 (28)',
      103 => '3 (26)',
      104 => '2 (22)',
      105 => '1 (19)',
      106 => '14 (54)',
      107 => '13 (52)',
      108 => '12 (48)',
      109 => '10 (44)',
      110 => '9 (41)',
      111 => '14 (54)',
      112 => '13 (52)',
      113 => '12 (48)',
      114 => '10 (44)',
      115 => '9 (41)',
      116 => '14 (54)',
      117 => '13 (52)',
      118 => '12 (48)',
      119 => '10 (44)',
      120 => '9 (41)',
      121 => '14 (54)',
      122 => '13 (52)',
      123 => '12 (48)',
      124 => '10 (44)',
      125 => '9 (41)',
      126 => '14 (54)',
      127 => '13 (52)',
      128 => '12 (48)',
      129 => '10 (44)',
      130 => '9 (41)',
      131 => '14 (54)',
      132 => '13 (52)',
      133 => '12 (48)',
      134 => '10 (44)',
      135 => '9 (41)',
      136 => '48',
      137 => '14 (54)',
      138 => '13 (52)',
      139 => '12 (48)',
      140 => '10 (44)',
      141 => '8 (40)',
      142 => '7 (38)',
      143 => '6 (32)',
      144 => '5 (29)',
      145 => '14 (54)',
      146 => '13 (52)',
      147 => '12 (48)',
      148 => '10 (44)',
      149 => '9 (41)',
      150 => '14 (54)',
      151 => '13 (52)',
      152 => '12 (48)',
      153 => '10 (44)',
      154 => '9 (41)',
      155 => '14 (53/54)',
      156 => '13 (51/52)',
      157 => '12 (47/48)',
      158 => '10 (43/44)',
      159 => '9 (41/42)',
      160 => '14 (54)',
      161 => '13 (52)',
      162 => '12 (48)',
      163 => '10 (44)',
      164 => '9 (41)',
      165 => '14 (53/54)',
      166 => '13 (51/52)',
      167 => '12 (47/48)',
      168 => '10 (43/44)',
      169 => '9 (41/42)',
      170 => '7 (37/38)',
      171 => '6 (31/32)',
      172 => '5 (29/30)',
      173 => '3 (25/26)',
      174 => '2 (21/22)',
      175 => '1 (19)',
      176 => '14 (54)',
      177 => '13 (52)',
      178 => '12 (48)',
      179 => '10 (44)',
      180 => '9 (41)',
      181 => '8 (40)',
      182 => '7 (38)',
      183 => '6 (32)',
      184 => '5 (29)',
      185 => '8 (40)',
      186 => '7 (38)',
      187 => '6 (32)',
      188 => '5 (29)',
      189 => '8 (40)',
      190 => '7 (38)',
      191 => '6 (32)',
      192 => '5 (29)',
      193 => '8 (40)',
      194 => '7 (38)',
      195 => '6 (32)',
      196 => '5 (29/30)',
      197 => '8 (40)',
      198 => '7 (38)',
      199 => '6 (32)',
      200 => '5 (29)',
      201 => '8 (40)',
      202 => '7 (38)',
      203 => '6 (32)',
      204 => '5 (29)',
      205 => '8 (40)',
      206 => '7 (38)',
      207 => '6 (32)',
      208 => '5 (29)',
      209 => '8 (40)',
      210 => '7 (38)',
      211 => '6 (32)',
      212 => '5 (29)',
      213 => '8 (40)',
      214 => '7 (38)',
      215 => '6 (32)',
      216 => '5 (29)',
      217 => '8 (40)',
      218 => '7 (38)',
      219 => '6 (32)',
      220 => '5 (29)',
      221 => '8 (40)',
      222 => '7 (38)',
      223 => '6 (32)',
      224 => '5 (29)',
      225 => '8 (40)',
      226 => '7 (38)',
      227 => '6 (32)',
      228 => '5 (29)',
      229 => '8 (40)',
      230 => '7 (38)',
      231 => '6 (32)',
      232 => '5 (29)',
      233 => '8 (40)',
      234 => '7 (38)',
      235 => '6 (32)',
      236 => '5 (29)',
      237 => '8 (40)',
      238 => '7 (38)',
      239 => '6 (32)',
      240 => '5 (29/30)',
      241 => '2 (22)',
      242 => '1 (19)',
      243 => '4 (28)',
      244 => '3 (26)',
      245 => '2 (22)',
      246 => '1 (19)',
      247 => '4 (28)',
      248 => '3 (26)',
      249 => '2 (22)',
      250 => '1 (19)',
      251 => '4 (28)',
      252 => '3 (26)',
      253 => '2 (22)',
      254 => '1 (19)',
      255 => '4 (28)',
      256 => '3 (26)',
      257 => '2 (22)',
      258 => '1 (19)',
      259 => '4 (28)',
      260 => '3 (26)',
      261 => '2 (22)',
      262 => '1 (19)',
      263 => '4 (28)',
      264 => '3 (26)',
      265 => '2 (22)',
      266 => '1 (19)',
      267 => '4 (28)',
      268 => '3 (26)',
      269 => '2 (22)',
      270 => '1 (19)',
      271 => '4 (28)',
      272 => '3 (26)',
      273 => '2 (22)',
      274 => '1 (19)',
      275 => '9 (41)',
      276 => '5 (29)',
      277 => '1 (19)',
      278 => 'Industrial Trainee',
      279 => '1 (19)',
      280 => '2 (22)',
      281 => '3 (26)',
      282 => '4 (28)',
    ),
    'mymail_form_options' => 
    array (
      'unit_options' => 
      array (
        '' => '- Pilih Unit -',
        1 => 'Bahagian Pembangunan Sumber Manusia (BPSM)',
        2 => 'Bahagian Kewangan',
        3 => 'Bahagian Audit Dalam',
        4 => 'Bahagian Pengurusan Maklumat (BPM)',
        5 => 'Unit Komunikasi Korporat (UKK)',
        6 => 'Unit Integriti',
        7 => 'Unit Undang-Undang',
        8 => 'Unit Naziran',
        9 => 'Unit Khidmat Pengurusan',
        10 => 'Unit Perolehan',
        11 => 'Unit Akaun',
        12 => 'Unit Pembangunan',
        13 => 'Unit Pelancongan',
        14 => 'Unit Kebudayaan',
        15 => 'Unit Kesenian',
        16 => 'Unit Warisan',
        17 => 'Unit Sukan',
        18 => 'Unit Antarabangsa',
        19 => 'Unit Penyelidikan & Pembangunan',
        20 => 'Unit Perhubungan Awam',
        21 => 'Perundangan',
        25 => 'Sekretariat Visit Malaysia',
      ),
      'grade_options' => 
      array (
        '' => '- Pilih Gred -',
        'Turus III' => 'Turus III',
        'Jusa A' => 'Jusa A',
        'Jusa B' => 'Jusa B',
        'Jusa C' => 'Jusa C',
        14 => '14',
        13 => '13',
        12 => '12',
        10 => '10',
        9 => '9',
      ),
    ),
    'loan_accessories_list' => 
    array (
      0 => 'Power Cable',
      1 => 'Bag',
      2 => 'Mouse',
      3 => 'HDMI Cable',
      4 => 'User Manual',
      5 => 'Charger',
      6 => 'Keyboard',
      7 => 'Stylus Pen',
    ),
    'notifications' => 
    array (
      'admin_email_recipient' => 'sysadmin@motac.gov.my',
    ),
    'helpdesk' => 
    array (
      'default_category' => 'General',
      'default_priority' => 'Medium',
      'support_email' => 'helpdesk@motac.gov.my',
      'sla_hours_high_priority' => 8,
      'sla_hours_medium_priority' => 24,
      'sla_hours_low_priority' => 72,
    ),
    'date_formats' => 
    array (
      'date_format_my_short' => 'd M Y',
      'date_format_my_long' => 'j F Y, l',
      'datetime_format_my' => 'd M Y, h:i A',
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'App\\Models\\Role',
      'user' => 'App\\Models\\User',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'role_pivot_key' => NULL,
      'permission_pivot_key' => NULL,
      'model_morph_key' => 'model_id',
      'team_foreign_key' => 'team_id',
    ),
    'register_permission_check_method' => true,
    'register_octane_reset_listener' => false,
    'events_enabled' => false,
    'teams' => false,
    'team_resolver' => 'Spatie\\Permission\\DefaultTeamResolver',
    'use_passport_client_credentials' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      \DateInterval::__set_state(array(
         'from_string' => true,
         'date_string' => '24 hours',
      )),
      'key' => 'spatie.permission.cache',
      'store' => 'default',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => NULL,
        'secret' => NULL,
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
    ),
    'batching' => 
    array (
      'database' => 'mysql',
      'table' => 'job_batches',
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'sanctum' => 
  array (
    'stateful' => 
    array (
      0 => '127.0.0.1',
    ),
    'guard' => 
    array (
      0 => 'web',
    ),
    'expiration' => NULL,
    'token_prefix' => '',
    'middleware' => 
    array (
      'authenticate_session' => 'Laravel\\Sanctum\\Http\\Middleware\\AuthenticateSession',
      'encrypt_cookies' => 'App\\Http\\Middleware\\EncryptCookies',
      'verify_csrf_token' => 'App\\Http\\Middleware\\VerifyCsrfToken',
    ),
  ),
  'services' => 
  array (
    'mailtrap-sdk' => 
    array (
      'host' => 'send.api.mailtrap.io',
      'apiKey' => NULL,
      'inboxId' => NULL,
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'resend' => 
    array (
      'key' => NULL,
    ),
    'slack' => 
    array (
      'notifications' => 
      array (
        'bot_user_oauth_token' => NULL,
        'channel' => NULL,
      ),
    ),
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
      'scheme' => 'https',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'motac_ict_loan_hrms_session',
    'path' => '/',
    'domain' => '127.0.0.1',
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
  ),
  'variables' => 
  array (
    'templateName' => 'MOTAC ICT LOAN HRMS',
    'templateDescription' => 'Sistem Dalaman Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia untuk pengurusan sumber bersepadu, pinjaman ICT, dan sistem meja bantuan.',
    'templateKeyword' => 'motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, sistem meja bantuan, kementerian pelancongan seni dan budaya',
    'productPage' => 'http://127.0.0.1/dashboard',
    'documentation' => '#',
    'repositoryUrl' => 'https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS',
    'facebookUrl' => 'https://www.facebook.com/MyMOTAC/',
    'twitterUrl' => 'https://twitter.com/MyMOTAC',
    'instagramUrl' => 'https://www.instagram.com/MyMOTAC',
    'githubUrl' => '#',
    'branding' => 
    array (
      'primary_color' => '#0047AB',
      'secondary_color' => '#FFD700',
      'accent_color' => '#28a745',
      'theme' => 'theme-motac',
      'logo_url' => '/assets/img/motac-logo.png',
      'favicon_url' => '/assets/img/favicon.ico',
    ),
    'helpdesk_support_email' => 'helpdesk@motac.gov.my',
    'helpdesk_default_category' => 'General',
    'helpdesk_default_priority' => 'Medium',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'C:\\XAMPP\\htdocs\\motac-irms\\resources\\views',
    ),
    'compiled' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\framework\\views',
  ),
  'webhook-client' => 
  array (
    'configs' => 
    array (
      0 => 
      array (
        'name' => 'default',
        'signing_secret' => NULL,
        'signature_header_name' => 'Signature',
        'signature_validator' => 'Spatie\\WebhookClient\\SignatureValidator\\DefaultSignatureValidator',
        'webhook_profile' => 'Spatie\\WebhookClient\\WebhookProfile\\ProcessEverythingWebhookProfile',
        'webhook_response' => 'Spatie\\WebhookClient\\WebhookResponse\\DefaultRespondsTo',
        'webhook_model' => 'Spatie\\WebhookClient\\Models\\WebhookCall',
        'store_headers' => 
        array (
        ),
        'process_webhook_job' => '',
      ),
    ),
    'delete_after_days' => 30,
    'add_unique_token_to_route_name' => false,
    'storage_table' => 'webhook_calls',
    'signing_secret' => NULL,
    'signature_header_name' => 'X-Hub-Signature',
    'signature_validator' => 'App\\Validator\\CustomSignatureValidator',
    'store_headers' => '*',
    'process_webhook_job' => NULL,
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'public_path' => NULL,
    'convert_entities' => true,
    'options' => 
    array (
      'font_dir' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\fonts',
      'font_cache' => 'C:\\XAMPP\\htdocs\\motac-irms\\storage\\fonts',
      'temp_dir' => 'C:\\Users\\exatf\\AppData\\Local\\Temp',
      'chroot' => 'C:\\XAMPP\\htdocs\\motac-irms',
      'allowed_protocols' => 
      array (
        'data://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'file://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'http://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'https://' => 
        array (
          'rules' => 
          array (
          ),
        ),
      ),
      'artifactPathValidation' => NULL,
      'log_output_file' => NULL,
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_paper_orientation' => 'portrait',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => false,
      'allowed_remote_hosts' => NULL,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'microscope' => 
  array (
    'is_enabled' => true,
    'no_fix' => false,
    'ignore' => 
    array (
    ),
    'log_unused_view_vars' => true,
    'ignored_namespaces' => 
    array (
    ),
    'class_search_buffer' => 2500,
    'action_comment_template' => 'microscope_package::actions_comment',
    'additional_route_files' => 
    array (
    ),
    'additional_config_paths' => 
    array (
    ),
    'additional_composer_paths' => 
    array (
    ),
    'colors' => 
    array (
      'line_separator' => 'gray',
    ),
  ),
  'boost' => 
  array (
    'enabled' => true,
    'browser_logs_watcher' => true,
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      6 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddContext',
      7 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionHandledStatus',
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
          1 => 'Authorization',
          2 => 'Cookie',
          3 => 'Set-Cookie',
          4 => 'X-CSRF-TOKEN',
          5 => 'X-XSRF-TOKEN',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
      18 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\OpenAiSolutionProvider',
      19 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\SailNetworkSolutionProvider',
      20 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownMysql8CollationSolutionProvider',
      21 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownMariadbCollationSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => 'C:\\XAMPP\\htdocs\\motac-irms',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
    'recorders' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\Recorders\\DumpRecorder\\DumpRecorder',
      1 => 'Spatie\\LaravelIgnition\\Recorders\\JobRecorder\\JobRecorder',
      2 => 'Spatie\\LaravelIgnition\\Recorders\\LogRecorder\\LogRecorder',
      3 => 'Spatie\\LaravelIgnition\\Recorders\\QueryRecorder\\QueryRecorder',
    ),
    'open_ai_key' => NULL,
    'with_stack_frame_arguments' => true,
    'argument_reducers' => 
    array (
      0 => 'Spatie\\Backtrace\\Arguments\\Reducers\\BaseTypeArgumentReducer',
      1 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ArrayArgumentReducer',
      2 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StdClassArgumentReducer',
      3 => 'Spatie\\Backtrace\\Arguments\\Reducers\\EnumArgumentReducer',
      4 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ClosureArgumentReducer',
      5 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeArgumentReducer',
      6 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeZoneArgumentReducer',
      7 => 'Spatie\\Backtrace\\Arguments\\Reducers\\SymphonyRequestArgumentReducer',
      8 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\ModelArgumentReducer',
      9 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\CollectionArgumentReducer',
      10 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StringableArgumentReducer',
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
