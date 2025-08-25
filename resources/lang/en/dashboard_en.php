<?php
// English translations for Dashboard module and related panels
// Mirrors lang/ms/dashboard_ms.php for synchronized bilingual support

return [
    // === Main Dashboard Key (for button and nav) ===
    'dashboard' => 'Dashboard',

    // === Dashboard Titles ===
    'admin_title'      => 'Administrator Dashboard',
    'approver_title'   => 'Approval Dashboard',
    'bpm_title'        => 'BPM Staff Dashboard (ICT Equipment Management)',
    'it_admin_title'   => 'IT Administrator Dashboard',
    'user_title'       => 'User Dashboard',
    'main_dashboard'   => 'Main Dashboard',

    // === Welcome Messages, Quick Actions, Navigation ===
    'welcome'                   => 'Welcome',
    'quick_actions'             => 'Quick Actions',
    'quick_shortcuts'           => 'Main Shortcuts',
    'ict_loan'                  => 'ICT Loan',
    'helpdesk'                  => 'Helpdesk',
    'notifications'             => 'Notifications',
    'view_all_notifications'    => 'View All Notifications',
    'apply_for_loan'            => 'Apply for ICT Loan',
    'apply_new_loan'            => 'Apply for New ICT Loan',
    'my_loan_applications'      => 'My Loan Applications',
    'view_my_loan_apps'         => 'View My Applications',
    'submit_helpdesk_ticket'    => 'Submit Helpdesk Ticket',
    'create_new_ticket'         => 'Create New Helpdesk Ticket',
    'my_helpdesk_tickets'       => 'My Helpdesk Tickets',
    'view_my_tickets'           => 'View My Helpdesk Tickets',
    'manage_all_tickets'        => 'Manage All Tickets',
    'manage_pending_approvals'  => 'Manage Pending Approvals',
    'view_loan_transactions'    => 'View Loan Transactions',
    'view_all_loan_applications'=> 'View All Applications',
    'view_all_my_tickets'       => 'View All My Tickets',

    // === Statistic Cards & Dashboard Summaries ===
    'total_users'               => 'Total System Users',
    'pending_approvals'         => 'Applications Awaiting Approval',
    'available_equipment'       => 'Available ICT Equipment',
    'loaned_equipment'          => 'ICT Equipment on Loan',
    'active_loans'              => 'Active Loans',
    'overdue_loans'             => 'Overdue Loans',
    'utilization_rate'          => 'Utilization Rate',
    'equipment_status_summary'  => 'Equipment Status Summary',
    'loan_stats_title'          => 'Loan Statistics',
    'no_loan_data_available'    => 'No loan data to display.',
    'loan_summary'              => 'Loan Summary',
    'on_loan'                   => 'On Loan',
    'approved_pending_issuance' => 'Approved (Pending Issuance)',
    'returned'                  => 'Returned',

    // === Admin/BPM/IT Admin Dashboard Cards ===
    'total_ict_equipment'           => 'Total ICT Equipment',
    'equipment_on_loan'             => 'Equipment on Loan',
    'equipment_available'           => 'Equipment Available',
    'total_loan_applications'       => 'Total ICT Loan Applications',
    'pending_loan_applications'     => 'Pending Loan Applications',
    'approved_loan_applications'    => 'Approved Loan Applications',
    'rejected_loan_applications'    => 'Rejected Loan Applications',
    'total_helpdesk_tickets'        => 'Total Helpdesk Tickets',
    'open_helpdesk_tickets'         => 'Open Helpdesk Tickets',
    'resolved_helpdesk_tickets'     => 'Resolved Helpdesk Tickets',

    // === BPM/IT/Admin Dashboard Specifics ===
    'add_new_equipment'         => 'Add New Equipment',
    'view_full_inventory'       => 'View Full Inventory',
    'inventory_stock_summary'   => 'Inventory Stock Summary',
    'laptops_available'         => 'Laptops Available',
    'projectors_available'      => 'Projectors Available',
    'printers_available'        => 'Printers Available',
    'view_detailed_inventory'   => 'View Detailed Inventory',
    'maintenance_equipment_title'=> 'Equipment Under Maintenance',
    'maintenance_equipment_text'=> 'A list of equipment currently under maintenance will be displayed here.',

    // === IT Admin Dashboard ===
    'pending_helpdesk_tickets'      => 'Pending Helpdesk Tickets',
    'in_progress_helpdesk_tickets'  => 'Helpdesk Tickets In Progress',
    'helpdesk_tickets_to_process_title' => 'Helpdesk Tickets Awaiting Action',
    'my_assigned_helpdesk_tickets'  => 'My Assigned Helpdesk Tickets',
    'view_all_helpdesk_tickets'     => 'View All Helpdesk Tickets',

    // === User Dashboard: Statistics & Recent Applications ===
    'my_loan_stats'                    => 'My Loan Statistics',
    'pending_loans'                    => 'Pending Approval',
    'approved_loans'                   => 'Approved',
    'rejected_loans'                   => 'Rejected',
    'total_loans'                      => 'Total Applications',
    'recent_loan_applications'         => 'Recent Applications',
    'no_recent_loan_applications'      => 'No recent loan applications.',
    'application_no'                   => 'Application No.',
    'item_name'                        => 'Item Name',
    'loan_purpose'                     => 'Loan Purpose',
    'status'                           => 'Status',
    'applied_on'                       => 'Applied On',

    // === Approval Tasks & Approver Dashboard ===
    'latest_tasks_title'           => 'Latest Approval Tasks',
    'view_all_tasks'               => 'View All Tasks',
    'approver_stats_title'         => 'Your Approval Stats (Last 30 Days)',
    'num_approved'                 => 'Total Approved:',
    'num_rejected'                 => 'Total Rejected:',
    'approval_guidance_title'      => 'Approval Guidance',
    'approval_guidance_text'       => 'Please review each application carefully before making a decision. Your decision will be notified to the applicant via email.',
    'pending_tasks_title'          => 'Applications Awaiting Your Action',
    'approval_history'             => 'View Approval History',

    // === Approval Dashboard (Summary & Actions) ===
    'awaiting_your_approval'       => 'Awaiting Your Approval',
    'total_pending_tasks'          => 'Total Pending Tasks:',
    'total_approved'               => 'Total Approved:',
    'total_rejected'               => 'Total Rejected:',
    'read_full_guidelines'         => 'Read Full Guidelines',

    // === Table Headers (for dashboard tables) ===
    'date'         => 'Date',
    'subject'      => 'Subject',
    'return_date'  => 'Return Date',
    'apply_date'   => 'Apply Date',
    'type'         => 'Type',
    'applicant'    => 'Applicant',
    'assigned_to'  => 'Assigned To',
    'priority'     => 'Priority',
    'category'     => 'Category',

    // === Activity, Transaction & Notification Summaries ===
    'ict_loans_in_process'         => 'ICT Loans In Process',
    'helpdesk_tickets_in_process'  => 'Helpdesk Tickets In Process',
    'active_ict_loans'             => 'Active ICT Loans',
    'recent_transactions'          => 'Recent Transactions',
    'no_recent_transactions'       => 'No recent transaction history.',
    'upcoming_returns'             => 'Upcoming Returns',
    'no_upcoming_returns'          => 'No upcoming ICT returns expected.',
    'recent_helpdesk_activity'     => 'Recent Helpdesk Activity',
    'no_recent_helpdesk_activity'  => 'No recent helpdesk activity.',

    // === Helpdesk User Panel ===
    'create_helpdesk_ticket_title' => 'Create Helpdesk Ticket',
    'create_helpdesk_ticket_text'  => 'Having an IT issue? Submit a new support request to our Helpdesk team.',
    'view_my_tickets_title'        => 'View My Helpdesk Tickets',
    'view_my_tickets_text'         => 'Track the status and history of your IT support requests.',

    // === General messages & others ===
    'no_data_found'                => 'No data to display.',
    'no_permission'                => 'No Permission',
    'notifications_title'          => 'Useful Resources', // Used in welcome.blade for resources section
    'notifications_text'           => 'View all your system notifications.',
    'your_recent_activity_summary' => 'Your Recent Activity Summary',
    'your_recent_activity_text'    => 'There is no recent activity to display.',
    'apply_ict_loan_title'         => 'ICT Equipment Loan', // For welcome.blade main feature
    'apply_ict_loan_text'          => 'Need ICT equipment for official duties? Check availability and submit your loan application here.',
    'view_my_loan_applications_title' => 'System User Guide', // As in welcome.blade
    'view_my_loan_applications_text'  => 'Review the status and details of your ICT equipment loan applications.',
    'view_my_loan_applications'       => 'View My Loan Applications', // Button/text
    'contact_us'                 => 'Contact BPM Helpdesk', // For resource/contact link

    // --- Add any additional keys from ms version if needed for future parity ---
];

// Notes:
// - This file mirrors the structure and keys of lang/ms/dashboard_ms.php for full bilingual parity.
// - Any changes to the Malay version must be reflected here for translation consistency.
// - Comments have been added for documentation and easier maintainability.
