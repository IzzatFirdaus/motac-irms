<?php

return [
    // ICT Equipment Loan Form
    'ict_loan_form_title'             => 'نموذج طلب استعارة معدات تكنولوجيا المعلومات للاستخدام الرسمي',
    'bpm_office_display'              => 'قسم إدارة المعلومات',
    'date_application_received_label' => 'تاريخ استلام نموذج الطلب كاملاً',

    'section_applicant_info_ict' => 'الجزء الأول | معلومات مقدم الطلب',
    'label_full_name'            => 'الاسم الكامل*', // "Name" -> "الاسم"
    'label_position_grade'       => 'المنصب والدرجة*', // "Position" -> "المنصب"
    'label_department_unit'      => 'القسم/الوحدة*', // "Department" -> "القسم"
    'label_application_purpose'  => 'الغرض من الطلب*',
    'label_phone_number'         => 'رقم الهاتف*', // "Mobile" -> "الموبايل"
    'label_location_ict'         => 'الموقع (الاستخدام)*',
    'label_loan_date'            => 'تاريخ الاستعارة*',
    'label_expected_return_date' => 'تاريخ الإرجاع المتوقع*',

    'section_responsible_officer_info'             => 'الجزء الثاني | معلومات الموظف المسؤول',
    'instruction_responsible_officer_is_applicant' => 'يرجى وضع علامة إذا كان مقدم الطلب هو الموظف المسؤول.',
    'instruction_responsible_officer_different'    => 'يجب تعبئة هذا القسم فقط إذا لم يكن الموظف المسؤول هو مقدم الطلب.',

    'section_equipment_details_ict' => 'الجزء الثالث | معلومات المعدات',
    'table_header_bil'              => 'الرقم',
    'table_header_equipment_type'   => 'نوع المعدات',
    'table_header_quantity'         => 'الكمية',
    'table_header_remarks'          => 'ملاحظات',

    'section_applicant_confirmation_ict' => 'الجزء الرابع | تأكيد مقدم الطلب (الموظف المسؤول)',
    'text_applicant_declaration_ict'     => 'أؤكد بموجب هذا وأقر بأن جميع المعدات المستعارة هي للاستخدام الرسمي وستكون تحت مسؤوليتي وإشرافي طوال الفترة المذكورة؛',
    'label_date'                         => 'التاريخ', //
    'label_signature_stamp'              => 'التوقيع والختم (إن وجد):',
    'label_name'                         => 'الاسم', //

    'section_section_unit_endorsement'     => 'الجزء الخامس | مصادقة القسم / الوحدة / الشعبة',
    'instruction_support_grade_ict'        => 'يجب أن يكون الطلب المكتمل من قبل مقدم الطلب مدعومًا من قبل موظف بدرجة 41 على الأقل فما فوق.',
    'label_application_status_endorsement' => 'هذا الطلب: * مدعوم / غير مدعوم',

    'section_bpm_use_only'    => 'لاستخدام قسم إدارة المعلومات فقط',
    'section_during_loan_ict' => 'الجزء السادس | أثناء الاستعارة',
    'label_issuing_officer'   => 'الموظف المُصدر',
    'label_receiving_officer' => 'الموظف المُستلم',

    'section_during_return_ict'       => 'الجزء السابع | أثناء الإرجاع',
    'label_returning_officer'         => 'الموظف المُرجع',
    'label_return_acceptance_officer' => 'موظف استلام الإرجاع',
    'label_remarks_optional'          => 'ملاحظات (إن وجدت):',

    'section_loan_details_ict'   => 'الجزء الثامن | تفاصيل الاستعارة',
    'table_header_brand_model'   => 'العلامة التجارية والموديل',
    'table_header_serial_tag_id' => 'الرقم التسلسلي / معرف العلامة', // "Serial Number" -> "الرقم التسلسلي"
    'table_header_accessories'   => 'الملحقات',
    'checkbox_power_adapter'     => 'محول الطاقة',
    'checkbox_bag'               => 'حقيبة',
    'checkbox_mouse'             => 'فأرة',
    'checkbox_usb_cable'         => 'كابل USB',
    'checkbox_hdmi_vga_cable'    => 'كابل HDMI/VGA',
    'checkbox_remote'            => 'جهاز تحكم عن بعد',
    'checkbox_others_specify'    => 'أخرى. حدد:',

    // Email/User ID Application Form
    'email_user_id_application_title'          => 'طلب البريد الإلكتروني ومعرف المستخدم لـ MOTAC',
    'section_applicant_declaration_email'      => 'إقرار مقدم الطلب',
    'text_applicant_declaration_lead_in_email' => 'أصرح بموجب هذا بما يلي:',
    'checkbox_info_is_true'                    => 'جميع المعلومات الواردة في هذا الطلب صحيحة.',
    'checkbox_data_usage_agreed'               => 'أوافق على استخدام المعلومات الواردة في هذا الطلب من قبل قسم إدارة المعلومات لمعالجة طلبي.',
    'checkbox_email_responsibility_agreed'     => 'أوافق على تحمل المسؤولية عن كل بريد إلكتروني يتم إرساله واستلامه عبر حساب البريد الإلكتروني الخاص بي.',

    'section_applicant_info_email'          => 'معلومات مقدم الطلب',
    'label_service_status'                  => 'حالة الخدمة:*',
    'placeholder_select_service_status'     => '- اختر حالة الخدمة -',
    'option_service_status_permanent'       => 'دائم',
    'option_service_status_contract_mystep' => 'عقد / تعيين MyStep',
    'option_service_status_intern'          => 'متدرب صناعي (المقر الرئيسي فقط)',
    'option_service_status_other_agency'    => 'موظف وكالة خارجية (صندوق بريد حالي)',

    'label_appointment_type'                     => 'التعيين:*',
    'placeholder_select_appointment_type'        => '- اختر التعيين -',
    'option_appointment_type_new'                => 'جديد',
    'option_appointment_type_promotion_transfer' => 'ترقية/نقل',
    'option_appointment_type_others'             => 'أخرى',

    'label_full_name_title'              => 'الاسم الكامل واللقب:*',
    'placeholder_full_name_example'      => 'مثال: أنيس أنوري',
    'label_nric_no'                      => 'رقم بطاقة الهوية الوطنية:*', // "National Number" -> "الرقم الوطني"
    'label_passport_no'                  => 'رقم جواز السفر/الموظف:*',
    'placeholder_nric_example'           => 'مثال: 800707-02-5044',
    'label_grade'                        => 'الدرجة:',
    'placeholder_select_grade'           => '- اختر الدرجة -',
    'label_position'                     => 'المنصب', //
    'placeholder_select_position'        => '- اختر المنصب -',
    'label_motac_dept_unit'              => 'ولاية/قسم/وحدة MOTAC:*',
    'placeholder_select_motac_dept_unit' => '- اختر ولاية/قسم/وحدة MOTAC -',
    'label_level_floor'                  => 'المستوى/الطابق',
    'placeholder_select_level_floor'     => '-اختر المستوى/الطابق -',
    'label_mobile_number'                => 'رقم الهاتف المحمول:*', // "Mobile" -> "الموبايل"
    'label_personal_email'               => 'البريد الإلكتروني الشخصي', // "Email" -> "البريد الإلكتروني"
    'placeholder_personal_email_example' => 'مثال: annisanwari@gmail.com',
    'label_group_email'                  => 'بريد المجموعة:',
    'placeholder_group_email_example'    => 'مثال: group all, groupunit',
    'label_previous_department_name'     => 'القسم السابق',
    'label_previous_department_email'    => 'البريد الإلكتروني الرسمي للقسم السابق',

    'label_contact_person_name_admin_eo_cc'        => 'اسم المسؤول/الموظف التنفيذي/جهة الاتصال:',
    'placeholder_contact_person_name_example'      => 'مثال: راشد بن ساردي',
    'label_contact_person_email_admin_eo_cc'       => 'بريد المسؤول/الموظف التنفيذي/جهة الاتصال:',
    'placeholder_contact_person_email_example'     => 'مثال: rashid@motac.gov.my',
    'label_proposed_id_email_reason'               => 'المعرف/البريد الإلكتروني المقترح الغرض/الملاحظات:',
    'placeholder_proposed_id_email_reason_example' => 'مثال: annis@motac.gov.my/ طلب لموظف جديد يلتحق بالعمل',

    'section_supporting_officer_info'     => 'معلومات الموظف الداعم',
    'label_supporter_name'                => 'الاسم',
    'placeholder_supporter_name_example'  => 'مثال: نور فريدة جاسني',
    'label_supporter_email'               => 'البريد الإلكتروني',
    'placeholder_supporter_email_example' => 'مثال: nur.faridah@motac.gov.my',
    'label_supporter_grade'               => 'الدرجة',

    'service_status_options' => [
        ''                              => '- اختر حالة الخدمة -',
        'tetap'                         => 'دائم',
        'lantikan_kontrak_mystep'       => 'عقد / تعيين MyStep',
        'pelajar_latihan_industri'      => 'متدرب صناعي (المقر الرئيسي فقط)',
        'other_agency_existing_mailbox' => 'يعمل في MOTAC (صندوق بريد الوكالة الأساسية)',
    ],

    'appointment_type_options' => [
        ''                            => '- اختر التعيين -',
        'baharu'                      => 'جديد',
        'kenaikan_pangkat_pertukaran' => 'ترقية/نقل',
        'lain_lain'                   => 'أخرى',
    ],

    'position_options_example' => [
        ''   => '- اختر المنصب -',
        '1'  => 'وزير',
        '21' => 'موظف تكنولوجيا المعلومات (ف)',
    ],

    'grade_options_example' => [
        ''   => '- اختر الدرجة -',
        '9'  => '9 (41)', // Example
        '41' => '41',   // Example
    ],

    'department_options_example' => [
        ''   => '- اختر ولاية/قسم/وحدة MOTAC -',
        '18' => 'قسم إدارة المعلومات',
    ],

    'level_options' => [
        ''  => '-اختر المستوى/الطابق -',
        '1' => '1',
        '2' => '2',
    ],
    'label_service_start_date' => 'تاريخ بدء الخدمة', // "Start date" -> "تاريخ بدأ العمل"
    'label_service_end_date'   => 'تاريخ انتهاء الخدمة', // "End Date" -> "تاريخ الإنتهاء"
    'label_address'            => 'العنوان', //
    'label_acquisition_date'   => 'تاريخ الاستحواذ', //
    'label_acquisition_type'   => 'نوع الاستحواذ', //
    'label_category'           => 'الفئة', //
    'label_center'             => 'المركز', //
    'label_contract_id'        => 'معرف العقد', //
    'label_degree'             => 'الدرجة العلمية', //
    'label_employee'           => 'الموظف', //
    'label_expected_price'     => 'السعر المتوقع', //
    'label_father_name'        => 'اسم الأب', //
    'label_first_name'         => 'الاسم الأول', //
    'label_gender'             => 'الجنس', //
    'label_handed_date'        => 'تاريخ التسليم', //
    'label_id'                 => 'المعرف', //
    'label_last_name'          => 'الاسم الأخير', //
    'label_mother_name'        => 'اسم الأم', //
    'label_national_number'    => 'الرقم الوطني', //
    'label_old_id'             => 'المعرف القديم', //
    'label_password'           => 'كلمة السر', //
    'label_quit_date'          => 'تاريخ الانفكاك', //
    'label_rate'               => 'النسبة', //
    'label_real_price'         => 'السعر الفعلي', //
    'label_reason'             => 'السبب', //
    'label_record_info'        => 'معلومات السجل', //
    'label_serial_number'      => 'الرقم التسلسلي', //
    'label_sub_category'       => 'الفئة الفرعية', //
    'label_birth_place'        => 'مكان الولادة', // Derived from "Birth & Place" -> "مكان وتاريخ الولادة"
    'label_select_gender'      => 'اختر الجنس', //
    'placeholder_select_dot'   => 'اختر..', //
    'search_id_category'       => 'بحث (المعرف، فئة...)', //
    'search_id_name'           => 'بحث (المعرف،الاسم...)', //
    'search_id_old_id_serial'  => 'بحث (المعرف، المعرف القديم، الرقم التسلسلي...)', //
    'search_id_sub_category'   => 'بحث (المعرف، فئة فرعية...)', //
    'search_placeholder'       => 'بحث...', //
];
