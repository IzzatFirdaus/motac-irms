<?php

return [
    // Email Application Statuses
    'email_draft' => 'Draft',
    'email_pending_support' => 'Pending Support',
    'email_pending_admin' => 'Pending Admin Action',
    'email_approved' => 'Approved',
    'email_rejected' => 'Rejected',
    'email_processing' => 'Processing',
    'email_provision_failed' => 'Provisioning Failed',
    'email_completed' => 'Completed',

    // Loan Application Statuses
    'loan_draft' => 'Draft',
    'loan_pending_support' => 'Pending Support (Supporting Officer)',
    'loan_pending_hod_review' => 'Pending HOD Review',
    'loan_pending_bpm_review' => 'Pending BPM Review',
    'loan_approved' => 'Approved',
    'loan_rejected' => 'Rejected',
    'loan_partially_issued' => 'Partially Issued',
    'loan_issued' => 'Issued',
    'loan_returned' => 'Returned',
    'loan_overdue' => 'Overdue',
    'loan_cancelled' => 'Cancelled',

    // Equipment Statuses
    'equipment_available' => 'Available',
    'equipment_on_loan' => 'On Loan',
    'equipment_under_maintenance' => 'Under Maintenance', // "Under Maintenance!" (as message)
    'equipment_disposed' => 'Disposed',
    'equipment_lost' => 'Lost',
    'equipment_damaged_needs_repair' => 'Damaged (Needs Repair)', // "Damaged"
    'equipment_in_service' => 'In Service', //

    // Equipment Condition Statuses
    'condition_new' => 'New',
    'condition_good' => 'Good', //
    'condition_fine' => 'Fine', //
    'condition_bad' => 'Bad', //
    'condition_fair' => 'Fair',
    'condition_minor_damage' => 'Minor Damage',
    'condition_major_damage' => 'Major Damage',
    'condition_unserviceable' => 'Unserviceable',

    // Loan Transaction Types
    'transaction_type_issue' => 'Issuance',
    'transaction_type_return' => 'Return',

    // Loan Transaction Statuses
    'transaction_pending' => 'Pending Action', // "Pending"
    'transaction_issued' => 'Issued',
    'transaction_returned_pending_inspection' => 'Returned (Pending Inspection)',
    'transaction_returned_good' => 'Returned (Good)',
    'transaction_returned_damaged' => 'Returned (Damaged)',
    'transaction_items_reported_lost' => 'Items Reported Lost',
    'transaction_completed' => 'Completed',
    'transaction_cancelled' => 'Cancelled',

    // Approval Statuses
    'approval_pending' => 'Pending Approval',
    'approval_approved' => 'Approved',
    'approval_rejected' => 'Rejected',

    'supported' => 'SUPPORTED',
    'not_supported' => 'NOT SUPPORTED',

    'api_status_active' => 'Active', // Derived from API Status & Active
    'api_status_inactive' => 'Inactive', // Derived from API Status & Inactive

    'status_present' => 'Present', //
    'status_absent_without_excuse' => 'Absent without excuse', //
    'status_partial_attendance' => 'Partial attendance', //
    'status_pending' => 'Pending', //
    'status_successful' => 'Successful', //
    'status_out_of_work' => 'Out of work', //
];
