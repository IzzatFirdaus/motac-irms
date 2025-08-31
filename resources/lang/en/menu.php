<?php
// Compatibility wrapper so tests and older code referencing `resources/lang/en/menu.php` still work.
// It re-uses `menu_en.php` and provides aliases for legacy keys.

$base = [];
if (file_exists(__DIR__ . '/menu_en.php')) {
    $base = require __DIR__ . '/menu_en.php';
}

return [
    'dashboard' => $base['dashboard'] ?? 'Dashboard',

    'section' => [
        'public' => $base['section']['public'] ?? 'Public',
        'resource_management' => $base['section']['resource_management'] ?? 'Resource Management',
        'reports_analytics' => $base['section']['reports_analytics'] ?? 'Reports & Analytics',
        'system_settings' => $base['section']['system_settings'] ?? 'System Settings',
    ],

    'home' => $base['home'] ?? 'Home',
    'contact_us' => $base['contact_us'] ?? 'Contact Us',
    'login' => $base['login'] ?? 'Login',

    'my_applications' => [
        'title' => $base['my_applications']['title'] ?? 'My Applications',
        'loan' => $base['my_applications']['loan'] ?? 'My Loan Applications',
        'helpdesk' => $base['my_applications']['helpdesk'] ?? 'My Helpdesk Tickets',
    ],

    'apply_for_resources' => [
        'title' => $base['apply_for_resources']['title'] ?? 'Apply for New Resources',
        'loan' => $base['apply_for_resources']['loan'] ?? 'New ICT Equipment Loan',
        'helpdesk_ticket' => $base['apply_for_resources']['helpdesk_ticket'] ?? 'New Helpdesk Ticket',
        'email' => 'Apply via Email',
    ],

    'approvals' => [
        'title' => $base['approvals']['title'] ?? 'Approvals',
        'pending_tasks' => $base['approvals']['pending_tasks'] ?? 'Pending Tasks',
        'approval_history' => $base['approvals']['approval_history'] ?? 'Approval History',
    ],

    'resource_inventory' => [
        'title' => $base['resource_inventory']['title'] ?? 'ICT Resource Inventory',
        'equipment' => $base['resource_inventory']['equipment'] ?? 'ICT Equipment Inventory',
        'loan_transactions' => $base['resource_inventory']['loan_transactions'] ?? 'ICT Loan Transactions',
    ],

    'reports' => [
        'title' => $base['reports']['title'] ?? 'Reports',
        'equipment_report' => $base['reports']['equipment_report'] ?? 'Equipment Inventory Report',
        'loan_applications_report' => $base['reports']['loan_applications_report'] ?? 'Loan Applications Report',
        'helpdesk_report' => $base['reports']['helpdesk_report'] ?? 'Helpdesk Report',
        'user_activity_report' => $base['reports']['user_activity_report'] ?? 'User Activity Report',
        'loan_history_report' => $base['reports']['loan_history_report'] ?? 'Loan History Report',
        'utilization_report' => $base['reports']['utilization_report'] ?? 'Equipment Utilization Report',
        'loan_status_summary_report' => $base['reports']['loan_status_summary_report'] ?? 'Loan Status Summary Report',
    ],

    'system_settings' => [
        'title' => $base['system_settings']['title'] ?? 'System Settings',
        'users' => $base['system_settings']['users'] ?? 'User Management',
        'roles' => $base['system_settings']['roles'] ?? 'Roles',
        'permissions' => $base['system_settings']['permissions'] ?? 'Permissions',
        'departments' => $base['system_settings']['departments'] ?? 'Departments',
        'grades' => $base['system_settings']['grades'] ?? 'Grades',
        'asset_types' => $base['system_settings']['asset_types'] ?? 'Asset Types',
        'equipment_conditions' => $base['system_settings']['equipment_conditions'] ?? 'Equipment Conditions',
        'accessories' => $base['system_settings']['accessories'] ?? 'Equipment Accessories',
        'positions' => 'Positions', // Added missing key
    ],

    'general_settings' => [
        'title' => $base['general_settings']['title'] ?? 'General Settings',
        'manage_grades' => $base['general_settings']['manage_grades'] ?? 'Manage Job Grades',
        'manage_departments' => $base['general_settings']['manage_departments'] ?? 'Manage Departments/Divisions',
        'manage_asset_types' => $base['general_settings']['manage_asset_types'] ?? 'Manage Asset Types',
        'manage_equipment_conditions' => $base['general_settings']['manage_equipment_conditions'] ?? 'Manage Equipment Conditions',
        'manage_accessories' => $base['general_settings']['manage_accessories'] ?? 'Manage Accessories',
    ],

    'system_logs' => $base['system_logs'] ?? 'System Logs',

    'notifications' => [
        'title' => $base['notifications']['title'] ?? 'Notifications',
        'view_all' => $base['notifications']['view_all'] ?? 'View All Notifications',
        'new_notification' => $base['notifications']['new_notification'] ?? 'New Notification',
    ],

    'profile' => $base['profile'] ?? 'My Profile',
    'logout' => $base['logout'] ?? 'Logout',

    // Legacy aliases for backward compatibility with tests
    'settings' => [
        'title' => $base['system_settings']['title'] ?? 'System Settings',
    ],

    'administration' => [
        'title' => 'Administration',
        'equipment_management' => $base['resource_inventory']['equipment'] ?? 'Equipment Management',
        'helpdesk_management' => 'Helpdesk Management',
    ],

    'helpdesk' => [
        'title' => 'Helpdesk',
    ],
];
