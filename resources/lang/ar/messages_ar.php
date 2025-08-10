<?php

return [
    // Instructions
    'instruction_mandatory_fields' => '* تشير إلى الحقول الإلزامية.',
    'instruction_fill_form_completely' => 'الرجاء تعبئة المعلومات التالية', //
    'instruction_ict_loan_terms_availability' => "يخضع الطلب لتوفر المعدات على أساس 'من يأتي أولاً يُخدم أولاً'.",
    'instruction_ict_loan_processing_time' => 'ستتم مراجعة الطلبات ومعالجتها في غضون ثلاثة (3) أيام عمل من تاريخ استلام الطلب كاملاً.',
    'instruction_ict_loan_bpm_responsibility' => 'قسم إدارة المعلومات غير مسؤول عن توفر المعدات إذا فشل مقدم الطلب في الالتزام بهذه الفترة.',
    'instruction_ict_loan_submit_form_on_pickup' => 'يجب على مقدم الطلب تقديم نموذج طلب استعارة معدات تكنولوجيا المعلومات المكتمل والموقع إلى قسم إدارة المعلومات عند استلام المعدات.',
    'instruction_ict_loan_check_equipment' => 'يُذكر المتقدمون بفحص المعدات والتأكد من اكتمالها عند الاستلام وقبل إرجاع المعدات المستعارة.',
    'instruction_ict_loan_liability' => 'فقدان أو تلف المعدات أثناء الإرجاع هو مسؤولية مقدم الطلب، وقد يتم اتخاذ إجراءات وفقًا للوائح الحالية.',
    'instruction_ict_loan_form_submission_location' => 'يجب إرسال النموذج المكتمل إلى:',
    'instruction_ict_loan_contact_for_enquiries' => 'لأية استفسارات، يرجى الاتصال بـ:',

    'instruction_email_declaration_checkboxes' => 'يرجى تحديد مربعات الإقرار الثلاثة للمتابعة في تقديم الطلب.',
    'instruction_email_account_creation_eligibility' => 'سيتم إنشاء حسابات بريد إلكتروني MOTAC فقط للموظفين الدائمين والمعينين بعقود وموظفي MySTEP.',
    'instruction_email_user_id_intern_eligibility' => 'سيتم تزويد طلاب التدريب الصناعي (مقر MOTAC الرئيسي) بمعرف مستخدم فقط.',
    'instruction_email_backup_configuration' => 'بالنسبة للموظفين العاملين في MOTAC ولكنهم يستخدمون صناديق بريد حالية من وكالتهم الأساسية، سيقوم قسم إدارة المعلومات بإعداد بريد MOTAC احتياطي لتمكين الاتصال باستخدام نطاق motac.gov.my.',
    'instruction_email_no_new_mailbox_for_backup' => 'لن يتم إنشاء حساب صندوق بريد MOTAC جديد لهذا الغرض.',
    'instruction_email_supporting_officer_grade' => 'تنبيه: يجب أن تكون الطلبات مدعومة من قبل موظف بدرجة 9 على الأقل فما فوق فقط.',

    // Definitions from ICT Loan Form
    'definition_applicant_ict' => 'مقدم الطلب يشير إلى الموظف الذي يقوم بتعبئة نموذج طلب استعارة معدات تكنولوجيا المعلومات.',
    'definition_responsible_officer_ict' => 'الموظف المسؤول يشير إلى الموظف المسؤول عن استخدام وأمن وتلف المعدات المستعارة.',
    'definition_issuing_officer_ict' => 'الموظف المُصدر يشير إلى موظف قسم إدارة المعلومات الذي يقوم بإصدار المعدات للموظف المُستلم.',
    'definition_receiving_officer_ict' => 'الموظف المُستلم يشير إلى الموظف الذي يستلم المعدات من الموظف المُصدر.',
    'definition_returning_officer_ict' => 'الموظف المُرجع يشير إلى الموظف الذي يقوم بإرجاع المعدات المستعارة.',
    'definition_return_acceptance_officer_ict' => 'موظف استلام الإرجاع يشير إلى موظف قسم إدارة المعلومات الذي يستلم المعدات المُرجعة من قبل الموظف المُرجع.',

    // Placeholders
    'placeholder_notes_optional' => 'ملاحظات (إن وجدت)',
    'placeholder_type_message_here' => 'أدخل الرسالة هنا', //

    // Confirmation messages
    'confirmation_application_submitted' => 'تم إرسال الطلب بنجاح.',
    'confirmation_draft_saved' => 'تم حفظ المسودة بنجاح.',
    'confirmation_are_you_sure' => 'متأكد؟', //
    'confirmation_delete_item' => 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
    'confirmation_cancel_application' => 'هل أنت متأكد أنك تريد إلغاء هذا الطلب؟',

    // Notifications
    'notification_application_needs_action_title' => 'الطلب يتطلب إجراء', // "Requires Attention!" -> "يرجى الانتباه!"
    'notification_application_approved_title' => 'تمت الموافقة على الطلب',
    'notification_application_rejected_title' => 'تم رفض الطلب',
    'notification_equipment_issued_title' => 'تم إصدار المعدات',
    'notification_equipment_returned_title' => 'تم إرجاع المعدات',
    'notification_return_reminder_title' => 'تذكير بإرجاع المعدات',
    'notification_view_all' => 'مشاهدة جميع التنبيهات', //

    // General UI Messages from ar.json
    'greeting_hi' => 'مرحباً،', //
    'welcome_to' => 'أهلاً وسهلاً بك في', //
    'login_prompt' => 'رجاء قم بتسجيل الدخول لحسابك', //
    'remember_me' => 'تذكرني', //
    'page_refresh_notice' => 'انتبه! من المقرر تحديث الصفحة في:', //
    'start_day_greeting' => 'ابدأ يومك بابتسامة', //
    'under_development_message' => 'نحن نعمل على تطوير أمر رائع, رجاء تحلى بالصبر ريثما ينتهي.', //
    'under_maintenance_message' => 'تحت الصيانة!', //
    'no_data_found_message' => 'لم يتم العثور على أي بيانات، يرجى إضافة بعض البيانات، ودع المرح يبدأ!', //
    'no_employees_found' => 'لم يتم العثور على أي موظف', //
    'no_leave_found' => 'لم يتم العثور على أي إجازة', //
    'success_record_created' => 'نجحت العملية، تم إنشاء السجل بنجاح!', //
    'success_record_updated' => 'نجحت العملية، تم تعديل السجل بنجاح', //
    'success_file_exported' => 'نجحت العملية! تم تصدير الملف بنجاح.', //
    'success_file_imported' => 'نجحت العملية! تم استيراد الملف بنجاح.', //
    'success_discounts_calculated' => 'تم حساب حسومات الموظفين بنجاح', //
    'success_fingerprint_imported' => 'تم استيراد ملف البصمات بنجاح', //
    'success_current_position_assigned' => 'تم تعيين المنصب الحالي بنجاح.', //
    'error_update_unavailable' => 'خطأ، التحديث غير متاح', //
    'info_all_sent' => 'لقد تم ارسال كل الرسائل بالفعل!', //
    'info_generating' => 'توليد...', //
    'info_sending' => 'جاري الإرسال', //
    'info_select_file_to_upload' => 'الرجاء تحديد الملف للتحميل', //
    'info_check_dates_from_gt_to' => 'الرجاء التحقق من التواريخ المدخلة، لا يمكن أن يكون "من التاريخ" أكبر من "حتى التاريخ"', //
    'info_check_times_start_gt_end' => 'الرجاء التحقق من الأوقات المدخلة، لا يمكن ان يكون "تبدأ من" أكبر من "تنتهي بـ"', //
    'info_employee_not_started_yet' => 'الموظف لم يبدأ العمل بعد', //
    'info_employee_resigned_on' => 'استقال الموظف في', //
    'info_cant_add_daily_leave_with_time' => 'لا يمكن إضافة إجازة يومية مع وقت!', //
    'info_cant_add_hourly_leave_without_time' => 'لا يمكن إضافة أجازة ساعية بدون وقت!', //
    'info_hourly_leave_same_day' => 'الإجازة الساعية يجب أن تكون في ذات اليوم!', //
    'info_no_new_updates' => 'لا يوجد تحديثات جديدة تدعو للقلق', //
    'info_time_to_relax' => 'وقت الإسترخاء!', //
    'info_work_matters_holidays_more' => 'العمل مهم، ولكن العطل أهم', //
    'info_made_with' => 'صنع بـ', //
    'info_by_namaa' => 'من قبل نماء', //
    'info_by_taalouf' => 'من قبل التآلف', //
    'info_by_unhcr' => 'من قبل مفوضية شؤون اللاجئين', //
    'info_get_into_the_details' => 'للغوص العميق في التفاصيل المثيرة!', //
    'info_for_better_work_environment' => 'لبيئة عمل أفضل.', //
    'info_step_1' => 'الخطوة الاولى', //
    'info_step_2' => 'الخطوة الثانية', //
    'info_step_3' => 'الخطوة الثالثة', //
    'info_step_4' => 'الخطوة الرابعة', //
    'info_it_department' => 'القسم التقني', //
    'info_human_resource' => 'الموارد البشرية', //
    'info_clear_chat' => 'مسح الرسالة', //
    'info_changelog' => 'سجل التطوير', //
    'info_if_not_added_yet' => ' ان لم تقم بإضافتهم بعد!', //
    'info_dont_forget_to_add_the' => 'لاتنسى إضافة ', //
    'info_dont_forget_to_import_the' => 'لاتنسى استيراد ملف', //
    'info_employees_leaves_crucial_import' => 'إجازات الموظف هي الخطوة الحاسمة قم باستيراد ملف الإجازات! ', //
    'info_magic_lies_in_fingerprints' => 'السحر يكمن في البصمات! قم بالاستيراد واسترح', //
    'info_make_sure_leaves_checked' => 'تأكد من أن جميع إجازات الموظفين قد تم التحقق منها بنجاح', //
    'info_pick_batch_generate_sms' => 'يرجى إختيار الكتلة المناسبة لتوليدالرسائل لهم', //
    'info_select_timeframe_discounts' => 'الرجاء اختيار الإطار الزمني لعرض الحسومات:', //
    'info_choose_dates_coffee_time' => 'اختر التاريخ المطلوب وانتظر ريثما يتم تحميل النتائج', //
    'info_discounts_calculations_done' => 'بوم! تم الانتهاء من حسابات الخصومات - الأمر سهل للغاية!', //
    'info_messages_on_their_way' => 'فلننطلق، الرسائل في طريقهم!', //
    'info_loading' => 'جاري التحميل...', //
];
