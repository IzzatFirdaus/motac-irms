<?php

// config/motac.php

return [
  'organization_name' => 'MOTAC',
  'organization_full_name' => 'Ministry of Tourism, Arts and Culture Malaysia',

  'approval' => [
    // Specific minimum grade level (numeric representation) required for a supporting officer for ICT Loans
    // As per ICT Loan Form (Part 5) & LoanApplicationService
    'min_loan_support_grade_level' => env('MOTAC_MIN_LOAN_SUPPORT_GRADE_LEVEL', 41), // Renamed for consistency

    // Specific minimum grade level (numeric representation) for a supporting officer for Email/ID applications
    // As per MyMail Form and system design (Section 7.2) & EmailApplicationService
    'min_email_supporting_officer_grade_level' => env('MOTAC_MIN_EMAIL_SUPPORTING_OFFICER_GRADE_LEVEL', 9),

    // General minimum grade level for viewing any approval records, if not specifically overridden.
    // Could default to the lowest of the specific approval grades (e.g., 9)
    // Used by ApprovalPolicy::viewAny as a general rule.
    'min_general_view_approval_grade_level' => env('MOTAC_MIN_GENERAL_VIEW_APPROVAL_GRADE_LEVEL', 9),
  ],

  'email_provisioning' => [
    'api_endpoint' => env('MOTAC_EMAIL_PROVISIONING_API_ENDPOINT'),
    'api_key' => env('MOTAC_EMAIL_PROVISIONING_API_KEY'),
    'default_domain' => env('MOTAC_EMAIL_DEFAULT_DOMAIN', 'motac.gov.my'), //
  ],

  'ict_equipment_loan' => [
    'default_loan_duration_days' => env('MOTAC_DEFAULT_LOAN_DURATION_DAYS', 7),
    'bpm_notification_recipient_email' => env('MOTAC_BPM_NOTIFICATION_RECIPIENT_EMAIL', 'bpm.ict@motac.gov.my'),
    'max_items_per_loan' => env('MOTAC_MAX_ITEMS_PER_LOAN', 5),
    'processing_time_working_days' => env('MOTAC_LOAN_PROCESSING_TIME_DAYS', 3), //
  ],

  'mymail_form_options' => [
    'appointment_types' => [
      '' => '- Pilih Pelantikan -',
      'baharu' => 'Baharu', // (Supplementary Document)
      'kenaikan_pangkat_pertukaran' => 'Kenaikan Pangkat/Pertukaran', // (Supplementary Document)
      'lain_lain' => 'Lain-lain', // (Supplementary Document)
    ],
    'aras_options' => [ // As per User model getLevelOptions and supplementary document (label part)
      '' => '- Pilih Aras -',
      '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5',
      '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10',
      '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',
      '16' => '16', '17' => '17', '18' => '18',
    ],
    'supporting_officer_grades' => [ // For MyMail form dropdown (Supplementary Document)
      '' => '- Pilih Gred -',
      'Turus III' => 'Turus III', 'JUSA A' => 'JUSA A', 'JUSA B' => 'JUSA B', 'JUSA C' => 'JUSA C',
      '54' => '54', '52' => '52', '48' => '48', '44' => '44', '41' => '41',
      '38' => '38', '32' => '32', '29' => '29', '26' => '26', '22' => '22', '19' => '19',
      '14' => '14', /*'13' => '13', '12' => '12', '10' => '10',*/ // Values from supplementary doc are Gred name not just number for some
      // Corrected based on supplementary doc values which are more descriptive than just numbers for higher grades
      // The supplementary doc has '14', '13', '12', '10', '9' as separate distinct options in the list for "Gred Penyokong".
      // Keep these if they represent distinct selectable options.
      // The example values '14', '13', '12', '10', '9' are simplified. Using more descriptive ones where available.
      // For grades like "14", the supplementary document text is just "14". So, '14' => '14' is correct.
      // It appears "supporting_officer_grades" in the config is intended to be a simplified list compared to the exhaustive user grade list.
      // The original config list for supporting_officer_grades is restored as it's likely a curated list for the specific dropdown.
      '14' => '14', '13' => '13', '12' => '12', '10' => '10', '9' => '9',
    ],
  ],

  'notifications' => [
    'admin_email_recipient' => env('MOTAC_ADMIN_EMAIL_RECIPIENT', 'sysadmin@motac.gov.my'),
  ],
];
