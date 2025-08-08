<?php
// Common language lines used throughout the system (English)
// Ensure translation keys match with ms version for bilingual support

return [
    // === Generic Actions & Buttons ===
    'manage'           => 'Manage',
    'all'              => 'All',
    'create'           => 'Create',
    'edit'             => 'Edit',
    'view'             => 'View',
    'delete'           => 'Delete',
    'save'             => 'Save',
    'add_new'          => 'Add New',
    'submit'           => 'Submit',
    'update'           => 'Update',
    'process'          => 'Process',
    'issue'            => 'Issue',
    'return'           => 'Return',
    'confirm'          => 'Confirm',
    'cancel'           => 'Cancel',
    'back'             => 'Back',
    'reset_search'     => 'Reset Search',

    // === Boolean Values (for badges, toggles, etc.) ===
    'yes'              => 'Yes',
    'no'               => 'No',

    // === Status / Progress Labels ===
    'not_available'              => 'N/A',
    'completed'                  => 'Completed',
    'in_process'                 => 'In Process',
    'on_loan'                    => 'On Loan',
    'approved_pending_issuance'  => 'Approved (Pending Issuance)',
    'returned'                   => 'Returned',
    'see_all'                    => 'See All',

    // === Entity / Table Labels ===
    'applicant'          => 'Applicant',
    'apply_date'         => 'Apply Date',
    'status'             => 'Status',
    'department'         => 'Department',
    'actions'            => 'Actions',
    'name'               => 'Name',
    'description'        => 'Description',
    'code'               => 'Code',
    'type'               => 'Type',
    'head_of_department' => 'Head of Department/Unit',
    'role'               => 'Role',
    'email'              => 'Email',
    'position'           => 'Position',
    'grade'              => 'Grade',
    'user'               => 'User',
    'users'              => 'Users',
    'add_user'           => 'Add User',
    'edit_user'          => 'Edit User',
    'view_user'          => 'View User',
    'password'           => 'Password',
    'confirm_password'   => 'Confirm Password',
    'change_password'    => 'Change Password',
    'last_login'         => 'Last Login',
    'active'             => 'Active',
    'inactive'           => 'Inactive',

    // === User/Profile Management ===
    'full_name'          => 'Full Name',
    'personal_email'     => 'Personal Email',
    'motac_email'        => 'MOTAC Email',
    'mobile_number'      => 'Mobile Number',
    'user_id_assigned'   => 'Network User ID',
    'service_status'     => 'Service Status',
    'appointment_type'   => 'Appointment Type',
    'status_user'        => 'User Status',
    'created_at'         => 'Created At',
    'updated_at'         => 'Updated At',

    // === Alerts & Notifications ===
    'success'            => 'Success!',
    'error'              => 'Error!',
    'warning'            => 'Warning!',
    'info'               => 'Info!',
    'record_created'     => 'Record created successfully.',
    'record_updated'     => 'Record updated successfully.',
    'record_deleted'     => 'Record deleted successfully.',
    'no_permission'      => 'You do not have permission to perform this action.',

    // === Confirmations ===
    'confirm_action'     => 'Are you sure you want to proceed with this action?',
    'confirm_delete'     => 'Are you sure you want to delete this record? This action cannot be undone.',

    // === Pagination ===
    'showing'            => 'Showing',
    'of'                 => 'of',
    'results'            => 'results',
    'per_page'           => 'Per Page',

    // === Miscellaneous UI Elements ===
    'main_title'                 => 'Main Dashboard - MOTAC IRMS',
    'footer_text'                => 'Ministry of Tourism, Arts and Culture Malaysia',
    'contact_us'                 => 'Contact Us',
    'system_settings'            => 'System Settings',
    'user_menu'                  => 'User Menu',
    'view_all_notifications'     => 'View All Notifications',
    'new_notification'           => 'New Notification',
    'app_name'                   => 'motac-irms',

    // === Department, Grade, Position Features ===
    'branch_type'                => 'Branch Type',
    'hq'                         => 'Headquarters',
    'state'                      => 'State',
    'add_department'             => 'Add New Department/Unit',
    'department_code'            => 'Department/Unit Code',
    'department_description'     => 'Department/Unit Description',
    'department_list'            => 'Department/Unit List',
    'grade_list'                 => 'Grade List',
    'position_list'              => 'Position List',
    'add_grade'                  => 'Add New Grade',
    'add_position'               => 'Add New Position',

    // === Empty State & Error Messages ===
    'no_records_found'         => 'No records found.',
    'no_users_found'           => 'No system users found.',
    'no_grades_found'          => 'No grade records found.',
    'no_positions_found'       => 'No position records found.',
    'no_departments_found'     => 'No departments/units found matching your search.',
    'try_different_search'     => 'Try a different search keyword.',

    // === Table Headers for User Dashboard Recent Applications ===
    'application_no'           => 'Application No.',
    'item_name'                => 'Item Name',
    'loan_purpose'             => 'Loan Purpose',
    'applied_on'               => 'Applied On',

    // === Units (used in inventory/stock) ===
    'units'                    => 'units',

    // === Helpdesk module fields ===
    'helpdesk'                 => 'Helpdesk',
    'ticket'                   => 'Ticket',
    'ticket_title'             => 'Ticket Title',
    'ticket_description'       => 'Ticket Description',
    'ticket_category'          => 'Ticket Category',
    'ticket_priority'          => 'Ticket Priority',
    'ticket_status'            => 'Ticket Status',
    'ticket_applicant'         => 'Ticket Applicant',
    'ticket_assigned_to'       => 'Assigned To',
    'ticket_created_at'        => 'Created At',
    'ticket_updated_at'        => 'Updated At',
    'ticket_closed_at'         => 'Closed At',
    'ticket_resolution_notes'  => 'Resolution Notes',
    'ticket_comments'          => 'Ticket Comments',
    'add_new_comment'          => 'Add New Comment',
    'comment_internal'         => 'Internal Comment (For support staff only)',
    'attachment_upload'        => 'Upload Attachment',
    'no_attachments'           => 'No attachments.',
    'status_open'              => 'Open',
    'status_in_progress'       => 'In Progress',
    'status_resolved'          => 'Resolved',
    'status_closed'            => 'Closed',
    'priority_low'             => 'Low',
    'priority_medium'          => 'Medium',
    'priority_high'            => 'High',
    'priority_critical'        => 'Critical',
    'category_hardware'        => 'Hardware',
    'category_software'        => 'Software',
    'category_network'         => 'Network',
    'category_account'         => 'Account',
    'category_other'           => 'Other',

    // === Language Switcher and Navbar keys ===
    'language_selector'    => 'Language Selector',
    'toggle_theme'         => 'Toggle Theme',
    'toggle_sidebar'       => 'Toggle Navigation Menu',
    'bahasa_melayu'        => 'Bahasa Melayu',
    'english'              => 'English',
    'login'                => 'Login',
    'language_switched_en' => 'Language has been switched to English.',
    'language_switched_ms' => 'Language has been switched to Bahasa Melayu.',
];

// Notes:
// - This file is intended to be kept in sync with common_ms.php (Bahasa Melayu version).
// - All keys and structure should match for easy bilingual support and translation management.
// - Update/add/remove keys here if the Malay version is updated for system consistency.
