<?php

// config/motac.php

// It's good practice to import models if their constants are used directly,
// but often these config values are simple types.
// use App\Models\User; // Example if using User constants directly

return [
  'organization_name' => 'MOTAC',
  'organization_full_name' => 'Ministry of Tourism, Arts and Culture Malaysia', // Kementerian Pelancongan, Seni dan Budaya

  'approval' => [
    // Minimum grade level (numeric representation) required to be an approver for ICT Loans
    'min_ict_loan_approver_grade_level' => env('MOTAC_MIN_ICT_LOAN_APPROVER_GRADE_LEVEL', 41), // As per ICT Loan Form (Part 5)

    // Minimum grade level (numeric representation) for a supporting officer for Email/ID applications
    'min_email_supporting_officer_grade_level' => env('MOTAC_MIN_EMAIL_SUPPORTING_OFFICER_GRADE_LEVEL', 9), // As per MyMail Form
  ],

  'email_provisioning' => [
    'api_endpoint' => env('MOTAC_EMAIL_PROVISIONING_API_ENDPOINT'),
    'api_key' => env('MOTAC_EMAIL_PROVISIONING_API_KEY'),
    'default_domain' => env('MOTAC_EMAIL_DEFAULT_DOMAIN', 'motac.gov.my'),
    // Add other necessary settings, e.g., for different service types if logic varies
  ],

  'ict_equipment_loan' => [
    'default_loan_duration_days' => env('MOTAC_DEFAULT_LOAN_DURATION_DAYS', 7),
    'bpm_notification_recipient_email' => env('MOTAC_BPM_NOTIFICATION_RECIPIENT_EMAIL', 'bpm.ict@motac.gov.my'), // Example email
    'max_items_per_loan' => env('MOTAC_MAX_ITEMS_PER_LOAN', 5),
    'processing_time_working_days' => env('MOTAC_LOAN_PROCESSING_TIME_DAYS', 3), // As per ICT Loan Form
  ],

  // Dropdown options primarily for MyMail form integration, as these are quite specific.
  // For other dropdowns like Departments, Positions, Grades, it's better to populate them from the database.
  'mymail_form_options' => [
    // Based on "Supplementary Document: Dropdown Menu Options for MyMail Integration"
    // These keys should ideally match the values stored in the database or used in application logic.

    'appointment_types' => [ // Section 2: Pelantikan
      // Key: stored value (e.g., from User::APPOINTMENT_TYPE_BAHARU)
      // Value: display label
      '' => '- Pilih Pelantikan -',
      'baharu' => 'Baharu',                             // Corresponds to User::APPOINTMENT_TYPE_BAHARU
      'kenaikan_pangkat_pertukaran' => 'Kenaikan Pangkat/Pertukaran', // Corresponds to User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN
      'lain_lain' => 'Lain-lain',                         // Corresponds to User::APPOINTMENT_TYPE_LAIN_LAIN
    ],

    'aras_options' => [ // Section 6: Aras (Level/Floor)
      '' => '- Pilih Aras -',
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
      '18' => '18', // Corrected from '181'
      // Add any other valid Aras options
    ],

    'supporting_officer_grades' => [ // Section 7: Gred Penyokong (MyMail Form)
      '' => '- Pilih Gred -',
      // These should be actual grade names or codes that your system uses/stores.
      // The list from MyMail form is quite extensive and sometimes descriptive.
      // It's better to map these to your 'grades' table if possible,
      // or use a simplified list of distinct, storable grade values.
      'Turus III' => 'Turus III',
      'JUSA A' => 'JUSA A', // Standardized casing
      'JUSA B' => 'JUSA B',
      'JUSA C' => 'JUSA C',
      '54' => '54',
      '52' => '52',
      '48' => '48',
      '44' => '44',
      '41' => '41',
      '38' => '38',
      '32' => '32',
      '29' => '29',
      '26' => '26',
      '22' => '22',
      '19' => '19',
      '9' => '9', // Explicitly Grade 9
      // This list should be curated based on how you actually store/validate supporting officer grades.
      // If they map to Grade IDs from `grades` table, this config might not be needed here.
    ],
    // Service Status (Taraf Perkhidmatan) options are best managed via User::$SERVICE_STATUS_LABELS
    // Jawatan and general Gred options are best managed via database tables (positions, grades)
  ],

  'notifications' => [
    'admin_email_recipient' => env('MOTAC_ADMIN_EMAIL_RECIPIENT', 'sysadmin@motac.gov.my'),
  ],

  // Add other MOTAC-specific configurations as needed
];
