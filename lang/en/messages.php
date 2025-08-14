<?php

return [
    // Instructions
    'instruction_mandatory_fields' => '* Indicates mandatory fields.', // From PDF via system design [cite: 179]
    'instruction_fill_form_completely' => 'Please fill out this form completely.', //
    'instruction_ict_loan_terms_availability' => "Application is subject to equipment availability on a 'First Come, First Serve' basis.", // From PDF via system design [cite: 177]
    'instruction_ict_loan_processing_time' => 'Applications will be reviewed and processed within three (3) working days from the date the complete application is received.', // From PDF via system design [cite: 187]
    'instruction_ict_loan_bpm_responsibility' => 'BPM is not responsible for equipment availability if the applicant fails to adhere to this period.', // From PDF via system design [cite: 187]
    'instruction_ict_loan_submit_form_on_pickup' => 'The applicant must submit the completed and signed ICT Equipment Loan Application Form to BPM when collecting the equipment.', // From PDF
    'instruction_ict_loan_check_equipment' => 'Applicants are reminded to check and inspect the completeness of the equipment when collecting and before returning the borrowed equipment.', //
    'instruction_ict_loan_liability' => 'Loss and damage to equipment during return are the responsibility of the applicant, and actions may be taken according to current regulations.', // From PDF via system design [cite: 188]
    'instruction_ict_loan_form_submission_location' => 'The completed form should be sent to:', // From PDF
    'instruction_ict_loan_contact_for_enquiries' => 'For any inquiries, please contact:', //

    'instruction_email_declaration_checkboxes' => 'Please Tick All Three Declaration Boxes to Proceed with the Application.', //
    'instruction_email_account_creation_eligibility' => 'MOTAC email accounts will only be created for Permanent Staff, Contract Hires, and MySTEP Personnel.', //
    'instruction_email_user_id_intern_eligibility' => 'Industrial Training Students (MOTAC Headquarters) will only be provided with a User ID.', //
    'instruction_email_backup_configuration' => 'For staff serving at MOTAC but using existing mailboxes from their primary agency, the Information Management Division will set up MOTAC backup email to enable communication using the motac.gov.my domain.', //
    'instruction_email_no_new_mailbox_for_backup' => 'No new MOTAC mailbox account will be created for this purpose.', //
    'instruction_email_supporting_officer_grade' => 'Attention: Applications must be SUPPORTED by an Officer of at least Grade 9 and above ONLY.', //
    'instruction_two_factor_auth_info' => 'Add additional security to your account using two factor authentication.', //
    'instruction_two_factor_enabled_prompt' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.', //
    'instruction_two_factor_setup_qr' => "Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.", //
    'instruction_two_factor_store_recovery_codes' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.', //
    'instruction_delete_account_warning' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.', // From
    'instruction_delete_account_confirmation' => 'Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.', //
    'instruction_update_password_security' => 'Ensure your account is using a long, random password to stay secure.', //
    'instruction_browser_session_management' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.', //

    // Definitions from ICT Loan Form
    'definition_applicant_ict' => 'Applicant refers to the staff member completing the ICT equipment loan application form.', //
    'definition_responsible_officer_ict' => 'Responsible Officer refers to the staff member responsible for the use, security, and damage of the loaned equipment.', // From system design
    'definition_issuing_officer_ict' => 'Issuing Officer refers to the BPM staff member who issues the equipment to the Receiving Officer.', //
    'definition_receiving_officer_ict' => 'Receiving Officer refers to the staff member who receives the equipment from the Issuing Officer.', //
    'definition_returning_officer_ict' => 'Returning Officer refers to the staff member who returns the borrowed equipment.', //
    'definition_return_acceptance_officer_ict' => 'Return Acceptance Officer refers to the BPM staff member who receives the equipment returned by the Returning Officer.', //

    // Placeholders
    'placeholder_notes_optional' => 'Notes (if any)', //
    'placeholder_type_message_here' => 'Type your message here', //

    // Confirmation messages
    'confirmation_application_submitted' => 'Application submitted successfully.', //
    'confirmation_draft_saved' => 'Draft saved successfully.', //
    'confirmation_are_you_sure' => 'Sure?', //
    'confirmation_delete_item' => 'Are you sure you want to delete this item?', //
    'confirmation_cancel_application' => 'Are you sure you want to cancel this application?', //

    // Notifications
    'notification_application_needs_action_title' => 'Application Requires Action', //
    'notification_application_approved_title' => 'Application Approved', //
    'notification_application_rejected_title' => 'Application Rejected', //
    'notification_equipment_issued_title' => 'Equipment Issued', //
    'notification_equipment_returned_title' => 'Equipment Returned', //
    'notification_return_reminder_title' => 'Equipment Return Reminder', //
    'notification_view_all' => 'View all notifications', //
    'notification_new_singular' => 'You have a new notification.', // Derived from "Anda mempunyai notifikasi baharu."
    'notification_new_plural' => 'You have :count new notifications.', // Derived
    'notification_no_new' => 'No new notifications at this time.', //
    'notification_refresh' => 'Refresh Notifications', // Derived from "Muat Semula Notifikasi"

    // General UI Messages
    'greeting_hi' => 'Hi,', //
    'welcome_to' => 'Welcome to', //
    'login_prompt' => 'Please sign-in to your account', //
    'remember_me' => 'Remember Me', //
    'page_refresh_notice' => 'Attention! The page is scheduled for a refined refresh in:', //
    'start_day_greeting' => 'Start your day with a smile', //
    'under_development_message' => "We're creating something awesome. Please keep calm until it's ready!", //
    'under_maintenance_message' => 'Under Maintenance!', //
    'service_unavailable_maintenance' => 'Service Unavailable. The system is currently under maintenance.', // Derived from "Perkhidmatan Tidak Tersedia. Sistem sedang dalam penyelenggaraan."
    'no_data_found_message' => 'No data found, please sprinkle some data in my virtual bowl, and let the fun begin!', //
    'no_details_available' => 'No details.', //
    'no_employees_found' => 'No Employees Found!', //
    'no_leave_found' => 'No Leave Found!', //
    'no_recent_email_id_requests' => 'No recent Email/ID requests found.', //
    'no_recent_ict_loan_requests' => 'No recent ICT loan requests found.', //
    'no_approval_tasks_filtered' => 'No approval tasks match your current filters.', // From previous "Papan Pemuka Kelulusan" -> "Approval Dashboard"
    'no_quick_actions' => 'No quick actions available', //
    'success_record_created' => 'Success, record created successfully!', //
    'success_record_updated' => 'Success, record updated successfully!', //
    'success_profile_updated' => 'Profile information updated successfully.',
    'success_password_updated' => 'Password updated successfully.',
    'success_file_exported' => 'Well done! The file has been exported successfully.', //
    'success_file_imported' => 'Well done! The file has been imported successfully.', //
    'success_discounts_calculated' => 'Successfully calculate the employees discounts', //
    'success_fingerprint_imported' => 'Successfully imported the fingerprint file', //
    'success_current_position_assigned' => 'The current position assigned successfully.', //
    'error_update_unavailable' => 'Error, Update unavailable', //
    'error_form_validation' => 'Warning! There are errors in your input.', //
    'info_all_sent' => 'Everything has sent already!', //
    'info_generating' => 'Generating......', //
    'info_sending' => 'Sending...', //
    'info_processing_decision' => 'Processing decision...',
    'info_loading_approvals' => 'Loading approvals...',
    'info_select_file_to_upload' => 'Please select the file to upload', //
    'info_check_dates_from_gt_to' => 'Check the dates entered. "From Date" cannot be greater than "To Date"', //
    'info_check_times_start_gt_end' => 'Check the times entered. "Start At" cannot be greater than "End To"', //
    'info_employee_not_started_yet' => "Employee hasn't started working yet", //
    'info_employee_resigned_on' => 'Employee resigned on', //
    'info_cant_add_daily_leave_with_time' => "Can't add daily leave with time!", //
    'info_cant_add_hourly_leave_without_time' => "Can't add hourly leave without time!", //
    'info_hourly_leave_same_day' => 'Hourly leave must be on the same day!', //
    'info_no_new_updates' => 'No new updates to worry about', //
    'info_time_to_relax' => 'Time to relax!', //
    'info_work_matters_holidays_more' => 'Work matters, but Holidays matter more!', //
    'info_made_with' => 'made with', //
    'info_by_namaa' => 'By Namaa', //
    'info_by_taalouf' => 'By Taalouf', //
    'info_by_unhcr' => 'By UNHCR', //
    'info_get_into_the_details' => 'for a deep dive into the juicy details!', //
    'info_for_better_work_environment' => 'for a better work environment.', //
    'info_step_1' => 'Step 1', //
    'info_step_2' => 'Step 2', //
    'info_step_3' => 'Step 3', //
    'info_step_4' => 'Step 4', //
    'info_it_department' => 'IT Department', //
    'info_human_resource' => 'Human Resource', //
    'info_clear_chat' => 'Clear Chat', //
    'info_changelog' => 'Changelog', //
    'info_if_not_added_yet' => ". If you haven't added them yet!", //
    'info_dont_forget_to_add_the' => "Don't forget to add the ", //
    'info_dont_forget_to_import_the' => "Don't forget to import the ", //
    'info_employees_leaves_crucial_import' => 'Employee Leaves is the crucial move! Import the Leaves file!! ', //
    'info_magic_lies_in_fingerprints' => 'The magic lies in Fingerprints! Import them and set off.', //
    'info_make_sure_leaves_checked' => 'Make sure that all Employees Leaves are Checked Successfully', //
    'info_pick_batch_generate_sms' => 'Please pick the batch to generate SMS for:', //
    'info_select_timeframe_discounts' => 'Please select the timeframe to display discounts: ', //
    'info_choose_dates_coffee_time' => 'Choose the dates and take a sip of coffee while the rocket makes its touchdown.', //
    'info_discounts_calculations_done' => 'Boom! Discounts calculations done and dusted — easy peasy!', //
    'info_messages_on_their_way' => "Let's go! Messages on their way!", //
    'info_loading' => 'Loading...', //
    'info_offline' => 'You are currently offline.', //
    'info_page_not_found_explanation' => 'Oops! 😖 The requested URL was not found on this server.', //
    'info_access_forbidden_explanation' => 'You do not have permission to access this page. Please return to the Home Page!', //
    'info_not_authorized_explanation' => 'You do not have sufficient permission to access this resource. Please return to the Home Page.', //
    'info_menu_not_loaded' => 'Menu could not be loaded or no menu items available for your role.', // Derived from "Menu tidak dimuatkan atau tiada item menu yang tersedia untuk peranan anda."
    'info_role_not_assigned' => 'Role Not Assigned', // From "Peranan Tidak Ditetapkan"
    'info_user_guest' => 'Guest User', // From "Pengguna Tetamu"
    'info_user_unknown' => 'Unknown User', // From "Pengguna Tidak Dikenali"
    'info_thank_you' => 'Thank you.', //
    'info_computer_generated_email' => 'This is a computer-generated email. Please do not reply to this email.', //
    'info_quick_actions_unavailable' => 'No quick actions available', //
    'info_start_day_check_tasks' => 'Start your day with a quick check here.', //
    'info_page_undergoing_updates' => 'Attention! This page is currently undergoing updates.', //

    'message_no_details' => 'No details.', //
    'message_not_specified' => 'Message not specified.', // Derived from "Mesej tidak dinyatakan."

    'auth_two_factor_title' => 'Two Factor Authentication', //
    'auth_two_factor_enabled' => 'You have enabled two factor authentication.', //
    'auth_two_factor_not_enabled' => 'You have not enabled two factor authentication.', //
    'auth_manage_browser_sessions' => 'Manage and log out your active sessions on other browsers and devices.', //
    'auth_logout_other_sessions' => 'Log Out Other Browser Sessions', //
    'auth_delete_account_title' => 'Delete Account', //
    'auth_delete_account_permanently' => 'Permanently delete your account.', //
    'auth_update_password_title' => 'Update Password', //
    'auth_profile_information_title' => 'Profile Information', //
    'auth_update_profile_information_description' => "Update your account's profile information and email address.", //
    'auth_saved' => 'Saved.', //
    'auth_email_unverified' => 'Your email address is unverified.', //
    'auth_resend_verification_email' => 'Click here to re-send the verification email.', //
    'auth_new_verification_link_sent' => 'A new verification link has been sent to your email address.', //
    'auth_this_device' => 'This device', //
    'auth_last_active' => 'Last active', //

    'keywords_meta' => 'motac, bpm, internal system, resource management, ict loan, email application, ministry of tourism arts and culture', //

    // Terms & Conditions for ICT Loan
    'terms_ict_loan_title' => 'Terms and Conditions for Official Use ICT Equipment Loan Application',
    'terms_reminder' => 'Reminder:',
    'terms_item1' => 'This ICT equipment loan is for official use only.',
    'terms_item2' => 'The applicant or responsible officer is fully responsible for any damage to or loss of the borrowed equipment.',
    'terms_item3' => 'The equipment must be returned to the Information Management Division (BPM) on or before the return date stated in the application.',
    'terms_item4' => 'The equipment must be returned in good and functional condition as when it was received. Any issues must be reported to BPM staff during the return process.',
    'terms_item5' => 'Application approval is subject to the availability of the equipment on the requested loan date.',
    'terms_item6' => 'BPM will not be held responsible for any equipment availability issues if the application is not processed within three (3) working days due to an incomplete application.',
    'terms_item7' => 'The applicant must ensure that all information provided in this application form is accurate and true.',

    // Application Form Logic Messages
    'session_expired' => 'Your session has expired. Please log in again.',
    'supporter_grade_requirement_failed' => 'The Supporting Officer does not meet the minimum grade requirement (Grade :grade).',
    'draft_saved_successfully' => 'Application draft saved successfully.',
    'application_submitted_successfully' => 'Loan application has been successfully submitted for approval.',
    'system_error_generic' => 'The system encountered an error. Please try again.',
    'loan_requires_min_one_item' => 'The application must have at least one equipment item.',
];
