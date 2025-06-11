<?php
// config/menu.php

return [
  'menu' => [
    [
      'url' => '/dashboard',
      'name' => 'menu.dashboard',
      'icon' => 'house-door-fill',
      'routeName' => 'dashboard',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver', 'HOD']
    ],
    [
      'menuHeader' => 'menu.section.resource_management',
      'role' => ['Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver', 'HOD']
    ],
    [
      'name' => 'menu.my_applications.title',
      'icon' => 'folder-check',
      'routeNamePrefix' => 'email-applications.index,loan-applications.index',
      'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
      'submenu' => [
        [
          'url' => '/email-applications',
          'name' => 'menu.my_applications.email',
          'icon' => 'envelope-paper-fill',
          'routeName' => 'email-applications.index'
        ],
        [
          'url' => '/loan-applications',
          'name' => 'menu.my_applications.loan',
          'icon' => 'laptop-fill',
          'routeName' => 'loan-applications.index'
        ]
      ]
    ],
    [
      'name' => 'menu.apply_for_resources.title',
      'icon' => 'file-earmark-plus-fill',
      'routeNamePrefix' => 'email-applications.create,loan-applications.create',
      'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
      'submenu' => [
        [
          'url' => '/email-applications/create',
          'name' => 'menu.apply_for_resources.email',
          'icon' => 'envelope-plus-fill',
          'routeName' => 'email-applications.create'
        ],
        [
          'url' => '/loan-applications/create',
          'name' => 'menu.apply_for_resources.loan',
          'icon' => 'box-arrow-up-right',
          'routeName' => 'loan-applications.create'
        ]
      ]
    ],
    [
      'url' => '/approvals/dashboard', // Corrected from '/approvals'
      'name' => 'menu.approvals_dashboard',
      'icon' => 'person-check-fill',
      'routeName' => 'approvals.dashboard',
      'role' => ['Admin', 'Approver', 'BPM Staff', 'IT Admin', 'HOD']
    ],
    [
      'name' => 'menu.administration.title',
      'icon' => 'gear-wide-connected',
      // Corrected prefix to match route groups
      'routeNamePrefix' => 'admin.',
      'role' => ['Admin', 'BPM Staff', 'IT Admin'],
      'submenu' => [
        [
            // This is a new sub-menu to group BPM operations
            'name' => 'menu.administration.bpm_operations.title',
            'icon' => 'tools',
            'role' => ['Admin', 'BPM Staff'],
            'submenu' => [
                // You would add specific BPM-related admin links here
            ]
        ],
        [
          'url' => '/admin/equipment', // Corrected URL
          'name' => 'menu.administration.equipment_management',
          'icon' => 'pc-display',
          // Corrected routeName to match web.php
          'routeName' => 'admin.equipment.index',
          'role' => ['Admin', 'BPM Staff']
        ],
        [
          'url' => '/admin/email-processing', // Corrected URL
          'name' => 'menu.administration.email_applications',
          'icon' => 'envelope-gear-fill',
          // Corrected routeName to match web.php
          'routeName' => 'admin.email-processing.index',
          'role' => ['Admin', 'IT Admin']
        ],
      ]
    ],
    [
      'menuHeader' => 'menu.section.system_config',
      'role' => ['Admin']
    ],
    [
      'name' => 'menu.settings.title',
      'icon' => 'sliders',
      'routeNamePrefix' => 'settings.', // Corrected prefix
      'role' => ['Admin'],
      'submenu' => [
        [
          'url' => '/settings/users',
          'name' => 'menu.settings.user_management',
          'icon' => 'person-lines-fill',
          'routeName' => 'settings.users.index'
        ],
        [
          'url' => '/settings/roles',
          'name' => 'menu.settings.roles_permissions',
          'icon' => 'person-badge-fill',
          'routeName' => 'settings.roles.index'
        ],
        [
          'url' => '/settings/grades',
          'name' => 'menu.settings.grades_management',
          'icon' => 'bar-chart-steps',
          'routeName' => 'settings.grades.index'
        ],
        [
          'url' => '/settings/departments',
          'name' => 'menu.settings.departments_management',
          'icon' => 'building-fill',
          'routeName' => 'settings.departments.index'
        ],
        [
          'url' => '/settings/positions',
          'name' => 'menu.settings.positions_management',
          'icon' => 'person-workspace',
          'routeName' => 'settings.positions.index'
        ]
      ]
    ],
    [
      'name' => 'menu.reports.title',
      'icon' => 'file-earmark-bar-graph-fill',
      'routeNamePrefix' => 'reports.', // Corrected prefix
      'role' => ['Admin', 'BPM Staff'],
      'submenu' => [
        [
          'url' => '/reports/equipment-inventory',
          'name' => 'menu.reports.equipment_report',
          'icon' => 'card-list',
          'routeName' => 'reports.equipment-inventory'
        ],
        [
          'url' => '/reports/loan-applications',
          'name' => 'menu.reports.loan_applications_report',
          'icon' => 'journal-text',
          'routeName' => 'reports.loan-applications'
        ],
        [
          'url' => '/reports/activity-log',
          'name' => 'menu.reports.user_activity_report',
          'icon' => 'person-bounding-box',
          'routeName' => 'reports.activity-log',
          'role' => ['Admin']
        ]
      ]
    ],
    [
      'url' => '/log-viewer',
      'name' => 'menu.system_logs',
      'icon' => 'file-text-fill',
      'routeName' => 'log-viewer.index',
      'target' => '_blank',
      'role' => ['Admin']
    ]
  ]
];
