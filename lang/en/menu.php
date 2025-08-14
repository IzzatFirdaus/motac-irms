<?php

return [
  'dashboard' => 'Dashboard',
  'section' => [
    'resource_management' => 'Resource Management',
    'reports_analytics' => 'Reports & Analytics', // Added
    'system_settings' => 'System Settings',       // Changed from system_config for consistency
  ],
  'my_applications' => [
    'title' => 'My Applications',
    'email' => 'My Email Applications',
    'loan' => 'My Loan Applications',
  ],
  'apply_for_resources' => [
    'title' => 'Apply for New Resources',
    'email_account' => 'New Email Account / User ID', // Updated for clarity
    'loan_application' => 'New ICT Equipment Loan',  // Updated for clarity
  ],
  'approvals' => [ // Changed from approvals_dashboard for consistency with config/menu.php
    'title' => 'Approvals',
    // Add specific approval sub-items if needed, e.g., 'email' => 'Email Approvals', 'loan' => 'Loan Approvals'
  ],
  'resource_inventory' => [ // Added for consistency with config/menu.php
    'title' => 'ICT Resource Inventory', // Updated title
    'equipment' => 'ICT Equipment Inventory',
    'loan_transactions' => 'ICT Loan Transactions',
  ],
  'reports' => [
    'title' => 'Reports', // Simplified title for the main menu item
    'equipment_report' => 'Equipment Inventory Report',
    'loan_applications_report' => 'Loan Applications Report',
    'email_applications_report' => 'Email Applications Report', // New entry
    'user_activity_report' => 'User Activity Report',          // New entry
    'loan_history_report' => 'Loan History Report',             // New entry
    'utilization_report' => 'Equipment Utilization Report',     // New entry
    'loan_status_summary_report' => 'Loan Status Summary Report', // New entry
  ],
  'system_settings' => [ // Changed from settings for consistency with config/menu.php
    'title' => 'System Settings',
    'users' => 'User Management',
    'roles' => 'Roles',
    'permissions' => 'Permissions',
    'grades' => 'Grades',
    'departments' => 'Departments',
    'positions' => 'Positions',
  ],
  'system_logs' => 'System Logs', // New entry
];
