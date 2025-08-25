MOTAC IRMS (Version 4.0)

Okay, this is a significant architectural change for the MOTAC Integrated Resource Management System, transitioning it from a dual-purpose system (Email/User ID Provisioning & ICT Equipment Loan) to a focused ICT Loan and new Helpdesk system. This requires careful planning and execution.
Here's a detailed plan for the 4.0 version, outlining how to separate and remove Email Application components and integrate a new Helpdesk system.
MOTAC Integrated Resource Management System (Version 4.0) - Refactoring & New Feature Integration Plan
Goals for Version 4.0:
Remove Email/User ID Provisioning: Eliminate all code, database tables, and UI elements related to the Email/User ID Provisioning module.
Integrate Helpdesk System: Introduce a new Helpdesk module to manage IT support tickets.
Maintain ICT Equipment Loan Management: Ensure the existing ICT Equipment Loan Management system remains fully functional and robust.

Phase 1: Planning and Preparation (Critical for Success)
Verify Scope and Requirements:
Email Removal: Confirm with stakeholders that all Email/User ID Provisioning functionality is to be removed, including historical data access if applicable.
Helpdesk Integration: Get a clear high-level understanding of the required Helpdesk features (e.g., ticket creation, assignment, status tracking, comments, attachments, reporting, user roles for agents/users). This plan assumes basic ticketing features.
Impact Assessment: Conduct a thorough review of shared components (e.g., User model, notifications) to understand how their dependencies will be affected by removing the email module and adding helpdesk.
Version Control Strategy:
Create a new branch: All work for v4.0 should be done on a dedicated branch (e.g., feature/v4.0-helpdesk-refactor) derived from the stable main or develop branch of v3.6.
Regular Commits: Commit small, logical changes frequently.
Backup Data & Code:
Perform a full database backup of the v3.6 system.
Ensure the current v3.6 codebase is committed and tagged.
Environment Setup:
Set up a dedicated development environment for v4.0 (ideally Dockerized as per system design).
Ensure all necessary tools (PHP, Composer, Node.js, npm, database client) are in place.

Phase 2: Removing Email Application Components (Refactoring & Deletion)
This phase focuses on surgically removing the email provisioning module while minimizing impact on the loan module.
2.1 Database Schema Clean-up (Models & Migrations)
Identify & Isolate:
email_applications table: This table is directly tied to the email provisioning.
users table: Examine motac_email, user_id_assigned, previous_department_name, previous_department_email, service_status, appointment_type fields. If these fields are exclusively used by the email provisioning module and have no other purpose for HRMS or Loan, they should be removed. Otherwise, they might need to be repurposed or retained. Assume for this plan that motac_email and user_id_assigned are removable, and others might be kept for general HR purposes.
approvals table: This is polymorphic. Review its relationship with EmailApplication. The polymorphic setup means the approvable_type and approvable_id columns store the necessary link. Records related to EmailApplication will become orphaned or need to be selectively deleted if historical approval data for email applications is not required.
Action Plan:
Create a new migration: php artisan make:migration remove_email_provisioning_components
Inside the migration's up() method:
Drop the email_applications table: Schema::dropIfExists('email_applications');
Remove specific columns from users table if confirmed they are not used elsewhere:
PHP
Schema::table('users', function (Blueprint $table) {
    if (Schema::hasColumn('users', 'motac_email')) {
        $table->dropUnique(['motac_email']); // Drop unique constraint first
        $table->dropColumn('motac_email');
    }
    if (Schema::hasColumn('users', 'user_id_assigned')) {
        $table->dropUnique(['user_id_assigned']); // Drop unique constraint first
        $table->dropColumn('user_id_assigned');
    }
    // Consider other fields like previous_department_name, previous_department_email, service_status, appointment_type
    // If they are purely for email app and not general HR, drop them.
    if (Schema::hasColumn('users', 'previous_department_name')) $table->dropColumn('previous_department_name');
    if (Schema::hasColumn('users', 'previous_department_email')) $table->dropColumn('previous_department_email');
    if (Schema::hasColumn('users', 'service_status')) $table->dropColumn('service_status');
    if (Schema::hasColumn('users', 'appointment_type')) $table->dropColumn('appointment_type');
});




