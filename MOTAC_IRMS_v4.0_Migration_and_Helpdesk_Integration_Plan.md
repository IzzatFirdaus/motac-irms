# MOTAC Integrated Resource Management System (IRMS) v4.0 Migration & Helpdesk Integration Plan

This document outlines the comprehensive migration plan for refactoring MOTAC IRMS from a dual-purpose (Email/User ID Provisioning & ICT Equipment Loan) system to a focused ICT Equipment Loan and Helpdesk ticketing system.

---

## Objectives

- **Remove Email/User ID Provisioning**: Eliminate all code, database tables, and UI elements related to the Email/User ID Provisioning module.
- **Integrate Helpdesk System**: Add a new module for IT support ticket management.
- **Maintain ICT Equipment Loan**: Ensure the ICT Equipment Loan Management system remains robust and functional.

---

## PHASE 1: Preparation

### 1. Version Control

- Create dedicated branch for the migration:

  ```shell
  git checkout -b release/v4.0
  ```

- Tag the current stable version:

  ```shell
  git tag v3.6-final
  ```

### 2. Backup

- Backup relevant databases: `email_applications` and related tables.
- Backup `.env` configuration.
- Backup file storage: `public/storage/` and `storage/app/public/`.

### 3. Environment Setup

- Prepare a dedicated v4.0 development environment (preferably Dockerized).
- Ensure latest versions of PHP, Composer, Node.js, npm, and database clients are installed.

---

## PHASE 2: Remove Email Application Module

### 1. Audit & Delete Artifacts

**Delete/Modify the following:**

- **Models**:  
  - `App\Models\EmailApplication.php`
  - Remove relationships in `User.php` (e.g., `hasMany(EmailApplication::class)`)
- **Controllers**:  
  - `App\Http\Controllers\EmailApplicationController.php`
  - `App\Http\Controllers\EmailAccountController.php`
  - `App\Http\Controllers\Api\EmailProvisioningController.php`
- **Services**:  
  - `App\Services\EmailApplicationService.php`
  - `App\Services\EmailProvisioningService.php`
- **Livewire Components**:  
  - `App\Livewire\ResourceManagement\MyApplications\Email\*`
  - `App\Livewire\EmailAccount\*`
- **Views**:  
  - `resources/views/email-applications/`
  - `resources/views/emails/application-submitted-notification.blade.php`
- **Routes**:  
  - All routes using `/email-*` in `routes/web.php` and `routes/api.php`
  - Remove Livewire routes for email applications
- **Migrations**:  
  - Drop tables: `email_applications`, `email_accounts`
- **Notifications**:  
  - Email-application-specific classes (e.g., `ApplicationSubmitted.php`, `EmailApplicationNeedsAction.php`)
- **Config & Language**:  
  - Remove email-related keys in `config/motac.php`
  - Remove translation strings under "email" or "email_application" in `resources/lang/en/` and `resources/lang/ms/`
- **Tests**:  
  - Remove feature and unit tests for email provisioning.
- **Menu/UI**:  
  - Update `resources/menu/verticalMenu.json` to remove "Email Provisioning"

### 2. Database Schema Cleanup

- Create migration:

  ```shell
  php artisan make:migration remove_email_provisioning_components
  ```

- In migration `up()`:

  ```php
  Schema::dropIfExists('email_applications');
  Schema::table('users', function (Blueprint $table) {
      foreach (['motac_email', 'user_id_assigned', 'previous_department_name', 'previous_department_email', 'service_status', 'appointment_type'] as $col) {
          if (Schema::hasColumn('users', $col)) {
              $table->dropColumn($col);
          }
      }
  });
  ```

- For `approvals` table (polymorphic):
  - **Option A:** Remove related records:

    ```php
    DB::table('approvals')->where('approvable_type', 'App\Models\EmailApplication')->delete();
    ```

  - **Option B:** Leave for audit (if historical records are needed).

### 3. Refactor Shared Components

- Remove or adjust logic in `User.php`, `ReportController.php`, `NotificationService.php`, and service providers corresponding to EmailApplication, where applicable.

---

## PHASE 3: Integrate Helpdesk Module

### 1. Database Design & Migrations

**Entities and Fields:**

- **tickets**
  - id, user_id, assigned_to_user_id, category_id, priority_id, subject, description, status (enum), due_date, resolution_notes, closed_at, created_by, updated_by, deleted_by, timestamps, deleted_at
- **ticket_categories**
  - id, name, description, is_active, created_by, updated_by, deleted_by, timestamps, deleted_at
- **ticket_priorities**
  - id, name, level, color_code, created_by, updated_by, deleted_by, timestamps, deleted_at
- **ticket_comments**
  - id, ticket_id, user_id, comment, is_internal, created_by, updated_by, deleted_by, timestamps, deleted_at
- **ticket_attachments** (polymorphic)
  - id, attachable_type, attachable_id, file_path, file_name, file_size, file_type, created_by, updated_by, deleted_by, timestamps, deleted_at

**Commands:**

