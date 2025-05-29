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
    // As per MyMail Form and system design (Section 7.2) & EmailApplicationService
    'min_email_supporting_officer_grade_level' => env('MOTAC_MIN_EMAIL_SUPPORTING_OFFICER_GRADE_LEVEL', 9),

    // General minimum grade level for viewing any approval records, if not specifically overridden.
    // Used by ApprovalPolicy::viewAny as a general rule.
    'min_general_view_approval_grade_level' => env('MOTAC_MIN_GENERAL_VIEW_APPROVAL_GRADE_LEVEL', 9),
  ],

  'email_provisioning' => [
    'api_endpoint' => env('MOTAC_EMAIL_PROVISIONING_API_ENDPOINT'),
    'api_key' => env('MOTAC_EMAIL_PROVISIONING_API_KEY'),
    'default_domain' => env('MOTAC_EMAIL_DEFAULT_DOMAIN', 'motac.gov.my'),
  ],

  'ict_equipment_loan' => [
    'default_loan_duration_days' => env('MOTAC_DEFAULT_LOAN_DURATION_DAYS', 7),
    'bpm_notification_recipient_email' => env('MOTAC_BPM_NOTIFICATION_RECIPIENT_EMAIL', 'bpm.ict@motac.gov.my'), // Example email
    'max_items_per_loan' => env('MOTAC_MAX_ITEMS_PER_LOAN', 5), // Example value
    'processing_time_working_days' => env('MOTAC_LOAN_PROCESSING_TIME_DAYS', 3), // As per System Design (Section 7.3)
  ],

  'mymail_form_options' => [
    // For EmailApplicationForm, mapping to users.appointment_type enum
    //  Dropdown Menu Options for MyMail Integration" (Section 2. Pelantikan)
    'appointment_types' => [
      '' => '- Pilih Pelantikan -', // Default unselected option
      'baharu' => 'Baharu', // "Value "1": Baharu" in Supplementary Document
      'kenaikan_pangkat_pertukaran' => 'Kenaikan Pangkat/Pertukaran', // "Value "2": Kenaikan Pangkat/Pertukaran" in Supplementary Document
      'lain_lain' => 'Lain-lain', // "Value "3": Lain-lain"

      // For EmailApplicationForm, mapping to users.level
      //  Dropdown Menu Options for MyMail Integration" (Section 6. Aras)
      'aras_options' => [
        '' => '- Pilih Aras -', // Default unselected option
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
        '11' => '11',
        '12' => '12',
        '13' => '13',
        '14' => '14',
        '15' => '15',
        '16' => '16',
        '17' => '17',
        '18' => '18',
      ],
      // For EmailApplicationForm, for the "Gred Penyokong" dropdown.
      //  Dropdown Menu Options for MyMail Integration" (Section 7. Gred Penyokong)
      'supporting_officer_grades' => [
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
    ],

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

    'notifications' => [
      'admin_email_recipient' => env('MOTAC_ADMIN_EMAIL_RECIPIENT', 'sysadmin@motac.gov.my'), // Example email
      // Add other notification-related configs if needed
    ],

    // Application-wide date formats (can also be in config/app.php, but placing here for module-specifics if any)
    'date_formats' => [
      'date_my' => 'd/m/Y', // Example: 29/05/2025
      'datetime_my' => 'd/m/Y H:i A', // Example: 29/05/2025 10:40 PM
    ],

    // Other application-specific settings
    'session_lifetime' => env('SESSION_LIFETIME', 120), // In minutes, aligns with system design note on session

  ],
];
