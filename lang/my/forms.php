<?php

return [
  // ICT Equipment Loan Form (BORANG PINJAMAN PERALATAN ICT)
  'ict_loan_form_title' => 'BORANG PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI',
  'bpm_office_display' => 'Bahagian Pengurusan Maklumat',
  'date_application_received_label' => 'TARIKH BORANG PERMOHONAN LENGKAP DITERIMA',

  'section_applicant_info_ict' => 'BAHAGIAN 1 | MAKLUMAT PEMOHON',
  'label_full_name' => 'Nama Penuh*',
  'label_position_grade' => 'Jawatan & Gred*',
  'label_department_unit' => 'Bahagian/Unit*',
  'label_application_purpose' => 'Tujuan Permohonan*',
  'label_phone_number' => 'No.Telefon*',
  'label_location_ict' => 'Lokasi*',
  'label_loan_date' => 'Tarikh Pinjaman*',
  'label_expected_return_date' => 'Tarikh Dijangka Pulang*',

  'section_responsible_officer_info' => 'BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB',
  'instruction_responsible_officer_is_applicant' => 'Sila tandakan jika Pemohon adalah Pegawai Bertanggungjawab.',
  'instruction_responsible_officer_different' => 'Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.',

  'section_equipment_details_ict' => 'BAHAGIAN 3 | MAKLUMAT PERALATAN',
  'table_header_bil' => 'Bil.',
  'table_header_equipment_type' => 'Jenis Peralatan',
  'table_header_quantity' => 'Kuantiti',
  'table_header_remarks' => 'Catatan', // from PDF, my.json has "Note"

  'section_applicant_confirmation_ict' => 'BAHAGIAN 4 | PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)',
  'text_applicant_declaration_ict' => 'Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut;',
  'label_date' => 'Tarikh', // from my.json "Date": "Tarikh"
  'label_signature_stamp' => 'Tandatangan & Cop (jika ada):',
  'label_name' => 'Nama', // from my.json "Name": "Nama"

  'section_section_unit_endorsement' => 'BAHAGIAN 5 | PENGESAHAN BAHAGIAN / UNIT / SEKSYEN',
  'instruction_support_grade_ict' => 'Permohonan yang lengkap diisi oleh pemohon hendaklah DISOKONG OLEH PEGAWAI SEKURANG-KURANGNYA GRED 41 DAN KE ATAS.',
  'label_application_status_endorsement' => 'Permohonan ini adalah: * DISOKONG/ TIDAK DISOKONG',

  'section_bpm_use_only' => 'KEGUNAAN BAHAGIAN PENGURUSAN MAKLUMAT SAHAJA',
  'section_during_loan_ict' => 'BAHAGIAN 6 | SEMASA PEMINJAMAN',
  'label_issuing_officer' => 'PEGAWAI PENGELUAR',
  'label_receiving_officer' => 'PEGAWAI PENERIMA',

  'section_during_return_ict' => 'BAHAGIAN 7 | SEMASA PEMULANGAN',
  'label_returning_officer' => 'PEGAWAI YANG MEMULANGKAN',
  'label_return_acceptance_officer' => 'PEGAWAI TERIMA PULANGAN',
  'label_remarks_optional' => 'Catatan (jika ada):',

  'section_loan_details_ict' => 'BAHAGIAN 8 | MAKLUMAT PEMINJAMAN',
  'table_header_brand_model' => 'JENAMA DAN MODEL',
  'table_header_serial_tag_id' => 'NO. SIRI / TAG ID',  // my.json has "Serial Number": "Nombor Siri"
  'table_header_accessories' => 'AKSESORI',
  'checkbox_power_adapter' => 'Power Adapter',
  'checkbox_bag' => 'Beg',
  'checkbox_mouse' => 'Mouse',
  'checkbox_usb_cable' => 'Kabel USB',
  'checkbox_hdmi_vga_cable' => 'Kabel HDMI/VGA',
  'checkbox_remote' => 'Remote',
  'checkbox_others_specify' => 'Lain-lain. Nyatakan :',

  // Email/User ID Application Form
  'email_user_id_application_title' => 'Permohonan Emel & ID Pengguna MOTAC',
  'section_applicant_declaration_email' => 'PERAKUAN PEMOHON',
  'text_applicant_declaration_lead_in_email' => 'Saya dengan ini mengesahkan bahawa:',
  'checkbox_info_is_true' => 'Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.',
  'checkbox_data_usage_agreed' => 'BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.',
  'checkbox_email_responsibility_agreed' => 'BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.',

  'section_applicant_info_email' => 'MAKLUMAT PEMOHON',
  'label_service_status' => 'Taraf Perkhidmatan:*',
  'placeholder_select_service_status' => '- Pilih Taraf Perkhidmatan -',
  'option_service_status_permanent' => 'Tetap',
  'option_service_status_contract_mystep' => 'Lantikan Kontrak / MyStep',
  'option_service_status_intern' => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
  'option_service_status_other_agency' => 'Kakitangan Agensi Luar (Mailbox Sedia Ada)',

  'label_appointment_type' => 'Pelantikan:*',
  'placeholder_select_appointment_type' => '- Pilih Pelantikan -',
  'option_appointment_type_new' => 'Baharu',
  'option_appointment_type_promotion_transfer' => 'Kenaikan Pangkat/Pertukaran',
  'option_appointment_type_others' => 'Lain-lain',

  'label_full_name_title' => 'Nama Penuh & Gelaran:*',
  'placeholder_full_name_example' => 'Contoh: Annis Anwari',
  'label_nric_no' => 'No. Kad Pengenalan:*',  // my.json has "National Number": "Nombor Nasional"
  'label_passport_no' => 'No. Pasport/Staff:*',
  'placeholder_nric_example' => 'Contoh: 800707-02-5044',
  'label_grade' => 'Gred:',
  'placeholder_select_grade' => '- Pilih Gred -',
  'label_position' => 'Jawatan', // from my.json "Position": "Jawatan"
  'placeholder_select_position' => '- Pilih Jawatan -',
  'label_motac_dept_unit' => 'MOTAC Negeri/ Bahagian/ Unit:*', // Maps to Department concept
  'placeholder_select_motac_dept_unit' => '- Pilih MOTAC Negeri/Bahagian/Unit -',
  'label_level_floor' => 'Aras',
  'placeholder_select_level_floor' => '-Pilih Aras -',
  'label_mobile_number' => 'No. Telefon Bimbit:*', // my.json has "Mobile": "Mudah Alih"
  'label_personal_email' => 'E-mel Peribadi', // from my.json "Email": "E-mel"
  'placeholder_personal_email_example' => 'Contoh: annisanwari@gmail.com',
  'label_group_email' => 'Group Email:',
  'placeholder_group_email_example' => 'Contoh: group all, groupunit',
  'label_previous_department_name' => 'Jabatan Terdahulu',
  'label_previous_department_email' => 'E-mel Rasmi Jabatan Terdahulu',

  'label_contact_person_name_admin_eo_cc' => 'Nama Admin/EO/CC:',
  'placeholder_contact_person_name_example' => 'Contoh: Rashid Bin Sardi',
  'label_contact_person_email_admin_eo_cc' => 'E-mel Admin/EO/CC:',
  'placeholder_contact_person_email_example' => 'Contoh: rashid@motac.gov.my',
  'label_proposed_id_email_reason' => 'Cadangan ID/E-mel Tujuan/Catatan:',
  'placeholder_proposed_id_email_reason_example' => 'Contoh: annis@motac.gov.my/ Permohonan bagi Pegawai baharu bertukar masuk',

  'section_supporting_officer_info' => 'MAKLUMAT PEGAWAI PENYOKONG',
  'label_supporter_name' => 'Nama:',
  'placeholder_supporter_name_example' => 'Contoh: Nur Faridah Jasni',
  'label_supporter_email' => 'E-mel:',
  'placeholder_supporter_email_example' => 'Contoh: nur.faridah@motac.gov.my',
  'label_supporter_grade' => 'Gred:',

  'service_status_options' => [
    '' => '- Pilih Taraf Perkhidmatan -',
    'tetap' => 'Tetap',
    'lantikan_kontrak_mystep' => 'Lantikan Kontrak / MyStep',
    'pelajar_latihan_industri' => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
    'other_agency_existing_mailbox' => 'Bertugas di MOTAC (Mailbox Agensi Utama)',
  ],

  'appointment_type_options' => [
    '' => '- Pilih Pelantikan -',
    'baharu' => 'Baharu',
    'kenaikan_pangkat_pertukaran' => 'Kenaikan Pangkat/Pertukaran',
    'lain_lain' => 'Lain-lain',
  ],

  'position_options_example' => [
    '' => '- Pilih Jawatan -',
    '1' => 'Menteri',
    '21' => 'Pegawai Teknologi Maklumat (F)',
  ],

  'grade_options_example' => [
    '' => '- Pilih Gred -',
    '9' => '9 (41)',
    '41' => '41',
  ],

  'department_options_example' => [
    '' => '- Pilih MOTAC Negeri/Bahagian/Unit -',
    '18' => 'Pengurusan Maklumat',
  ],

  'level_options' => [
    '' => '-Pilih Aras -',
    '1' => '1',
    '2' => '2',
  ],
  'label_service_start_date' => 'Tarikh Mula Berkhidmat', // my.json has "Start date": "Tarikh mula"
  'label_service_end_date' => 'Tarikh Akhir Berkhidmat', // my.json has "End Date": "Tarikh Tamat"
  'label_address' => 'Alamat', //
  'label_acquisition_date' => 'Tarikh Perolehan', //
  'label_acquisition_type' => 'Jenis Perolehan', //
  'label_category' => 'Kategori', //
  'label_center' => 'Pusat', //
  'label_contract_id' => 'ID Kontrak', //
  'label_degree' => 'Ijazah', //
  'label_employee' => 'Pekerja', //
  'label_expected_price' => 'Harga Dijangka', //
  'label_father_name' => 'Nama Bapa', //
  'label_first_name' => 'Nama Pertama', //
  'label_gender' => 'Jantina', //
  'label_handed_date' => 'Tarikh Serahan', //
  'label_id' => 'ID', //
  'label_last_name' => 'Nama Akhir', //
  'label_mother_name' => 'Nama Ibu', //
  'label_national_number' => 'Nombor Nasional', //
  'label_old_id' => 'ID Lama', //
  'label_password' => 'Kata Laluan', //
  'label_quit_date' => 'Tarikh berhenti', //
  'label_rate' => 'Kadar', //
  'label_real_price' => 'Harga Sebenar', //
  'label_reason' => 'Sebab', //
  'label_record_info' => 'Maklumat Rekod', //
  'label_serial_number' => 'Nombor Siri', //
  'label_sub_category' => 'Sub-Kategori', //
  'label_birth_place' => 'Tempat Kelahiran', // Derived from "Birth & Place": "Kelahiran & Tempat"
  'label_select_gender' => 'Pilih Jantina', //
  'placeholder_select_dot' => 'Pilih..', //
  'search_id_category' => 'Cari (ID, Kategori...)', //
  'search_id_name' => 'Cari (ID, Nama...)', //
  'search_id_old_id_serial' => 'Cari (ID, ID Lama, Nombor Siri...)', //
  'search_id_sub_category' => 'Cari (ID, Sub-Kategori...)', //
  'search_placeholder' => 'Cari...', //
];
