<?php

// config/menu.php

return [
  'menu' => [
    [
      'url' => '/dashboard',
      'name' => 'menu.dashboard',
      'icon' => 'house-door-fill',
      'routeName' => 'dashboard',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver', 'HOD'],
    ],
    [
      'menuHeader' => 'menu.section.resource_management',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver', 'HOD'],
    ],
    [
      'name' => 'menu.my_applications.title',
      'icon' => 'folder-check',
      'routeNamePrefix' => 'email-applications.index,loan-applications.index', // Keep as is for prefix matching
      'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
      'submenu' => [
        [
          'url' => '/email-applications',
          'name' => 'menu.my_applications.email',
          'icon' => 'envelope-paper-fill',
          'routeName' => 'email-applications.index',
        ],
        [
          'url' => '/loan-applications',
          'name' => 'menu.my_applications.loan',
          'icon' => 'laptop-fill',
          'routeName' => 'loan-applications.index',
        ],
      ],
    ],
    [
      'name' => 'menu.apply_for_resources.title',
      'icon' => 'file-earmark-plus',
      'role' => ['User'],
      'submenu' => [
        [
          'url' => '/apply/email-account',
          'name' => 'menu.apply_for_resources.email_account',
          'icon' => 'envelope-plus-fill',
          'routeName' => 'email-applications.create',
        ],
        [
          'url' => '/apply/loan-application',
          'name' => 'menu.apply_for_resources.loan_application',
          'icon' => 'laptop-fill',
          'routeName' => 'loan-applications.create',
        ],
      ],
    ],
    [
      'name' => 'menu.approvals.title',
      'icon' => 'clipboard-check-fill',
      'routeName' => 'approvals.dashboard',
      'role' => ['Admin', 'Approver', 'HOD'],
      'submenu' => [
        // Add specific approval sub-items if needed, e.g., 'email-approvals.index', 'loan-approvals.index'
      ],
    ],
    [
      'name' => 'menu.resource_inventory.title',
      'icon' => 'boxes',
      'routeNamePrefix' => 'equipment.index,equipment.show,equipment.create,equipment.edit,loan-transactions.index,loan-transactions.show', // Keep as is for prefix matching
      'role' => ['Admin', 'BPM Staff', 'IT Admin'],
      'submenu' => [
        [
          'url' => '/equipment',
          'name' => 'menu.resource_inventory.equipment',
          'icon' => 'tools',
          'routeName' => 'equipment.index',
        ],
        [
          'url' => '/loan-transactions',
          'name' => 'menu.resource_inventory.loan_transactions',
          'icon' => 'exchange',
          'routeName' => 'loan-transactions.index',
        ],
      ],
    ],

    // Reports & Analytics Section
    [
      'menuHeader' => 'menu.section.reports_analytics',
      'role' => ['Admin', 'BPM Staff', 'IT Admin'],
    ],
    [
      'name' => 'menu.reports.title',
      'icon' => 'file-earmark-bar-graph-fill',
      'routeNamePrefix' => 'reports.', // The prefix to match all report routes
      'role' => ['Admin', 'BPM Staff', 'IT Admin'],
      'submenu' => [
        [
          'url' => '/reports/equipment-inventory',
          'name' => 'menu.reports.equipment_report',
          'icon' => 'card-list',
          'routeName' => 'reports.equipment-inventory',
        ],
        [
          'url' => '/reports/loan-applications',
          'name' => 'menu.reports.loan_applications_report',
          'icon' => 'journal-text',
          'routeName' => 'reports.loan-applications',
        ],
        [
          'url' => '/reports/email-accounts', // Assuming this route exists and matches email_applications
          'name' => 'menu.reports.email_applications_report',
          'icon' => 'envelope-paper-fill',
          'routeName' => 'reports.email-accounts',
          'role' => ['Admin', 'IT Admin'], // Restrict to Admin and IT Admin based on common usage
        ],
        [
          // This is the specific update for the User Activity Log
          'url' => '/reports/activity-log',
          'name' => 'menu.reports.user_activity_report',
          'icon' => 'person-check-fill', // Updated icon to bi bi-person-check-fill
          'routeName' => 'reports.activity-log', // <--- IMPORTANT: Matches the Livewire route
          'role' => ['Admin', 'BPM Staff', 'IT Admin'],
        ],
        [
          'url' => '/reports/loan-history',
          'name' => 'menu.reports.loan_history_report',
          'icon' => 'clock-history',
          'routeName' => 'reports.loan-history',
          'role' => ['Admin', 'BPM Staff'],
        ],
        [
          'url' => '/reports/utilization-report',
          'name' => 'menu.reports.utilization_report',
          'icon' => 'graph-up',
          'routeName' => 'reports.utilization-report',
          'role' => ['Admin', 'BPM Staff', 'IT Admin'],
        ],
        [
          'url' => '/reports/loan-status-summary',
          'name' => 'menu.reports.loan_status_summary_report',
          'icon' => 'pie-chart-fill',
          'routeName' => 'reports.loan-status-summary',
          'role' => ['Admin', 'BPM Staff'],
        ],
      ],
    ],

    // System Settings Section (Admin Only)
    [
      'menuHeader' => 'menu.section.system_settings',
      'role' => ['Admin'],
    ],
    [
      'name' => 'menu.system_settings.title',
      'icon' => 'gear-fill',
      'routeNamePrefix' => 'settings.',
      'role' => ['Admin'],
      'submenu' => [
        [
          'url' => '/settings/users',
          'name' => 'menu.system_settings.users',
          'icon' => 'people-fill',
          'routeName' => 'settings.users.index',
        ],
        [
          'url' => '/settings/roles',
          'name' => 'menu.system_settings.roles',
          'icon' => 'person-badge-fill',
          'routeName' => 'settings.roles.index',
        ],
        [
          'url' => '/settings/permissions',
          'name' => 'menu.system_settings.permissions',
          'icon' => 'key-fill',
          'routeName' => 'settings.permissions.index',
        ],
        [
          'url' => '/settings/grades',
          'name' => 'menu.system_settings.grades',
          'icon' => 'award-fill',
          'routeName' => 'settings.grades.index',
        ],
        [
          'url' => '/settings/departments',
          'name' => 'menu.system_settings.departments',
          'icon' => 'building-fill',
          'routeName' => 'settings.departments.index',
        ],
        [
          'url' => '/settings/positions',
          'name' => 'menu.system_settings.positions',
          'icon' => 'briefcase-fill',
          'routeName' => 'settings.positions.index',
        ],
      ],
    ],
    [
      'url' => '/log-viewer',
      'name' => 'menu.system_logs',
      'icon' => 'file-text-fill',
      'routeName' => 'log-viewer.index',
      'target' => '_blank',
      'role' => ['Admin'],
    ],
  ],
];
