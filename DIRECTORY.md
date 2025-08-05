# MOTAC_ICT_LOAN_HRMS — File & Directory Structure

This directory listing is based on the architecture and references in the provided documentation, especially **System_Design_Rev_3.6.md**, **Technical_Documentation_MOTAC_IRMS_Rev_1.md**, and related markdown files.

---

## Root Directory

- README.md
- DIRECTORY.md
- .env.example
- composer.json
- package.json
- artisan
- LICENSE.md
- SECURITY.md
- docker-compose.yml
- .dockerignore
- .gitignore

---

## /app

### /app/Models
- User.php
- Department.php
- Position.php
- Grade.php
- EmailApplication.php
- Equipment.php
- EquipmentCategory.php
- SubCategory.php
- Location.php
- LoanApplication.php
- LoanApplicationItem.php
- LoanTransaction.php
- LoanTransactionItem.php
- Approval.php
- Notification.php
- Setting.php

### /app/Http/Controllers
- Controller.php (base)
- language/LanguageController.php
- WebhookController.php
- Api/EmailProvisioningController.php
- ApprovalController.php
- EmailAccountController.php
- EmailApplicationController.php
- EquipmentController.php
- LoanApplicationController.php
- LoanTransactionController.php
- NotificationController.php
- ReportController.php
- MiscErrorController.php
- Admin/GradeController.php
- Admin/EquipmentController.php
- Admin/DepartmentController.php
- Admin/PositionController.php
- Admin/LocationController.php
- Admin/SettingsController.php

### /app/Services
- ApprovalService.php
- EmailApplicationService.php
- EmailProvisioningService.php
- LoanApplicationService.php
- LoanTransactionService.php
- EquipmentService.php
- NotificationService.php

### /app/Policies
- UserPolicy.php
- GradePolicy.php
- EquipmentPolicy.php
- LoanApplicationPolicy.php
- LoanTransactionPolicy.php
- ApprovalPolicy.php

### /app/Observers
- BlameableObserver.php

### /app/Livewire
- ResourceManagement/
  - EmailAccount/ApplicationForm.php
  - LoanApplication/ApplicationForm.php
  - MyApplications/Loan/Index.php
  - MyApplications/Email/Index.php
  - Approval/Dashboard.php
  - Admin/BPM/ProcessIssuance.php
  - Admin/BPM/ProcessReturn.php
- Settings/
  - Users/Index.php

### /app/Notifications
- ApplicationSubmitted.php
- ApplicationNeedsAction.php
- ApplicationApproved.php
- ApplicationRejected.php
- LoanApplicationReadyForIssuanceNotification.php
- EquipmentIssuedNotification.php
- EquipmentReturnedNotification.php
- EquipmentReturnReminderNotification.php
- EquipmentOverdueNotification.php
- EquipmentIncidentNotification.php
- EquipmentLostNotification.php
- EquipmentDamagedNotification.php
- EmailApplicationReadyForProcessingNotification.php
- EmailProvisionedNotification.php

### /app/Helpers
- Helpers.php

---

## /database

### /database/migrations
- [All migration files for tables referenced in System_Design_Rev_3.6.md:]
  - create_users_table.php
  - create_departments_table.php
  - create_positions_table.php
  - create_grades_table.php
  - create_email_applications_table.php
  - create_equipment_table.php
  - create_equipment_categories_table.php
  - create_sub_categories_table.php
  - create_locations_table.php
  - create_loan_applications_table.php
  - create_loan_application_items_table.php
  - create_loan_transactions_table.php
  - create_loan_transaction_items_table.php
  - create_approvals_table.php
  - create_notifications_table.php
  - create_settings_table.php

### /database/seeders
- UserSeeder.php
- AdminUserSeeder.php
- DepartmentSeeder.php
- PositionSeeder.php
- GradesSeeder.php
- EquipmentCategorySeeder.php
- SubCategoriesSeeder.php
- EquipmentSeeder.php
- LocationSeeder.php
- LoanApplicationSeeder.php
- LoanTransactionSeeder.php
- ApprovalSeeder.php
- NotificationSeeder.php
- SettingsSeeder.php
- RoleAndPermissionSeeder.php

### /database/factories
- UserFactory.php
- EquipmentCategoryFactory.php
- SubCategoryFactory.php
- EquipmentFactory.php
- LocationFactory.php
- LoanApplicationFactory.php
- LoanApplicationItemFactory.php
- LoanTransactionFactory.php
- LoanTransactionItemFactory.php
- ApprovalFactory.php
- NotificationFactory.php
- SettingFactory.php

---

## /resources

### /resources/views
- welcome.blade.php
- dashboard.blade.php
- layouts/
  - app.blade.php
  - admin.blade.php
- livewire/
  - resource-management/
    - loan-application/application-form.blade.php
    - my-applications/loan/index.blade.php
    - email-account/application-form.blade.php
    - my-applications/email/index.blade.php
    - approval/dashboard.blade.php
    - admin/bpm/process-issuance.blade.php
    - admin/bpm/process-return.blade.php
  - settings/
    - users/index.blade.php
- loan_transactions/
  - issue.blade.php
  - return.blade.php
- approvals/
  - pending.blade.php
  - history.blade.php
  - show.blade.php
- notifications/
  - index.blade.php
- reports/
  - equipment.blade.php
  - email-accounts.blade.php
- emails/
  - equipment-returned.blade.php
  - application-status.blade.php
  - email-provisioned.blade.php
- loan-applications/pdf/
  - print-form.blade.php
- components/
  - approval-status-badge.blade.php
  - user-info-card.blade.php
  - [Other reusable components]

---

## /config

- app.php
- motac.php
- mail.php
- database.php
- fortify.php
- jetstream.php
- broadcast.php

---

## /routes

- web.php
- api.php
- channels.php

---

## /public

- motac-icon.svg
- motac-header.png
- [Other assets: CSS, JS, images]

---

## /resources/lang

- en/
- ms/
  - [Translation files for Bahasa Melayu and English]

---

## /docs

- System_Design_Rev_3.6.md
- Technical_Documentation_MOTAC_IRMS_Rev_1.md
- Core_User_&_Organizational_Data_Tables_Mk_2.md
- Loan_System_Flow_Mk_2.md
- Email_Feature_Implementation.md
- Design_Document_Mk_2_MOTAC_IRMS.md

---

## Additional Files/Directories (as referenced)

- .github/
  - ISSUE_TEMPLATE/
  - workflows/
    - ci.yml
    - deploy.yml

- /storage
  - [For uploads, logs, etc.]

---

## Notes

- All table, model, controller, seeder, and factory files are named and organized as per Laravel conventions and the referenced documentation.
- If any module is extended (e.g., reporting, user settings), expect additional controllers, views, or Livewire components in their respective directories.
- Dropdown values and options for select fields are referenced in supplementary markdown files and populated via seeders or enums.
- For PDF export, see print-form.blade.php under loan-applications/pdf/.

---

**This structure reflects the current MOTAC IRMS system and is updated to match the latest shared documentation (August 2025).**
