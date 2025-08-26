<?php

// English translations for system reports, report filters, and report tables
// This file is kept in sync with reports_ms.php for bilingual consistency and maintainability.

return [

    // === Main Report Titles & Navigation ===
    'page_title'   => 'System Reports',
    'page_header'  => 'Available System Reports',
    'view_report'  => 'View Report', // Used on report-card components
    'back_to_list' => 'Back to Reports List',

    // === Common Filters for All Reports ===
    'filters' => [
        'user'               => 'User',
        'all_users'          => 'All Users',
        'transaction_type'   => 'Transaction Type',
        'all_types'          => 'All Types',
        'type_issue'         => 'Issue',
        'type_return'        => 'Return',
        'date_from'          => 'Date From',
        'date_to'            => 'Date To',
        'filter_button'      => 'Filter',
        'search_placeholder' => 'Search...',
        'reset_button'       => 'Reset',
        'filter'             => 'Filter',
        'reset'              => 'Reset',
    ],

    // === User Activity Reports ===
    'activity_log' => [
        'title'      => 'User Activity Report',
        'loan_apps'  => 'Loan Applications',
        'approvals'  => 'Approvals',
        'registered' => 'Registered',
        'no_results' => 'No user activity data available.',
    ],
    'user_activity' => [
        'title'       => 'User Activity Report',
        'description' => 'Generate reports on user activity related to loan applications and approvals.',
        'table'       => [
            'count' => 'No.',
        ],
    ],

    // === Approval Reports ===
    'approvals' => [
        'table' => [
            'count' => 'Approval Count',
        ],
    ],

    // === Equipment Inventory Reports ===
    'equipment_inventory' => [
        'title'       => 'ICT Equipment Inventory Report',
        'description' => 'Get an overview of ICT equipment inventory registered in the system.',
        'list_header' => 'Equipment List',
        'no_results'  => 'No ICT equipment found for this report.',
        'table'       => [
            'asset_tag_id'     => 'Asset Tag ID',
            'asset_type'       => 'Asset Type',
            'brand'            => 'Brand',
            'model'            => 'Model',
            'serial_no'        => 'Serial No.',
            'op_status'        => 'Operational Status',
            'condition_status' => 'Condition',
            'department'       => 'Department',
            'current_user'     => 'Current User',
            'loan_date'        => 'Loan Date',
        ],
    ],

    // === Loan Applications Reports ===
    'loan_applications' => [
        'title'              => 'Loan Application Report',
        'description'        => 'Check report status and history of ICT equipment loan applications.',
        'no_results'         => 'No loan application data found for these criteria.',
        'search_placeholder' => 'Search Applicant or Application ID...',
        'table'              => [
            'applicant'   => 'Applicant',
            'department'  => "Applicant's Department",
            'loan_dates'  => 'Loan Dates',
            'return_date' => 'Return Date',
            'status'      => 'Status',
            'count'       => 'No.',
        ],
    ],

    // === Loan History Reports ===
    'loan_history' => [
        'title'       => 'Loan History Report',
        'page_header' => 'ICT Loan Transaction History Report',
        'description' => 'View detailed transaction history for ICT equipment loans (issuances & returns).',
        'no_results'  => 'No loan transaction history found.',
        'table'       => [
            'transaction_id' => 'Transaction ID',
            'application_id' => 'Application ID',
            'equipment'      => 'Equipment',
            'user'           => 'User',
            'type'           => 'Type',
            'date'           => 'Transaction Date',
            'officer'        => 'Officer In Charge',
        ],
    ],

    // === Loan Status Summary Reports ===
    'loan_status_summary' => [
        'title'       => 'Loan Application Status Summary',
        'description' => 'Provides a summary of the number of applications by status.',
        'labels'      => [
            'status' => 'Status',
            'count'  => 'Count',
        ],
        'no_results' => 'No status summary data found.',
    ],

    // === Equipment Utilization Reports ===
    'utilization' => [
        'title'       => 'Equipment Utilization Report',
        'description' => 'Analyze utilization rates and status summary for equipment.',
        'labels'      => [
            'utilization_rate' => 'Utilization Rate',
            'status_summary'   => 'Status Summary',
        ],
        'no_results' => 'No utilization data found.',
    ],

    // === Helpdesk Reports ===
    'helpdesk_report' => [
        'title'              => 'Helpdesk Report',
        'description'        => 'View ticket statuses and details for all helpdesk tickets.',
        'filter_heading'     => 'Filter Report',
        'filter_status'      => 'Ticket Status',
        'filter_priority'    => 'Ticket Priority',
        'filter_category'    => 'Ticket Category',
        'filter_assigned_to' => 'Assigned To',
        'search_placeholder' => 'Search Subject, Applicant or Ticket ID...',
        'date_from'          => 'Created Date From',
        'date_to'            => 'Created Date To',
        'table'              => [
            'ticket_id'   => 'Ticket ID',
            'subject'     => 'Subject',
            'applicant'   => 'Applicant',
            'category'    => 'Category',
            'priority'    => 'Priority',
            'status'      => 'Status',
            'assigned_to' => 'Assigned To',
            'created_at'  => 'Created Date',
            'closed_at'   => 'Closed Date',
        ],
        'no_results' => 'No helpdesk tickets found for this report.',
    ],

    // === Detailed Report Variations ===
    'equipment_inventory_report' => [
        'title'               => 'Equipment Inventory Report',
        'description'         => 'View the complete list of ICT equipment inventory registered in the system.',
        'total_equipment'     => 'Total Equipment:',
        'available_equipment' => 'Available Equipment:',
        'on_loan_equipment'   => 'Equipment on Loan:',
        'in_repair_equipment' => 'Equipment in Repair:',
        'table'               => [
            'asset_tag_no'     => 'Asset Tag No.',
            'type'             => 'Type',
            'brand'            => 'Brand',
            'model'            => 'Model',
            'status'           => 'Status',
            'current_location' => 'Current Location',
            'owner_department' => 'Owner Department',
            'acquisition_date' => 'Acquisition Date',
        ],
        'no_results' => 'No ICT equipment found for this report.',
    ],

    'loan_applications_report' => [
        'title'              => 'ICT Loan Applications Report',
        'description'        => 'View status and details of all ICT equipment loan applications.',
        'filter_heading'     => 'Filter Report',
        'filter_status'      => 'Application Status',
        'search_placeholder' => 'Search Applicant or Application ID...',
        'date_from'          => 'Applied Date From',
        'date_to'            => 'Applied Date To',
        'table'              => [
            'application_id'  => 'Application ID',
            'applicant'       => 'Applicant',
            'department'      => 'Department',
            'purpose'         => 'Purpose',
            'loan_start_date' => 'Loan Start Date',
            'loan_end_date'   => 'Loan End Date',
            'status'          => 'Status',
            'submitted_at'    => 'Submitted On',
        ],
        'no_results' => 'No loan applications found for this report.',
    ],

    'loan_history_report' => [
        'title'                   => 'ICT Equipment Loan History Report',
        'description'             => 'View complete records of all ICT equipment loan and return transactions.',
        'filter_heading'          => 'Filter Report',
        'filter_equipment'        => 'Equipment',
        'filter_transaction_type' => 'Transaction Type',
        'search_placeholder'      => 'Search Application ID, Asset Tag No., Applicant...',
        'table'                   => [
            'transaction_id'   => 'Transaction ID',
            'application_id'   => 'Application ID',
            'asset_tag_no'     => 'Asset Tag No.',
            'equipment_type'   => 'Equipment Type',
            'borrower'         => 'Borrower',
            'department'       => 'Department',
            'transaction_type' => 'Transaction Type',
            'transaction_date' => 'Transaction Date',
            'status'           => 'Status',
        ],
        'no_results' => 'No loan transaction history found for this report.',
    ],

    'loan_status_summary_report' => [
        'title'            => 'ICT Loan Status Summary Report',
        'description'      => 'Provides a summary of ICT equipment currently on loan.',
        'total_loans'      => 'Total Active Loans:',
        'overdue_loans'    => 'Overdue Loans:',
        'upcoming_returns' => 'Upcoming Returns (7 Days):',
        'table'            => [
            'asset_tag_no'           => 'Asset Tag No.',
            'equipment_type'         => 'Equipment Type',
            'brand_model'            => 'Brand & Model',
            'borrower'               => 'Borrower',
            'department'             => 'Department',
            'loan_start_date'        => 'Start Date',
            'expected_return_date'   => 'Expected Return Date',
            'days_remaining_overdue' => 'Days (Remaining/Overdue)',
            'status'                 => 'Status',
        ],
        'no_results' => 'No active loans found for this report.',
    ],

    'utilization_report' => [
        'title'                      => 'ICT Equipment Utilization Report',
        'description'                => 'Analyze the usage rate and availability of ICT equipment.',
        'total_equipment_registered' => 'Total Registered Equipment:',
        'average_loan_duration'      => 'Average Loan Duration (Days):',
        'most_loaned_equipment'      => 'Most Loaned Equipment:',
        'least_loaned_equipment'     => 'Least Loaned Equipment:',
        'table'                      => [
            'asset_tag_no'            => 'Asset Tag No.',
            'equipment_type'          => 'Equipment Type',
            'brand_model'             => 'Brand & Model',
            'total_loan_count'        => 'Total Loan Count',
            'total_loan_days'         => 'Total Loan Days',
            'availability_percentage' => 'Availability Percentage',
        ],
        'no_results' => 'No equipment utilization data found.',
    ],

];
