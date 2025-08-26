<?php

// As per System Design Rev. 3, mail configurations are managed here and in the .env file. [cite: 65]
// Mailtrap is recommended for development environments, configured via .env variables. [cite: 50]

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    | System Design Rev. 3 refers to MAIL_MAILER in .env. [cite: 65]
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            // MAIL_HOST, MAIL_PORT, MAIL_ENCRYPTION, MAIL_USERNAME, MAIL_PASSWORD
            // are configured via .env as per System Design Rev. 3. [cite: 65]
            // For development, these would point to Mailtrap. [cite: 50]
            'host'         => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port'         => env('MAIL_PORT', 587),
            'encryption'   => env('MAIL_ENCRYPTION', 'tls'),
            'username'     => env('MAIL_USERNAME'),
            'password'     => env('MAIL_PASSWORD'),
            'timeout'      => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'), // Formerly 'ehlo_domain' in older Laravel versions
        ],

        'ses' => [
            'transport' => 'ses',
            // SES configuration typically involves AWS key, secret, and region set in .env
            // and potentially in config/services.php
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // Mailgun domain and secret are usually set in .env and config/services.php
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'postmark' => [
            'transport' => 'postmark',
            // Postmark token is usually set in .env and config/services.php
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'), // Logs emails to a log channel instead of sending
        ],

        'array' => [
            'transport' => 'array', // Used for testing, stores emails in an array
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => [
                'smtp', // Primary mailer
                'log',  // Fallback mailer
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    | MAIL_FROM_ADDRESS and MAIL_FROM_NAME are configured via .env
    | as per System Design Rev. 3. [cite: 65]
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    | System Design Rev. 3 indicates use of Markdown emails (e.g., ProvisioningFailedNotification). [cite: 216]
    */

    'markdown' => [
        'theme' => 'default', // You can customize this theme

        'paths' => [
            resource_path('views/vendor/mail'), // For customized mail components
        ],
    ],

];
