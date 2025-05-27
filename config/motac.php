<?php

// config/motac.php

return [
  'organization_name' => 'MOTAC',
  'organization_full_name' => 'Ministry of Tourism, Arts and Culture Malaysia',

  'approval' => [
    // Specific minimum grade level (numeric representation) required to be an approver for ICT Loans
    // As per ICT Loan Form (Part 5)
    'min_ict_loan_approver_grade_level' => env('MOTAC_MIN_ICT_LOAN_APPROVER_GRADE_LEVEL', 41),

    // Specific minimum grade level (numeric representation) for a supporting officer for Email/ID applications
    // As per MyMail Form and system design (Section 7.2)
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
    'aras_options' => [
      '' => '- Pilih Aras -',
      '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5',
      '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10',
      '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',
      '16' => '16', '17' => '17', '18' => '18', // (Supplementary Document, corrected from 181)
    ],
    'supporting_officer_grades' => [ // For MyMail form dropdown (Supplementary Document)
      '' => '- Pilih Gred -',
      'Turus III' => 'Turus III', 'JUSA A' => 'JUSA A', 'JUSA B' => 'JUSA B', 'JUSA C' => 'JUSA C',
      '54' => '54', '52' => '52', '48' => '48', '44' => '44', '41' => '41',
      '38' => '38', '32' => '32', '29' => '29', '26' => '26', '22' => '22', '19' => '19',
      '14' => '14', '13' => '13', '12' => '12', '10' => '10', '9' => '9', // Grade 9 explicitly mentioned
    ],
  ],

  'notifications' => [
    'admin_email_recipient' => env('MOTAC_ADMIN_EMAIL_RECIPIENT', 'sysadmin@motac.gov.my'),
  ],
];