```shell
php artisan make:model Ticket -m
php artisan make:model TicketCategory -m
php artisan make:model TicketPriority -m
php artisan make:model TicketComment -m
php artisan make:model TicketAttachment -m
php artisan migrate
```

### 2. Codebase Additions

- **Models**:  
  - `App\Models\Ticket.php`
  - `App\Models\TicketCategory.php`
  - `App\Models\TicketPriority.php`
  - `App\Models\TicketComment.php`
  - `App\Models\TicketAttachment.php`
- **Controllers**:  
  - `App\Http\Controllers/Helpdesk/TicketController.php` (web)
  - `App\Http\Controllers/Api/HelpdeskApiController.php` (optional)
- **Services**:  
  - `App\Services\HelpdeskService.php` (business logic)
  - `App\Services\TicketNotificationService.php` (notifications)
- **Policies**:  
  - `App\Policies\TicketPolicy.php`
  - Register in `AuthServiceProvider.php`
- **Livewire Components**:  
  - `App\Livewire\Helpdesk\CreateTicketForm.php`
  - `App\Livewire\Helpdesk\MyTicketsIndex.php`
  - `App\Livewire\Helpdesk\TicketDetails.php`
  - `App\Livewire\Helpdesk\Admin\TicketManagement.php`
  - `App\Livewire\Helpdesk\Admin\TicketReport.php`
- **Views**:  
  - `resources/views/livewire/helpdesk/*`
  - `resources/views/helpdesk/index.blade.php`
  - `resources/views/helpdesk/create.blade.php`
  - `resources/views/helpdesk/show.blade.php`
  - `resources/views/helpdesk/admin/index.blade.php`
  - `resources/views/emails/helpdesk/ticket-created.blade.php`
  - `resources/views/emails/helpdesk/ticket-status-updated.blade.php`
  - `resources/views/emails/helpdesk/ticket-assigned.blade.php`
- **Routes** (web):

  ```php
  Route::middleware(['auth', 'verified'])->prefix('helpdesk')->group(function () {
      Route::get('/', App\Livewire\Helpdesk\MyTicketsIndex::class)->name('helpdesk.index');
      Route::get('/create', App\Livewire\Helpdesk\CreateTicketForm::class)->name('helpdesk.create');
      Route::get('/{ticket}', App\Livewire\Helpdesk\TicketDetails::class)->name('helpdesk.show');
      Route::middleware(['role:Admin|IT Admin'])->group(function () {
          Route::get('/admin/tickets', App\Livewire\Helpdesk\Admin\TicketManagement::class)->name('helpdesk.admin.index');
          // Add other admin-specific routes as needed
      });
  });
  ```

- **Notifications**:  
  - `App/Notifications/TicketCreatedNotification.php`
  - `App/Notifications/TicketStatusUpdatedNotification.php`
  - `App/Notifications/TicketAssignedNotification.php`
- **Menu/UI**:  
  - Update `resources/menu/verticalMenu.json` to add "Helpdesk"

### 3. Configuration

- Update `config/motac.php` to add Helpdesk-related keys (e.g., default assignment rules, allowed attachment types, helpdesk email).

---

## PHASE 4: Testing & Validation

### 1. Unit & Feature Tests

- Create tests for Helpdesk module:
  - `tests/Feature/Helpdesk/CreateTicketTest.php`
  - `tests/Unit/HelpdeskServiceTest.php`
- Ensure all ICT Loan tests pass after refactor.
- Manual UAT: Verify all Loan and Helpdesk workflows (creation, assignment, commenting, etc.).

---

## PHASE 5: Finalization & Deployment

### 1. Documentation

- Update:
  - `README.md` (remove email provisioning, add helpdesk)
  - User Manual (Helpdesk flows)
  - System Design v4.0 (new architecture, models, user flow)

### 2. Staging & Production Deployment

- Deploy `release/v4.0` to staging for final UAT.
- On approval, merge to `main`.
- Deploy with:

  ```shell
  php artisan migrate --force
  ```

---

## Summary Table

| Task                           | Outcome          |
|---------------------------------|------------------|
| Email module fully removed      | ✅               |
| Helpdesk module added           | ✅               |
| Database & UI cleaned           | ✅               |
| System focused on Loan & Support| ✅               |

---

## Manual Testing Checklist

| Feature                    | Status      |
|----------------------------|------------|
| ICT Loan Application       | ✅         |
| ICT Loan Approval          | ✅         |
| Equipment Issuance         | ✅         |
| Equipment Return           | ✅         |
| Helpdesk Ticket Creation   | 🔲         |
| Helpdesk Assigning         | 🔲         |
| Helpdesk Commenting        | 🔲         |

---

## Notes & Best Practices

- **Keep commits atomic and meaningful.**
- **Document all changes for future maintenance.**
- **Backup before migration; have a rollback plan.**
- **Test thoroughly at every stage (unit, feature, UAT).**
- **Engage stakeholders for feedback and UAT.**
- **Train users on new Helpdesk workflows.**

---

**For team circulation: If you need this plan in PDF format for distribution, export this Markdown document using your preferred tool.**
