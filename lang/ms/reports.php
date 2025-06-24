<?php

// lang/ms/reports.php

return [
    'page_title' => 'Laporan Sistem',
    'page_header' => 'Laporan Sistem Yang Tersedia',
    'view_report' => 'Lihat Laporan',
    'back_to_list' => 'Kembali ke Senarai Laporan',

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
    ],

    'equipment_inventory' => [
        'title' => 'Laporan Inventori Peralatan ICT',
        'description' => 'Jana dan lihat laporan terperinci mengenai inventori semasa peralatan ICT.',
        'list_header' => 'Senarai Peralatan',
        'no_results' => 'Tiada peralatan ICT ditemui untuk laporan ini.',
        'table' => [
            'asset_tag_id' => 'ID Tag Aset',
            'asset_type' => 'Jenis Aset',
            'brand' => 'Jenama',
            'model' => 'Model',
            'serial_no' => 'No. Siri',
            'op_status' => 'Status Operasi',
            'condition_status' => 'Status Kondisi',
            'department' => 'Jabatan',
            'current_user' => 'Pengguna Semasa',
            'loan_date' => 'Tarikh Pinjam',
        ],
    ],
    'email_applications' => [
        'title' => 'Laporan Permohonan E-mel',
        'description' => 'Analisa status dan trend permohonan akaun e-mel dan ID pengguna.',
        'list_header' => 'Senarai Permohonan E-mel',
        'no_results' => 'Tiada permohonan e-mel ditemui untuk laporan ini.',
        'search_placeholder' => 'Cari Pemohon atau E-mel...',
        'table' => [
            'applicant' => 'Pemohon',
            'application_type' => 'Jenis Permohonan',
            'application_date' => 'Tarikh Permohonan',
            'proposed_email' => 'Cadangan E-mel',
            'assigned_email' => 'E-mel/ID Yang Ditetapkan',
            'status' => 'Status',
        ],
    ],
    'user_activity' => [
        'title' => 'Laporan Aktiviti Pengguna',
        'description' => 'Pantau aktiviti pengguna dalam sistem termasuk jumlah permohonan dan kelulusan.',
    ],
    'loan_applications' => [
        'title' => 'Laporan Permohonan Pinjaman',
        'description' => 'Semak laporan status dan sejarah permohonan pinjaman peralatan ICT.',
        'list_header' => 'Senarai Permohonan Pinjaman',
        'no_results' => 'Tiada data permohonan pinjaman ditemui untuk kriteria ini.',
        'search_placeholder' => 'Cari ID, Pemohon, atau Tujuan...',
        'table' => [
            'applicant' => 'Pemohon',
            'department' => 'Jabatan Pemohon',
            'loan_dates' => 'Tarikh Pinjaman',
            'return_date' => 'Tarikh Pulang',
            'status' => 'Status',
        ],
    ],
    'loan_history' => [
        'title' => 'Laporan Sejarah Pinjaman',
        'page_header' => 'Laporan Sejarah Transaksi Pinjaman ICT',
        'description' => 'Lihat sejarah terperinci transaksi pinjaman peralatan ICT (pengeluaran & pemulangan).',
        'no_results' => 'Tiada sejarah transaksi pinjaman ditemui.',
        'table' => [
            'transaction_id' => 'ID Transaksi',
            'application_id' => 'ID Permohonan',
            'equipment' => 'Peralatan',
            'user' => 'Pengguna',
            'type' => 'Jenis',
            'date' => 'Tarikh Transaksi',
            'officer' => 'Pegawai Bertugas',
        ],
    ],
];
