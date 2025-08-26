<?php

// English translations for Loan Transaction details and issuance/return forms
// This file is kept in sync with transaction_ms.php for bilingual support and maintainability.

return [
    // --- Titles and Navigation ---
    'show_title'          => 'Loan Transaction Details',
    'back_to_list'        => 'All Transactions List',
    'back_to_application' => 'Back to Application Details',

    // --- Basic Transaction Information ---
    'basic_info'           => 'Basic Transaction Information',
    'related_loan_app_id'  => 'Related Loan Application ID:',
    'transaction_type'     => 'Transaction Type:',
    'transaction_status'   => 'Transaction Status:',
    'transaction_datetime' => 'Transaction Logged Date & Time:',

    // --- Equipment Items Section ---
    'involved_items' => 'Equipment Items Involved In This Transaction',
    'quantity'       => 'Quantity',

    // --- Issuance Details ---
    'issue_details'         => 'Issuance Details',
    'issuing_officer'       => 'Issuing Officer (BPM):',
    'receiver'              => 'Equipment Received By:',
    'actual_issue_datetime' => 'Actual Issuance Date & Time:',
    'accessories_issued'    => 'Accessories Issued:',
    'issue_notes'           => 'Issuance Notes:',

    // --- Return Details ---
    'return_details'         => 'Return Details',
    'returner'               => 'Equipment Returned By:',
    'return_receiver'        => 'Return Accepted By (BPM Officer):',
    'actual_return_datetime' => 'Actual Return Date & Time:',
    'accessories_returned'   => 'Accessories Returned:',
    'return_notes'           => 'Return Notes:',
    'findings_on_return'     => 'Findings Status Upon Return',

    // --- NEW: Equipment Issuance Form Section ---
    'issuance_form' => [
        'page_title'                   => 'Process Equipment Issuance #:id',
        'header'                       => 'Record Equipment Issuance',
        'for_application'              => 'For Application',
        'related_application_details'  => 'Related Loan Application Details',
        'applicant'                    => 'Applicant',
        'purpose'                      => 'Application Purpose',
        'loan_date'                    => 'Loan Date',
        'expected_return_date'         => 'Expected Return Date',
        'approved_items'               => 'Approved Equipment Items',
        'equipment_type'               => 'Equipment Type',
        'approved_qty'                 => 'Approved Qty.',
        'balance_to_issue'             => 'Balance to Issue',
        'actual_issuance_record'       => 'Actual Equipment Issuance Record',
        'no_items_to_issue'            => 'No remaining equipment to be issued for this application.',
        'issue_item_header'            => 'Issuance Item #:index',
        'select_specific_equipment'    => 'Select Specific Equipment (Tag ID)',
        'placeholder_select_equipment' => '-- Select Equipment --',
        'no_equipment_available'       => 'No equipment of this type is available.',
        'accessories_checklist'        => 'Accessories Checklist',
        'no_accessories_configured'    => 'No accessories list is configured.',
        'received_by'                  => 'Equipment Received By (Applicant/Rep.)',
        'placeholder_select_receiver'  => '-- Please Select Receiver --',
        'option_applicant'             => 'Applicant',
        'option_responsible_officer'   => 'Responsible Officer',
        'issuance_date'                => 'Issuance Date',
        'issuance_notes'               => 'Issuance Notes (If Any)',
        'placeholder_issuance_notes'   => 'e.g., Equipment in good condition upon issuance.',
        'button_cancel'                => 'Cancel',
        'button_record_issuance'       => 'Record Issuance',
    ],

    // --- NEW: Equipment Return Form Section (for parity and future expansion) ---
    'return_form' => [
        'page_title'                  => 'Process Equipment Return #:id',
        'header'                      => 'Record Equipment Return',
        'for_application'             => 'For Application',
        'related_application_details' => 'Related Loan Application Details',
        'applicant'                   => 'Applicant',
        'returner'                    => 'Equipment Returned By',
        'return_receiver'             => 'Return Accepted By (BPM Officer)',
        'loan_date'                   => 'Loan Date',
        'actual_return_datetime'      => 'Actual Return Date & Time',
        'equipment_items'             => 'Equipment Items Returned',
        'equipment_type'              => 'Equipment Type',
        'tag_id'                      => 'Tag ID',
        'accessories_returned'        => 'Accessories Returned',
        'findings_on_return'          => 'Findings Upon Return',
        'accessories_checklist'       => 'Accessories Checklist',
        'no_accessories_configured'   => 'No accessories list is configured.',
        'return_notes'                => 'Return Notes (If Any)',
        'placeholder_return_notes'    => 'e.g., Minor scratches observed on the equipment casing.',
        'button_cancel'               => 'Cancel',
        'button_record_return'        => 'Record Return',
    ],
];

// Notes:
// - The 'issuance_form' and 'return_form' arrays are designed for the process pages for issuance and return of equipment.
// - Add or update keys to match the Malay version for consistent bilingual support.
// - Comments are included to explain the use of each section for easier maintenance and onboarding.
