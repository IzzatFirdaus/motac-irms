<?php

// Bahasa Melayu translations for Loan Transaction details and issuance/return forms
// Disusun mengikut kategori untuk kemudahan rujukan dan penyelenggaraan

return [
    // ============================================================================
    // --- MAKLUMAT ASAS TRANSAKSI ---
    // ============================================================================
    'show_title'           => 'Butiran Transaksi Pinjaman',
    'back_to_list'         => 'Senarai Semua Transaksi',
    'basic_info'           => 'Maklumat Asas Transaksi',
    'related_loan_app_id'  => 'ID Permohonan Pinjaman Berkaitan:',
    'transaction_type'     => 'Jenis Transaksi:',
    'transaction_status'   => 'Status Transaksi:',
    'transaction_datetime' => 'Tarikh & Masa Transaksi Dicatat:',
    'involved_items'       => 'Item Peralatan Terlibat Dalam Transaksi Ini',
    'quantity'             => 'Kuantiti',

    // ============================================================================
    // --- BUTIRAN PENGELUARAN PERALATAN ---
    // ============================================================================
    'issue_details'         => 'Butiran Pengeluaran',
    'issuing_officer'       => 'Pegawai Pengeluar (BPM):',
    'receiver'              => 'Peralatan Diterima Oleh:',
    'actual_issue_datetime' => 'Tarikh & Masa Sebenar Pengeluaran:',
    'accessories_issued'    => 'Aksesori Dikeluarkan:',
    'issue_notes'           => 'Catatan Pengeluaran:',
    'back_to_application'   => 'Kembali ke Butiran Permohonan',

    // ============================================================================
    // --- BUTIRAN PEMULANGAN PERALATAN ---
    // ============================================================================
    'return_details'         => 'Butiran Pemulangan',
    'returner'               => 'Peralatan Dipulangkan Oleh:',
    'return_receiver'        => 'Pemulangan Diterima Oleh (Pegawai BPM):',
    'actual_return_datetime' => 'Tarikh & Masa Sebenar Pemulangan:',
    'accessories_returned'   => 'Aksesori Dipulangkan:',
    'return_notes'           => 'Catatan Pemulangan:',
    'findings_on_return'     => 'Status Penemuan Semasa Pulangan',

    // ============================================================================
    // --- BORANG PROSES PENGELUARAN PERALATAN ---
    // ============================================================================
    'issuance_form' => [
        // Tajuk dan header borang pengeluaran
        'page_title' => 'Proses Pengeluaran Peralatan #:id',
        'header'     => 'Rekod Pengeluaran Peralatan',

        // Maklumat permohonan berkaitan
        'for_application'             => 'Untuk Permohonan',
        'related_application_details' => 'Butiran Permohonan Pinjaman Berkaitan',
        'applicant'                   => 'Pemohon',
        'purpose'                     => 'Tujuan Permohonan',
        'loan_date'                   => 'Tarikh Pinjaman',
        'expected_return_date'        => 'Tarikh Dijangka Pulang',

        // Item peralatan diluluskan
        'approved_items'   => 'Item Peralatan Diluluskan',
        'equipment_type'   => 'Jenis Peralatan',
        'approved_qty'     => 'Qty. Lulus',
        'balance_to_issue' => 'Baki Untuk Dikeluarkan',

        // Rekod sebenar pengeluaran
        'actual_issuance_record' => 'Rekod Pengeluaran Peralatan Sebenar',
        'no_items_to_issue'      => 'Tiada baki peralatan untuk dikeluarkan bagi permohonan ini.',
        'issue_item_header'      => 'Item Pengeluaran #:index',

        // Pilihan peralatan
        'select_specific_equipment'    => 'Pilih Peralatan Spesifik (Tag ID)',
        'placeholder_select_equipment' => '-- Pilih Peralatan --',
        'no_equipment_available'       => 'Tiada peralatan jenis ini tersedia.',

        // Senarai aksesori
        'accessories_checklist'     => 'Senarai Semak Aksesori',
        'no_accessories_configured' => 'Tiada senarai aksesori dikonfigurasi.',

        // Penerimaan dan catatan pengeluaran
        'received_by'                 => 'Peralatan Diterima Oleh (Pemohon/Wakil)',
        'placeholder_select_receiver' => '-- Sila Pilih Penerima --',
        'option_applicant'            => 'Pemohon',
        'option_responsible_officer'  => 'Pegawai Bertanggungjawab',
        'issuance_date'               => 'Tarikh Pengeluaran',
        'issuance_notes'              => 'Catatan Pengeluaran (Jika Ada)',
        'placeholder_issuance_notes'  => 'cth: Peralatan dalam keadaan baik semasa dikeluarkan.',

        // Butang tindakan
        'button_cancel'          => 'Batal',
        'button_record_issuance' => 'Rekod Pengeluaran',
    ],
];

// Penjelasan:
// - Setiap bahagian utama (asas transaksi, pengeluaran, pemulangan, borang pengeluaran) dikumpulkan dan diberi komen untuk memudahkan pencarian dan penyelenggaraan.
// - Kunci nested dalam 'issuance_form' mengikut proses dan label sebenar pada borang frontend.
// - Struktur ini membolehkan penggunaan konsisten pada setiap paparan transaksi dalam sistem.
