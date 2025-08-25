<?php

// config/menu.php

return [
  /*
    |--------------------------------------------------------------------------
    | Vertical Menu Data (Refactored for Bootstrap Icons)
    |--------------------------------------------------------------------------
    |
    | - icon: Bootstrap Icon name (e.g., 'house-door-fill').
    |
    */
  'menu' => [
    [
      'url' => '/dashboard',
      'name' => 'menu.dashboard',
      'icon' => 'house-door-fill', // Changed from 'menu-icon tf-icons ti ti-smart-home'
      'slug' => 'dashboard',
      'routeName' => 'dashboard',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver'], // "User" instead of "Employee"
    ],
    [
      'menuHeader' => 'menu.section.resource_management',
      'name' => 'menu.section.resource_management',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver'], // "User"
    ],
    [
      'name' => 'menu.my_applications.title',
      'icon' => 'folder-check', // Changed from 'menu-icon tf-icons ti ti-folder-search', consistent with original JSON
      'slug' => 'my-applications',
      'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver'], // "User"
      'submenu' => [
        [
          'url' => '/email-applications',
          'name' => 'menu.my_applications.email',
          'icon' => 'envelope-paper-fill', // Added from original JSON
          'slug' => 'email-applications.index',
          'routeName' => 'email-applications.index',
          'role' => ['User'], // "User"
        ],
        [
          'url' => '/loan-applications',
          'name' => 'menu.my_applications.loan',
          'icon' => 'laptop-fill', // Added from original JSON
          'slug' => 'loan-applications.index',
          'routeName' => 'loan-applications.index',
          'role' => ['User'], // "User"
        ],
      ],
    ],
    [
      'name' => 'menu.apply_for_resources.title',
      'icon' => 'file-earmark-plus-fill', // Changed from 'menu-icon tf-icons ti ti-file-plus', consistent with original JSON
      'slug' => 'application-forms',
      'role' => ['User', 'Admin'], // "User"
      'submenu' => [
        [
          'url' => '/email-applications/create',
          'name' => 'menu.apply_for_resources.email',
          'icon' => 'envelope-plus-fill', // Added from original JSON
          'slug' => 'email-applications.create',
          'routeName' => 'email-applications.create',
          'role' => ['User'],
        ],
        [
          'url' => '/loan-applications/create',
          'name' => 'menu.apply_for_resources.loan',
          'icon' => 'box-arrow-up-right', // Added from original JSON
          'slug' => 'loan-applications.create',
          'routeName' => 'loan-applications.create',
          'role' => ['User'],
        ],
      ],
    ],
    [
      'url' => '/approvals/dashboard',
      'name' => 'menu.approvals_dashboard',
      'icon' => 'person-check-fill', // Changed from 'menu-icon tf-icons ti ti-user-check'
      'slug' => 'approvals.dashboard',
      'routeName' => 'approvals.dashboard',
      'role' => ['Admin', 'Approver', 'BPM Staff', 'IT Admin'],
    ],
    [
      'name' => 'menu.administration.title',
      'icon' => 'gear-wide-connected', // Changed from 'menu-icon tf-icons ti ti-settings-cog'
      'slug' => 'resource-management',
      'routeNamePrefix' => 'resource-management',
      'role' => ['Admin', 'BPM Staff', 'IT Admin'],
      'submenu' => [
        [
          'name' => 'menu.administration.bpm_operations.title',
          'icon' => 'tools', // Changed from 'menu-icon tf-icons ti ti-tool'
          'slug' => 'resource-management.bpm',
          'routeNamePrefix' => 'resource-management.bpm',
          'role' => ['Admin', 'BPM Staff'],
          'submenu' => [
            [
              'url' => '/resource-management/bpm/outstanding-loans',
              'name' => 'menu.administration.bpm_operations.outstanding_loans',
              'icon' => 'hourglass-split', // Added from original JSON
              'slug' => 'resource-management.bpm.outstanding-loans',
              'routeName' => 'resource-management.bpm.outstanding-loans',
            ],
            [
              'url' => '/resource-management/bpm/issued-loans',
              'name' => 'menu.administration.bpm_operations.issued_loans',
              'icon' => 'box-arrow-in-up-right', // Added from original JSON
              'slug' => 'resource-management.bpm.issued-loans',
              'routeName' => 'resource-management.bpm.issued-loans',
            ],
          ],
        ],
        [
          'url' => '/resource-management/equipment-admin',
          'name' => 'menu.administration.equipment_management',
          'icon' => 'pc-display', // Changed from 'menu-icon tf-icons ti ti-device-laptop', consistent with original JSON
          'slug' => 'resource-management.equipment-admin.index',
          'routeName' => 'resource-management.equipment-admin.index',
          'role' => ['Admin', 'BPM Staff'],
        ],
        [
          'url' => '/resource-management/email-applications-admin',
          'name' => 'menu.administration.email_applications',
          'icon' => 'envelope-gear-fill', // Changed from 'menu-icon tf-icons ti ti-mail-cog'
          'slug' => 'resource-management.email-applications-admin.index',
          'routeName' => 'resource-management.email-applications-admin.index',
          'role' => ['Admin', 'IT Admin'],
        ],
        [
          'url' => '/resource-management/users-admin',
          'name' => 'menu.administration.users_list',
          'icon' => 'people-fill', // Changed from 'menu-icon tf-icons ti ti-users-group'
          'slug' => 'resource-management.users-admin.index',
          'routeName' => 'resource-management.users-admin.index',
          'role' => ['Admin'],
        ],
      ],
    ],
    [
      'menuHeader' => 'menu.section.system_config',
      'name' => 'menu.section.system_config',
      'role' => ['Admin'],
    ],
    [
      'name' => 'menu.settings.title',
      'icon' => 'sliders', // Changed from 'menu-icon tf-icons ti ti-adjustments-horizontal'
      'slug' => 'settings',
      'routeNamePrefix' => 'settings',
      'role' => ['Admin'],
      'submenu' => [
        [
          'url' => '/settings/users',
          'name' => 'menu.settings.user_management',
          'icon' => 'person-lines-fill', // Added from original JSON
          'slug' => 'settings.users.index',
          'routeName' => 'settings.users.index',
        ],
        [
          'url' => '/settings/roles',
          'name' => 'menu.settings.roles_permissions',
          'icon' => 'person-badge-fill', // Added from original JSON
          'slug' => 'settings.roles.index',
          'routeName' => 'settings.roles.index',
        ],
        [
          'url' => '/settings/grades',
          'name' => 'menu.settings.grades_management',
          'icon' => 'bar-chart-steps', // Added from original JSON
          'slug' => 'settings.grades.index',
          'routeName' => 'settings.grades.index',
        ],
        [
          'url' => '/settings/departments',
          'name' => 'menu.settings.departments_management',
          'icon' => 'building-fill', // Added from original JSON
          'slug' => 'settings.departments.index',
          'routeName' => 'settings.departments.index',
        ],
        [
          'url' => '/settings/positions',
          'name' => 'menu.settings.positions_management',
          'icon' => 'person-workspace', // Added from original JSON
          'slug' => 'settings.positions.index',
          'routeName' => 'settings.positions.index',
        ],
      ],
    ],
    [
      'name' => 'menu.reports.title',
      'icon' => 'file-earmark-bar-graph-fill', // Changed from 'menu-icon tf-icons ti ti-chart-bar'
      'slug' => 'reports',
      'routeNamePrefix' => 'reports',
      'role' => ['Admin', 'BPM Staff'],
      'submenu' => [
        [
          'url' => '/reports/equipment-inventory',
          'name' => 'menu.reports.equipment_report',
          'icon' => 'card-list', // Added from original JSON
          'slug' => 'reports.equipment-inventory',
          'routeName' => 'reports.equipment-inventory',
        ],
        [
          'url' => '/reports/email-accounts',
          'name' => 'menu.reports.email_accounts_report',
          'icon' => 'envelope-check-fill', // Added from original JSON
          'slug' => 'reports.email-accounts',
          'routeName' => 'reports.email-accounts',
        ],
        [
          'url' => '/reports/loan-applications',
          'name' => 'menu.reports.loan_applications_report',
          'icon' => 'journal-text', // Added.
          'slug' => 'reports.loan-applications',
          'routeName' => 'reports.loan-applications',
        ],
        [
          'url' => '/reports/activity-log',
          'name' => 'menu.reports.user_activity_report',
          'icon' => 'person-bounding-box', // Added from original JSON
          'slug' => 'reports.activity-log',
          'routeName' => 'reports.activity-log',
          'role' => ['Admin'],
        ],
      ],
    ],
    [
      'url' => '/log-viewer',
      'name' => 'menu.system_logs',
      'icon' => 'file-text-fill', // Changed from 'menu-icon tf-icons ti ti-file-text'
      'slug' => 'log-viewer.index',
      'routeName' => 'log-viewer.index',
      'target' => '_blank',
      'role' => ['Admin'],
    ],
  ],
];
