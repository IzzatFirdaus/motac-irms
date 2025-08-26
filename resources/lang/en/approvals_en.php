<?php

// English translations for Approval Dashboard module and actions
// Structured by category for ease of maintenance and reference
// (Mirrored to match the updated structure of approvals_ms.php)

return [
    // === Main Approval Dashboard Title ===
    'title' => 'Approval Dashboard',

    // === Approval Task Table ===
    'table' => [
        'title'            => 'Approval Task List',
        'task_id'          => 'Task ID',
        'application_type' => 'Application Type',
        'applicant'        => 'Applicant',
        'stage'            => 'Stage',
        'status'           => 'Task Status',
        'date_received'    => 'Date Received',
        'actions'          => 'Actions',
    ],

    // === Approval Task Filter/Search ===
    'filter' => [
        'by_type'         => 'Filter by Type',
        'by_status'       => 'Filter by Status',
        'advanced_search' => 'Advanced Search',
        'placeholder'     => 'Search ID, Applicant Name...',
    ],

    // === Approval Module Actions ===
    'actions' => [
        'review'               => 'Review',
        'view_details'         => 'View Details',
        'view_task'            => 'View Task',
        'view_full_app'        => 'View Full Application',
        'submit_decision'      => 'Submit Decision',
        'no_permission'        => 'No permission',
        'approve_option'       => 'Approve',       // Added for parity with ms
        'reject_option'        => 'Reject',        // Added for parity with ms
        'return_for_amendment' => 'Return for Amendment',
        'transfer_to_officer'  => 'Transfer to Another Officer',
        'select_officer'       => 'Select Officer',
    ],

    // === Popup/Modals & Dialog - Approval Task Review ===
    'modal' => [
        'title'                      => 'Review Approval Task',
        'app_details'                => 'Application Details',
        'app_type'                   => 'Application Type',
        'applicant'                  => 'Applicant',
        'submission_date'            => 'Submission Date',
        'current_status'             => 'Current Status',
        'applied_items'              => 'Applied Items',
        'quantity'                   => 'Quantity',
        'purpose'                    => 'Purpose',
        'loan_period'                => 'Loan Period',
        'usage_location'             => 'Usage Location',
        'supporting_officer_details' => 'Supporting Officer Details',
        'supporting_officer_name'    => 'Supporting Officer Name',
        'supporting_officer_date'    => 'Support Date',
        'supporting_officer_status'  => 'Support Status',
        'approval_decision'          => 'Approval Decision',
        'decision'                   => 'Decision',
        'comments'                   => 'Comments (Required for Rejection)',
        'approve_option'             => 'Approve',
        'reject_option'              => 'Reject',
        'return_for_amendment'       => 'Return for Amendment',
        'transfer_to_officer'        => 'Transfer to Another Officer',
        'select_officer'             => 'Select Officer',
        'confirmation_message'       => 'Are you sure about this decision?',
        'processing_message'         => 'Processing your decision...',
        'success_message'            => 'Your decision has been successfully recorded.',
        'error_message'              => 'Failed to record your decision.',
    ],

    // === System Messages: Status, Error, Confirmation, etc. ===
    'messages' => [
        'not_found'         => 'Approval task not found.',
        'load_error'        => 'Error loading details.',
        'task_unavailable'  => 'Task is no longer available.',
        'unauthenticated'   => 'User not authenticated.',
        'unauthorized'      => 'Action not authorized.',
        'decision_recorded' => 'Decision successfully recorded for #:id.',
        'generic_error'     => 'An error occurred: ',
    ],

    // === Approval Form Input Validation ===
    'validation' => [
        'decision_required' => 'A decision is required.',
        'decision_invalid'  => 'The selected decision is invalid.',
        'comments_required' => 'Comments are required for rejection.',
        'comments_min'      => 'Comments must be at least :min characters.',
        'items_invalid'     => 'The item data is invalid.',
        'quantity_required' => 'The quantity for :itemType is required.',
        'quantity_integer'  => 'The quantity for :itemType must be a number.',
        'quantity_min'      => 'The quantity for :itemType must be at least 0.',
        'quantity_max'      => 'The quantity for :itemType cannot exceed :max.',
    ],

    // === Application Type Options ===
    'application_types' => [
        'ict_loan_application' => 'ICT Loan Application',
        'helpdesk_ticket'      => 'Helpdesk Ticket',
    ],

    // === Task/Application Statuses ===
    'status' => [
        'pending'                => 'Pending',
        'approved'               => 'Approved',
        'rejected'               => 'Rejected',
        'returned_for_amendment' => 'Returned for Amendment',
        'in_progress'            => 'In Progress',
        'completed'              => 'Completed',
        'cancelled'              => 'Cancelled',
        'draft'                  => 'Draft',
    ],
];

// Notes:
// - This file structure matches approvals_ms.php for synchronized translation and maintainability.
// - If you add new keys or sections to the Malay version, mirror them here and translate accordingly.
