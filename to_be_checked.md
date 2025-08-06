Here is a review of the directories/subdirectories from `DIRECTORY.md` that have **not been explicitly checked or discussed yet** in this Copilot Space:

---

## Not Yet Checked Directories (with Description & File Listing)

---

### /app

#### /app/Models  
**Description:**  
Contains all Eloquent models representing database tables/entities for users, departments, positions, grades, equipment, applications, transactions, approvals, notifications, and settings.

**Files:**  
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

---

#### /app/Http/Controllers  
**Description:**  
Holds all HTTP controller classes responsible for handling requests, business logic, and returning responses/views for web and API endpoints.

**Files:**  
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

---

#### /app/Services  
**Description:**  
Service classes encapsulate business logic for various modules, enabling code reuse and separation of concerns.

**Files:**  
- ApprovalService.php
- EmailApplicationService.php
- EmailProvisioningService.php
- LoanApplicationService.php
- LoanTransactionService.php
- EquipmentService.php
- NotificationService.php

---

#### /app/Policies  
**Description:**  
Policy classes define authorization logic for models, determining which user roles can perform actions on resources.

**Files:**  
- UserPolicy.php
- GradePolicy.php
- EquipmentPolicy.php
- LoanApplicationPolicy.php
- LoanTransactionPolicy.php
- ApprovalPolicy.php

---

#### /app/Observers  
**Description:**  
Model observers for automating common actions (such as tracking who created/updated a record).

**Files:**  
- BlameableObserver.php

---

#### /app/Livewire  
**Description:**  
Livewire components for reactive user interfaces, especially for resource management (applications, approvals), settings, and admin functions.

**Files:**  
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

---

#### /app/Notifications  
**Description:**  
Notification classes for sending alerts on application status changes, equipment events, provisioning results, etc.

**Files:**  
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

---

#### /app/Helpers  
**Description:**  
Utility/helper classes for commonly used functions across the app.

**Files:**  
- Helpers.php

---

### /database

#### /database/migrations  
**Description:**  
Migration files for creating/updating database tables as per Laravel conventions.

**Files:**  
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

---

#### /database/seeders  
**Description:**  
Seeder files for populating tables with initial/demo data.

**Files:**  
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

---

#### /database/factories  
**Description:**  
Factory files for generating fake/test data for models.

**Files:**  
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

### /resources

#### /resources/views  
**Description:**  
Blade template files for the system's user interface, including all page layouts, Livewire component views, partials, emails, and print forms.

**Files:**  
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

#### /resources/lang  
**Description:**  
Language translation files for English and Bahasa Melayu (ms).

**Files:**  
- en/
- ms/
  - [Translation files for Bahasa Melayu and English]

---

### /docs

**Description:**  
Documentation files for system design, technical details, data tables, process flows, and feature implementation.

**Files:**  
- System_Design_Rev_3.6.md
- Technical_Documentation_MOTAC_IRMS_Rev_1.md
- Core_User_&_Organizational_Data_Tables_Mk_2.md
- Loan_System_Flow_Mk_2.md
- Email_Feature_Implementation.md
- Design_Document_Mk_2_MOTAC_IRMS.md

---

### /public

**Description:**  
Publicly accessible assets such as icons, images, CSS, and JS.

**Files:**  
- motac-icon.svg
- motac-header.png
- [Other assets: CSS, JS, images]

---

### /storage

**Description:**  
Storage directory for file uploads, logs, cache, and framework-generated files.

**Files:**  
- [For uploads, logs, etc.]

---

### /.github

**Description:**  
GitHub-specific configuration for issues, workflows, and CI/CD.

**Files:**  
- ISSUE_TEMPLATE/
- workflows/
  - ci.yml
  - deploy.yml

---

**Note:**  
Some files/directories (like config, routes) have already been checked and/or updated. If you want to check any of the above directories in detail (for example, to remove legacy EmailApplication code or add Helpdesk features), please specify which directory or files you want to review or update next.
