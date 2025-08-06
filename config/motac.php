<?php

// config/motac.php

return [
    'organization_name' => env('MOTAC_ORGANIZATION_NAME', 'MOTAC'),
    'organization_full_name' => env('MOTAC_ORGANIZATION_FULL_NAME', 'Ministry of Tourism, Arts and Culture Malaysia'),

    'approval' => [
        // Specific minimum grade level (numeric representation) required for a supporting officer for ICT Loans
        // As per ICT Loan Form (Part 5) & LoanApplicationService
        'min_loan_support_grade_level' => env('MOTAC_MIN_LOAN_SUPPORT_GRADE_LEVEL', 41),

        // Specific minimum grade level (numeric representation) for a supporting officer for Email/ID applications
        // 'min_email_supporting_officer_grade_level' => env('MOTAC_MIN_EMAIL_SUPPORTING_OFFICER_GRADE_LEVEL', 9), // REMOVED as per transformation plan

        // General minimum grade level for viewing any approval records, if not specifically overridden.
        // Used by ApprovalPolicy::viewAny as a general rule.
        'min_general_view_approval_grade_level' => env('MOTAC_MIN_GENERAL_VIEW_APPROVAL_GRADE_LEVEL', 9),

        // New key for the "General Approver" stage after initial support officer for loan applications
        'min_loan_general_approver_grade_level' => env('MOTAC_MIN_LOAN_GENERAL_APPROVER_GRADE_LEVEL', 44), // Example: Grade 44 or higher
    ],

    // 'email_provisioning' => [ // REMOVED entire section as per transformation plan
    //     'api_base_url' => env('EMAIL_PROVISIONING_API_BASE_URL', 'http://127.0.0.1:8001/api'),
    //     'api_token' => env('EMAIL_PROVISIONING_API_TOKEN', 'your-secret-token'),
    //     'default_domain' => env('EMAIL_PROVISIONING_DEFAULT_DOMAIN', 'motac.gov.my'),
    //     'default_password_length' => env('EMAIL_PROVISIONING_DEFAULT_PASSWORD_LENGTH', 12),
    // ],

    'mymail_form_options' => [
        'unit_options' => [ // Options for the 'Unit' field on MyMail form_options
            '' => '- Pilih Unit -', // Default unselected option
            '1' => 'Bahagian Pembangunan Sumber Manusia (BPSM)',
            '2' => 'Bahagian Kewangan',
            '3' => 'Bahagian Audit Dalam',
            '4' => 'Bahagian Pengurusan Maklumat (BPM)',
            '5' => 'Unit Komunikasi Korporat (UKK)',
            '6' => 'Unit Integriti',
            '7' => 'Unit Undang-Undang',
            '8' => 'Unit Naziran',
            '9' => 'Unit Khidmat Pengurusan',
            '10' => 'Unit Perolehan',
            '11' => 'Unit Akaun',
            '12' => 'Unit Pembangunan',
            '13' => 'Unit Pelancongan',
            '14' => 'Unit Kebudayaan',
            '15' => 'Unit Kesenian',
            '16' => 'Unit Warisan',
            '17' => 'Unit Sukan',
            '18' => 'Unit Antarabangsa',
            '19' => 'Unit Penyelidikan & Pembangunan',
            '20' => 'Unit Perhubungan Awam',
            '21' => 'Perundangan',
            '25' => 'Sekretariat Visit Malaysia',
        ],
        'grade_options' => [ // Grade options from MyMail form_options
            '' => '- Pilih Gred -', // Default unselected option
            'Turus III' => 'Turus III',
            'Jusa A' => 'Jusa A',
            'Jusa B' => 'Jusa B',
            'Jusa C' => 'Jusa C',
            '14' => '14',
            '13' => '13',
            '12' => '12',
            '10' => '10',
            '9' => '9',
        ],
    ], // REVISED: Closing bracket for mymail_form_options was moved

    // Standard list of loanable accessories
    // Used by ProcessIssuance and ProcessReturn Livewire components and LoanTransaction model
    'loan_accessories_list' => [
        'Power Cable',
        'Bag',
        'Mouse',
        'HDMI Cable',
        'User Manual',
        'Charger', // Often listed separately from power cable
        'Keyboard',
        'Stylus Pen',
        // Add other common accessories as needed
    ],

    // Notification settings
    'notifications' => [
        'admin_email_recipient' => env('MOTAC_ADMIN_EMAIL_RECIPIENT', 'sysadmin@motac.gov.my'),
        // Add more notification configs as needed
    ],

    // Helpdesk system configuration section
    'helpdesk' => [
        'default_category' => env('HELPDESK_DEFAULT_CATEGORY', 'General'),
        'default_priority' => env('HELPDESK_DEFAULT_PRIORITY', 'Medium'),
        'support_email' => env('HELPDESK_SUPPORT_EMAIL', 'helpdesk@motac.gov.my'),
        // SLA settings (example: hours until escalation for high priority)
        'sla_hours_high_priority' => env('HELPDESK_SLA_HOURS_HIGH', 8),
        'sla_hours_medium_priority' => env('HELPDESK_SLA_HOURS_MEDIUM', 24),
        'sla_hours_low_priority' => env('HELPDESK_SLA_HOURS_LOW', 72),
    ],

    // Application-wide date formats (can also be in config/app.php, but placing here for module-specifics if any)
    'date_formats' => [
        'date_format_my_short' => 'd M Y',           // Example: 28 Mei 2025
        'date_format_my_long' => 'j F Y, l',        // Example: 28 Mei 2025, Rabu
        'datetime_format_my' => 'd M Y, h:i A',     // Example: 28 Mei 2025, 10:30 PG
    ],
];
