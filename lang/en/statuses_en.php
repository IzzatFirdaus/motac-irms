<?php
// English translations for statuses used in loan, equipment, transactions, approval, and helpdesk

return [
    // Loan Application Statuses
    'loan_draft' => 'Draft',
    'loan_pending_support' => 'Pending Support (Support Officer)',
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
    'equipment_under_maintenance' => 'Under Maintenance',
    'equipment_disposed' => 'Disposed',
    'equipment_lost' => 'Lost',
    'equipment_damaged_needs_repair' => 'Damaged (Needs Repair)',
    'equipment_in_repair' => 'In Repair',

    // Transaction Types
    'transaction_type_issue' => 'Issue',
    'transaction_type_return' => 'Return',

    // Loan Transaction Statuses
    'transaction_pending' => 'Pending',
    'transaction_issued' => 'Issued',
    'transaction_returned_pending_inspection' => 'Returned (Pending Inspection)',
    'transaction_returned_good' => 'Returned (Good)',
    'transaction_returned_damaged' => 'Returned (Damaged)',
    'transaction_items_reported_lost' => 'Items Reported Lost',
    'transaction_returned_with_loss' => 'Returned (Lost)',
    'transaction_returned_with_damage_and_loss' => 'Returned (Damaged & Lost)',
    'transaction_partially_returned' => 'Partially Returned',
    'transaction_completed' => 'Completed',
    'transaction_cancelled' => 'Cancelled',
    'transaction_overdue' => 'Overdue',
    'transaction_returned' => 'Returned',

    // Approval Statuses
    'approval_pending' => 'Pending',
    'approval_approved' => 'Approved',
    'approval_rejected' => 'Rejected',
    'approval_canceled' => 'Cancelled',

    'supported' => 'SUPPORTED',
    'not_supported' => 'NOT SUPPORTED',

    // API-related status
    'api_status_active' => 'Active',
    'api_status_inactive' => 'Inactive',

    // Attendance / Other status
    'status_present' => 'Present',
    'status_absent_without_excuse' => 'Absent without Excuse',
    'status_partial_attendance' => 'Partial Attendance',

    // Helpdesk Ticket Statuses
    'ticket_open' => 'Open',
    'ticket_in_progress' => 'In Progress',
    'ticket_resolved' => 'Resolved',
    'ticket_closed' => 'Closed',
    'ticket_reopened' => 'Reopened',
    'ticket_on_hold' => 'On Hold',
    'ticket_awaiting_user_response' => 'Awaiting User Response',
    'ticket_escalated' => 'Escalated',
    'ticket_overdue' => 'Overdue',

    // Helpdesk Priority Statuses
    'priority_low' => 'Low',
    'priority_medium' => 'Medium',
    'priority_high' => 'High',
    'priority_critical' => 'Critical',
];
