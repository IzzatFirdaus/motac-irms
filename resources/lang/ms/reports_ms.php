<?php

/**
 * Bahasa Melayu translations for reports pages, filters, and report tables
 * Disusun mengikut kategori: tajuk utama, penapis, laporan, dan tabel
 *
 * This file contains all Malaysian language translations for the reporting system,
 * organized by categories: main titles, filters, reports, and tables.
 *
 * Last updated: 2025-08-08 by IzzatFirdaus
 */

return [

    // === Tajuk Utama & Navigasi Laporan ===
    // Main Report Titles & Navigation
    'page_title' => 'Laporan Sistem',
    'page_header' => 'Laporan Sistem Yang Tersedia',
    'view_report' => 'Lihat Laporan', // Used on report-card components
    'back_to_list' => 'Kembali ke Senarai Laporan',

    // === Penapis / Filters Umum untuk Semua Laporan ===
    // Common Filters for All Reports
    'filters' => [
        'user' => 'Pengguna',
        'all_users' => 'Semua Pengguna',
        'transaction_type' => 'Jenis Transaksi',
        'all_types' => 'Semua Jenis',
        'type_issue' => 'Pengeluaran',
        'type_return' => 'Pemulangan',
        'date_from' => 'Dari Tarikh',
        'date_to' => 'Hingga Tarikh',
        'filter_button' => 'Tapis',
        'search_placeholder' => 'Cari...',
        'reset_button' => 'Set Semula',
        'filter' => 'Tapis',
        'reset' => 'Set Semula',
    ],

    // === Laporan Aktiviti Pengguna ===
    // User Activity Reports
    'activity_log' => [
        'title' => 'Laporan Aktiviti Pengguna',
        'loan_apps' => 'Permohonan Pinjaman',
        'approvals' => 'Kelulusan',
        'registered' => 'Tarikh Daftar',
        'no_results' => 'Tiada data aktiviti pengguna tersedia.',
    ],
    'user_activity' => [
        'title' => 'Laporan Aktiviti Pengguna',
        'description' => 'Jana laporan aktiviti pengguna yang berkaitan dengan permohonan pinjaman dan kelulusan.',
        'table' => [
            'count' => 'Bil.',
        ],
    ],

    // === Laporan Kelulusan ===
    // Approval Reports
    'approvals' => [
        'table' => [
            'count' => 'Bil. Kelulusan',
        ],
    ],

    // === Laporan Inventori Peralatan ICT ===
    // Equipment Inventory Reports
    'equipment_inventory' => [
        'title' => 'Laporan Inventori Peralatan ICT',
        'description' => 'Dapatkan gambaran keseluruhan inventori peralatan ICT yang didaftarkan dalam sistem.',
        'list_header' => 'Senarai Peralatan',
        'no_results' => 'Tiada peralatan ICT ditemui untuk laporan ini.',
        'table' => [
            'asset_tag_id' => 'Tag ID Aset',
            'asset_type' => 'Jenis Aset',
            'brand' => 'Jenama',
            'model' => 'Model',
            'serial_no' => 'No. Siri',
            'op_status' => 'Status Operasi',
            'condition_status' => 'Status Keadaan',
            'department' => 'Jabatan',
            'current_user' => 'Pengguna Semasa',
            'loan_date' => 'Tarikh Pinjam',
        ],
    ],

    // === Laporan Permohonan E-mel ===
    // Email Applications Report
    'email_applications' => [
        'title' => 'Laporan Permohonan E-mel',
        'description' => 'Laporan status dan sejarah permohonan akaun e-mel.',
    ],

    // === Laporan Permohonan Pinjaman ICT ===
    // Loan Applications Reports
    'loan_applications' => [
        'title' => 'Laporan Permohonan Pinjaman Peralatan ICT',
        'description' => 'Semak laporan status dan sejarah permohonan pinjaman peralatan ICT.',
        'no_results' => 'Tiada permohonan pinjaman ditemui.',
        'table' => [
            'applicant' => 'Pemohon',
            'department' => 'Jabatan',
            'loan_dates' => 'Tempoh Pinjaman',
            'return_date' => 'Tarikh Pulang',
            'status' => 'Status',
            'count' => 'Bil.',
        ],
    ],

    // === Laporan Sejarah Pinjaman ICT ===
    // Loan History Reports
    'loan_history' => [
        'title' => 'Laporan Sejarah Pinjaman',
        'page_header' => 'Sejarah Transaksi Pinjaman',
        'description' => 'Lihat sejarah terperinci transaksi pinjaman peralatan ICT (pengeluaran & pemulangan).',
        'no_results' => 'Tiada sejarah pinjaman ditemui.',
        'table' => [
            'transaction_id' => 'ID Transaksi',
            'application_id' => 'ID Permohonan',
            'equipment' => 'Peralatan',
            'user' => 'Pengguna',
            'type' => 'Jenis',
            'date' => 'Tarikh',
            'officer' => 'Pegawai',
        ],
    ],

    // === Laporan Ringkasan Status Pinjaman ICT ===
    // Loan Status Summary Reports
    'loan_status_summary' => [
        'title' => 'Ringkasan Status Pinjaman',
        'description' => 'Gambaran ringkas bilangan permohonan mengikut status.',
        'labels' => [
            'status' => 'Status',
            'count' => 'Bilangan',
        ],
        'no_results' => 'Tiada data ringkasan status ditemui.',
    ],

    // === Laporan Penggunaan Peralatan ICT ===
    // Equipment Utilization Reports
    'utilization' => [
        'title' => 'Laporan Guna Pakai',
        'description' => 'Laporan kadar penggunaan peralatan dan ringkasan status.',
        'labels' => [
            'utilization_rate' => 'Kadar Penggunaan',
            'status_summary' => 'Ringkasan Status',
        ],
        'no_results' => 'Tiada data penggunaan ditemui.',
    ],

    // === Laporan Meja Bantuan (Helpdesk) ===
    // Helpdesk Reports (for future implementation)
    'helpdesk_report' => [
        'title' => 'Laporan Meja Bantuan',
        'description' => 'Melihat status dan butiran semua tiket meja bantuan.',
        'filter_heading' => 'Tapis Laporan',
        'filter_status' => 'Status Tiket',
        'filter_priority' => 'Prioriti Tiket',
        'filter_category' => 'Kategori Tiket',
        'filter_assigned_to' => 'Diserahkan Kepada',
        'search_placeholder' => 'Cari Subjek, Pemohon atau ID Tiket...',
        'date_from' => 'Dari Tarikh Dicipta',
        'date_to' => 'Hingga Tarikh Dicipta',
        'table' => [
            'ticket_id' => 'ID Tiket',
            'subject' => 'Subjek',
            'applicant' => 'Pemohon',
            'category' => 'Kategori',
            'priority' => 'Prioriti',
            'status' => 'Status',
            'assigned_to' => 'Diserahkan Kepada',
            'created_at' => 'Tarikh Dicipta',
            'closed_at' => 'Tarikh Ditutup',
        ],
        'no_results' => 'Tiada tiket meja bantuan ditemui untuk laporan ini.',
    ],

    // === Detailed Report Variations ===
    // Expanded reporting sections for more detailed implementations
    'equipment_inventory_report' => [
        'title' => 'Laporan Inventori Peralatan',
        'description' => 'Melihat senarai lengkap inventori peralatan ICT yang didaftarkan dalam sistem.',
        'total_equipment' => 'Jumlah Peralatan:',
        'available_equipment' => 'Peralatan Tersedia:',
        'on_loan_equipment' => 'Peralatan Sedang Dipinjam:',
        'in_repair_equipment' => 'Peralatan Dalam Pembaikan:',
        'table' => [
            'asset_tag_no' => 'No. Tag Aset',
            'type' => 'Jenis',
            'brand' => 'Jenama',
            'model' => 'Model',
            'status' => 'Status',
            'current_location' => 'Lokasi Semasa',
            'owner_department' => 'Jabatan Pemilik',
            'acquisition_date' => 'Tarikh Perolehan',
        ],
        'no_results' => 'Tiada peralatan ICT ditemui untuk laporan ini.',
    ],

    'loan_applications_report' => [
        'title' => 'Laporan Permohonan Pinjaman ICT',
        'description' => 'Melihat status dan butiran semua permohonan pinjaman peralatan ICT.',
        'filter_heading' => 'Tapis Laporan',
        'filter_status' => 'Status Permohonan',
        'search_placeholder' => 'Cari Pemohon atau ID Permohonan...',
        'date_from' => 'Dari Tarikh Mohon',
        'date_to' => 'Hingga Tarikh Mohon',
        'table' => [
            'application_id' => 'ID Permohonan',
            'applicant' => 'Pemohon',
            'department' => 'Jabatan',
            'purpose' => 'Tujuan',
            'loan_start_date' => 'Mula Pinjaman',
            'loan_end_date' => 'Akhir Pinjaman',
            'status' => 'Status',
            'submitted_at' => 'Dihantar Pada',
        ],
        'no_results' => 'Tiada permohonan pinjaman ditemui untuk laporan ini.',
    ],

    'loan_history_report' => [
        'title' => 'Laporan Sejarah Pinjaman Peralatan ICT',
        'description' => 'Melihat rekod lengkap semua transaksi pinjaman dan pemulangan peralatan ICT.',
        'filter_heading' => 'Tapis Laporan',
        'filter_equipment' => 'Peralatan',
        'filter_transaction_type' => 'Jenis Transaksi',
        'search_placeholder' => 'Cari ID Permohonan, No. Tag Aset, Pemohon...',
        'table' => [
            'transaction_id' => 'ID Transaksi',
            'application_id' => 'ID Permohonan',
            'asset_tag_no' => 'No. Tag Aset',
            'equipment_type' => 'Jenis Peralatan',
            'borrower' => 'Peminjam',
            'department' => 'Jabatan',
            'transaction_type' => 'Jenis Transaksi',
            'transaction_date' => 'Tarikh Transaksi',
            'status' => 'Status',
        ],
        'no_results' => 'Tiada sejarah transaksi pinjaman ditemui untuk laporan ini.',
    ],

    'loan_status_summary_report' => [
        'title' => 'Laporan Ringkasan Status Pinjaman ICT',
        'description' => 'Menyediakan ringkasan status peralatan ICT yang sedang dipinjam.',
        'total_loans' => 'Jumlah Pinjaman Aktif:',
        'overdue_loans' => 'Pinjaman Lewat Tempoh:',
        'upcoming_returns' => 'Pemulangan Akan Datang (7 Hari):',
        'table' => [
            'asset_tag_no' => 'No. Tag Aset',
            'equipment_type' => 'Jenis Peralatan',
            'brand_model' => 'Jenama & Model',
            'borrower' => 'Peminjam',
            'department' => 'Jabatan',
            'loan_start_date' => 'Tarikh Mula',
            'expected_return_date' => 'Tarikh Dijangka Pulang',
            'days_remaining_overdue' => 'Hari (Baki/Lewat)',
            'status' => 'Status',
        ],
        'no_results' => 'Tiada pinjaman aktif ditemui untuk laporan ini.',
    ],

    'utilization_report' => [
        'title' => 'Laporan Penggunaan Peralatan ICT',
        'description' => 'Menganalisis kadar penggunaan dan ketersediaan peralatan ICT.',
        'total_equipment_registered' => 'Jumlah Peralatan Didaftar:',
        'average_loan_duration' => 'Purata Tempoh Pinjaman (Hari):',
        'most_loaned_equipment' => 'Peralatan Paling Banyak Dipinjam:',
        'least_loaned_equipment' => 'Peralatan Paling Kurang Dipinjam:',
        'table' => [
            'asset_tag_no' => 'No. Tag Aset',
            'equipment_type' => 'Jenis Peralatan',
            'brand_model' => 'Jenama & Model',
            'total_loan_count' => 'Jumlah Pinjaman',
            'total_loan_days' => 'Jumlah Hari Dipinjam',
            'availability_percentage' => 'Peratus Ketersediaan',
        ],
        'no_results' => 'Tiada data penggunaan peralatan ditemui untuk laporan ini.',
    ],

];
