<?php
// English translations for system reports, report filters, and report tables

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

    'activity_log' => [
        'title' => 'User Activity Report',
        'loan_apps' => 'Loan Apps',
        'approvals' => 'Approvals',
        'registered' => 'Registered',
        'no_results' => 'No user activity data available.',
    ],
    'user_activity' => [
        'title' => 'User Activity Report',
        'description' => 'Monitor user activities within the system, including application and approval counts.',
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

    'utilization' => [
        'title' => 'Equipment Utilization Report',
        'description' => 'View a summary of equipment status and current inventory utilization rate.',
        'labels' => [
            'utilization_rate' => 'Utilization Rate',
            'status_summary' => 'Equipment Status Summary',
        ],
        'no_results' => 'No data available to display.',
    ],

    'loan_status_summary' => [
        'title' => 'Loan Application Status Summary',
        'description' => 'Displays an overview of current ICT loan application statuses.',
        'labels' => [
            'status' => 'Status',
            'count' => 'Application Count',
        ],
        'no_results' => 'No loan application data found.',
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

    // Helpdesk Reports
    'helpdesk_tickets_summary' => [
        'title' => 'Helpdesk Ticket Summary',
        'description' => 'Provides an overview of current helpdesk ticket statuses, categories, and priorities.',
        'labels' => [
            'status' => 'Status',
            'category' => 'Category',
            'priority' => 'Priority',
            'count' => 'Ticket Count',
            'avg_resolution_time' => 'Avg. Resolution Time (Hours)',
            'overdue_tickets' => 'Overdue Tickets',
        ],
        'no_results' => 'No helpdesk ticket data found.',
    ],
    'helpdesk_tickets_report' => [
        'title' => 'Helpdesk Tickets Report',
        'description' => 'View detailed reports on helpdesk tickets, including resolution times and agent performance.',
        'list_header' => 'Helpdesk Ticket List',
        'no_results' => 'No helpdesk ticket data found for these criteria.',
        'filter_heading' => 'Filter Tickets',
        'filter_status' => 'Status',
        'filter_category' => 'Category',
        'filter_priority' => 'Priority',
        'filter_assigned_to' => 'Assigned To',
        'search_placeholder' => 'Search Ticket ID, Title, or Applicant...',
        'table' => [
            'ticket_id' => 'Ticket ID',
            'title' => 'Title',
            'category' => 'Category',
            'status' => 'Status',
            'priority' => 'Priority',
            'applicant' => 'Applicant',
            'assigned_to' => 'Assigned To',
            'created_at' => 'Created Date',
            'closed_at' => 'Closed Date',
            'resolution_time' => 'Resolution Time (Hours)',
        ],
    ],
];
