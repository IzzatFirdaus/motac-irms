<?php

// config/variables.php
// Defines global variables accessible via config('variables.keyName')
// Used by Helpers.php, Blade templates, etc.
// Updated for Helpdesk integration and removal of Email/User ID Provisioning

return [
    // Application Specific Variables for MOTAC
    'templateName'        => env('APP_NAME', 'Sistem Pengurusan Sumber MOTAC'),
    'templateDescription' => 'Sistem Dalaman Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia untuk pengurusan sumber bersepadu, pinjaman ICT, dan sistem meja bantuan.',
    'templateKeyword'     => 'motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, sistem meja bantuan, kementerian pelancongan seni dan budaya',

    // URLs
    'productPage'   => rtrim(env('APP_URL', 'http://localhost'), '/') . '/dashboard',
    'documentation' => '#',
    'repositoryUrl' => 'https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS',

    // Social media or contact links (MOTAC specific)
    'facebookUrl'  => 'https://www.facebook.com/MyMOTAC/',
    'twitterUrl'   => 'https://twitter.com/MyMOTAC',
    'instagramUrl' => 'https://www.instagram.com/MyMOTAC',
    'githubUrl'    => '#',

    // Branding and theme variables for UI
    'branding' => [
        'primary_color'   => '#0047AB',
        'secondary_color' => '#FFD700',
        'accent_color'    => '#28a745',
        'theme'           => env('APP_THEME', 'theme-motac'),
        'logo_url'        => env('APP_LOGO_URL', '/assets/img/motac-logo.png'),
        'favicon_url'     => env('APP_FAVICON_URL', '/assets/img/favicon.ico'),
    ],

    // Helpdesk System variables
    'helpdesk_support_email'    => env('HELPDESK_SUPPORT_EMAIL', 'helpdesk@motac.gov.my'),
    'helpdesk_default_category' => env('HELPDESK_DEFAULT_CATEGORY', 'General'),
    'helpdesk_default_priority' => env('HELPDESK_DEFAULT_PRIORITY', 'Medium'),
];
