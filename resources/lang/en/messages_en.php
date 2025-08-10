<?php
// English translations for generic and form/system messages
// This file is kept in sync with messages_ms.php for bilingual consistency and maintainability.

return [
    // === General Form Instructions & Notes ===
    'instruction_mandatory_fields'              => '* Indicates mandatory fields.',
    'instruction_fill_form_completely'          => 'Please fill out this form completely.',
    'instruction_ict_loan_terms_availability'   => "Application is subject to equipment availability on a 'First Come, First Serve' basis.",
    'instruction_ict_loan_processing_time'      => 'Applications will be reviewed and processed within three (3) working days from the date the complete application is received.',
    'instruction_ict_loan_bpm_responsibility'   => 'BPM is not responsible for equipment availability if the applicant fails to adhere to this period.',
    'instruction_ict_loan_submit_form_on_pickup'=> 'The applicant must submit the completed and signed ICT Equipment Loan Application Form to BPM when collecting the equipment.',
    'instruction_ict_loan_check_equipment'      => 'Applicants are reminded to check and inspect the completeness of the equipment when collecting and before returning the borrowed equipment.',
    'instruction_ict_loan_liability'            => 'Loss and damage to equipment during return are the responsibility of the applicant, and actions may be taken according to current regulations.',
    'instruction_ict_loan_form_submission_location' => 'The completed form must be submitted to:',
    'instruction_ict_loan_contact_for_enquiries'=> 'For any enquiries, please contact:',

    // === General Success, Error, Confirmation Messages ===
    'record_created_success'     => 'Record created successfully.',
    'record_updated_success'     => 'Record updated successfully.',
    'record_deleted_success'     => 'Record deleted successfully.',
    'action_successful'          => 'Action successful!',
    'action_failed'              => 'Action failed.',
    'confirm_delete'             => 'Are you sure you want to delete this record? This action cannot be undone.',
    'delete_not_allowed'         => 'Record cannot be deleted because it is currently in use.',
    'no_changes_made'            => 'No changes detected.',
    'not_found'                  => 'Resource not found.',
    'unauthorized_action'        => 'You are not authorized to perform this action.',
    'invalid_input'              => 'Invalid input.',
    'system_error'               => 'A system error occurred. Please try again later.',
    'data_integrity_error'       => 'Data integrity error.',
    'file_upload_success'        => 'File uploaded successfully.',
    'file_upload_failed'         => 'File upload failed.',
    'file_delete_success'        => 'File deleted successfully.',
    'file_delete_failed'         => 'Failed to delete file.',
    'operation_timeout'          => 'Operation timed out.',

    // === Validation & Input ===
    'validation_error_heading'   => 'Please correct the following errors:',
    'validation_generic_error'   => 'Please review and correct the entered information.',
    'validation_errors'          => 'Please correct the following errors:',

    // === Empty States & Search ===
    'no_records_found'           => 'No records found.',
    'no_users_found'             => 'No system users found.',
    'no_grades_found'            => 'No grade records found.',
    'no_positions_found'         => 'No position records found.',
    'no_departments_found'       => 'No departments/units found matching your search.',
    'try_different_search'       => 'Try a different search keyword.',
    'no_results_found'           => 'No results found for your search.',

    // === ICT Loan Module ===
    'loan_application_submitted_success'        => 'Your ICT Equipment Loan Application has been successfully submitted.',
    'loan_application_updated_success'          => 'ICT Equipment Loan Application updated successfully.',
    'loan_application_cancelled_success'        => 'ICT Equipment Loan Application cancelled successfully.',
    'loan_application_return_processed_success' => 'ICT equipment return processed successfully.',
    'loan_application_issued_success'           => 'ICT equipment issued successfully.',
    'loan_application_not_found'                => 'Loan application not found.',
    'loan_application_not_editable'             => 'Application cannot be edited in the current status.',
    'loan_application_not_cancellable'          => 'Application cannot be cancelled in the current status.',
    'loan_equipment_not_available'              => 'The requested quantity of equipment is not available.',
    'loan_equipment_already_issued'             => 'This equipment has already been issued.',
    'loan_equipment_not_on_loan'                => 'This equipment is not currently on loan.',
    'loan_equipment_return_date_earlier'        => 'Return date cannot be earlier than the loan start date.',

    // === Terms & Conditions (ICT Loan) ===
    'terms_title'   => 'Terms and Conditions for ICT Equipment Loan',
    'terms_item1'   => 'ICT equipment loans are for official use only.',
    'terms_item2'   => 'The maximum loan period is three (3) months from the issue date, unless with special approval.',
    'terms_item3'   => 'The applicant is fully responsible for the borrowed equipment and must ensure proper care.',
    'terms_item4'   => 'The equipment must be returned in good condition on or before the specified date.',
    'terms_item5'   => 'Loss or damage to equipment will incur compensation based on the current equipment value.',
    'terms_item6'   => 'BPM is not responsible for any equipment availability issues if the application is not processed within three (3) working days due to incomplete applications.',
    'terms_item7'   => 'Applicants must ensure all information provided in the application form is accurate and true.',
    'terms_item8'   => 'Any misuse or violation of the terms will result in disciplinary or legal action.',

    // === Helpdesk Module ===
    'ticket_created_success'        => 'Your Helpdesk Ticket has been created successfully.',
    'ticket_updated_success'        => 'Ticket updated successfully.',
    'ticket_assigned_success'       => 'Ticket successfully assigned to :officer.',
    'ticket_status_updated_success' => 'Ticket status updated to :status.',
    'comment_added_success'         => 'Comment added successfully.',
    'ticket_closed_success'         => 'Ticket closed successfully.',
    'ticket_already_closed'         => 'This ticket has already been closed.',
    'ticket_reopened_success'       => 'Ticket reopened successfully.',
    'ticket_not_found'              => 'Ticket not found.',
    'ticket_not_authorized'         => 'You are not authorized to access this ticket.',
    'attachment_too_large'          => 'Attachment file size cannot exceed :max_size MB.',
    'attachment_invalid_type'       => 'Attachment file type is not allowed. Allowed types: :allowed_types.',
    'sla_breached'                  => 'This ticket has breached its SLA.',
    'sla_warning'                   => 'This ticket is at risk of breaching its SLA.',

    // === API Token Actions ===
    'api_token_created'             => 'API token created successfully.',
    'api_token_deleted'             => 'API token deleted successfully.',
    'api_token_permissions_updated' => 'Token permissions updated successfully.',

    // === Approval Messages ===
    'approval_decision_required'    => 'Please select a decision.',
    'approval_comment_required'     => 'Comment is required for this action.',
    'approval_forward_required'     => 'Please select an officer to forward to.',
    'approval_quantity_invalid'     => 'Approved quantity is invalid.',

    // === Session, Status, and Alert System ===
    'login_success'                   => 'Login successful.',
    'logout_success'                  => 'Logout successful.',
    'register_success'                => 'Registration successful.',
    'password_reset_link_sent'        => 'Password reset link sent to your email.',
    'password_reset_success'          => 'Your password has been reset successfully.',
    'profile_update_success'          => 'Profile updated successfully.',
    'record_created'                  => 'Record created successfully.',
    'record_updated'                  => 'Record updated successfully.',
    'record_deleted'                  => 'Record deleted successfully.',
    'no_permission'                   => 'You do not have permission to perform this action.',
    'operation_failed'                => 'Operation failed.',
    'invalid_credentials'             => 'Invalid login credentials.',
    'account_inactive'                => 'Your account is inactive.',
    'password_confirmation_mismatch'  => 'Password confirmation does not match.',
    'incorrect_password'              => 'Incorrect password.',
    'session_expired'                 => 'Your session has expired. Please log in again.',
    'account_locked'                  => 'Your account has been locked. Please contact the system administrator.',
    'account_not_found'               => 'Account not found.',
    'email_verified'                  => 'Email address has been verified.',
    'email_verification_sent'         => 'Verification email has been sent.',
    '2fa_required'                    => 'Two-factor authentication is required.',
    '2fa_invalid_code'                => 'Invalid authentication code.',
    '2fa_invalid_recovery_code'       => 'Invalid recovery code.',
    'action_forbidden'                => 'Access is forbidden.',
    'back_to_list'                    => 'Back to List',
    'back_to_home'                    => 'Back to Homepage',

    // === Notification, Alert & Banner System ===
    'saved_successfully' => 'Saved successfully.', // For action-message default slot
    'success'            => 'Success!',           // Success alert/banner title
    'error'              => 'Error!',             // Error alert/banner title
    'warning'            => 'Warning!',           // Warning alert/banner title
    'info'               => 'Information',        // Info alert/banner title
    'close'              => 'Close',              // Close button for alert/banner

    // === Email Notification Specific (for email templates) ===
    'notification_see_ticket'         => 'View Ticket Details',
    'notification_ticket_details'     => 'Ticket Details',
    'notification_ticket_created'     => 'Your New IT Support Ticket Has Been Created',
    'notification_ticket_assigned'    => 'IT Support Ticket Assigned to You',
    'notification_ticket_status_updated' => 'IT Support Ticket Status Update',
    'notification_new_comment_added'  => 'New Comment Added to IT Support Ticket',
    'notification_team_invitation'    => 'Team Invitation',
    'notification_greeting'           => 'Greetings',
    'notification_thank_you'          => 'Thank you.',
    'notification_do_not_reply'       => 'This is a system-generated email. Please do not reply to this email.',
    'notification_all_rights_reserved'=> 'All Rights Reserved.',
    'notification_footer_org'         => 'Ministry of Tourism, Arts and Culture Malaysia',
    'notification_footer_team'        => 'System Administrator Team',
    'notification_footer_bpm'         => 'Information Management Division (BPM)',
    'notification_sender_bpm'         => 'Information Management Division',
    'notification_sender_org'         => 'Ministry of Tourism, Arts and Culture Malaysia',
    'notification_salutation_executor'=> 'Sincerely,',
    'notification_new_application'    => 'Action Required: New Application Submitted',
    'notification_application_approved' => 'Application Approved',
    'notification_application_rejected' => 'Application Rejected',
    'notification_equipment_issued'   => 'ICT Equipment Loan Issued',
    'notification_equipment_returned' => 'Loaned Equipment Returned',
    'notification_equipment_overdue'  => 'Reminder: ICT Equipment Loan Overdue',
    'notification_equipment_return_reminder' => 'ICT Equipment Return Reminder',
    'notification_ready_for_issuance' => 'Action Required: Loan Application Ready for Issuance',
    'notification_approval_needed'    => 'Approval Action Required',
    'notification_approval_action_needed' => 'Approval Action Required',
    'notification_application_details' => 'Application Details',
    'notification_equipment_return_details' => 'Equipment Return Details',
    'notification_equipment_details'   => 'Issued Equipment Details',
    'notification_equipment_pending_return' => 'Equipment Pending Return',
    'notification_equipment_returned_accessories' => 'Returned Accessories',
    'notification_equipment_return_notes' => 'Return Notes',
    'notification_rejection_reason'    => 'Reason for Rejection',
    'notification_comment_by'          => 'Comment Added By',
    'notification_comment'             => 'Comment',
    'notification_status_change'       => 'Status Change',
];

// Notes:
// - All keys and structure are mirrored to the Malay (messages_ms.php) version for translation consistency.
// - Update this file in tandem with the Malay version for accurate bilingual support.
