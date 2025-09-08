<?php

// Bahasa Melayu translations for ICT Loan Application details, history, and statuses
// Disusun mengikut kategori untuk kemudahan penyelenggaraan dan rujukan

return [
    // ==============================================================================
    // --- TAJUK & TINDAKAN UTAMA ---
    // ==============================================================================
    'title'                  => 'Butiran Permohonan Pinjaman ICT',
    'title_with_id'          => 'Butiran Permohonan Pinjaman ICT #:id',
    'print_form'             => 'Cetak Borang',
    'update_draft'           => 'Kemaskini Draf',
    'submit_application'     => 'Hantar Permohonan',
    'resubmit_application'   => 'Hantar Semula',
    'submit_confirm_message' => 'Adakah anda pasti untuk menghantar permohonan ini?',
    'process_return'         => 'Proses Pemulangan Peralatan',
    'back_to_list'           => 'Kembali ke Senarai',
    'skip_to_main_content'   => 'Langkau ke Kandungan Utama',
    'form_title'             => 'Borang Permohonan Pinjaman ICT',
    'section_applicant'      => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
    'full_name'              => 'Nama Penuh:',
    'position_grade'         => 'Jawatan & Gred:',
    'phone_number'           => 'No. Telefon',
    'phone_help'             => 'Masukkan nombor telefon yang boleh dihubungi.',
    'department'             => 'Bahagian/Unit:',
    'purpose'                => 'Tujuan Permohonan',
    'purpose_help'           => 'Nyatakan tujuan permohonan pinjaman ICT.',
    'usage_location'         => 'Lokasi Penggunaan',
    'return_location'        => 'Lokasi Pemulangan',
    'start_date'             => 'Tarikh Mula',
    'end_date'               => 'Tarikh Pulang',
    'section_responsible_officer' => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
    'is_responsible_officer' => 'Pemohon adalah Pegawai Bertanggungjawab.',
    'responsible_officer_help' => 'Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.',
    'responsible_officer_name' => 'Nama Penuh Pegawai Bertanggungjawab',
    'select_officer'         => 'Pilih Pegawai',
    // ==============================================================================
    // --- TAMBAHAN KUNCI TERJEMAHAN UNTUK BORANG PERMOHONAN PINJAMAN ICT ---
    // ==============================================================================
    'supporting_officer_section' => 'MAKLUMAT PEGAWAI PENYOKONG',
    'supporting_officer_name' => 'Nama Penuh Pegawai Penyokong',
    'select_supporting_officer' => 'Pilih Pegawai Penyokong',
    'supporting_officer_grade_help' => 'Pegawai Penyokong mestilah sekurang-kurangnya Gred :grade atau setara.',
    'equipment_section' => 'BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON',
    'add_item' => 'Tambah Item',
    'equipment_list_help' => 'Sila senaraikan peralatan ICT yang diperlukan.',
    'equipment_type' => 'Jenis Peralatan',
    'select_type' => 'Pilih Jenis',
    'quantity' => 'Kuantiti',
    'additional_notes' => 'Catatan Tambahan',
    'remove' => 'Buang',
    'at_least_one_item' => 'Pastikan sekurang-kurangnya satu item peralatan disenaraikan.',
    'confirmation_section' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON',
    'confirmation_text' => 'Saya dengan ini mengesahkan dan memperakukan bahawa semua maklumat yang diberikan adalah benar dan peralatan yang dipinjam adalah untuk kegunaan rasmi dan akan berada di bawah tanggungjawab serta penyeliaan saya (atau Pegawai Bertanggungjawab yang dinamakan) sepanjang tempoh pinjaman. Saya juga bersetuju untuk mematuhi semua syarat dan peraturan peminjaman yang ditetapkan oleh pihak MOTAC.',
    'confirmation_agree' => 'Saya faham dan bersetuju dengan perakuan di atas.',
    'search_officer_placeholder' => 'Cari nama pegawai...',
    'loading_officer_list' => 'Memuatkan senarai pegawai...',

    // ==============================================================================
    // --- SEKSYEN DALAM BORANG PERMOHONAN ---
    // ==============================================================================
    'sections' => [
        'applicant'              => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
        'application_details'    => 'BUTIRAN PERMOHONAN PINJAMAN',
        'responsible_officer'    => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
        'equipment_details'      => 'BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON',
        'applicant_confirmation' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON',
        'approval_history'       => 'SEJARAH KELULUSAN & TINDAKAN',
        'transaction_history'    => 'SEJARAH TRANSAKSI PINJAMAN',
    ],

    // ==============================================================================
    // --- LABEL / MEJA & FORM FIELD LABELS ---
    // ==============================================================================
    'labels' => [
        'application_id'           => 'ID Permohonan',
        'applicant_is_responsible' => 'Pemohon adalah Pegawai Bertanggungjawab',
        'applicant_notes'          => 'Catatan Pemohon',
        'approved_qty'             => 'Kuantiti Diluluskan',
        'equipment_type'           => 'Jenis Peralatan',
        'issued_qty'               => 'Kuantiti Dikeluarkan',
        'loan_datetime'            => 'Tarikh/Masa Pinjaman',
        'no_equipment_requested'   => 'Tiada peralatan dipohon',
        'purpose'                  => 'Tujuan',
        'requested_qty'            => 'Kuantiti Dipohon',
        'return_datetime'          => 'Tarikh/Masa Pulang',
        'return_location'          => 'Lokasi Pulang',
        'submitted_date'           => 'Tarikh Hantar',
        'usage_location'           => 'Lokasi Penggunaan',
        // Tambahkan label lain di sini jika ada, pastikan lengkap di masa depan
        'not_confirmed'            => 'Belum Disahkan oleh Pemohon',
        'on_date'                  => 'pada',
        'stage'                    => 'Peringkat',
        'officer'                  => 'Pegawai',
        'status'                   => 'Status',
        'action_date'              => 'Tarikh Tindakan',
        'comments'                 => 'Catatan',
        'pending_decision'         => 'Menunggu Keputusan',
        'transaction'              => 'Transaksi',
        'transaction_date'         => 'Tarikh Transaksi',
        'issuing_officer'          => 'Pegawai Pengeluar (BPM):',
        'receiving_officer'        => 'Pegawai Penerima (Pemohon/Wakil):',
        'returning_officer'        => 'Pegawai Pemulang (Pemohon/Wakil):',
        'return_receiving_officer' => 'Pegawai Terima Pulangan (BPM):',
        'rejection_reason'         => 'Sebab Penolakan',
    ],

    // ==============================================================================
    // --- STATUS PERMOHONAN ---
    // ==============================================================================
    'statuses' => [
        'approved'                              => 'Diluluskan',
        'completed'                             => 'Selesai',
        'draft'                                 => 'Draf',
        'pending_support'                       => 'Menunggu Sokongan Pegawai',
        'pending_approver_review'               => 'Menunggu Kelulusan',
        'pending_bpm_review'                    => 'Menunggu Pengesahan BPM',
        'approved_pending_issuance'             => 'Diluluskan (Menunggu Agihan)',
        'partially_issued'                      => 'Sebahagian Dikeluarkan',
        'issued'                                => 'Telah Dikeluarkan',
        'on_loan'                               => 'Sedang Dipinjam',
        'processing'                            => 'Sedang Diproses',
        'overdue'                               => 'Tertunggak',
        'pending_return'                        => 'Menunggu Pemulangan',
        'partially_returned_pending_inspection' => 'Sebahagian Dipulangkan (Menunggu Pemeriksaan)',
        'returned'                              => 'Telah Dipulangkan',
        'rejected'                              => 'Ditolak',
        'cancelled'                             => 'Dibatalkan',
        'returned_for_amendment'                => 'Dipulangkan untuk Pindaan',
    ],

    // ==============================================================================
    // --- MESEJ & NOTIFIKASI SISTEM ---
    // ==============================================================================
    'messages' => [
        'update_draft_success'       => 'Draf permohonan berjaya dikemaskini.',
        'submit_success'             => 'Permohonan pinjaman berjaya dihantar.',
        'resubmit_success'           => 'Permohonan pinjaman berjaya dihantar semula.',
        'process_return_success'     => 'Proses pemulangan peralatan berjaya.',
        'not_found'                  => 'Permohonan pinjaman tidak ditemui.',
        'unauthorized'               => 'Anda tidak dibenarkan untuk mengakses permohonan ini.',
        'already_submitted'          => 'Permohonan ini telah dihantar.',
        'already_processed'          => 'Permohonan ini telah diproses.',
        'return_success_with_issues' => 'Peralatan berjaya dipulangkan dengan beberapa isu.',
    ],

    // ==============================================================================
    // --- VALIDASI & MESEJ RALAT PADA BORANG ---
    // ==============================================================================
    'fields' => [
        'required_quantity'         => 'Kuantiti diperlukan untuk :item.',
        'invalid_quantity'          => 'Kuantiti tidak sah untuk :item.',
        'missing_equipment_details' => 'Sila masukkan butiran peralatan yang dipohon.',
        'loan_dates_invalid'        => 'Tarikh mula dan tamat pinjaman tidak sah.',
        'loan_period_exceeded'      => 'Tempoh pinjaman melebihi had yang dibenarkan.',
    ],
];

// Penjelasan:
// - Fail ini mengandungi terjemahan Bahasa Melayu untuk paparan permohonan pinjaman ICT, termasuk status, label, mesej sistem, dan validasi.
// - Setiap blok dikategorikan untuk rujukan pantas dan kemudahan penyelenggaraan.
// - Jika ada label atau mesej tambahan, boleh ditambah dalam blok berkaitan agar konsisten.
