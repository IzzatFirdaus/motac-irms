<?php

// config/menu.php

return [
    /*
    |--------------------------------------------------------------------------
    | Vertical Menu Data
    |--------------------------------------------------------------------------
    |
    | This structure is directly used by App\Livewire\Sections\Menu\VerticalMenu.php
    | by accessing config('menu.menu').
    |
    | Structure for each item:
    | - name: The display name of the menu item (translatable string key).
    | - icon: Tabler Icon class (e.g., 'ti ti-smart-home').
    | - routeName: The Laravel named route for the menu item.
    | - routeNamePrefix: A prefix for routes to make parent menu active (e.g., 'admin.users').
    | - url: A direct URL if not using a named route.
    | - submenu: An array of submenu items, following the same structure.
    | - menuHeader: If set, this item will be a header text.
    | - role: An array or string of roles that can see this menu item.
    | - slug: A string identifier, often used for active state checking.
    | - target: HTML target attribute for links (e.g., '_blank').
    |
    */
    'menu' => [
        [
            'url' => '/dashboard',
            'name' => 'menu.dashboard',
            'icon' => 'menu-icon tf-icons ti ti-smart-home',
            'slug' => 'dashboard',
            'routeName' => 'dashboard',
            'role' => ['Admin', 'BPM Staff', 'IT Admin', 'Employee', 'Approver'],
        ],
        [
            'menuHeader' => 'menu.section.resource_management',
            'name' => 'menu.section.resource_management', // name property added for consistency if menuHeader is also an item
            'role' => ['Admin', 'BPM Staff', 'IT Admin', 'Employee', 'Approver'],
        ],
        [
            'name' => 'menu.my_applications.title',
            'icon' => 'menu-icon tf-icons ti ti-folder-search',
            'slug' => 'my-applications',
            'role' => ['Employee', 'Admin', 'BPM Staff', 'IT Admin', 'Approver'],
            'submenu' => [
                [
                    'url' => '/email-applications',
                    'name' => 'menu.my_applications.email',
                    'slug' => 'email-applications.index',
                    'routeName' => 'email-applications.index',
                    'role' => ['Employee'],
                ],
                [
                    'url' => '/loan-applications',
                    'name' => 'menu.my_applications.loan',
                    'slug' => 'loan-applications.index',
                    'routeName' => 'loan-applications.index',
                    'role' => ['Employee'],
                ],
            ],
        ],
        [
            'name' => 'menu.apply_for_resources.title',
            'icon' => 'menu-icon tf-icons ti ti-file-plus',
            'slug' => 'application-forms',
            'role' => ['Employee', 'Admin'],
            'submenu' => [
                [
                    'url' => '/email-applications/create',
                    'name' => 'menu.apply_for_resources.email',
                    'slug' => 'email-applications.create',
                    'routeName' => 'email-applications.create',
                ],
                [
                    'url' => '/loan-applications/create',
                    'name' => 'menu.apply_for_resources.loan',
                    'slug' => 'loan-applications.create',
                    'routeName' => 'loan-applications.create',
                ],
            ],
        ],
        [
            'url' => '/approvals/dashboard',
            'name' => 'menu.approvals_dashboard',
            'icon' => 'menu-icon tf-icons ti ti-user-check',
            'slug' => 'approvals.dashboard',
            'routeName' => 'approvals.dashboard',
            'role' => ['Admin', 'Approver', 'BPM Staff', 'IT Admin'],
        ],
        [
            'name' => 'menu.administration.title',
            'icon' => 'menu-icon tf-icons ti ti-settings-cog',
            'slug' => 'resource-management',
            'routeNamePrefix' => 'resource-management',
            'role' => ['Admin', 'BPM Staff', 'IT Admin'],
            'submenu' => [
                [
                    'name' => 'menu.administration.bpm_operations.title',
                    'icon' => 'menu-icon tf-icons ti ti-tool', // Icon can be added to submenu parents too
                    'slug' => 'resource-management.bpm',
                    'routeNamePrefix' => 'resource-management.bpm',
                    'role' => ['Admin', 'BPM Staff'],
                    'submenu' => [
                        [
                            'url' => '/resource-management/bpm/outstanding-loans',
                            'name' => 'menu.administration.bpm_operations.outstanding_loans',
                            'slug' => 'resource-management.bpm.outstanding-loans',
                            'routeName' => 'resource-management.bpm.outstanding-loans',
                        ],
                        [
                            'url' => '/resource-management/bpm/issued-loans',
                            'name' => 'menu.administration.bpm_operations.issued_loans',
                            'slug' => 'resource-management.bpm.issued-loans',
                            'routeName' => 'resource-management.bpm.issued-loans',
                        ],
                    ],
                ],
                [
                    'url' => '/resource-management/equipment-admin',
                    'name' => 'menu.administration.equipment_management',
                    'icon' => 'menu-icon tf-icons ti ti-device-laptop',
                    'slug' => 'resource-management.equipment-admin.index',
                    'routeName' => 'resource-management.equipment-admin.index',
                    'role' => ['Admin', 'BPM Staff'],
                ],
                [
                    'url' => '/resource-management/email-applications-admin',
                    'name' => 'menu.administration.email_applications',
                    'icon' => 'menu-icon tf-icons ti ti-mail-cog',
                    'slug' => 'resource-management.email-applications-admin.index',
                    'routeName' => 'resource-management.email-applications-admin.index',
                    'role' => ['Admin', 'IT Admin'],
                ],
                [
                    'url' => '/resource-management/users-admin',
                    'name' => 'menu.administration.users_list',
                    'icon' => 'menu-icon tf-icons ti ti-users-group',
                    'slug' => 'resource-management.users-admin.index',
                    'routeName' => 'resource-management.users-admin.index',
                    'role' => ['Admin'],
                ],
            ],
        ],
        [
            'menuHeader' => 'menu.section.system_config',
            'name' => 'menu.section.system_config', // name property added
            'role' => ['Admin'],
        ],
        [
            'name' => 'menu.settings.title',
            'icon' => 'menu-icon tf-icons ti ti-adjustments-horizontal',
            'slug' => 'settings',
            'routeNamePrefix' => 'settings',
            'role' => ['Admin'],
            'submenu' => [
                [
                    'url' => '/settings/users',
                    'name' => 'menu.settings.user_management',
                    'slug' => 'settings.users.index',
                    'routeName' => 'settings.users.index',
                ],
                [
                    'url' => '/settings/roles',
                    'name' => 'menu.settings.roles_permissions',
                    'slug' => 'settings.roles.index',
                    'routeName' => 'settings.roles.index',
                ],
                [
                    'url' => '/settings/grades',
                    'name' => 'menu.settings.grades_management',
                    'slug' => 'settings.grades.index',
                    'routeName' => 'settings.grades.index',
                ],
                [
                    'url' => '/settings/departments',
                    'name' => 'menu.settings.departments_management',
                    'slug' => 'settings.departments.index',
                    'routeName' => 'settings.departments.index',
                ],
                [
                    'url' => '/settings/positions',
                    'name' => 'menu.settings.positions_management',
                    'slug' => 'settings.positions.index',
                    'routeName' => 'settings.positions.index',
                ],
            ],
        ],
        [
            'name' => 'menu.reports.title',
            'icon' => 'menu-icon tf-icons ti ti-chart-bar',
            'slug' => 'reports',
            'routeNamePrefix' => 'reports',
            'role' => ['Admin', 'BPM Staff'],
            'submenu' => [
                [
                    'url' => '/reports/equipment-inventory',
                    'name' => 'menu.reports.equipment_report',
                    'slug' => 'reports.equipment-inventory',
                    'routeName' => 'reports.equipment-inventory',
                ],
                [
                    'url' => '/reports/email-accounts',
                    'name' => 'menu.reports.email_accounts_report',
                    'slug' => 'reports.email-accounts',
                    'routeName' => 'reports.email-accounts',
                ],
                [
                    'url' => '/reports/loan-applications',
                    'name' => 'menu.reports.loan_applications_report',
                    'slug' => 'reports.loan-applications',
                    'routeName' => 'reports.loan-applications',
                ],
                [
                    'url' => '/reports/activity-log',
                    'name' => 'menu.reports.user_activity_report',
                    'slug' => 'reports.activity-log',
                    'routeName' => 'reports.activity-log',
                    'role' => ['Admin'],
                ],
            ],
        ],
        [
            'url' => '/log-viewer',
            'name' => 'menu.system_logs',
            'icon' => 'menu-icon tf-icons ti ti-file-text',
            'slug' => 'log-viewer.index',
            'routeName' => 'log-viewer.index',
            'target' => '_blank',
            'role' => ['Admin'],
        ],
    ],
];
