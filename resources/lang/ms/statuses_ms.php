<?php
// Bahasa Melayu translations for statuses used in loan, equipment, transactions, approval, helpdesk, API, and general user attendance/statuses.

return [
    // === Status Permohonan Pinjaman ICT ===
    'loan_draft'             => 'Draf',
    'loan_pending_support'   => 'Menunggu Sokongan (Pegawai Penyokong)',
    'loan_pending_hod_review'=> 'Menunggu Semakan Ketua Bahagian',
    'loan_pending_bpm_review'=> 'Menunggu Semakan BPM',
    'loan_approved'          => 'Diluluskan',
    'loan_rejected'          => 'Ditolak',
    'loan_partially_issued'  => 'Dikeluarkan Sebahagian',
    'loan_issued'            => 'Dikeluarkan',
    'loan_returned'          => 'Dipulangkan',
    'loan_overdue'           => 'Tertunggak',
    'loan_cancelled'         => 'Dibatalkan',

    // === Status Peralatan ICT ===
    'equipment_available'         => 'Tersedia',
    'equipment_on_loan'           => 'Dalam Pinjaman',
    'equipment_in_repair'         => 'Dalam Pembaikan',
    'equipment_disposed'          => 'Dilupuskan',
    'equipment_lost'              => 'Hilang',
    'equipment_damaged'           => 'Rosak',
    'equipment_under_maintenance' => 'Dalam Penyelenggaraan',

    // === Jenis Transaksi Pinjaman ===
    'transaction_type_issue'  => 'Pengeluaran',
    'transaction_type_return' => 'Pemulangan',

    // === Status Transaksi Pinjaman ===
    'transaction_pending'                     => 'Menunggu Tindakan',
    'transaction_issued'                      => 'Dikeluarkan',
    'transaction_returned_pending_inspection' => 'Dipulangkan (Menunggu Pemeriksaan)',
    'transaction_returned_good'               => 'Dipulangkan (Baik)',
    'transaction_returned_damaged'            => 'Dipulangkan (Rosak)',
    'transaction_items_reported_lost'         => 'Item Dilaporkan Hilang',
    'transaction_returned_with_loss'          => 'Dipulangkan (Hilang)',
    'transaction_returned_with_damage_and_loss'=> 'Dipulangkan (Rosak & Hilang)',
    'transaction_partially_returned'          => 'Dipulangkan Sebahagian',
    'transaction_completed'                   => 'Selesai',
    'transaction_cancelled'                   => 'Dibatalkan',
    'transaction_overdue'                     => 'Tertunggak',
    'transaction_returned'                    => 'Dipulangkan',

    // === Status Kelulusan Permohonan ===
    'approval_pending'  => 'Menunggu Kelulusan',
    'approval_approved' => 'Diluluskan',
    'approval_rejected' => 'Ditolak',
    'approval_canceled' => 'Dibatalkan',
    'supported'         => 'DISOKONG',
    'not_supported'     => 'TIDAK DISOKONG',

    // === Status API Token/Integration ===
    'api_status_active'   => 'Aktif',
    'api_status_inactive' => 'Tidak Aktif',

    // === Status Kehadiran Pengguna/Umum ===
    'status_present'   => 'Hadir',
    'status_absent'    => 'Tidak Hadir',
    'status_leave'     => 'Cuti',
    'status_sick'      => 'Sakit',

    // === Status Pengguna Am (Akaun, Profil, dsb) ===
    'status_active'    => 'Aktif',      // User is active
    'status_inactive'  => 'Tidak Aktif', // User is inactive
    'status_suspended' => 'Digantung',   // User is suspended
    'status_pending'   => 'Menunggu',    // User is pending

    // === Status Tiket Meja Bantuan / Helpdesk ===
    'ticket_open'                   => 'Terbuka',
    'ticket_in_progress'            => 'Dalam Proses',
    'ticket_pending_user_feedback'  => 'Menunggu Maklum Balas Pengguna',
    'ticket_resolved'               => 'Diselesaikan',
    'ticket_closed'                 => 'Ditutup',
    'ticket_reopened'               => 'Dibuka Semula',
];

// Penjelasan:
// - File ini mengelompokkan status mengikut modul untuk rujukan yang mudah dan konsisten di seluruh sistem aplikasi.
// - Gunakan kunci yang sesuai untuk status pada modul pinjaman, peralatan, transaksi, kelulusan, API, kehadiran, pengguna serta tiket meja bantuan.
