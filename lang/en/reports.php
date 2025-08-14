<?php

return [

  // General Page Meta
  'page_title' => 'System Reports',
  'page_header' => 'Available System Reports',
  'view_report' => 'View Report',
  'back_to_list' => 'Back to Reports List',

  // Filters
  'filters' => [
    'user' => 'User',
    'all_users' => 'All Users',
    'transaction_type' => 'Transaction Type',
    'all_types' => 'All Types',
    'type_issue' => 'Issue',
    'type_return' => 'Return',
    'date_from' => 'Date From',
    'date_to' => 'Date To',
    'filter_button' => 'Filter',
    'search_placeholder' => 'Search...',
  ],

  // Activity Log / User Activity
  'activity_log' => [
    'title' => 'User Activity Report',
    'email_apps' => 'Email Apps',
    'loan_apps' => 'Loan Apps',
    'approvals' => 'Approvals',
    'registered' => 'Registered',
    'no_results' => 'No user activity data available.',
  ],
  'user_activity' => [
    'title' => 'User Activity Report',
    'description' => 'Monitor user activities within the system, including application and approval counts.',
  ],

  // Equipment Inventory
  'equipment_inventory' => [
    'title' => 'ICT Equipment Inventory Report',
    'description' => 'Generate and view detailed reports on the current inventory of ICT equipment.',
    'list_header' => 'Equipment List',
    'no_results' => 'No ICT equipment found for this report.',
    'table' => [
      'asset_tag_id' => 'Asset Tag ID',
      'asset_type' => 'Asset Type',
      'brand' => 'Brand',
      'model' => 'Model',
      'serial_no' => 'Serial No.',
      'op_status' => 'Operational Status',
      'condition_status' => 'Condition',
      'department' => 'Department',
      'current_user' => 'Current User',
      'loan_date' => 'Loan Date',
    ],
  ],

  // Utilization Report
  'utilization' => [
    'title' => 'Equipment Utilization Report',
    'description' => 'View a summary of equipment status and current inventory utilization rate.',
    'labels' => [
      'utilization_rate' => 'Utilization Rate',
      'status_summary' => 'Equipment Status Summary',
    ],
    'no_results' => 'No data available to display.',
  ],

  // Loan Status Summary
  'loan_status_summary' => [
    'title' => 'Loan Application Status Summary',
    'description' => 'Displays an overview of current ICT loan application statuses.',
    'labels' => [
      'status' => 'Status',
      'count' => 'Application Count',
    ],
    'no_results' => 'No loan application data found.',
  ],

  // Loan Applications
  'loan_applications' => [
    'title' => 'Loan Application Report',
    'description' => 'Review status and history reports for ICT equipment loan applications.',
    'list_header' => 'Loan Application List',
    'no_results' => 'No loan application data found for these criteria.',
    'search_placeholder' => 'Search ID, Applicant, or Purpose...',
    'table' => [
      'applicant' => 'Applicant',
      'department' => 'Applicant\'s Department',
      'loan_dates' => 'Loan Dates',
      'return_date' => 'Return Date',
      'status' => 'Status',
    ],
  ],

  // Loan History
  'loan_history' => [
    'title' => 'Loan History Report',
    'page_header' => 'ICT Loan Transaction History Report',
    'description' => 'View the detailed transaction history of ICT equipment loans (issuances & returns).',
    'no_results' => 'No loan transaction history found.',
    'table' => [
      'transaction_id' => 'Transaction ID',
      'application_id' => 'Application ID',
      'equipment' => 'Equipment',
      'user' => 'User',
      'type' => 'Type',
      'date' => 'Transaction Date',
      'officer' => 'Officer In Charge',
    ],
  ],

  // Email Applications
  'email_applications' => [
    'title' => 'Email Application Report',
    'description' => 'Analyze the status and trends of email account and user ID applications.',
    'list_header' => 'Email Application List',
    'no_results' => 'No email applications found for this report.',
    'search_placeholder' => 'Search Applicant or Email...',
    'table' => [
      'applicant' => 'Applicant',
      'application_type' => 'Application Type',
      'application_date' => 'Application Date',
      'proposed_email' => 'Proposed Email',
      'assigned_email' => 'Assigned Email/ID',
      'status' => 'Status',
    ],
  ],
];
