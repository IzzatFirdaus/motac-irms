<?php
// Bahasa Melayu translations for generic and form/system messages
// Disusun mengikut kategori untuk kemudahan penyelenggaraan dan rujukan

return [
    // === Arahan & Nota Borang Umum ===
    'instruction_mandatory_fields'              => 'Tanda * WAJIB diisi.',
    'instruction_fill_form_completely'          => 'Sila isi borang ini dengan lengkap.',
    'instruction_ict_loan_terms_availability'   => "Permohonan adalah tertakluk kepada ketersediaan peralatan melalui konsep 'First Come, First Serve'.",
    'instruction_ict_loan_processing_time'      => 'Permohonan akan diteliti dan diuruskan dalam tempoh tiga (3) hari bekerja dari tarikh permohonan lengkap diterima.',
    'instruction_ict_loan_bpm_responsibility'   => 'BPM tidak bertanggungjawab di atas ketersediaan peralatan jika pemohon gagal mematuhi tempoh ini.',
    'instruction_ict_loan_submit_form_on_pickup'=> 'Pemohon hendaklah mengemukakan Borang Permohonan Pinjaman Peralatan ICT yang lengkap diisi dan ditandatangani kepada BPM semasa mengambil peralatan.',
    'instruction_ict_loan_check_equipment'      => 'Pemohon diingatkan untuk menyemak dan memeriksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam.',
    'instruction_ict_loan_liability'            => 'Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon dan tindakan melalui peraturan-peraturan yang berkuatkuasa boleh diambil.',
    'instruction_ict_loan_form_submission_location' => 'Borang yang telah lengkap diisi hendaklah dihantar kepada:',
    'instruction_ict_loan_contact_for_enquiries'=> 'Sebarang pertanyaan sila hubungi:',

    // === Mesej Umum Kejayaan, Ralat, Pengesahan ===
    'record_created_success'     => 'Rekod berjaya dicipta.',
    'record_updated_success'     => 'Rekod berjaya dikemaskini.',
    'record_deleted_success'     => 'Rekod berjaya dipadam.',
    'action_successful'          => 'Tindakan berjaya!',
    'action_failed'              => 'Tindakan gagal.',
    'confirm_delete'             => 'Adakah anda pasti mahu memadam rekod ini? Tindakan ini tidak boleh diundur.',
    'delete_not_allowed'         => 'Rekod tidak boleh dipadam kerana ia sedang digunakan.',
    'no_changes_made'            => 'Tiada perubahan dikesan.',
    'not_found'                  => 'Sumber tidak ditemui.',
    'unauthorized_action'        => 'Anda tidak dibenarkan untuk melakukan tindakan ini.',
    'invalid_input'              => 'Input tidak sah.',
    'system_error'               => 'Ralat sistem berlaku. Sila cuba sebentar lagi.',
    'data_integrity_error'       => 'Ralat integriti data.',
    'file_upload_success'        => 'Fail berjaya dimuat naik.',
    'file_upload_failed'         => 'Gagal memuat naik fail.',
    'file_delete_success'        => 'Fail berjaya dipadam.',
    'file_delete_failed'         => 'Gagal memadam fail.',
    'operation_timeout'          => 'Operasi tamat masa.',

    // === Validasi & Input ===
    'validation_error_heading'   => 'Sila perbetulkan ralat berikut:',
    'validation_generic_error'   => 'Sila semak dan betulkan maklumat yang dimasukkan.',
    'validation_errors'          => 'Sila semak dan betulkan ralat berikut:',

    // === Empty States & Carian ===
    'no_records_found'           => 'Tiada rekod ditemui.',
    'no_users_found'             => 'Tiada pengguna sistem ditemui.',
    'no_grades_found'            => 'Tiada rekod gred ditemui.',
    'no_positions_found'         => 'Tiada rekod jawatan ditemui.',
    'no_departments_found'       => 'Tiada jabatan/unit ditemui yang sepadan dengan carian anda.',
    'try_different_search'       => 'Cuba kata kunci carian yang berbeza.',
    'no_results_found'           => 'Tiada hasil ditemui untuk carian anda.',

    // === Modul Pinjaman ICT ===
    'loan_application_submitted_success'        => 'Permohonan Pinjaman Peralatan ICT anda berjaya dihantar.',
    'loan_application_updated_success'          => 'Permohonan Pinjaman Peralatan ICT berjaya dikemaskini.',
    'loan_application_cancelled_success'        => 'Permohonan Pinjaman Peralatan ICT berjaya dibatalkan.',
    'loan_application_return_processed_success' => 'Pemulangan peralatan ICT berjaya diproses.',
    'loan_application_issued_success'           => 'Peralatan ICT berjaya dikeluarkan.',
    'loan_application_not_found'                => 'Permohonan pinjaman tidak ditemui.',
    'loan_application_not_editable'             => 'Permohonan tidak boleh disunting dalam status semasa.',
    'loan_application_not_cancellable'          => 'Permohonan tidak boleh dibatalkan dalam status semasa.',
    'loan_equipment_not_available'              => 'Jumlah peralatan yang dimohon tidak tersedia.',
    'loan_equipment_already_issued'             => 'Peralatan ini sudah dikeluarkan.',
    'loan_equipment_not_on_loan'                => 'Peralatan ini tidak dalam pinjaman.',
    'loan_equipment_return_date_earlier'        => 'Tarikh pulangan tidak boleh lebih awal dari tarikh mula pinjaman.',

    // === Terma & Syarat (Pinjaman ICT) ===
    'terms_title'   => 'Terma dan Syarat Pinjaman Peralatan ICT',
    'terms_item1'   => 'Pinjaman peralatan ICT adalah untuk kegunaan rasmi sahaja.',
    'terms_item2'   => 'Tempoh pinjaman maksimum adalah tiga (3) bulan dari tarikh pengeluaran, kecuali dengan kelulusan khas.',
    'terms_item3'   => 'Peralatan yang dipinjam adalah tanggungjawab pemohon sepenuhnya dan perlu dijaga dengan baik.',
    'terms_item4'   => 'Pemohon perlu memulangkan peralatan dalam keadaan baik pada atau sebelum tarikh yang ditetapkan.',
    'terms_item5'   => 'Kehilangan atau kerosakan peralatan akan dikenakan bayaran ganti rugi mengikut nilai semasa peralatan.',
    'terms_item6'   => 'BPM tidak akan bertanggungjawab sekiranya berlaku sebarang masalah ketersediaan peralatan jika permohonan tidak diproses dalam tempoh tiga (3) hari bekerja akibat permohonan yang tidak lengkap.',
    'terms_item7'   => 'Pemohon perlu memastikan semua maklumat yang diberikan di dalam borang permohonan adalah tepat dan benar.',
    'terms_item8'   => 'Sebarang penyalahgunaan atau pelanggaran syarat akan mengakibatkan tindakan tatatertib atau perundangan diambil.',

    // === Modul Helpdesk / Meja Bantuan ===
    'ticket_created_success'        => 'Tiket Meja Bantuan anda berjaya dicipta.',
    'ticket_updated_success'        => 'Tiket berjaya dikemaskini.',
    'ticket_assigned_success'       => 'Tiket berjaya diserahkan kepada :officer.',
    'ticket_status_updated_success' => 'Status tiket berjaya dikemaskini kepada :status.',
    'comment_added_success'         => 'Ulasan berjaya ditambah.',
    'ticket_closed_success'         => 'Tiket berjaya ditutup.',
    'ticket_already_closed'         => 'Tiket ini sudah ditutup.',
    'ticket_reopened_success'       => 'Tiket berjaya dibuka semula.',
    'ticket_not_found'              => 'Tiket tidak ditemui.',
    'ticket_not_authorized'         => 'Anda tidak dibenarkan untuk mengakses tiket ini.',
    'attachment_too_large'          => 'Saiz fail lampiran tidak boleh melebihi :max_size MB.',
    'attachment_invalid_type'       => 'Jenis fail lampiran tidak dibenarkan. Jenis yang dibenarkan: :allowed_types.',
    'sla_breached'                  => 'Tiket ini telah melanggar SLA.',
    'sla_warning'                   => 'Tiket ini hampir melanggar SLA.',

    // === API Token Actions ===
    'api_token_created'             => 'Token API berjaya dicipta.',
    'api_token_deleted'             => 'Token API berjaya dipadam.',
    'api_token_permissions_updated' => 'Kebenaran token berjaya dikemaskini.',

    // === Kelulusan (Approval Messages) ===
    'approval_decision_required'    => 'Sila pilih keputusan.',
    'approval_comment_required'     => 'Catatan diperlukan untuk tindakan ini.',
    'approval_forward_required'     => 'Sila pilih pegawai untuk majukan.',
    'approval_quantity_invalid'     => 'Kuantiti diluluskan tidak sah.',

    // === Sesi, Status, dan Alert System ===
    'login_success'                   => 'Log masuk berjaya.',
    'logout_success'                  => 'Log keluar berjaya.',
    'register_success'                => 'Pendaftaran berjaya.',
    'password_reset_link_sent'        => 'Pautan tetapan semula kata laluan telah dihantar ke e-mel anda.',
    'password_reset_success'          => 'Kata laluan anda telah berjaya ditetapkan semula.',
    'profile_update_success'          => 'Profil berjaya dikemaskini.',
    'record_created'                  => 'Rekod berjaya dicipta.',
    'record_updated'                  => 'Rekod berjaya dikemaskini.',
    'record_deleted'                  => 'Rekod berjaya dipadam.',
    'no_permission'                   => 'Anda tidak mempunyai kebenaran untuk melakukan tindakan ini.',
    'operation_failed'                => 'Operasi gagal dilaksanakan.',
    'invalid_credentials'             => 'Maklumat log masuk tidak sah.',
    'account_inactive'                => 'Akaun anda tidak aktif.',
    'password_confirmation_mismatch'  => 'Sahkan kata laluan tidak sepadan.',
    'incorrect_password'              => 'Kata laluan tidak tepat.',
    'session_expired'                 => 'Sesi anda telah tamat. Sila log masuk semula.',
    'account_locked'                  => 'Akaun anda telah dikunci. Sila hubungi pentadbir sistem.',
    'account_not_found'               => 'Akaun tidak ditemui.',
    'email_verified'                  => 'Alamat e-mel telah disahkan.',
    'email_verification_sent'         => 'Pautan pengesahan e-mel telah dihantar.',
    '2fa_required'                    => 'Pengesahan dua faktor diperlukan.',
    '2fa_invalid_code'                => 'Kod pengesahan tidak sah.',
    '2fa_invalid_recovery_code'       => 'Kod pemulihan tidak sah.',
    'action_forbidden'                => 'Akses tidak dibenarkan.',
    'back_to_list'                    => 'Kembali ke Senarai',
    'back_to_home'                    => 'Kembali ke Halaman Utama',

    // === Notifikasi, Alert & Banner System ===
    'saved_successfully' => 'Berjaya disimpan.', // Untuk action-message default slot
    'success'            => 'Berjaya!',         // Tajuk alert/banner success
    'error'              => 'Ralat!',           // Tajuk alert/banner error
    'warning'            => 'Amaran!',          // Tajuk alert/banner warning
    'info'               => 'Makluman',         // Tajuk alert/banner info
    'close'              => 'Tutup',            // Butang tutup alert/banner

    // === Email Notification Specific (untuk templat email) ===
    // Common ticket/email notification words
    'notification_see_ticket'         => 'Lihat Butiran Tiket',
    'notification_ticket_details'     => 'Butiran Tiket',
    'notification_ticket_created'     => 'Tiket Sokongan IT Baru Anda Telah Dicipta',
    'notification_ticket_assigned'    => 'Tiket Sokongan IT Ditugaskan Kepada Anda',
    'notification_ticket_status_updated' => 'Kemas Kini Status Tiket Sokongan IT',
    'notification_new_comment_added'  => 'Komen Baru Ditambah pada Tiket Sokongan IT',
    'notification_team_invitation'    => 'Jemputan Menyertai Pasukan',
    'notification_greeting'           => 'Salam Sejahtera',
    'notification_thank_you'          => 'Sekian, terima kasih.',
    'notification_do_not_reply'       => 'Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.',
    'notification_all_rights_reserved'=> 'Hak Cipta Terpelihara.',
    'notification_footer_org'         => 'Kementerian Pelancongan, Seni dan Budaya Malaysia',
    'notification_footer_team'        => 'Pasukan Pentadbir Sistem',
    'notification_footer_bpm'         => 'Bahagian Pengurusan Maklumat (BPM)',
    'notification_sender_bpm'         => 'Bahagian Pengurusan Maklumat',
    'notification_sender_org'         => 'Kementerian Pelancongan, Seni dan Budaya Malaysia',
    'notification_salutation_executor'=> 'Yang menjalankan amanah,',
    'notification_new_application'    => 'Tindakan Diperlukan: Permohonan Baru Dihantar',
    'notification_application_approved' => 'Permohonan Diluluskan',
    'notification_application_rejected' => 'Permohonan Ditolak',
    'notification_equipment_issued'   => 'Peralatan Pinjaman ICT Telah Dikeluarkan',
    'notification_equipment_returned' => 'Peralatan Pinjaman Telah Dipulangkan',
    'notification_equipment_overdue'  => 'Peringatan: Pinjaman Peralatan ICT Lewat Dipulangkan',
    'notification_equipment_return_reminder' => 'Peringatan Pulangan Peralatan ICT',
    'notification_ready_for_issuance' => 'Tindakan Diperlukan: Permohonan Pinjaman Sedia Untuk Pengeluaran',
    'notification_approval_needed'    => 'Tindakan Kelulusan Diperlukan',
    'notification_approval_action_needed' => 'Tindakan Kelulusan Diperlukan',
    'notification_application_details' => 'Maklumat Permohonan',
    'notification_equipment_return_details' => 'Butiran Pulangan Peralatan',
    'notification_equipment_details'   => 'Butiran Peralatan yang Dikeluarkan',
    'notification_equipment_pending_return' => 'Peralatan yang Masih Belum Dipulangkan',
    'notification_equipment_returned_accessories' => 'Aksesori yang Dipulangkan',
    'notification_equipment_return_notes' => 'Catatan Pulangan',
    'notification_rejection_reason'    => 'Sebab Penolakan',
    'notification_comment_by'          => 'Komen Ditambah Oleh',
    'notification_comment'             => 'Komen',
    'notification_status_change'       => 'Perubahan Status',
];
