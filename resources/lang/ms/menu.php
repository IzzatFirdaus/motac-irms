<?php
// Compatibility wrapper so tests and older code referencing `resources/lang/ms/menu.php` still work.
// It re-uses `menu_ms.php` and provides aliases for legacy keys.

$base = [];
if (file_exists(__DIR__ . '/menu_ms.php')) {
    $base = require __DIR__ . '/menu_ms.php';
}

return [
    'dashboard' => $base['dashboard'] ?? 'Papan Pemuka',

    'section' => [
        'public' => $base['section']['public'] ?? 'Awam',
        'resource_management' => $base['section']['resource_management'] ?? 'Pengurusan Sumber',
        'reports_analytics' => $base['section']['reports_analytics'] ?? 'Laporan & Analitik',
        'system_settings' => $base['section']['system_settings'] ?? 'Tetapan Sistem',
    ],

    'home' => $base['home'] ?? 'Laman Utama',
    'contact_us' => $base['contact_us'] ?? 'Hubungi Kami',
    'login' => $base['login'] ?? 'Log Masuk',

    'my_applications' => [
        'title' => $base['my_applications']['title'] ?? 'Permohonan Saya',
        'loan' => $base['my_applications']['loan'] ?? 'Permohonan Pinjaman Saya',
        'helpdesk' => $base['my_applications']['helpdesk'] ?? 'Tiket Meja Bantuan Saya',
    ],

    'apply_for_resources' => [
        'title' => $base['apply_for_resources']['title'] ?? 'Mohon Sumber Baharu',
        'loan' => $base['apply_for_resources']['loan'] ?? 'Pinjaman Peralatan ICT Baharu',
        'helpdesk_ticket' => $base['apply_for_resources']['helpdesk_ticket'] ?? 'Tiket Meja Bantuan Baharu',
        'email' => 'Mohon melalui E-mel',
    ],

    'approvals' => [
        'title' => $base['approvals']['title'] ?? 'Kelulusan',
        'pending_tasks' => $base['approvals']['pending_tasks'] ?? 'Tugasan Menunggu',
        'approval_history' => $base['approvals']['approval_history'] ?? 'Sejarah Kelulusan',
    ],

    'resource_inventory' => [
        'title' => $base['resource_inventory']['title'] ?? 'Inventori Sumber ICT',
        'equipment' => $base['resource_inventory']['equipment'] ?? 'Inventori Peralatan ICT',
        'loan_transactions' => $base['resource_inventory']['loan_transactions'] ?? 'Transaksi Pinjaman ICT',
    ],

    'reports' => [
        'title' => $base['reports']['title'] ?? 'Laporan',
        'equipment_report' => $base['reports']['equipment_report'] ?? 'Laporan Inventori Peralatan',
        'loan_applications_report' => $base['reports']['loan_applications_report'] ?? 'Laporan Permohonan Pinjaman',
        'helpdesk_report' => $base['reports']['helpdesk_report'] ?? 'Laporan Meja Bantuan',
        'user_activity_report' => $base['reports']['user_activity_report'] ?? 'Laporan Aktiviti Pengguna',
        'loan_history_report' => $base['reports']['loan_history_report'] ?? 'Laporan Sejarah Pinjaman',
        'utilization_report' => $base['reports']['utilization_report'] ?? 'Laporan Penggunaan Peralatan',
        'loan_status_summary_report' => $base['reports']['loan_status_summary_report'] ?? 'Laporan Ringkasan Status Pinjaman',
    ],

    'system_settings' => [
        'title' => $base['system_settings']['title'] ?? 'Tetapan Sistem',
        'users' => $base['system_settings']['users'] ?? 'Pengurusan Pengguna',
        'roles' => $base['system_settings']['roles'] ?? 'Peranan',
        'permissions' => $base['system_settings']['permissions'] ?? 'Kebenaran',
        'departments' => $base['system_settings']['departments'] ?? 'Jabatan',
        'grades' => $base['system_settings']['grades'] ?? 'Gred',
        'asset_types' => $base['system_settings']['asset_types'] ?? 'Jenis Aset',
        'equipment_conditions' => $base['system_settings']['equipment_conditions'] ?? 'Kondisi Peralatan',
        'accessories' => $base['system_settings']['accessories'] ?? 'Aksesori Peralatan',
        'positions' => 'Jawatan', // Added missing key
    ],

    'general_settings' => [
        'title' => $base['general_settings']['title'] ?? 'Tetapan Umum',
        'manage_grades' => $base['general_settings']['manage_grades'] ?? 'Urus Gred Jawatan',
        'manage_departments' => $base['general_settings']['manage_departments'] ?? 'Urus Jabatan/Bahagian',
        'manage_asset_types' => $base['general_settings']['manage_asset_types'] ?? 'Urus Jenis Aset',
        'manage_equipment_conditions' => $base['general_settings']['manage_equipment_conditions'] ?? 'Urus Kondisi Peralatan',
        'manage_accessories' => $base['general_settings']['manage_accessories'] ?? 'Urus Aksesori',
    ],

    'system_logs' => $base['system_logs'] ?? 'Log Sistem',

    'notifications' => [
        'title' => $base['notifications']['title'] ?? 'Notifikasi',
        'view_all' => $base['notifications']['view_all'] ?? 'Lihat Semua Notifikasi',
        'new_notification' => $base['notifications']['new_notification'] ?? 'Notifikasi Baharu',
    ],

    'profile' => $base['profile'] ?? 'Profil Saya',
    'logout' => $base['logout'] ?? 'Log Keluar',

    // Legacy aliases for backward compatibility with tests
    'settings' => [
        'title' => $base['system_settings']['title'] ?? 'Tetapan Sistem',
    ],

    'administration' => [
        'title' => 'Pentadbiran',
        'equipment_management' => $base['resource_inventory']['equipment'] ?? 'Pengurusan Peralatan',
        'helpdesk_management' => 'Pengurusan Meja Bantuan',
    ],

    'helpdesk' => [
        'title' => 'Meja Bantuan',
    ],
];
