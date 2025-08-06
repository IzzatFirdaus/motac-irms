<?php
// Bahasa Melayu translations for ICT Loan Application details, history, and statuses

return [
  'title' => 'Butiran Permohonan Pinjaman ICT',
  'title_with_id' => 'Butiran Permohonan Pinjaman ICT #:id',
  'print_form' => 'Cetak Borang',
  'update_draft' => 'Kemaskini Draf',
  'submit_application' => 'Hantar Permohonan',
  'resubmit_application' => 'Hantar Semula',
  'submit_confirm_message' => 'Adakah anda pasti untuk menghantar permohonan ini?',
  'process_return' => 'Proses Pemulangan Peralatan',
  'back_to_list' => 'Kembali ke Senarai',

  'sections' => [
    'applicant' => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
    'application_details' => 'BUTIRAN PERMOHONAN PINJAMAN',
    'responsible_officer' => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
    'equipment_details' => 'BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON',
    'applicant_confirmation' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON',
    'approval_history' => 'SEJARAH KELULUSAN & TINDAKAN',
    'transaction_history' => 'SEJARAH TRANSAKSI PINJAMAN',
  ],

  'labels' => [
    'application_id' => 'ID Permohonan',
    'applicant_is_responsible' => 'Pemohon adalah Pegawai Bertanggungjawab',
    // ... (Lain-lain label seperti dalam file asal, pastikan lengkap)
    'not_confirmed' => 'Belum Disahkan oleh Pemohon',
    'on_date' => 'pada',
    'stage' => 'Peringkat',
    'officer' => 'Pegawai',
    'status' => 'Status',
    'action_date' => 'Tarikh Tindakan',
    'comments' => 'Catatan',
    'pending_decision' => 'Menunggu Keputusan',
    'transaction' => 'Transaksi',
    'transaction_date' => 'Tarikh Transaksi',
    'issuing_officer' => 'Pegawai Pengeluar (BPM):',
    'receiving_officer' => 'Pegawai Penerima (Pemohon/Wakil):',
    'returning_officer' => 'Pegawai Pemulang (Pemohon/Wakil):',
    'return_receiving_officer' => 'Pegawai Terima Pulangan (BPM):',
    'rejection_reason' => 'Sebab Penolakan',
  ],

  'statuses' => [
    'draft' => 'Draf',
    'pending_support' => 'Menunggu Sokongan Pegawai',
    'pending_approver_review' => 'Menunggu Kelulusan',
    'pending_bpm_review' => 'Menunggu Pengesahan BPM',
    'approved_pending_issuance' => 'Diluluskan (Menunggu Agihan)',
    'on_loan' => 'Sedang Dipinjam',
    'pending_return' => 'Menunggu Pemulangan',
    'returned' => 'Telah Dipulangkan',
    'rejected' => 'Ditolak',
    'cancelled' => 'Dibatalkan',
    'returned_for_amendment' => 'Dipulangkan untuk Pindaan',
  ],

  'messages' => [
    'update_draft_success' => 'Draf permohonan berjaya dikemaskini.',
    'submit_success' => 'Permohonan pinjaman berjaya dihantar.',
    'resubmit_success' => 'Permohonan pinjaman berjaya dihantar semula.',
    'process_return_success' => 'Proses pemulangan peralatan berjaya.',
    'not_found' => 'Permohonan pinjaman tidak ditemui.',
    'unauthorized' => 'Anda tidak dibenarkan untuk mengakses permohonan ini.',
    'already_submitted' => 'Permohonan ini telah dihantar.',
    'already_processed' => 'Permohonan ini telah diproses.',
    'return_success_with_issues' => 'Peralatan berjaya dipulangkan dengan beberapa isu.',
  ],

  'fields' => [
    'required_quantity' => 'Kuantiti diperlukan untuk :item.',
    'invalid_quantity' => 'Kuantiti tidak sah untuk :item.',
    'missing_equipment_details' => 'Sila masukkan butiran peralatan yang dipohon.',
    'loan_dates_invalid' => 'Tarikh mula dan tamat pinjaman tidak sah.',
    'loan_period_exceeded' => 'Tempoh pinjaman melebihi had yang dibenarkan.',
  ]
];
