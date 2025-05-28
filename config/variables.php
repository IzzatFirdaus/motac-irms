<?php

// config/variables.php
// Defines global variables accessible via config('variables.keyName')
// Used by Helpers.php, commonMaster.blade.php, etc.
// Design Language: Prominent MOTAC Branding, Bahasa Melayu as Primary Language.

return [
    // Application Specific Variables for MOTAC
    'templateName'        => env('APP_NAME', 'Sistem Pengurusan Sumber MOTAC'),
    'templateDescription' => 'Sistem Dalaman Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia untuk pengurusan sumber bersepadu.',
    'templateKeyword'     => 'motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, permohonan emel, kementerian pelancongan seni dan budaya',

    // URLs
    'productPage' => rtrim(env('APP_URL', 'http://localhost'), '/') . '/dashboard', // Main landing page after login
    'documentation'  => '#', // Link to user manual or SOP if available
    'repositoryUrl'  => 'https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS',

    // Social media or contact links (MOTAC specific, if any to be shown in footer or elsewhere)
    // These are examples, update with actual MOTAC links if needed, or remove if not used.
    'facebookUrl'    => 'https://www.facebook.com/MyMOTAC/',
    'twitterUrl'     => 'https://twitter.com/MyMOTAC',
    'instagramUrl'   => 'https://www.instagram.com/MyMOTAC',
    'githubUrl'      => '#', // If MOTAC has a public GitHub for certain projects


    // Original template variables (can be kept if your theme structure uses them, or removed if simplified)
    // 'creatorName' => 'Pixinvent',
    // 'creatorUrl' => 'https://pixinvent.com',
    // 'support' => 'https://github.com/pixinvent/vuexy-html-admin-template/issues',
    // 'buyNow' => 'https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599',
];
