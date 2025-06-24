<?php

// lang/en/reports.php

return [
    'page_title' => 'System Reports',
    'page_header' => 'Available System Reports',
    'view_report' => 'View Report',
    'back_to_list' => 'Back to Reports List',

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
    'user_activity' => [
        'title' => 'User Activity Report',
        'description' => 'Monitor user activities within the system, including application and approval counts.',
    ],
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
];