Handle approvals table: Decide on historical data.
Option A (Delete associated history): DB::table('approvals')->where('approvable_type', 'App\Models\EmailApplication')->delete(); (Run before dropping email_applications table to ensure relationships are handled cleanly, or use a separate migration specifically for data cleanup if the table is already dropped).
Option B (Keep orphaned history for audit): If historical approval records are needed for audit purposes, leave them. The foreign key constraint won't break if approvable_id is not strictly enforced on the id of email_applications table (which it wouldn't be for polymorphic relations).
2.2 Code Component Deletion & Refactoring
Controllers:
DELETE:
App/Http/Controllers/Api/EmailProvisioningController.php
App/Http/Controllers/EmailAccountController.php
App/Http/Controllers/EmailApplicationController.php
MODIFY:
App/Http/Controllers/ReportController.php: Remove emailAccounts method and any related logic for email reports.
Livewire Components (and their Blade views):
DELETE:
App/Livewire/ResourceManagement/EmailApplication/ApplicationForm.php (or similar naming based on actual file structure) and its corresponding Blade file: resources/views/livewire/resource-management/email-application/application-form.blade.php
App/Livewire/ResourceManagement/MyApplications/Email/Index.php (or similar) and its corresponding Blade file: resources/views/livewire/resource-management/my-applications/email/index.blade.php
Any other Livewire components strictly dedicated to email provisioning (e.g., admin panels for email management).
Services:
DELETE:
App/Services/EmailApplicationService.php
App/Services/EmailProvisioningService.php
MODIFY:
App/Services/NotificationService.php: Review and remove any methods or logic specific to email application notifications.
Policies:
DELETE:
App/Policies/EmailApplicationPolicy.php
Models:
DELETE: EmailApplication.php
MODIFY: User.php: Remove constants related to service_status, appointment_type, and title if these were solely for email application context. Update $fillable array if these fields were present there.
Providers:
MODIFY:
AppServiceProvider.php: In the register() method, remove the registration of EmailApplicationService and EmailProvisioningService (e.g., $this->app->singleton(...)).
AuthServiceProvider.php: In the $policies array, remove EmailApplication::class => EmailApplicationPolicy::class,.
EventServiceProvider.php: In the boot() method, if EmailApplication was registered with BlameableObserver, remove it.
Routes:
MODIFY:
routes/api.php: Remove the entire route definition for /api/email-provisioning.
routes/web.php: Systematically remove all routes that point to EmailApplicationController, EmailAccountController, EmailProvisioningController, and any Livewire components related to email provisioning. Look for:
Route::prefix('email-applications') groups.
Route::get('/email-accounts/admin') or similar.
Livewire routes like Route::get('/my-email-applications', ...)->name('my-email-applications.index').
Views (Blade Templates & Layouts):
DELETE:
resources/views/emails/application-submitted-notification.blade.php (if it's purely for email apps).
MODIFY:
resources/views/ (various): Thoroughly check all Blade files, especially dashboard.blade.php, navigation-menu.blade.php, _partials/_modals/ for any hardcoded references or links to email application features. Remove or comment them out.
resources/menu/verticalMenu.json: Remove the menu item(s) for Email/User ID Provisioning.
Configuration:
MODIFY: config/motac.php: Remove motac.approval.min_email_supporting_officer_grade_level and any other configuration settings exclusively for the email module.
Tests:
DELETE: Remove all feature and unit tests located in tests/Feature/ and tests/Unit/ that are specifically written for the email provisioning module.

Phase 3: Integrating Helpdesk System (New Features)
This phase focuses on adding the new Helpdesk module, leveraging Laravel's MVC structure and Livewire.
3.1 Database Schema Design (New Models & Migrations)
New Tables:
tickets: Stores core ticket information.
Fields: id, user_id (applicant/reporter, FK to users), assigned_to_user_id (agent, FK to users, nullable), category_id (FK to ticket_categories), priority_id (FK to ticket_priorities), subject (string), description (text), status (enum: 'open', 'in_progress', 'pending_user_feedback', 'resolved', 'closed', 'reopened'), due_date (datetime, nullable), resolution_notes (text, nullable), closed_at (timestamp, nullable), created_by, updated_by, deleted_by, timestamps, deleted_at.
ticket_categories: Defines categories for tickets (e.g., 'Hardware', 'Software', 'Network', 'Account').
Fields: id, name (string), description (text, nullable), is_active (boolean), created_by, updated_by, deleted_by, timestamps, deleted_at.
ticket_priorities: Defines priority levels (e.g., 'Low', 'Medium', 'High', 'Critical').
Fields: id, name (string), level (integer for sorting), color_code (string, nullable), created_by, updated_by, deleted_by, timestamps, deleted_at.
ticket_comments: Stores comments on tickets.
Fields: id, ticket_id (FK to tickets), user_id (FK to users), comment (text), is_internal (boolean, for agent-only notes), created_by, updated_by, deleted_by, timestamps, deleted_at.
ticket_attachments: Stores file attachments for tickets/comments.
Fields: id, attachable_type (polymorphic), attachable_id (polymorphic), file_path (string), file_name (string), file_size (integer), file_type (string), created_by, updated_by, deleted_by, timestamps, deleted_at.
Action Plan:
php artisan make:model Ticket -m (and for other new models)
Define schemas in the new migration files.
Run php artisan migrate.
MODIFY: EventServiceProvider.php: Register new models (Ticket, TicketCategory, TicketPriority, TicketComment, TicketAttachment) with BlameableObserver.
3.2 Code Component Development
Models:
NEW: App/Models/Ticket.php, App/Models/TicketCategory.php, App/Models/TicketPriority.php, App/Models/TicketComment.php, App/Models/TicketAttachment.php. Define relationships, constants for enums, and any specific logic.
Controllers:
NEW:
App/Http/Controllers/Helpdesk/TicketController.php: For standard web routes (e.g., index, show, store, update, destroy, possibly assign, changeStatus).
App/Http/Controllers/Api/HelpdeskApiController.php (if a public API for submitting tickets is needed).
MODIFY:
App/Http/Controllers/ReportController.php: Add new methods (e.g., ticketTrends, agentPerformance) for Helpdesk reports.
Livewire Components (and their Blade views):
NEW:
App/Livewire/Helpdesk/CreateTicketForm.php
App/Livewire/Helpdesk/MyTicketsIndex.php
App/Livewire/Helpdesk/TicketDetails.php
App/Livewire/Helpdesk/Admin/TicketManagement.php (for agents to view/manage all tickets)
App/Livewire/Helpdesk/Admin/TicketReport.php
NEW Blade Views: Create corresponding Blade files in resources/views/livewire/helpdesk/.
Services:
NEW:
App/Services/HelpdeskService.php: Encapsulate business logic for ticket creation, updates, assignments, status changes, comments, attachments.
App/Services/TicketNotificationService.php: Handle notifications specific to helpdesk events (e.g., new ticket, status update, comment added, ticket assigned).
MODIFY:
AppServiceProvider.php: Register HelpdeskService and TicketNotificationService as singletons.
Policies:
NEW: App/Policies/TicketPolicy.php: Define authorization for ticket actions (e.g., viewAny, view, create, update, delete, assign, close).
MODIFY: AuthServiceProvider.php: Register Ticket::class => TicketPolicy::class,.
Routes:
NEW: routes/web.php: Add new route groups for the Helpdesk module.
PHP
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        Route::get('/', App\Livewire\Helpdesk\MyTicketsIndex::class)->name('index');
        Route::get('/create', App\Livewire\Helpdesk\CreateTicketForm::class)->name('create');
        Route::get('/{ticket}', App\Livewire\Helpdesk\TicketDetails::class)->name('show');
        // Admin routes for managing tickets
        Route::middleware(['role:Admin|IT Admin'])->group(function () {
            Route::get('/admin/tickets', App\Livewire\Helpdesk\Admin\TicketManagement::class)->name('admin.index');
            // Add other admin-specific routes (e.g., reports)
        });
    });
});




