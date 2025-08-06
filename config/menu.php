<?php

/**
 * MOTAC IRMS v4.0 Menu Configuration
 * Canonical menu structure for sidebar and navigation.
 * This file is the authoritative source for menu items.
 *
 * Fields:
 * - url: Direct URL for the menu link
 * - name: Translation key for the menu label
 * - icon: Bootstrap icon class (without 'bi-')
 * - routeName: Named route for Laravel route() helper
 * - routeNamePrefix: For menu highlighting of subroutes
 * - role: Array of roles allowed to view the menu item
 * - submenu: Array of child menu items
 * - menuHeader: Translation key for header sections
 * - target: Link target (_blank, etc.)
 */

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
            'routeNamePrefix' => 'loan-applications.index,helpdesk-tickets.index',
            'role' => ['User', 'Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'],
            'submenu' => [
                [
                    'url' => '/loan-applications',
                    'name' => 'menu.my_applications.loan',
                    'icon' => 'laptop-fill',
                    'routeName' => 'loan-applications.index',
                ],
                [
                    'url' => '/helpdesk-tickets',
                    'name' => 'menu.my_applications.helpdesk',
                    'icon' => 'life-preserver',
                    'routeName' => 'helpdesk-tickets.index',
                ],
            ],
        ],
        [
            'name' => 'menu.apply_for_resources.title',
            'icon' => 'file-earmark-plus-fill',
            'role' => ['User'],
            'submenu' => [
                [
                    'url' => '/loan-applications/create',
                    'name' => 'menu.apply_for_resources.loan',
                    'icon' => 'box-arrow-up-right',
                    'routeName' => 'loan-applications.create',
                ],
                [
                    'url' => '/helpdesk-tickets/create',
                    'name' => 'menu.apply_for_resources.helpdesk_ticket',
                    'icon' => 'ticket-fill',
                    'routeName' => 'helpdesk-tickets.create',
                ],
            ],
        ],
        [
            'name' => 'menu.approvals.title',
            'icon' => 'clipboard-check-fill',
            'routeName' => 'approvals.dashboard',
            'role' => ['Admin', 'Approver', 'HOD'],
            'submenu' => [
                // Add specific approval sub-items if needed, e.g., 'loan-approvals.index'
            ],
        ],
        [
            'name' => 'menu.resource_inventory.title',
            'icon' => 'boxes',
            'routeNamePrefix' => 'equipment.index,equipment.show,equipment.create,equipment.edit,loan-transactions.index,loan-transactions.show',
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
                    'url' => '/reports/helpdesk-tickets',
                    'name' => 'menu.reports.helpdesk_report',
                    'icon' => 'life-preserver',
                    'routeName' => 'reports.helpdesk-tickets',
                ],
                [
                    'url' => '/reports/activity-log',
                    'name' => 'menu.reports.user_activity_report',
                    'icon' => 'person-check-fill',
                    'routeName' => 'reports.activity-log',
                ],
                [
                    'url' => '/reports/loan-history',
                    'name' => 'menu.reports.loan_history_report',
                    'icon' => 'clock-history',
                    'routeName' => 'reports.loan-history',
                ],
                [
                    'url' => '/reports/utilization-report',
                    'name' => 'menu.reports.utilization_report',
                    'icon' => 'graph-up',
                    'routeName' => 'reports.utilization-report',
                ],
                [
                    'url' => '/reports/loan-status-summary',
                    'name' => 'menu.reports.loan_status_summary_report',
                    'icon' => 'pie-chart-fill',
                    'routeName' => 'reports.loan-status-summary',
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
    // Optionally add a version or last_updated for syncing with JSON
    'version' => '2025-08-06',
    'last_updated' => '2025-08-06T12:19:04Z'
];
