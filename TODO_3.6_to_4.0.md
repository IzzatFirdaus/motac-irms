# MOTAC_ICT_LOAN_HRMS v3.6 → v4.0 Upgrade — TO DO List

This checklist tracks all required updates for the 4.0 release, including removal of the Email/User ID Provisioning module and addition of a Helpdesk module. Each item includes a description, the related files, and a "Done" status to be marked once completed.

---

| #  | Description                                                             | Related Files/Directories                                                     | Done |
|----|-------------------------------------------------------------------------|-------------------------------------------------------------------------------|------|
| 1  | Create new upgrade branch `release/v4.0`                                | git branch, README.md, composer.json, package.json                            | ✅   |
| 2  | Tag current stable version as `v3.6-final`                              | git tag                                                                       |      |
| 3  | Backup email_applications table and related data                        | database/email_applications, database/email_accounts                          |      |
| 4  | Backup .env configuration                                               | .env                                                                          | ✅   |
| 5  | Backup storage folders                                                   | public/storage/, storage/app/public/                                          |      |
| 6  | Audit and list all email-related files/components for removal            | See full audit list below                                                     | ✅   |
| 7  | Remove EmailApplication model & migration                                | app/Models/EmailApplication.php, database/migrations/create_email_applications_table.php | ✅   |
| 8  | Remove EmailAccount/EmailApplication controllers                         | app/Http/Controllers/EmailApplicationController.php, EmailAccountController.php, Api/EmailProvisioningController.php | ✅   |
| 9  | Remove EmailApplication services                                         | app/Services/EmailApplicationService.php, EmailProvisioningService.php         | ✅   |
| 10 | Remove EmailApplication seeders & factories                              | database/seeders/EmailApplicationSeeder.php, factories/EmailApplicationFactory.php | ✅   |
| 11 | Remove EmailApplication Livewire components                              | app/Livewire/ResourceManagement/EmailAccount/, MyApplications/Email/           | ✅   |
| 12 | Remove EmailApplication views & blades                                   | resources/views/livewire/resource-management/email-account/, resources/views/email-applications/, resources/views/emails/application-submitted-notification.blade.php, resources/views/emails/application-*.blade.php | ✅   |
| 13 | Remove EmailApplication-related routes                                   | routes/web.php, routes/api.php (Route::resource('email-applications', ...), Route::resource('email-accounts', ...), /email-* routes) | ✅   |
| 14 | Remove EmailApplication policies                                         | app/Policies/EmailApplicationPolicy.php                                        | ✅   |
| 15 | Remove EmailApplication notifications                                    | app/Notifications/ApplicationSubmitted.php, EmailApplicationNeedsAction.php, EmailApplicationReadyForProcessingNotification.php, EmailProvisionedNotification.php | ✅   |
| 16 | Remove email-related fields from users table if not needed elsewhere     | database/migrations, app/Models/User.php, config/motac.php                     | ✅   |
| 17 | Remove email provisioning references from approvals table and data       | database/migrations, app/Models/Approval.php                                   | ✅   |
| 18 | Remove email provisioning references from event and auth service providers| app/Providers/EventServiceProvider.php, AuthServiceProvider.php                | ✅   |
| 19 | Remove email provisioning from language/translation files                | resources/lang/en/*.php, resources/lang/ms/*.php                               | ✅   |
| 20 | Remove unused keys in config                                             | config/motac.php (approval.min_email_supporting_officer_grade_level, etc.)     | ✅   |
| 21 | Remove EmailApplication role/permission from seeders                     | database/seeders/RoleAndPermissionSeeder.php                                   | ✅   |
| 22 | Remove EmailApplication tests                                            | tests/Feature/EmailApplicationTest.php, tests/Unit/EmailApplicationServiceTest.php, Dusk tests if any                      | ✅   |
| 23 | Remove EmailApplication references in navigation, layout, menus          | resources/views/layouts/app.blade.php, resources/menu/verticalMenu.json        | ✅   |
| 24 | Remove EmailApplication references from documentation                    | docs/Email_Feature_Implementation.md, System_Design_Rev_3.6.md, Technical_Documentation_MOTAC_IRMS_Rev_1.md, README.md |      |
| 25 | Remove email provisioning history or migrate legacy data if needed       | database/migrations, database/legacy/                                          | ✅   |
| 26 | Impact assessment on shared components (User model, notifications, etc.) | app/Models/User.php, app/Services/NotificationService.php                      |      |
| 27 | Confirm with stakeholders regarding historical email data retention      | Project meeting notes, approval                                                |      |
| 28 | Environment setup for v4.0 (Docker, tool versions, etc.)                 | docker-compose.yml, .env, documentation                                       | ✅   |
| 29 | Commit changes in small, logical increments during migration             | git history                                                                   |      |
| 30 | Add Helpdesk database migrations                                         | database/migrations/create_tickets_table.php, create_ticket_categories_table.php, create_ticket_statuses_table.php, create_ticket_priorities_table.php, create_ticket_comments_table.php, create_ticket_attachments_table.php | ✅   |
| 31 | Add Helpdesk models                                                      | app/Models/Ticket.php, TicketCategory.php, TicketPriority.php, TicketComment.php, TicketAttachment.php       | ✅   |
| 32 | Add Helpdesk controllers                                                 | app/Http/Controllers/Helpdesk/TicketController.php, Api/HelpdeskApiController.php | ✅   |
| 33 | Add Helpdesk Livewire components                                         | app/Livewire/Helpdesk/CreateTicketForm.php, MyTicketsIndex.php, TicketDetails.php, Admin/TicketManagement.php, Admin/TicketReport.php         | ✅   |
| 34 | Add Helpdesk services                                                    | app/Services/HelpdeskService.php, TicketNotificationService.php                | ✅   |
| 35 | Add Helpdesk Blade views                                                 | resources/views/livewire/helpdesk/*, resources/views/helpdesk/index.blade.php, create.blade.php, show.blade.php, admin/index.blade.php, resources/views/emails/helpdesk/ticket-created.blade.php, ticket-status-updated.blade.php, ticket-assigned.blade.php | ✅   |
| 36 | Add Helpdesk notifications                                               | app/Notifications/TicketCreatedNotification.php, TicketStatusUpdatedNotification.php, TicketAssignedNotification.php | ✅   |
| 37 | Add Helpdesk routes                                                      | routes/web.php (helpdesk group as per example), routes/api.php                 | ✅   |
| 38 | Add Helpdesk seeders                                                     | database/seeders/TicketCategorySeeder.php, TicketStatusSeeder.php, TicketPrioritySeeder.php              | ✅   |
| 39 | Add Helpdesk policies                                                    | app/Policies\TicketPolicy.php                                                  | ✅   |
| 40 | Add Helpdesk to navigation/menu                                          | resources/views/layouts/app.blade.php, resources/menu/verticalMenu.json        | ✅   |
| 41 | Modify EventServiceProvider to observe Helpdesk models                   | app/Providers/EventServiceProvider.php                                         | ✅   |
| 42 | Modify AuthServiceProvider to register TicketPolicy                      | app/Providers/AuthServiceProvider.php                                          | ✅   |
| 43 | Update configuration for Helpdesk settings                               | config/motac.php, config/app.php, config/variables.php                        | ✅   |
| 44 | Update README and documentation for new system scope                     | README.md, docs/SUMMARY.md, docs/DIRECTORY.md, docs/System_Design_v4.0.md, Technical_Documentation_MOTAC_IRMS_Rev_1.md |      |
| 45 | Update user manual and system design docs for Helpdesk usage flow        | docs/System_Design_v4.0.md, user_manuals/                                     |      |
| 46 | Conduct training for Helpdesk usage                                      | Training materials, user guides                                               |      |
| 47 | Test for regression in ICT Loan flows                                    | tests/Feature/LoanApplicationTest.php, manual testing                          | ✅   |
| 48 | Confirm removal of all email-related dropdown/enums                      | config/motac.php, System_Design_Rev_3.6.md, Technical_Documentation_MOTAC_IRMS_Rev_1.md | ✅   |
| 49 | Manual/Unit/Feature Tests for Helpdesk module                            | tests/Feature/Helpdesk/CreateTicketTest.php, tests/Unit/HelpdeskServiceTest.php | ✅   |
| 50 | Manual Testing: Helpdesk Ticket Creation, Assignment, Commenting         | Manual test checklist                                                          |      |
| 51 | Staging deployment and UAT                                               | Staging environment, UAT sign-off                                              |      |
| 52 | Production rollout, migration, and go-live                               | Main branch, production server, php artisan migrate --force                    |      |
| 53 | Rollback plan and verify backup/restoration procedures                   | Documentation, backup scripts                                                  |      |
| 54 | Gather feedback from end users and IT support after go-live              | Project meeting notes, feedback forms                                          |      |

---

**Instructions:**  
- Mark "Done" as "✅" once the item is completed.  
- Add notes/comment columns if needed as work progresses.  

---
