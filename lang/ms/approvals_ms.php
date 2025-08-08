<?php
// Bahasa Melayu translations for Approval Dashboard module and actions
// Disusun mengikut kategori untuk memudahkan penyelenggaraan dan rujukan

return [
    // === Tajuk Utama Papan Pemuka Kelulusan ===
    'title' => 'Papan Pemuka Kelulusan',

    // === Jadual Tugasan Kelulusan ===
    'table' => [
        'title'            => 'Senarai Tugasan Kelulusan',
        'task_id'          => 'ID Tugasan',
        'application_type' => 'Jenis Permohonan',
        'applicant'        => 'Pemohon',
        'stage'            => 'Peringkat',
        'status'           => 'Status Tugasan',
        'date_received'    => 'Tarikh Diterima',
        'actions'          => 'Tindakan',
    ],

    // === Penapis / Carian Tugasan ===
    'filter' => [
        'by_type'         => 'Tapis Mengikut Jenis',
        'by_status'       => 'Tapis Mengikut Status',
        'advanced_search' => 'Carian Terperinci',
        'placeholder'     => 'Cari ID, Nama Pemohon...',
    ],

    // === Tindakan Modul Kelulusan ===
    'actions' => [
        'review'           => 'Semak',
        'view_details'     => 'Lihat Butiran',
        'view_task'        => 'Lihat Tugasan',
        'view_full_app'    => 'Lihat Permohonan Penuh',
        'submit_decision'  => 'Hantar Keputusan',
        'no_permission'    => 'Tiada kebenaran',
    ],

    // === Popup/Modals & Dialog - Semakan Tugasan Kelulusan ===
    'modal' => [
        'title'                      => 'Semak Tugasan Kelulusan',
        'app_details'                => 'Butiran Permohonan',
        'app_type'                   => 'Jenis Permohonan',
        'applicant'                  => 'Pemohon',
        'submission_date'            => 'Tarikh Permohonan',
        'current_status'             => 'Status Semasa',
        'applied_items'              => 'Item Dipohon',
        'quantity'                   => 'Kuantiti',
        'purpose'                    => 'Tujuan',
        'loan_period'                => 'Tempoh Pinjaman',
        'usage_location'             => 'Lokasi Penggunaan',
        'supporting_officer_details' => 'Butiran Pegawai Penyokong',
        'supporting_officer_name'    => 'Nama Pegawai Penyokong',
        'supporting_officer_date'    => 'Tarikh Sokongan',
        'supporting_officer_status'  => 'Status Sokongan',
        'approval_decision'          => 'Keputusan Kelulusan',
        'decision'                   => 'Keputusan',
        'comments'                   => 'Ulasan (Wajib untuk Penolakan)',
        'approve_option'             => 'Luluskan',
        'reject_option'              => 'Tolak',
        'return_for_amendment'       => 'Pulangkan untuk Pindaan',
        'transfer_to_officer'        => 'Pindah ke Pegawai Lain',
        'select_officer'             => 'Pilih Pegawai',
        'confirmation_message'       => 'Adakah anda pasti dengan keputusan ini?',
        'processing_message'         => 'Memproses keputusan anda...',
        'success_message'            => 'Keputusan anda telah berjaya direkodkan.',
        'error_message'              => 'Gagal merekodkan keputusan anda.',
    ],

    // === Mesej Sistem: Status, Ralat, Pengesahan, dll ===
    'messages' => [
        'not_found'         => 'Tugasan kelulusan tidak ditemui.',
        'load_error'        => 'Ralat memuatkan butiran.',
        'task_unavailable'  => 'Tugasan tidak lagi tersedia.',
        'unauthenticated'   => 'Pengguna tidak disahkan.',
        'unauthorized'      => 'Tindakan tidak dibenarkan.',
        'decision_recorded' => 'Keputusan berjaya direkodkan untuk #:id.',
        'generic_error'     => 'Berlaku ralat: ',
    ],

    // === Validasi Input Borang Kelulusan ===
    'validation' => [
        'decision_required' => 'Sila pilih keputusan.',
        'decision_invalid'  => 'Keputusan tidak sah.',
        'comments_required' => 'Ulasan diperlukan untuk penolakan.',
        'comments_min'      => 'Ulasan mesti sekurang-kurangnya :min aksara.',
        'items_invalid'     => 'Data item tidak sah.',
        'quantity_required' => 'Kuantiti untuk :itemType wajib diisi.',
        'quantity_integer'  => 'Kuantiti untuk :itemType mesti nombor.',
        'quantity_min'      => 'Kuantiti untuk :itemType mesti sekurang-kurangnya 0.',
        'quantity_max'      => 'Kuantiti untuk :itemType tidak boleh melebihi :max.',
    ],

    // === Pilihan Jenis Permohonan ===
    'application_types' => [
        'ict_loan_application' => 'Permohonan Pinjaman ICT',
        'helpdesk_ticket'      => 'Tiket Meja Bantuan',
    ],

    // === Status Tugasan / Permohonan ===
    'status' => [
        'pending'                => 'Menunggu',
        'approved'               => 'Diluluskan',
        'rejected'               => 'Ditolak',
        'returned_for_amendment' => 'Dipulangkan untuk Pindaan',
        'in_progress'            => 'Dalam Proses',
        'completed'              => 'Selesai',
        'cancelled'              => 'Dibatalkan',
        'draft'                  => 'Draf',
    ],
];

// Penjelasan:
// - Struktur fail ini mengumpulkan terjemahan mengikut fungsi/kategori utama dalam modul kelulusan (dashboard, jadual, penapis, aksi, popup/modal, mesej, validasi, status).
// - Komen pada setiap blok membantu penyelenggaraan dan onboarding pasukan.
// - Kunci keterangan dan arahan adalah konsisten dengan antara muka pengguna modul kelulusan.