routes/api.php: Add API routes for Helpdesk if required (e.g., for external systems to submit tickets).
Views (Blade Templates & Layouts):
NEW:
resources/views/helpdesk/index.blade.php (main page for user tickets)
resources/views/helpdesk/create.blade.php
resources/views/helpdesk/show.blade.php
resources/views/helpdesk/admin/index.blade.php
resources/views/emails/helpdesk/ticket-created.blade.php
resources/views/emails/helpdesk/ticket-status-updated.blade.php
resources/views/emails/helpdesk/ticket-assigned.blade.php
MODIFY:
resources/menu/verticalMenu.json: Add a new menu item for "Helpdesk" or "Sistem Meja Bantuan".
Notifications:
NEW: App/Notifications/TicketCreatedNotification.php, App/Notifications/TicketStatusUpdatedNotification.php, etc. (using Illuminate\Notifications\Notification).
Configuration:
MODIFY: config/motac.php: Add new configuration keys for the Helpdesk module, e.g., default assignment rules, default priority, allowed attachment types, helpdesk email address.

Phase 4: Testing, Deployment, and Documentation
Unit Testing:
Create comprehensive unit tests for all new Helpdesk models, services, policies, and controllers.
Ensure existing ICT Loan unit tests still pass after refactoring.
Feature/Integration Testing:
Write feature tests for all Helpdesk user flows (creating, viewing, commenting, updating, assigning tickets).
Execute existing ICT Loan feature tests to ensure no regressions.
Test the overall system integration, ensuring navigation and data flow between ICT Loan and Helpdesk (if any) are seamless.
Test authorization and role-based access control for both modules.
User Acceptance Testing (UAT):
Engage key stakeholders (users, IT staff, BPM staff) to test the new Helpdesk system and confirm the removal of Email Provisioning. Gather feedback for any necessary adjustments.
Deployment Strategy:
Database Migrations: Ensure all new migrations are correctly applied to production. Consider data seeding for initial categories/priorities.
Code Deployment: Use CI/CD pipeline (e.g., GitHub Actions as mentioned in System Design) to deploy the new v4.0 branch to staging/production environments.
Rollback Plan: Have a clear rollback plan in case of unforeseen issues.
Documentation & Training:
Update System Design Document: Create a "System Design (Rev. 4.0)" document reflecting the removed email module, new Helpdesk module, and any updated architecture.
Update README.md: Reflect the changes (removal of email provisioning, addition of helpdesk).
User Manuals: Update user manuals for both the ICT Loan and the new Helpdesk functionalities.
Training: Conduct training sessions for end-users and IT support staff on the new Helpdesk system and changes to existing workflows.
This detailed plan provides a structured approach to achieve the desired system evolution to Version 4.0, ensuring a smooth transition and robust new functionality.
