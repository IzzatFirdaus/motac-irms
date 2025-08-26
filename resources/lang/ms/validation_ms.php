<?php

// Bahasa Melayu translations for validation errors and field names
// Disusun mengikut kategori untuk memudahkan rujukan dan penyelenggaraan

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Default Laravel/Framework)
    |--------------------------------------------------------------------------
    */
    'accepted'        => 'Medan :attribute mesti diterima.',
    'accepted_if'     => 'Medan :attribute mesti diterima apabila :other adalah :value.',
    'active_url'      => 'Medan :attribute mestilah URL yang sah.',
    'after'           => 'Medan :attribute mestilah tarikh selepas :date.',
    'after_or_equal'  => 'Medan :attribute mestilah tarikh selepas atau sama dengan :date.',
    'alpha'           => 'Medan :attribute hanya boleh mengandungi huruf.',
    'alpha_dash'      => 'Medan :attribute hanya boleh mengandungi huruf, nombor, sengkang dan garis bawah.',
    'alpha_num'       => 'Medan :attribute hanya boleh mengandungi huruf dan nombor.',
    'array'           => 'Medan :attribute mestilah jujukan.',
    'ascii'           => ':attribute mestilah aksara alfanumerik bait tunggal dan simbol sahaja.',
    'before'          => 'Medan :attribute mestilah tarikh sebelum :date.',
    'before_or_equal' => 'Medan :attribute mestilah tarikh sebelum atau sama dengan :date.',
    'between'         => [
        'array'   => 'Medan :attribute mesti mempunyai antara :min dan :max perkara.',
        'file'    => 'Medan :attribute mesti antara :min dan :max kilobait.',
        'numeric' => 'Medan :attribute mesti antara :min dan :max.',
        'string'  => 'Medan :attribute mesti antara :min dan :max aksara.',
    ],
    'boolean'           => 'Medan :attribute mestilah benar atau palsu.',
    'can'               => 'Medan :attribute mengandungi nilai yang tidak dibenarkan.',
    'confirmed'         => 'Pengesahan medan :attribute tidak sepadan.',
    'current_password'  => 'Kata laluan salah.',
    'date'              => 'Medan :attribute mestilah tarikh yang sah.',
    'date_equals'       => 'Medan :attribute mestilah tarikh yang sama dengan :date.',
    'date_format'       => 'Medan :attribute mesti sepadan dengan format :format.',
    'decimal'           => 'Medan :attribute mesti mempunyai :decimal tempat perpuluhan.',
    'declined'          => 'Medan :attribute mesti ditolak.',
    'declined_if'       => 'Medan :attribute mesti ditolak apabila :other adalah :value.',
    'different'         => 'Medan :attribute dan :other mestilah berbeza.',
    'digits'            => 'Medan :attribute mestilah :digits digit.',
    'digits_between'    => 'Medan :attribute mestilah antara :min dan :max digit.',
    'dimensions'        => 'Medan :attribute mempunyai dimensi imej yang tidak sah.',
    'distinct'          => 'Medan :attribute mempunyai nilai duplikasi.',
    'doesnt_contain'    => 'Medan :attribute tidak boleh mengandungi salah satu daripada yang berikut: :values.',
    'doesnt_start_with' => 'Medan :attribute tidak boleh bermula dengan salah satu daripada yang berikut: :values.',
    'email'             => 'Medan :attribute mestilah alamat e-mel yang sah.',
    'ends_with'         => 'Medan :attribute mestilah berakhir dengan salah satu daripada yang berikut: :values.',
    'enum'              => 'Yang dipilih :attribute tidak sah.',
    'exists'            => 'Yang dipilih :attribute tidak sah.',
    'file'              => 'Medan :attribute mestilah fail.',
    'filled'            => 'Medan :attribute mesti mempunyai nilai.',
    'gt'                => [
        'array'   => 'Medan :attribute mesti mempunyai lebih daripada :value perkara.',
        'file'    => 'Medan :attribute mestilah lebih besar daripada :value kilobait.',
        'numeric' => 'Medan :attribute mestilah lebih besar daripada :value.',
        'string'  => 'Medan :attribute mestilah lebih besar daripada :value aksara.',
    ],
    'gte' => [
        'array'   => 'Medan :attribute mesti mempunyai :value perkara atau lebih.',
        'file'    => 'Medan :attribute mestilah lebih besar daripada atau sama dengan :value kilobait.',
        'numeric' => 'Medan :attribute mestilah lebih besar daripada atau sama dengan :value.',
        'string'  => 'Medan :attribute mestilah lebih besar daripada atau sama dengan :value aksara.',
    ],
    'image'     => 'Medan :attribute mestilah imej.',
    'in'        => 'Yang dipilih :attribute tidak sah.',
    'in_array'  => 'Medan :attribute tidak wujud dalam :other.',
    'integer'   => 'Medan :attribute mestilah integer.',
    'ip'        => 'Medan :attribute mestilah alamat IP yang sah.',
    'ipv4'      => 'Medan :attribute mestilah alamat IPv4 yang sah.',
    'ipv6'      => 'Medan :attribute mestilah alamat IPv6 yang sah.',
    'json'      => 'Medan :attribute mestilah rentetan JSON yang sah.',
    'lowercase' => 'Medan :attribute mestilah huruf kecil.',
    'lt'        => [
        'array'   => 'Medan :attribute mesti mempunyai kurang daripada :value perkara.',
        'file'    => 'Medan :attribute mestilah kurang daripada :value kilobait.',
        'numeric' => 'Medan :attribute mestilah kurang daripada :value.',
        'string'  => 'Medan :attribute mestilah kurang daripada :value aksara.',
    ],
    'lte' => [
        'array'   => 'Medan :attribute tidak boleh mempunyai lebih daripada :value perkara.',
        'file'    => 'Medan :attribute mestilah kurang daripada atau sama dengan :value kilobait.',
        'numeric' => 'Medan :attribute mestilah kurang daripada atau sama dengan :value.',
        'string'  => 'Medan :attribute mestilah kurang daripada atau sama dengan :value aksara.',
    ],
    'mac_address' => 'Medan :attribute mestilah alamat MAC yang sah.',
    'max'         => [
        'array'   => 'Medan :attribute tidak boleh mempunyai lebih daripada :max perkara.',
        'file'    => 'Medan :attribute tidak boleh lebih besar daripada :max kilobait.',
        'numeric' => 'Medan :attribute tidak boleh lebih besar daripada :max.',
        'string'  => 'Medan :attribute tidak boleh lebih besar daripada :max aksara.',
    ],
    'max_digits' => 'Medan :attribute tidak boleh mempunyai lebih daripada :max digit.',
    'mimes'      => 'Medan :attribute mestilah fail jenis: :values.',
    'mimetypes'  => 'Medan :attribute mestilah fail jenis: :values.',
    'min'        => [
        'array'   => 'Medan :attribute mesti mempunyai sekurang-kurangnya :min perkara.',
        'file'    => 'Medan :attribute mestilah sekurang-kurangnya :min kilobait.',
        'numeric' => 'Medan :attribute mestilah sekurang-kurangnya :min.',
        'string'  => 'Medan :attribute mestilah sekurang-kurangnya :min aksara.',
    ],
    'min_digits'           => 'Medan :attribute mesti mempunyai sekurang-kurangnya :min digit.',
    'missing'              => 'Medan :attribute mesti tiada.',
    'missing_if'           => 'Medan :attribute mesti tiada apabila :other adalah :value.',
    'missing_unless'       => 'Medan :attribute mesti tiada melainkan :other adalah :value.',
    'missing_with'         => 'Medan :attribute mesti tiada apabila :values ada.',
    'missing_with_all'     => 'Medan :attribute mesti tiada apabila :values ada.',
    'multiple_of'          => 'Medan :attribute mestilah gandaan :value.',
    'not_in'               => 'Yang dipilih :attribute tidak sah.',
    'not_regex'            => 'Format medan :attribute tidak sah.',
    'numeric'              => 'Medan :attribute mestilah nombor.',
    'present'              => 'Medan :attribute mesti wujud.',
    'prohibited'           => 'Medan :attribute adalah dilarang.',
    'prohibited_if'        => 'Medan :attribute adalah dilarang apabila :other adalah :value.',
    'prohibited_unless'    => 'Medan :attribute adalah dilarang melainkan :other ada dalam :values.',
    'prohibits'            => 'Medan :attribute melarang :other daripada hadir.',
    'regex'                => 'Format medan :attribute tidak sah.',
    'required'             => 'Medan :attribute wajib diisi.',
    'required_array_keys'  => 'Medan :attribute mesti mengandungi entri untuk: :values.',
    'required_if'          => 'Medan :attribute wajib diisi apabila :other adalah :value.',
    'required_if_accepted' => 'Medan :attribute wajib diisi apabila :other diterima.',
    'required_unless'      => 'Medan :attribute wajib diisi melainkan :other ada dalam :values.',
    'required_with'        => 'Medan :attribute wajib diisi apabila :values ada.',
    'required_with_all'    => 'Medan :attribute wajib diisi apabila :values ada.',
    'required_without'     => 'Medan :attribute wajib diisi apabila :values tiada.',
    'required_without_all' => 'Medan :attribute wajib diisi apabila tiada satu pun daripada :values ada.',
    'same'                 => 'Medan :attribute dan :other mesti sepadan.',
    'size'                 => [
        'array'   => 'Medan :attribute mesti mengandungi :size perkara.',
        'file'    => 'Medan :attribute mestilah :size kilobait.',
        'numeric' => 'Medan :attribute mestilah :size.',
        'string'  => 'Medan :attribute mestilah :size aksara.',
    ],
    'starts_with' => 'Medan :attribute mestilah bermula dengan salah satu daripada yang berikut: :values.',
    'string'      => 'Medan :attribute mestilah rentetan.',
    'timezone'    => 'Medan :attribute mestilah zon waktu yang sah.',
    'unique'      => 'Medan :attribute telah wujud.',
    'uploaded'    => 'Medan :attribute gagal dimuat naik.',
    'uppercase'   => 'Medan :attribute mestilah huruf besar.',
    'url'         => 'Medan :attribute mestilah URL yang sah.',
    'uuid'        => 'Medan :attribute mestilah UUID yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    | Untuk custom rules atau field tertentu, boleh tambah di sini.
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Field Attributes
    |--------------------------------------------------------------------------
    | Gunakan seksyen ini untuk menukar nama field supaya mesej ralat lebih mesra pengguna
    */
    'attributes' => [
        // === Field Umum/Admin/User/Department/Grade/Position ===
        'name'                  => 'Nama',
        'email'                 => 'E-mel',
        'password'              => 'Kata Laluan',
        'current_password'      => 'Kata Laluan Semasa',
        'confirm_password'      => 'Sahkan Kata Laluan',
        'title'                 => 'Gelaran',
        'full_name'             => 'Nama Penuh',
        'nric_no'               => 'No. Kad Pengenalan',
        'identification_number' => 'No. Kad Pengenalan',
        'passport_number'       => 'No. Passport',
        'position_grade'        => 'Jawatan & Gred',
        'department_unit'       => 'Jabatan/Unit',
        'level'                 => 'Tahap Gred',
        'mobile_number'         => 'No. Telefon Bimbit',
        'status'                => 'Status',
        'description'           => 'Keterangan',
        'code'                  => 'Kod',
        'branch_type'           => 'Jenis Cawangan',
        'is_active'             => 'Status',
        'head_of_department_id' => 'Ketua Jabatan/Unit',
        'personal_email'        => 'E-mel Peribadi',
        'motac_email'           => 'E-mel MOTAC',
        'user_id_assigned'      => 'ID Pengguna Rangkaian',
        'service_status'        => 'Taraf Perkhidmatan',
        'appointment_type'      => 'Jenis Pelantikan',
        'position_id'           => 'Jawatan',
        'grade_id'              => 'Gred',
        'min_approval_grade_id' => 'Gred Minimum Untuk Meluluskan',
        'is_approver_grade'     => 'Gred Pelulus',
        'department_id'         => 'Jabatan/Unit',

        // === Auth & User Profile ===
        'password_confirmation' => 'Sahkan Kata Laluan',
        'terms'                 => 'Terma Perkhidmatan',
        'department'            => 'Jabatan/Unit',
        'position'              => 'Jawatan',
        'grade'                 => 'Gred',
        'user_id'               => 'ID Pengguna',
        'role'                  => 'Peranan',
        'roles'                 => 'Peranan',
        'remember'              => 'Ingat Saya',

        // === Transaction/Loan ===
        'loan_application_item_id' => 'Item Permohonan',
        'equipment_id'             => 'Peralatan',
        'quantity_issued'          => 'Kuantiti Dikeluarkan',
        'receiving_officer_id'     => 'Pegawai Penerima',
        'issue_notes'              => 'Nota Pengeluaran',

        // === Report Filters ===
        'start_date' => 'Tarikh Mula',
        'end_date'   => 'Tarikh Tamat',

        // === 2FA/Authentication ===
        'recovery_code' => 'Kod Pemulihan',

        // === For Approval Forms ===
        'decision'            => 'Keputusan',
        'forward_approver_id' => 'Pegawai Dimajukan',
        'approved_quantities' => 'Kuantiti Diluluskan',
        'comments'            => 'Catatan Pegawai',

        // === API Tokens ===
        'api_token_name' => 'Nama Token API',
        'permissions'    => 'Kebenaran',

        // === Loan Application Fields ===
        'purpose'                                 => 'Tujuan Pinjaman',
        'location'                                => 'Lokasi Penggunaan',
        'loan_start_date'                         => 'Tarikh Mula Pinjaman',
        'loan_end_date'                           => 'Tarikh Akhir Pinjaman',
        'responsible_officer_id'                  => 'Pegawai Bertanggungjawab',
        'loan_application_items'                  => 'Item Pinjaman',
        'loan_application_items.*.equipment_type' => 'Jenis Peralatan',
        'loan_application_items.*.quantity'       => 'Kuantiti',
        'return_date'                             => 'Tarikh Pemulangan',

        // === Helpdesk Ticket Fields ===
        'subject'             => 'Subjek Tiket',
        'category_id'         => 'Kategori Tiket',
        'priority_id'         => 'Prioriti Tiket',
        'assigned_to_user_id' => 'Diserahkan Kepada',
        'comment'             => 'Ulasan',
        'file_path'           => 'Fail Lampiran',
        'file_name'           => 'Nama Fail',
        'file_size'           => 'Saiz Fail',
        'file_type'           => 'Jenis Fail',

        // === Custom validation headings/messages ===
        'validation_error_heading' => 'Sila perbetulkan ralat berikut:', // Used for main validation error heading
    ],
];
