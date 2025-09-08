# MOTAC IRMS v3.6 → v4.0 Migration Plan (Repo: @IzzatFirdaus/motac-irms)

This plan details all tasks for upgrading the system from version 3.6 to 4.0, focusing on **removal of Email/User ID Provisioning** and **integration of Helpdesk/Ticketing System**.  
Each table contains: **Related Files**, **Description**, and **Progress** (✅ yes / ❌ no).

---

## 1. Preparation

| Related Files/Dirs                      | Description                                                         | Progress |
|-----------------------------------------|---------------------------------------------------------------------|----------|
| git branch, README.md, composer.json, package.json | Create upgrade branch `release/v4.0`, update metadata               | ❌       |
| git tag                                 | Tag current stable version as `v3.6-final`                          | ❌       |
| database/email_applications, database/email_accounts | Backup email application related tables                             | ❌       |
| .env                                    | Backup environment configuration                                    | ❌       |
| public/storage/, storage/app/public/    | Backup file storage folders                                         | ❌       |

---

## 2. Remove Email/User ID Provisioning Module

### 2.1 Audit & Remove Files/Components

| Related Files/Dirs                                                                | Description                                                         | Progress |
|-----------------------------------------------------------------------------------|---------------------------------------------------------------------|----------|
| app/Models/EmailApplication.php                                                   | Remove EmailApplication model                                       | ❌       |
| database/migrations/create_email_applications_table.php                           | Remove EmailApplication migration                                   | ❌       |
| app/Http/Controllers/EmailApplicationController.php, EmailAccountController.php, Api/EmailProvisioningController.php | Remove email-related controllers                                    | ❌       |
| app/Services/EmailApplicationService.php, EmailProvisioningService.php            | Remove email-related services                                       | ❌       |
| database/seeders/EmailApplicationSeeder.php, factories/EmailApplicationFactory.php| Remove email-related seeders/factories                              | ❌       |
| app/Livewire/ResourceManagement/EmailAccount/, MyApplications/Email/              | Remove email-related Livewire components                            | ❌       |
| resources/views/livewire/resource-management/email-account/, email-applications/, resources/views/emails/application-submitted-notification.blade.php, resources/views/emails/application-*.blade.php | Remove email-related Blade views                                    | ❌       |
| routes/web.php, routes/api.php                                                    | Delete email-related routes (resource, groups, prefixes)            | ❌       |
| app/Policies/EmailApplicationPolicy.php                                           | Remove email-related policies                                       | ❌       |
| app/Notifications/ApplicationSubmitted.php, EmailApplicationNeedsAction.php, EmailApplicationReadyForProcessingNotification.php, EmailProvisionedNotification.php | Remove email-related notifications                                  | ❌       |
| config/motac.php                                                                 | Remove unused email-related config keys                             | ❌       |
| resources/lang/en/*.php, resources/lang/ms/*.php                                 | Remove email-related translation strings                            | ❌       |
| resources/menu/verticalMenu.json                                                  | Remove Email Provisioning from menu                                 | ❌       |
| resources/views/layouts/app.blade.php                                             | Remove navigation/menu references to email features                 | ❌       |

### 2.2 Database & Data Cleanup

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| database/migrations                                 | Remove email-related fields from users table                        | ❌       |
| app/Models/User.php                                 | Remove/review email-related fields, constants, relationships        | ❌       |
| database/migrations, app/Models/Approval.php        | Remove email provisioning references from approvals table and data  | ❌       |
| database/legacy/                                   | Migrate legacy email application history if needed                  | ❌       |

### 2.3 Providers & Shared Components

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| app/Providers/EventServiceProvider.php, AuthServiceProvider.php | Remove email provisioning observers and policy registration         | ❌       |
| app/Services/NotificationService.php                | Remove references to email application notifications                | ❌       |
| app/Models/User.php                                 | Impact assessment and update for shared fields/logic                | ❌       |

### 2.4 Docs & Testing

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| docs/Email_Feature_Implementation.md, System_Design_Rev_3.6.md, Technical_Documentation_MOTAC_IRMS_Rev_1.md, README.md | Remove documentation about email provisioning                       | ❌       |
| tests/Feature/EmailApplicationTest.php, tests/Unit/EmailApplicationServiceTest.php, Dusk tests | Remove email provisioning tests                                     | ❌       |

---

## 3. Add Helpdesk/Ticketing System Module

### 3.1 Database & Models

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| database/migrations/create_tickets_table.php, create_ticket_categories_table.php, create_ticket_statuses_table.php, create_ticket_priorities_table.php, create_ticket_comments_table.php, create_ticket_attachments_table.php | Add migrations for Helpdesk module                                  | ❌       |
| app/Models/Ticket.php, TicketCategory.php, TicketPriority.php, TicketComment.php, TicketAttachment.php | Add Eloquent models for helpdesk entities                           | ❌       |

### 3.2 Components & Logic

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| app/Http/Controllers/Helpdesk/TicketController.php, Api/HelpdeskApiController.php | Add controllers for Helpdesk/Ticketing                              | ❌       |
| app/Livewire/Helpdesk/CreateTicketForm.php, MyTicketsIndex.php, TicketDetails.php, Admin/TicketManagement.php, Admin/TicketReport.php | Add Livewire components for helpdesk flows                          | ❌       |
| app/Services/HelpdeskService.php, TicketNotificationService.php | Add business logic/services for helpdesk                            | ❌       |
| app/Policies/TicketPolicy.php                       | Add helpdesk policies                                               | ❌       |

### 3.3 Blade Views & UI

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| resources/views/livewire/helpdesk/*, resources/views/helpdesk/index.blade.php, create.blade.php, show.blade.php, admin/index.blade.php | Add Blade views for helpdesk UI                                     | ❌       |
| resources/views/emails/helpdesk/ticket-created.blade.php, ticket-status-updated.blade.php, ticket-assigned.blade.php | Add email templates for helpdesk notifications                      | ❌       |
| resources/menu/verticalMenu.json                    | Add Helpdesk to main menu                                           | ❌       |

### 3.4 Routing, Config & Notifications

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| routes/web.php, routes/api.php                      | Add new route groups for Helpdesk/Ticketing                         | ❌       |
| app/Notifications/TicketCreatedNotification.php, TicketStatusUpdatedNotification.php, TicketAssignedNotification.php | Add notification classes for helpdesk events                        | ❌       |
| config/motac.php, config/app.php, config/variables.php | Add/update config for Helpdesk module                               | ❌       |

### 3.5 Seeders & Factories

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| database/seeders/TicketCategorySeeder.php, TicketStatusSeeder.php, TicketPrioritySeeder.php | Add seeders for helpdesk categories/priorities/statuses             | ❌       |
| database/factories/TicketFactory.php                | Add factories for helpdesk models                                   | ❌       |

---

## 4. Testing & Validation

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| tests/Feature/Helpdesk/CreateTicketTest.php, tests/Unit/HelpdeskServiceTest.php | Add unit/feature tests for Helpdesk module                          | ❌       |
| tests/Feature/LoanApplicationTest.php               | Test for regression in ICT Loan flows                               | ❌       |
| Manual test checklist                              | Manual testing for ticket creation, assignment, commenting          | ❌       |

---

## 5. Documentation, Deployment & Training

| Related Files/Dirs                                  | Description                                                         | Progress |
|-----------------------------------------------------|---------------------------------------------------------------------|----------|
| README.md, docs/SUMMARY.md, docs/DIRECTORY.md, docs/System_Design_v4.0.md, Technical_Documentation_MOTAC_IRMS_Rev_1.md | Update documentation to reflect new system scope                    | ❌       |
| user_manuals/                                       | Update user manual for Helpdesk usage flow                          | ❌       |
| Training materials                                  | Conduct training for Helpdesk usage                                 | ❌       |
| Staging environment, UAT sign-off                   | Staging deployment and UAT                                          | ❌       |
| Main branch, production server, php artisan migrate --force | Production rollout, migration, and go-live                          | ❌       |
| Documentation, backup scripts                       | Rollback plan and verify backup/restoration procedures              | ❌       |
| Project meeting notes, feedback forms               | Gather feedback from end users and IT support after go-live         | ❌       |

---

## Legend

- ✅ : Completed
- ❌ : Not yet done

---

> **Instructions:**  
> Mark "Progress" as ✅ once the item is completed.  
> Update notes/comment columns if needed as work progresses.  
> Adapt tables for your team’s workflow and circulate as needed.
