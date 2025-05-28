<?php declare(strict_types=1);

// resources/lang/my/validation.php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    | Design Language: Clear Instructions & Actionable Feedback (validation messages).
    */

    'accepted' => 'Medan :attribute mesti diterima.',
    'accepted_if' => 'Medan :attribute mesti diterima apabila :other adalah :value.',
    'active_url' => 'Medan :attribute mestilah URL yang sah.',
    'after' => 'Medan :attribute mestilah tarikh selepas :date.',
    'after_or_equal' => 'Medan :attribute mestilah tarikh selepas atau sama dengan :date.',
    'alpha' => 'Medan :attribute hanya boleh mengandungi huruf.',
    'alpha_dash' => 'Medan :attribute hanya boleh mengandungi huruf, nombor, sengkang dan garis bawah.',
    'alpha_num' => 'Medan :attribute hanya boleh mengandungi huruf dan nombor.',
    'array' => 'Medan :attribute mestilah jujukan.',
    'ascii' => ':attribute mestilah aksara alfanumerik bait tunggal dan simbol sahaja.',
    'before' => 'Medan :attribute mestilah tarikh sebelum :date.',
    'before_or_equal' => 'Medan :attribute mestilah tarikh sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Medan :attribute mesti mempunyai antara :min dan :max perkara.',
        'file' => 'Medan :attribute mesti antara :min dan :max kilobait.',
        'numeric' => 'Medan :attribute mesti antara :min dan :max.',
        'string' => 'Medan :attribute mesti antara :min dan :max aksara.',
    ],
    'boolean' => 'Medan :attribute mesti benar atau salah.',
    'confirmed' => 'Pengesahan :attribute tidak sepadan.',
    'current_password' => 'Kata laluan semasa adalah salah.',
    'date' => 'Medan :attribute bukan tarikh yang sah.',
    'date_equals' => 'Medan :attribute mestilah tarikh yang sama dengan :date.',
    'date_format' => 'Medan :attribute tidak mengikut format :format.',
    'decimal' => ':attribute mestilah mempunyai :decimal tempat perpuluhan.',
    'declined' => 'Medan :attribute mesti ditolak.',
    'declined_if' => 'Medan :attribute mesti ditolak apabila :other adalah :value.',
    'different' => 'Medan :attribute dan :other mesti berlainan.',
    'digits' => 'Medan :attribute mesti :digits digit.',
    'digits_between' => 'Medan :attribute mesti antara :min dan :max digit.',
    'dimensions' => 'Medan :attribute mempunyai dimensi imej yang tidak sah.',
    'distinct' => 'Medan :attribute mempunyai nilai yang berulang.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu daripada: :values.',
    'doesnt_start_with' => ':attribute tidak boleh dimulakan dengan salah satu daripada: :values.',
    'email' => 'Medan :attribute mestilah alamat e-mel yang sah.',
    'ends_with' => 'Medan :attribute mesti berakhir dengan salah satu dari berikut: :values.',
    'enum' => ':attribute yang dipilih tidak sah.',
    'exists' => ':attribute yang dipilih tidak sah.',
    'file' => 'Medan :attribute mesti fail.',
    'filled' => 'Medan :attribute mesti mempunyai nilai.',
    'gt' => [
        'array' => 'Medan :attribute mesti mempunyai lebih daripada :value perkara.',
        'file' => 'Medan :attribute mesti lebih besar daripada :value kilobait.',
        'numeric' => 'Medan :attribute mesti lebih besar daripada :value.',
        'string' => 'Medan :attribute mesti lebih besar daripada :value aksara.',
    ],
    'gte' => [
        'array' => 'Medan :attribute mesti mempunyai :value perkara atau lebih.',
        'file' => 'Medan :attribute mesti lebih besar daripada atau sama dengan :value kilobait.',
        'numeric' => 'Medan :attribute mesti lebih besar daripada atau sama dengan :value.',
        'string' => 'Medan :attribute mesti lebih besar daripada atau sama dengan :value aksara.',
    ],
    'image' => 'Medan :attribute mesti imej.',
    'in' => ':attribute yang dipilih tidak sah.',
    'in_array' => 'Medan :attribute tidak wujud dalam :other.',
    'integer' => 'Medan :attribute mesti integer.',
    'ip' => 'Medan :attribute mesti alamat IP yang sah.',
    'ipv4' => 'Medan :attribute mesti alamat IPv4 yang sah.',
    'ipv6' => 'Medan :attribute mesti alamat IPv6 yang sah.',
    'json' => 'Medan :attribute mesti rentetan JSON yang sah.',
    'lowercase' => ':attribute mestilah huruf kecil.',
    'lt' => [
        'array' => 'Medan :attribute mesti mempunyai kurang daripada :value perkara.',
        'file' => 'Medan :attribute mesti kurang daripada :value kilobait.',
        'numeric' => 'Medan :attribute mesti kurang daripada :value.',
        'string' => 'Medan :attribute mesti kurang daripada :value aksara.',
    ],
    'lte' => [
        'array' => 'Medan :attribute tidak boleh mempunyai lebih daripada :value perkara.',
        'file' => 'Medan :attribute mesti kurang daripada atau sama dengan :value kilobait.',
        'numeric' => 'Medan :attribute mesti kurang daripada atau sama dengan :value.',
        'string' => 'Medan :attribute mesti kurang daripada atau sama dengan :value aksara.',
    ],
    'mac_address' => 'Medan :attribute mesti alamat MAC yang sah.',
    'max' => [
        'array' => 'Medan :attribute tidak boleh mempunyai lebih daripada :max perkara.',
        'file' => 'Medan :attribute tidak boleh lebih besar daripada :max kilobait.',
        'numeric' => 'Medan :attribute tidak boleh lebih besar daripada :max.',
        'string' => 'Medan :attribute tidak boleh lebih besar daripada :max aksara.',
    ],
    'max_digits' => ':attribute tidak boleh mempunyai lebih daripada :max digit.',
    'mimes' => 'Medan :attribute mesti fail jenis: :values.',
    'mimetypes' => 'Medan :attribute mesti fail jenis: :values.',
    'min' => [
        'array' => 'Medan :attribute mesti mempunyai sekurang-kurangnya :min perkara.',
        'file' => 'Medan :attribute mesti sekurang-kurangnya :min kilobait.',
        'numeric' => 'Medan :attribute mesti sekurang-kurangnya :min.',
        'string' => 'Medan :attribute mesti sekurang-kurangnya :min aksara.',
    ],
    'min_digits' => ':attribute mesti mempunyai sekurang-kurangnya :min digit.',
    'missing' => 'Medan :attribute mesti tiada.',
    'missing_if' => 'Medan :attribute mesti tiada apabila :other adalah :value.',
    'missing_unless' => 'Medan :attribute mesti tiada kecuali :other adalah :value.',
    'missing_with' => 'Medan :attribute mesti tiada apabila :values hadir.',
    'missing_with_all' => 'Medan :attribute mesti tiada apabila :values hadir.',
    'multiple_of' => 'Medan :attribute mesti gandaan :value.',
    'not_in' => ':attribute yang dipilih tidak sah.',
    'not_regex' => 'Format :attribute tidak sah.',
    'numeric' => 'Medan :attribute mesti nombor.',
    'password' => [
        'letters' => 'Medan :attribute mesti mengandungi sekurang-kurangnya satu huruf.',
        'mixed' => 'Medan :attribute mesti mengandungi sekurang-kurangnya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Medan :attribute mesti mengandungi sekurang-kurangnya satu nombor.',
        'symbols' => 'Medan :attribute mesti mengandungi sekurang-kurangnya satu simbol.',
        'uncompromised' => ':attribute yang diberikan telah muncul dalam kebocoran data. Sila pilih :attribute yang berbeza.',
    ],
    'present' => 'Medan :attribute mesti wujud.',
    'prohibited' => 'Medan :attribute adalah dilarang.',
    'prohibited_if' => 'Medan :attribute adalah dilarang apabila :other adalah :value.',
    'prohibited_unless' => 'Medan :attribute adalah dilarang kecuali :other berada dalam :values.',
    'prohibits' => 'Medan :attribute melarang :other daripada hadir.',
    'regex' => 'Format :attribute tidak sah.',
    'required' => 'Medan :attribute adalah wajib diisi.',
    'required_array_keys' => 'Medan :attribute mesti mengandungi entri untuk: :values.',
    'required_if' => 'Medan :attribute adalah wajib diisi apabila :other adalah :value.',
    'required_if_accepted' => 'Medan :attribute adalah wajib diisi apabila :other diterima.',
    'required_unless' => 'Medan :attribute adalah wajib diisi kecuali :other berada dalam :values.',
    'required_with' => 'Medan :attribute adalah wajib diisi apabila :values wujud.',
    'required_with_all' => 'Medan :attribute adalah wajib diisi apabila :values wujud.',
    'required_without' => 'Medan :attribute adalah wajib diisi apabila :values tidak wujud.',
    'required_without_all' => 'Medan :attribute adalah wajib diisi apabila tiada :values wujud.',
    'same' => 'Medan :attribute dan :other mesti sepadan.',
    'size' => [
        'array' => 'Medan :attribute mesti mengandungi :size perkara.',
        'file' => 'Medan :attribute mesti :size kilobait.',
        'numeric' => 'Medan :attribute mesti :size.',
        'string' => 'Medan :attribute mesti :size aksara.',
    ],
    'starts_with' => 'Medan :attribute mesti bermula dengan salah satu dari berikut: :values.',
    'string' => 'Medan :attribute mesti rentetan.',
    'timezone' => 'Medan :attribute mesti zon waktu yang sah.',
    'unique' => ':attribute telah wujud.',
    'uploaded' => ':attribute gagal dimuat naik.',
    'uppercase' => ':attribute mestilah huruf besar.',
    'url' => 'Format :attribute tidak sah.',
    'ulid' => ':attribute mestilah ULID yang sah.',
    'uuid' => ':attribute mestilah UUID yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'mesej-tersuai',
        ],
        'newLeaveInfo.fromDate' => [
            'after_or_equal' => 'Tarikh mula cuti mestilah tarikh semasa atau akan datang.',
        ],
        'newLeaveInfo.toDate' => [
            'after_or_equal' => 'Tarikh akhir cuti mestilah sama atau selepas tarikh mula cuti.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    | You can specify custom names for attributes to make messages more friendly.
    | Example: 'email' => 'Alamat E-mel Anda'
    | System Design Reference: Dashboard Modals (modal-leaveWithEmployee.blade.php inputs)
    */
    'attributes' => [
        // User Model Fields
        'name' => __('Nama Penuh'),
        'email' => __('Alamat E-mel'),
        'password' => __('Kata Laluan'),
        'title' => __('Gelaran'),
        'identification_number' => __('No. Kad Pengenalan'),
        'passport_number' => __('No. Pasport'),
        'position_id' => __('Jawatan'),
        'grade_id' => __('Gred'),
        'department_id' => __('Jabatan/Unit'),
        'level' => __('Aras'),
        'mobile_number' => __('No. Telefon Bimbit'),
        'motac_email' => __('E-mel Rasmi MOTAC'),
        'user_id_assigned' => __('ID Pengguna (Sistem)'),
        'service_status' => __('Taraf Perkhidmatan'),
        'appointment_type' => __('Jenis Pelantikan'),
        'status' => __('Status'),

        // Email Application Fields
        'application_reason_notes' => __('Tujuan/Catatan Permohonan'),
        'proposed_email' => __('Cadangan E-mel ID'),
        'contact_person_name' => __('Nama Admin/EO/CC'),
        'contact_person_email' => __('E-mel Admin/EO/CC'),
        'supporting_officer_id' => __('Pegawai Penyokong'),
        'cert_info_is_true' => __('Perakuan Maklumat Benar'),
        'cert_data_usage_agreed' => __('Perakuan Penggunaan Data'),
        'cert_email_responsibility_agreed' => __('Perakuan Tanggungjawab E-mel'),

        // Loan Application Fields
        'purpose' => __('Tujuan Pinjaman'),
        'location' => __('Lokasi Penggunaan'),
        'loan_start_date' => __('Tarikh Mula Pinjaman'),
        'loan_end_date' => __('Tarikh Akhir Pinjaman'),
        'responsible_officer_id' => __('Pegawai Bertanggungjawab'),
        'applicationItems' => __('Item Pinjaman'),
        'applicationItems.*.equipment_type' => __('Jenis Peralatan'),
        'applicationItems.*.quantity_requested' => __('Kuantiti Dimohon'),

        // Modal Leave Fields (from modal-leaveWithEmployee)
        'selectedEmployeeId' => __('Kakitangan'),
        'newLeaveInfo.LeaveId' => __('Jenis Cuti'),
        'newLeaveInfo.fromDate' => __('Tarikh Mula Cuti'),
        'newLeaveInfo.toDate' => __('Tarikh Akhir Cuti'),
        'newLeaveInfo.startAt' => __('Masa Mula Cuti'),
        'newLeaveInfo.endAt' => __('Masa Akhir Cuti'),
        'newLeaveInfo.note' => __('Catatan Cuti'),
    ],
];
