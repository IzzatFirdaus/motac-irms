<?php

// English translations for ICT Loan Application details, history, and statuses
// This file is structured to mirror the Malay version (loan-applications_ms.php) for consistency and maintainability.

return [
    // ==============================================================================
    // --- MAIN TITLE & PRIMARY ACTIONS ---
    // ==============================================================================
    'title'                  => 'ICT Loan Application Details',
    'title_with_id'          => 'ICT Loan Application Details #:id',
    'print_form'             => 'Print Form',
    'update_draft'           => 'Update Draft',
    'submit_application'     => 'Submit Application',
    'resubmit_application'   => 'Resubmit',
    'submit_confirm_message' => 'Are you sure you want to submit this application?',
    'process_return'         => 'Process Equipment Return',
    'back_to_list'           => 'Back to List',

    // ==============================================================================
    // --- FORM SECTIONS IN THE APPLICATION ---
    // ==============================================================================
    'sections' => [
        'applicant'              => 'PART 1 | APPLICANT INFORMATION',
        'application_details'    => 'LOAN APPLICATION DETAILS',
        'responsible_officer'    => 'PART 2 | RESPONSIBLE OFFICER INFORMATION',
        'equipment_details'      => 'PART 3 | REQUESTED EQUIPMENT INFORMATION',
        'applicant_confirmation' => 'PART 4 | APPLICANT CONFIRMATION',
        'approval_history'       => 'APPROVAL & ACTION HISTORY',
        'transaction_history'    => 'LOAN TRANSACTION HISTORY',
    ],

    // ==============================================================================
    // --- LABELS / TABLE & FORM FIELD LABELS ---
    // ==============================================================================
    'labels' => [
        'application_id'           => 'Application ID',
        'applicant_is_responsible' => 'Applicant is the Responsible Officer',
        'applicant_notes'          => 'Applicant Notes',
        'approved_qty'             => 'Approved Qty',
        'equipment_type'           => 'Equipment Type',
        'issued_qty'               => 'Issued Qty',
        'loan_datetime'            => 'Loan Date/Time',
        'no_equipment_requested'   => 'No equipment requested',
        'purpose'                  => 'Purpose',
        'requested_qty'            => 'Requested Qty',
        'return_datetime'          => 'Return Date/Time',
        'return_location'          => 'Return Location',
        'submitted_date'           => 'Submitted Date',
        'usage_location'           => 'Usage Location',
        // The following keys below mirror those in the Malay version and are designed for table/field labels and system messages
        'not_confirmed'            => 'Not yet confirmed by applicant',
        'on_date'                  => 'on',
        'stage'                    => 'Stage',
        'officer'                  => 'Officer',
        'status'                   => 'Status',
        'action_date'              => 'Action Date',
        'comments'                 => 'Comments',
        'pending_decision'         => 'Pending Decision',
        'transaction'              => 'Transaction',
        'transaction_date'         => 'Transaction Date',
        'issuing_officer'          => 'Issuing Officer (BPM):',
        'receiving_officer'        => 'Receiving Officer (Applicant/Rep.):',
        'returning_officer'        => 'Returning Officer (Applicant/Rep.):',
        'return_receiving_officer' => 'Return Receiving Officer (BPM):',
        'rejection_reason'         => 'Reason for Rejection',
    ],

    // ==============================================================================
    // --- APPLICATION STATUSES ---
    // ==============================================================================
    'statuses' => [
        'approved'                              => 'Approved',
        'completed'                             => 'Completed',
        'draft'                                 => 'Draft',
        'pending_support'                       => 'Pending Officer Support',
        'pending_approver_review'               => 'Pending Approval',
        'pending_bpm_review'                    => 'Pending BPM Review',
        'approved_pending_issuance'             => 'Approved (Pending Issuance)',
        'partially_issued'                      => 'Partially Issued',
        'issued'                                => 'Issued',
        'on_loan'                               => 'On Loan',
        'processing'                            => 'Processing',
        'overdue'                               => 'Overdue',
        'pending_return'                        => 'Pending Return',
        'partially_returned_pending_inspection' => 'Partially Returned (Pending Inspection)',
        'returned'                              => 'Returned',
        'rejected'                              => 'Rejected',
        'cancelled'                             => 'Cancelled',
        'returned_for_amendment'                => 'Returned for Amendment',
    ],

    // ==============================================================================
    // --- SYSTEM MESSAGES & NOTIFICATIONS ---
    // ==============================================================================
    'messages' => [
        'update_draft_success'       => 'Draft application updated successfully.',
        'submit_success'             => 'Loan application submitted successfully.',
        'resubmit_success'           => 'Loan application resubmitted successfully.',
        'process_return_success'     => 'Equipment return processed successfully.',
        'not_found'                  => 'Loan application not found.',
        'unauthorized'               => 'You are not authorized to access this application.',
        'already_submitted'          => 'This application has already been submitted.',
        'already_processed'          => 'This application has already been processed.',
        'return_success_with_issues' => 'Equipment returned with some issues.',
    ],

    // ==============================================================================
    // --- VALIDATION & FORM ERROR MESSAGES ---
    // ==============================================================================
    'fields' => [
        'required_quantity'         => 'Quantity is required for :item.',
        'invalid_quantity'          => 'Invalid quantity for :item.',
        'missing_equipment_details' => 'Please enter the details of the equipment requested.',
        'loan_dates_invalid'        => 'Invalid loan start and end dates.',
        'loan_period_exceeded'      => 'Loan period exceeds the allowed limit.',
    ],
];

// Notes:
// - All keys and structure are designed to match the Malay version (loan-applications_ms.php).
// - Sections are separated and commented for clarity and maintainability.
// - Update this file in tandem with the Malay version for consistent bilingual support.
