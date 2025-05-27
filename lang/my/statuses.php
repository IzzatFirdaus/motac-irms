<?php

return [
  // Email Application Statuses
  'email_draft' => 'Draf',
  'email_pending_support' => 'Menunggu Sokongan',
  'email_pending_admin' => 'Menunggu Tindakan Pentadbir',
  'email_approved' => 'Diluluskan',
  'email_rejected' => 'Ditolak',
  'email_processing' => 'Sedang Diproses',
  'email_provision_failed' => 'Gagal Diproseskan',
  'email_completed' => 'Selesai',

  // Loan Application Statuses
  'loan_draft' => 'Draf',
  'loan_pending_support' => 'Menunggu Sokongan (Pegawai Penyokong)',
  'loan_pending_hod_review' => 'Menunggu Semakan Ketua Bahagian',
  'loan_pending_bpm_review' => 'Menunggu Semakan BPM',
  'loan_approved' => 'Diluluskan',
  'loan_rejected' => 'Ditolak',
  'loan_partially_issued' => 'Dikeluarkan Sebahagian',
  'loan_issued' => 'Dikeluarkan',
  'loan_returned' => 'Dipulangkan',
  'loan_overdue' => 'Tertunggak',
  'loan_cancelled' => 'Dibatalkan',

  // Equipment Statuses
  'equipment_available' => 'Tersedia',
  'equipment_on_loan' => 'Dalam Pinjaman',
  'equipment_under_maintenance' => 'Dalam Selenggaraan', // my.json has "Under Maintenance!": "Dalam Penyenggaraan!"
  'equipment_disposed' => 'Dilupuskan',
  'equipment_lost' => 'Hilang',
  'equipment_damaged_needs_repair' => 'Rosak (Perlu Pembaikan)', // my.json has "Damaged": "Rosak"
  'equipment_in_service' => 'Dalam Perkhidmatan', //

  // Equipment Condition Statuses
  'condition_new' => 'Baharu',
  'condition_good' => 'Baik', //
  'condition_fine' => 'Baik', // "Fine"
  'condition_bad' => 'Tidak Baik', //
  'condition_fair' => 'Sederhana',
  'condition_minor_damage' => 'Kerosakan Kecil',
  'condition_major_damage' => 'Kerosakan Teruk',
  'condition_unserviceable' => 'Tidak Boleh Digunakan',

  // Loan Transaction Types
  'transaction_type_issue' => 'Pengeluaran',
  'transaction_type_return' => 'Pemulangan',

  // Loan Transaction Statuses
  'transaction_pending' => 'Menunggu Tindakan', // my.json has "Pending": "Menunggu"
  'transaction_issued' => 'Dikeluarkan',
  'transaction_returned_pending_inspection' => 'Dipulangkan (Menunggu Pemeriksaan)',
  'transaction_returned_good' => 'Dipulangkan (Baik)',
  'transaction_returned_damaged' => 'Dipulangkan (Rosak)',
  'transaction_items_reported_lost' => 'Item Dilaporkan Hilang',
  'transaction_completed' => 'Selesai',
  'transaction_cancelled' => 'Dibatalkan',

  // Approval Statuses
  'approval_pending' => 'Menunggu Kelulusan',
  'approval_approved' => 'Diluluskan',
  'approval_rejected' => 'Ditolak',

  'supported' => 'DISOKONG',
  'not_supported' => 'TIDAK DISOKONG',

  'api_status_active' => 'Aktif', // From "API Status" & "Active"
  'api_status_inactive' => 'Tidak Aktif', // From "API Status" & "Inactive"

  'status_present' => 'Hadir', //
  'status_absent_without_excuse' => 'Tidak hadir tanpa alasan', //
  'status_partial_attendance' => 'Kehadiran separa', //
  'status_pending' => 'Menunggu', //
  'status_successful' => 'Berjaya', //
  'status_out_of_work' => 'Tidak bekerja', //
];
