<?php

return [
    // Email Application Statuses
    'email_draft' => 'مسودة',
    'email_pending_support' => 'في انتظار الدعم',
    'email_pending_admin' => 'في انتظار إجراء المسؤول',
    'email_approved' => 'تمت الموافقة',
    'email_rejected' => 'مرفوض',
    'email_processing' => 'قيد المعالجة',
    'email_provision_failed' => 'فشل التزويد',
    'email_completed' => 'مكتمل',

    // Loan Application Statuses
    'loan_draft' => 'مسودة',
    'loan_pending_support' => 'في انتظار الدعم (الموظف الداعم)',
    'loan_pending_hod_review' => 'في انتظار مراجعة رئيس القسم',
    'loan_pending_bpm_review' => 'في انتظار مراجعة قسم إدارة المعلومات',
    'loan_approved' => 'تمت الموافقة',
    'loan_rejected' => 'مرفوض',
    'loan_partially_issued' => 'تم الإصدار جزئيًا',
    'loan_issued' => 'تم الإصدار',
    'loan_returned' => 'تم الإرجاع',
    'loan_overdue' => 'متأخر',
    'loan_cancelled' => 'ملغى',

    // Equipment Statuses
    'equipment_available' => 'متوفر',
    'equipment_on_loan' => 'مستعار',
    'equipment_under_maintenance' => 'تحت الصيانة', // "Under Maintenance!" as message
    'equipment_disposed' => 'تم التخلص منه',
    'equipment_lost' => 'مفقود',
    'equipment_damaged_needs_repair' => 'تالف (يحتاج إصلاح)', // "Damaged" -> "تالفة"
    'equipment_in_service' => 'حالة الخدمة', // "In Service"

    // Equipment Condition Statuses
    'condition_new' => 'جديد',
    'condition_good' => 'ممتازة', //
    'condition_fine' => 'جيدة', //
    'condition_bad' => 'سيئة', //
    'condition_fair' => 'مقبول',
    'condition_minor_damage' => 'تلف بسيط',
    'condition_major_damage' => 'تلف كبير',
    'condition_unserviceable' => 'غير قابل للخدمة',

    // Loan Transaction Types
    'transaction_type_issue' => 'إصدار',
    'transaction_type_return' => 'إرجاع',

    // Loan Transaction Statuses
    'transaction_pending' => 'في انتظار الإجراء', // "Pending" -> "معلقة"
    'transaction_issued' => 'تم الإصدار',
    'transaction_returned_pending_inspection' => 'تم الإرجاع (في انتظار الفحص)',
    'transaction_returned_good' => 'تم الإرجاع (بحالة جيدة)',
    'transaction_returned_damaged' => 'تم الإرجاع (تالف)',
    'transaction_items_reported_lost' => 'تم الإبلاغ عن فقدان العناصر',
    'transaction_completed' => 'مكتمل',
    'transaction_cancelled' => 'ملغى',

    // Approval Statuses
    'approval_pending' => 'في انتظار الموافقة',
    'approval_approved' => 'تمت الموافقة',
    'approval_rejected' => 'مرفوض',

    'supported' => 'مدعوم',
    'not_supported' => 'غير مدعوم',

    'api_status_active' => 'نشط', // Derived from API Status & Active
    'api_status_inactive' => 'غير نشط', // Derived from API Status & Inactive

    'status_present' => 'الآن', // "Present"
    'status_absent_without_excuse' => 'غياب بدون عذر', //
    'status_partial_attendance' => 'دوام جزئي', //
    'status_pending' => 'معلقة', //
    'status_successful' => 'ناجح', //
    'status_out_of_work' => 'مستقيل', //
];
