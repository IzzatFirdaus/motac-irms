<?php
// Bahasa Melayu translations for Dashboard module and related panels
// Disusun supaya setiap key SELARI dengan dashboard_en.php untuk bilingual parity
// Tambahan: 'dashboard' key untuk digunakan pada welcome.blade.php, dsb.

return [
    // === Key for Dashboard Menu/Button ===
    'dashboard' => 'Papan Pemuka',

    // === Tajuk Papan Pemuka Utama & Peranan ===
    'admin_title'      => 'Papan Pemuka Pentadbir',
    'approver_title'   => 'Papan Pemuka Kelulusan',
    'bpm_title'        => 'Papan Pemuka Staf BPM (Pengurusan Peralatan ICT)',
    'it_admin_title'   => 'Papan Pemuka Pentadbir IT',
    'user_title'       => 'Papan Pemuka Pengguna',
    'main_dashboard'   => 'Papan Pemuka Utama',

    // === Mesej Alu-Aluan/Utama, Tindakan Pantas & Navigasi ===
    'welcome'                   => 'Selamat Datang',
    'quick_actions'             => 'Tindakan Pantas',
    'quick_shortcuts'           => 'Pintasan Utama',
    'ict_loan'                  => 'Pinjaman ICT',
    'helpdesk'                  => 'Meja Bantuan',
    'notifications'             => 'Notifikasi',
    'view_all_notifications'    => 'Lihat Semua Notifikasi',
    'apply_for_loan'            => 'Mohon Pinjaman ICT',
    'apply_new_loan'            => 'Mohon Pinjaman ICT Baharu',
    'my_loan_applications'      => 'Permohonan Pinjaman Saya',
    'view_my_loan_apps'         => 'Lihat Permohonan Saya',
    'submit_helpdesk_ticket'    => 'Hantar Tiket Meja Bantuan',
    'create_new_ticket'         => 'Cipta Tiket Meja Bantuan Baharu',
    'my_helpdesk_tickets'       => 'Tiket Meja Bantuan Saya',
    'view_my_tickets'           => 'Lihat Tiket Meja Bantuan Saya',
    'manage_all_tickets'        => 'Urus Semua Tiket Meja Bantuan',
    'manage_pending_approvals'  => 'Urus Kelulusan Menunggu',
    'view_loan_transactions'    => 'Lihat Transaksi Pinjaman',
    'view_all_loan_applications'=> 'Lihat Semua Permohonan',
    'view_all_my_tickets'       => 'Lihat Semua Tiket Saya',

    // === Kad Statistik (Stat Cards) dan Ringkasan Papan Pemuka ===
    'total_users'               => 'Jumlah Pengguna Sistem',
    'pending_approvals'         => 'Permohonan Menunggu Kelulusan',
    'available_equipment'       => 'Peralatan ICT Tersedia',
    'loaned_equipment'          => 'Peralatan ICT Sedang Dipinjam',
    'active_loans'              => 'Pinjaman Aktif',
    'overdue_loans'             => 'Pinjaman Lewat',
    'utilization_rate'          => 'Kadar Penggunaan',
    'equipment_status_summary'  => 'Ringkasan Status Peralatan',
    'loan_stats_title'          => 'Statistik Pinjaman',
    'no_loan_data_available'    => 'Tiada data pinjaman untuk dipaparkan.',
    'loan_summary'              => 'Ringkasan Pinjaman',
    'on_loan'                   => 'Sedang Dipinjam',
    'approved_pending_issuance' => 'Diluluskan (Menunggu Agihan)',
    'returned'                  => 'Telah Dipulangkan',

    // === Kad Ringkasan (Admin/BPM/IT Admin) ===
    'total_ict_equipment'           => 'Jumlah Peralatan ICT',
    'equipment_on_loan'             => 'Peralatan Sedang Dipinjam',
    'equipment_available'           => 'Peralatan Tersedia',
    'total_loan_applications'       => 'Jumlah Permohonan Pinjaman ICT',
    'pending_loan_applications'     => 'Permohonan Pinjaman Menunggu',
    'approved_loan_applications'    => 'Permohonan Pinjaman Diluluskan',
    'rejected_loan_applications'    => 'Permohonan Pinjaman Ditolak',
    'total_helpdesk_tickets'        => 'Jumlah Tiket Meja Bantuan',
    'open_helpdesk_tickets'         => 'Tiket Meja Bantuan Terbuka',
    'resolved_helpdesk_tickets'     => 'Tiket Meja Bantuan Diselesaikan',

    // === BPM/IT/Admin Dashboard Specifics ===
    'add_new_equipment'         => 'Tambah Peralatan Baharu',
    'view_full_inventory'       => 'Lihat Inventori Penuh',
    'inventory_stock_summary'   => 'Ringkasan Stok Inventori',
    'laptops_available'         => 'Komputer Riba Tersedia',
    'projectors_available'      => 'Projektor Tersedia',
    'printers_available'        => 'Pencetak Tersedia',
    'view_detailed_inventory'   => 'Lihat Inventori Terperinci',
    'maintenance_equipment_title'=> 'Peralatan Dalam Penyelenggaraan',
    'maintenance_equipment_text'=> 'Senarai peralatan yang sedang dalam proses penyelenggaraan akan dipaparkan di sini.',

    // === IT Admin Dashboard ===
    'pending_helpdesk_tickets'      => 'Tiket Meja Bantuan Tertunda',
    'in_progress_helpdesk_tickets'  => 'Tiket Meja Bantuan Dalam Proses',
    'helpdesk_tickets_to_process_title' => 'Tiket Meja Bantuan Menunggu Tindakan',
    'my_assigned_helpdesk_tickets'  => 'Tiket Meja Bantuan Saya Yang Ditugaskan',
    'view_all_helpdesk_tickets'     => 'Lihat Semua Tiket Meja Bantuan',

    // === User Dashboard: Statistik & Permohonan Terkini ===
    'my_loan_stats'                    => 'Statistik Pinjaman Saya',
    'pending_loans'                    => 'Menunggu Kelulusan',
    'approved_loans'                   => 'Diluluskan',
    'rejected_loans'                   => 'Ditolak',
    'total_loans'                      => 'Jumlah Permohonan',
    'recent_loan_applications'         => 'Permohonan Terkini',
    'no_recent_loan_applications'      => 'Tiada permohonan terkini.',
    'application_no'                   => 'No. Permohonan',
    'item_name'                        => 'Nama Item',
    'loan_purpose'                     => 'Tujuan Pinjaman',
    'status'                           => 'Status',
    'applied_on'                       => 'Tarikh Mohon',

    // === Approval Tasks & Approver Dashboard ===
    'latest_tasks_title'           => 'Tugasan Kelulusan Terkini',
    'view_all_tasks'               => 'Lihat Semua Tugasan',
    'approver_stats_title'         => 'Statistik Kelulusan Anda (30 Hari Terakhir)',
    'num_approved'                 => 'Jumlah Diluluskan:',
    'num_rejected'                 => 'Jumlah Ditolak:',
    'approval_guidance_title'      => 'Panduan Kelulusan',
    'approval_guidance_text'       => 'Sila semak setiap permohonan dengan teliti sebelum membuat keputusan. Keputusan anda akan dimaklumkan kepada pemohon melalui e-mel.',
    'pending_tasks_title'          => 'Permohonan Menunggu Tindakan Anda',
    'approval_history'             => 'Lihat Sejarah Kelulusan',

    // === Papan Pemuka Kelulusan (Summary & Tindakan) ===
    'awaiting_your_approval'       => 'Menunggu Kelulusan Anda',
    'total_pending_tasks'          => 'Jumlah Tugasan Menunggu:',
    'total_approved'               => 'Jumlah Diluluskan:',
    'total_rejected'               => 'Jumlah Ditolak:',
    'read_full_guidelines'         => 'Baca Garis Panduan Penuh',

    // === Table Headers (for dashboard tables) ===
    'date'         => 'Tarikh',
    'subject'      => 'Perkara',
    'return_date'  => 'Tarikh Pulang',
    'apply_date'   => 'Tarikh Mohon',
    'type'         => 'Jenis',
    'applicant'    => 'Pemohon',
    'assigned_to'  => 'Diserahkan Kepada',
    'priority'     => 'Prioriti',
    'category'     => 'Kategori',

    // === Ringkasan Aktiviti, Transaksi & Notifikasi ===
    'ict_loans_in_process'         => 'Pinjaman ICT Dalam Proses',
    'helpdesk_tickets_in_process'  => 'Tiket Meja Bantuan Dalam Proses',
    'active_ict_loans'             => 'Pinjaman ICT Aktif',
    'recent_transactions'          => 'Transaksi Terkini',
    'no_recent_transactions'       => 'Tiada sejarah transaksi terkini.',
    'upcoming_returns'             => 'Pemulangan Akan Datang',
    'no_upcoming_returns'          => 'Tiada pemulangan ICT dijangka.',
    'recent_helpdesk_activity'     => 'Aktiviti Meja Bantuan Terkini',
    'no_recent_helpdesk_activity'  => 'Tiada aktiviti meja bantuan terkini.',

    // === Helpdesk User Panel ===
    'create_helpdesk_ticket_title' => 'Cipta Tiket Meja Bantuan',
    'create_helpdesk_ticket_text'  => 'Mengalami isu IT? Hantar permintaan sokongan baharu kepada pasukan Meja Bantuan kami.',
    'view_my_tickets_title'        => 'Lihat Tiket Meja Bantuan Saya',
    'view_my_tickets_text'         => 'Jejaki status dan sejarah permintaan sokongan IT yang anda hantar.',

    // === Mesej Umum & Lain-lain ===
    'no_data_found'                => 'Tiada data untuk dipaparkan.',
    'no_permission'                => 'Tiada Kebenaran',
    'notifications_title'          => 'Pemberitahuan',
    'notifications_text'           => 'Lihat semua pemberitahuan sistem anda.',
    'your_recent_activity_summary' => 'Ringkasan Aktiviti Terkini Anda',
    'your_recent_activity_text'    => 'Tiada aktiviti terkini untuk dipaparkan.',
    'apply_ict_loan_title'         => 'Mohon Pinjaman ICT',
    'apply_ict_loan_text'          => 'Perlukan peralatan ICT untuk tugasan rasmi? Mohon pinjaman peralatan di sini.',
    'view_my_loan_applications_title' => 'Lihat Permohonan Pinjaman Saya',
    'view_my_loan_applications_text'  => 'Semak status dan butiran permohonan pinjaman peralatan ICT anda.',
    'view_my_loan_applications'       => 'Lihat Permohonan Pinjaman Saya',
    'contact_us'                 => 'Hubungi Kami', // Untuk link sumber/pautan bantuan

    // --- Penambahan kunci baru perlu dicerminkan di dashboard_en.php untuk pariti bilingual ---
];
