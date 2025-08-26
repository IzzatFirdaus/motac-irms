<?php

// Bahasa Melayu translations for main menu and navigation items
// Struktur disusun untuk navigasi utama aplikasi MOTAC IRMS

return [
    // === Menu Utama ===
    'dashboard' => 'Papan Pemuka',

    // === Bahagian: Awam, Pengurusan Sumber, Laporan, Tetapan Sistem ===
    'section' => [
        'public'              => 'Awam',
        'resource_management' => 'Pengurusan Sumber',
        'reports_analytics'   => 'Laporan & Analitik',
        'system_settings'     => 'Tetapan Sistem',
    ],

    // === Menu Awam / Tetamu Sahaja ===
    'home'       => 'Laman Utama',
    'contact_us' => 'Hubungi Kami',
    'login'      => 'Log Masuk',

    // === Permohonan Saya (Submenu) ===
    'my_applications' => [
        'title'    => 'Permohonan Saya',
        'loan'     => 'Permohonan Pinjaman Saya',
        'helpdesk' => 'Tiket Meja Bantuan Saya',
    ],

    // === Mohon Sumber Baharu ===
    'apply_for_resources' => [
        'title'            => 'Mohon Sumber Baharu',
        'loan'             => 'Pinjaman Peralatan ICT Baharu',
        'loan_application' => 'Pinjaman Peralatan ICT Baharu', // untuk keserasian ke belakang
        'helpdesk_ticket'  => 'Tiket Meja Bantuan Baharu',
    ],

    // === Kelulusan ===
    'approvals' => [
        'title'            => 'Kelulusan',
        'pending_tasks'    => 'Tugasan Menunggu',
        'approval_history' => 'Sejarah Kelulusan',
    ],

    // === Inventori Sumber ICT ===
    'resource_inventory' => [
        'title'             => 'Inventori Sumber ICT',
        'equipment'         => 'Inventori Peralatan ICT',
        'loan_transactions' => 'Transaksi Pinjaman ICT',
    ],

    // === Laporan & Analitik ===
    'reports' => [
        'title'                      => 'Laporan',
        'equipment_report'           => 'Laporan Inventori Peralatan',
        'loan_applications_report'   => 'Laporan Permohonan Pinjaman',
        'helpdesk_report'            => 'Laporan Meja Bantuan',
        'user_activity_report'       => 'Laporan Aktiviti Pengguna',
        'loan_history_report'        => 'Laporan Sejarah Pinjaman',
        'utilization_report'         => 'Laporan Penggunaan Peralatan',
        'loan_status_summary_report' => 'Laporan Ringkasan Status Pinjaman',
    ],

    // === Tetapan Sistem (Submenu) ===
    'system_settings' => [
        'title'                => 'Tetapan Sistem',
        'users'                => 'Pengurusan Pengguna',
        'roles'                => 'Peranan',
        'permissions'          => 'Kebenaran',
        'departments'          => 'Jabatan',
        'grades'               => 'Gred',
        'asset_types'          => 'Jenis Aset',
        'equipment_conditions' => 'Kondisi Peralatan',
        'accessories'          => 'Aksesori Peralatan',
    ],

    // === Tetapan Umum (Submenu) ===
    'general_settings' => [
        'title'                       => 'Tetapan Umum',
        'manage_grades'               => 'Urus Gred Jawatan',
        'manage_departments'          => 'Urus Jabatan/Bahagian',
        'manage_asset_types'          => 'Urus Jenis Aset',
        'manage_equipment_conditions' => 'Urus Kondisi Peralatan',
        'manage_accessories'          => 'Urus Aksesori',
    ],

    // === Log Sistem ===
    'system_logs' => 'Log Sistem',

    // === Notifikasi ===
    'notifications' => [
        'title'            => 'Notifikasi',
        'view_all'         => 'Lihat Semua Notifikasi',
        'new_notification' => 'Notifikasi Baharu',
    ],

    // === Menu Profil dan Log Keluar ===
    'profile' => 'Profil Saya',
    'logout'  => 'Log Keluar',
];
