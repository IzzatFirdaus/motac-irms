<?php

return [
  'dashboard' => 'Papan Pemuka',
  'section' => [
    'resource_management' => 'Pengurusan Sumber',
    'reports_analytics' => 'Laporan & Analitik', // Added
    'system_settings' => 'Tetapan Sistem',       // Changed from system_config for consistency
  ],
  'my_applications' => [
    'title' => 'Permohonan Saya',
    'email' => 'Permohonan Emel Saya',
    'loan' => 'Permohonan Pinjaman Saya',
  ],
  'apply_for_resources' => [
    'title' => 'Mohon Sumber Baharu',
    'email_account' => 'Akaun E-mel / ID Pengguna Baharu', // Updated for clarity
    'loan_application' => 'Pinjaman Peralatan ICT Baharu', // Updated for clarity
  ],
  'approvals' => [ // Changed from approvals_dashboard for consistency with config/menu.php
    'title' => 'Kelulusan',
    // Add specific approval sub-items if needed, e.g., 'email' => 'Kelulusan Emel', 'loan' => 'Kelulusan Pinjaman'
  ],
  'resource_inventory' => [ // Added for consistency with config/menu.php
    'title' => 'Inventori Sumber ICT', // Updated title
    'equipment' => 'Inventori Peralatan ICT',
    'loan_transactions' => 'Transaksi Pinjaman ICT',
  ],
  'reports' => [
    'title' => 'Laporan', // Simplified title for the main menu item
    'equipment_report' => 'Laporan Inventori Peralatan',
    'loan_applications_report' => 'Laporan Permohonan Pinjaman',
    'email_applications_report' => 'Laporan Permohonan E-mel', // New entry
    'user_activity_report' => 'Laporan Aktiviti Pengguna',     // New entry
    'loan_history_report' => 'Laporan Sejarah Pinjaman',       // New entry
    'utilization_report' => 'Laporan Penggunaan Peralatan',    // New entry
    'loan_status_summary_report' => 'Laporan Ringkasan Status Pinjaman', // New entry
  ],
  'system_settings' => [ // Changed from settings for consistency with config/menu.php
    'title' => 'Tetapan Sistem',
    'users' => 'Pengurusan Pengguna',
    'roles' => 'Peranan',
    'permissions' => 'Kebenaran Akses',
    'grades' => 'Gred',
    'departments' => 'Jabatan/Bahagian',
    'positions' => 'Jawatan',
  ],
  'system_logs' => 'Log Sistem', // New entry
];
