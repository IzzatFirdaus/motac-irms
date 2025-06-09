<?php

// lang/en/menu.php

return [
    'dashboard' => 'Dashboard',
    'section' => [
        'resource_management' => 'Resource Management',
        'system_config' => 'System Configuration',
    ],
    'my_applications' => [
        'title' => 'My Applications',
        'email' => 'My Email Requests',
        'loan' => 'My Loan Applications',
    ],
    'apply_for_resources' => [
        'title' => 'Apply For Resources',
        'email' => 'New Email/ID Request',
        'loan' => 'New Loan Application',
    ],
    'approvals_dashboard' => 'Approvals Dashboard',
    'administration' => [
        'title' => 'Administration',
        'bpm_operations' => [
            'title' => 'BPM Operations',
            'outstanding_loans' => 'Outstanding ICT Loans',
            'issued_loans' => 'Issued ICT Loans',
        ],
        'equipment_management' => 'Equipment Management',
        'email_applications' => 'Email/ID Admin', // For admin section
        'users_list' => 'User Accounts (Admin)',
    ],
    'settings' => [
        'title' => 'System Settings',
        'user_management' => 'User Management',
        'roles_permissions' => 'Roles & Permissions',
        'grades_management' => 'Grades Management',
        'departments_management' => 'Departments Management',
        'positions_management' => 'Positions Management',
    ],
    'reports' => [
        'title' => 'Reports',
        'equipment_report' => 'Equipment Inventory Report',
        'email_accounts_report' => 'Email Accounts Report',
        'loan_applications_report' => 'Loan Applications Report',
        'user_activity_report' => 'User Activity Log',
    ],
    'system_logs' => 'System Logs',
    // Fallback for the '-' key if it's ever directly translated by __('-')
    // However, the menu should now use actual keys.
    // '-' => '-',
];
