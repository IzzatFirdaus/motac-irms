<?php

// config/menu.php

return [
    /*
    |--------------------------------------------------------------------------
    | Vertical Menu Data
    |--------------------------------------------------------------------------
    |
    | Structure:
    | - name: The display name of the menu item (translatable string key).
    | - icon: Tabler Icon class (e.g., 'ti ti-smart-home').
    | - routeName: The Laravel named route for the menu item.
    | - routeNamePrefix: A prefix for routes to make parent menu active (e.g., 'admin.users').
    | - url: A direct URL if not using a named route.
    | - submenu: An array of submenu items, following the same structure.
    | - menuHeader: If set, this item will be a header text.
    | - role: An array or string of roles that can see this menu item.
    |         Checked against the user's role from VerticalMenu.php.
    |         If not set, visible to all authenticated users with a role, or based on permissions.
    |         The 'Admin' role typically sees all items due to Blade logic.
    | - permissions: An array or string of permissions required (if using Spatie permissions and Blade logic is adapted).
    |
    */
    'menu' => [
        // Dashboard
        [
            'name' => 'Dashboard',
            'icon' => 'ti ti-smart-home',
            'routeName' => 'dashboard', // General dashboard route
        ],

        // My Applications (for general users)
        [
            'menuHeader' => 'Aplikasi Saya',
            // No specific role here, visibility determined by items. Assumes a basic authenticated user role.
        ],
        [
            'name' => 'Permohonan Emel & ID Pengguna',
            'icon' => 'ti ti-mail',
            'routeName' => 'my-applications.email.index', // User's list of email applications
            'routeNamePrefix' => 'my-applications.email',
            // 'role' => ['User'], // Example if 'User' is a defined role for non-admins
        ],
        [
            'name' => 'Permohonan Pinjaman Peralatan ICT',
            'icon' => 'ti ti-device-laptop',
            'routeName' => 'my-applications.loan.index', // User's list of loan applications
            'routeNamePrefix' => 'my-applications.loan',
            // 'role' => ['User'],
        ],

        // Approvals
        [
            'menuHeader' => 'Tindakan Kelulusan',
            'role' => ['Admin', 'Approver'], // 'Approver' role or users with approval tasks
        ],
        [
            'name' => 'Senarai Kelulusan',
            'icon' => 'ti ti-checks',
            'routeName' => 'approvals.index', // Approval dashboard/list
            'role' => ['Admin', 'Approver'],
        ],

        // ICT Equipment (Public View)
        [
            'menuHeader' => 'Maklumat Peralatan ICT',
        ],
        [
            'name' => 'Senarai Peralatan ICT',
            'icon' => 'ti ti-list-details',
            'routeName' => 'equipment.index', // Public listing of equipment
        ],

        // Resource Management (Primarily for Admin, BPM Staff, IT Admin)
        [
            'menuHeader' => 'Pengurusan Sumber',
            'role' => ['Admin', 'BPM Staff', 'IT Admin'],
        ],
        [
            'name' => 'Permohonan Emel (Proses)',
            'icon' => 'ti ti-mail-cog',
            'routeName' => 'admin.email-applications.index', // IT Admin processing email applications
            'role' => ['Admin', 'IT Admin'],
        ],
        [
            'name' => 'Permohonan Pinjaman (Proses)',
            'icon' => 'ti ti-settings-cog',
            'role' => ['Admin', 'BPM Staff'],
            'routeNamePrefix' => 'admin.loan-applications.management', // Prefix for the group
            'submenu' => [
                [
                    'name' => 'Semua Permohonan Pinjaman',
                    'routeName' => 'admin.loan-applications.management.index', // Admin/BPM view of all loan apps
                ],
                [
                    'name' => 'Proses Pengeluaran',
                    'routeName' => 'admin.loan-applications.management.issue', // Page for issuing
                ],
                [
                    'name' => 'Proses Pemulangan',
                    'routeName' => 'admin.loan-applications.management.return', // Page for returning
                ],
            ],
        ],
        [
            'name' => 'Inventori Peralatan ICT',
            'icon' => 'ti ti-building-warehouse',
            'routeName' => 'admin.equipment-inventory.index', // CRUD for equipment
            'role' => ['Admin', 'BPM Staff'],
        ],

        // System Administration (Admin Role)
        [
            'menuHeader' => 'Pentadbiran Sistem',
            'role' => ['Admin'],
        ],
        [
            'name' => 'Pengurusan Pengguna',
            'icon' => 'ti ti-users',
            'routeName' => 'admin.users.index', // User CRUD
            'role' => ['Admin'],
        ],
        [
            'name' => 'Struktur Organisasi',
            'icon' => 'ti ti-hierarchy-2',
            'role' => ['Admin'],
            'routeNamePrefix' => 'settings.organization',
            'submenu' => [
                [
                    'name' => 'Jabatan',
                    'routeName' => 'settings.departments.index', // Department CRUD
                ],
                [
                    'name' => 'Jawatan',
                    'routeName' => 'settings.positions.index', // Position CRUD
                ],
                [
                    'name' => 'Gred',
                    'routeName' => 'settings.grades.index', // Grade CRUD
        ],
            ],
        ],
        [
            'name' => 'Peranan & Kebenaran',
            'icon' => 'ti ti-user-shield',
            'routeName' => 'settings.roles.index', // Roles & Permissions UI
            'role' => ['Admin'],
        ],
        [
            'name' => 'Laporan Sistem',
            'icon' => 'ti ti-report-analytics',
            'role' => ['Admin'],
            'routeNamePrefix' => 'admin.reports',
            'submenu' => [
                [
                    'name' => 'Laporan Akaun Emel',
                    'routeName' => 'admin.reports.email-accounts',
                ],
                [
                    'name' => 'Laporan Peralatan ICT',
                    'routeName' => 'admin.reports.equipment',
                ],
                [
                    'name' => 'Laporan Pinjaman ICT',
                    'routeName' => 'admin.reports.loan-applications',
                ],
                [
                    'name' => 'Laporan Aktiviti Pengguna',
                    'routeName' => 'admin.reports.user-activity',
                ],
            ],
        ],
        // Example of a Settings link if not covered above
        // [
        // 'name' => 'Konfigurasi Sistem',
        // 'icon' => 'ti ti-settings',
        // 'routeName' => 'admin.settings.index',
        // 'role' => ['Admin'],
        // ],
    ],
];
