<?php

// English translations for main menu and navigation items
// This file mirrors the structure and keys of menu_ms.php for bilingual synchronization.

return [
    // === Main Menu ===
    'dashboard' => 'Dashboard',

    // === Sections: Public, Resource Management, Reports, System Settings ===
    'section' => [
        'public'              => 'Public',
        'resource_management' => 'Resource Management',
        'reports_analytics'   => 'Reports & Analytics',
        'system_settings'     => 'System Settings',
    ],

    // === Guest-only / Public Menu Items ===
    'home'       => 'Home',
    'contact_us' => 'Contact Us',
    'login'      => 'Login',

    // === My Applications (Submenu) ===
    'my_applications' => [
        'title'    => 'My Applications',
        'loan'     => 'My Loan Applications',
        'helpdesk' => 'My Helpdesk Tickets',
    ],

    // === Apply for New Resources ===
    'apply_for_resources' => [
        'title'            => 'Apply for New Resources',
        'loan'             => 'New ICT Equipment Loan',
        'loan_application' => 'New ICT Equipment Loan', // for backward compatibility
        'helpdesk_ticket'  => 'New Helpdesk Ticket',
    ],

    // === Approvals ===
    'approvals' => [
        'title'            => 'Approvals',
        'pending_tasks'    => 'Pending Tasks',
        'approval_history' => 'Approval History',
    ],

    // === ICT Resource Inventory ===
    'resource_inventory' => [
        'title'             => 'ICT Resource Inventory',
        'equipment'         => 'ICT Equipment Inventory',
        'loan_transactions' => 'ICT Loan Transactions',
    ],

    // === Reports & Analytics ===
    'reports' => [
        'title'                      => 'Reports',
        'equipment_report'           => 'Equipment Inventory Report',
        'loan_applications_report'   => 'Loan Applications Report',
        'helpdesk_report'            => 'Helpdesk Report',
        'user_activity_report'       => 'User Activity Report',
        'loan_history_report'        => 'Loan History Report',
        'utilization_report'         => 'Equipment Utilization Report',
        'loan_status_summary_report' => 'Loan Status Summary Report',
    ],

    // === System Settings (Submenu) ===
    'system_settings' => [
        'title'                => 'System Settings',
        'users'                => 'User Management',
        'roles'                => 'Roles',
        'permissions'          => 'Permissions',
        'departments'          => 'Departments',
        'grades'               => 'Grades',
        'asset_types'          => 'Asset Types',
        'equipment_conditions' => 'Equipment Conditions',
        'accessories'          => 'Equipment Accessories',
    ],

    // === General Settings (Submenu) ===
    'general_settings' => [
        'title'                       => 'General Settings',
        'manage_grades'               => 'Manage Job Grades',
        'manage_departments'          => 'Manage Departments/Divisions',
        'manage_asset_types'          => 'Manage Asset Types',
        'manage_equipment_conditions' => 'Manage Equipment Conditions',
        'manage_accessories'          => 'Manage Accessories',
    ],

    // === System Logs ===
    'system_logs' => 'System Logs',

    // === Notifications ===
    'notifications' => [
        'title'            => 'Notifications',
        'view_all'         => 'View All Notifications',
        'new_notification' => 'New Notification',
    ],

    // === Profile Menu and Logout ===
    'profile' => 'My Profile',
    'logout'  => 'Logout',
];
