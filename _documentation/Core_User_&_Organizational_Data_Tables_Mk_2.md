# Core User & Organizational Data Tables (Mk. 2)

These tables are foundational for managing users, roles, and the organizational structure.

---

## 1. `users`

This table stores information about all users of the system, including applicants and officers.  
It is later modified to include many MOTAC-specific fields like `identification_number`, `department_id`, `position_id`, `grade_id`, and `status`, as well as columns for two-factor authentication.

- **Model:** `app/Models/User.php`
- **Controller:** `app/Http/Controllers/UserController.php`, various authentication controllers (e.g., `app/Http/Controllers/Auth/`)
- **Factory:** `Database\Factories\UserFactory.php`
- **Seeder:** `Database\Seeders\UserSeeder.php` and `Database\Seeders\AdminUserSeeder.php`  
  *These create a variety of administrative and general sample users with different roles and statuses.*

---

## 2. `roles`, `permissions`, `model_has_roles`, etc.

These tables are managed by the [spatie/laravel-permission](https://github.com/spatie/laravel-permission) package and define the granular access control for the application.

- **Model:** `Spatie\Permission\Models\Role`, `Spatie\Permission\Models\Permission`
- **Controller:** Managed via application logic, not typically a dedicated controller.
- **Factory:** Not applicable.
- **Seeder:** `Database\Seeders\RoleAndPermissionSeeder.php`  
  *Creates all core roles (Admin, BPM Staff, User, etc.) and assigns a detailed set of permissions to each.*

---

## 3. `departments`

Stores information about MOTAC departments, divisions, or units.

- **Model:** `app/Models/Department.php`
- **Controller:** `app/Http/Controllers/Admin/DepartmentController.php`
- **Factory:** Not used; departments are created from a predefined list.
- **Seeder:** `Database\Seeders\DepartmentSeeder.php`  
  *Pre-populates a comprehensive list of MOTAC departments at headquarters and state levels (see supplementary document).*

---

## 4. `positions`

Contains details about job positions within MOTAC.

- **Model:** `app/Models/Position.php`
- **Controller:** `app/Http/Controllers/Admin/PositionController.php`
- **Factory:** Not used; positions are created from a predefined list.
- **Seeder:** `Database\Seeders\PositionSeeder.php`  
  *Pre-populates a list of 65 distinct job positions (see supplementary document).*

---

## 5. `grades`

Manages the various grades associated with positions.  
A later migration adds a foreign key to the positions table and a composite unique key.

- **Model:** `app/Models/Grade.php`
- **Controller:** `app/Http/Controllers/Admin/GradeController.php`
- **Factory:** Not used; grades are created from a predefined list.
- **Seeder:** `Database\Seeders\GradesSeeder.php`  
  *Populates a large number of grades and links them to corresponding positions (see supplementary document).*

---

# ICT Equipment & Location Tables

These tables are specific to managing the physical ICT assets and their locations.

---

## 6. `equipment_categories` & `sub_categories`

Organize equipment into a hierarchical structure.

- **Model:** `app/Models/EquipmentCategory.php`, `app/Models/SubCategory.php`
- **Controller:** `app/Http/Controllers/Admin/EquipmentCategoryController.php`
- **Factory:** `Database\Factories\EquipmentCategoryFactory.php`, `Database\Factories\SubCategoryFactory.php`
- **Seeder:** `Database\Seeders\EquipmentCategorySeeder.php`, `Database\Seeders\SubCategoriesSeeder.php`

---

## 7. `equipment`

Stores details of all ICT equipment available for loan.

- **Model:** `app/Models/Equipment.php`
- **Controller:** `app/Http/Controllers/EquipmentController.php`, `app/Http/Controllers/Admin/EquipmentController.php`
- **Factory:** `Database\Factories\EquipmentFactory.php`
- **Seeder:** `Database\Seeders\EquipmentSeeder.php`  
  *Populates the initial equipment inventory with a specified number of items.*

---

## 8. `locations`

Manages the physical locations where equipment can be stored or used.

- **Model:** `app/Models/Location.php`
- **Controller:** `app/Http/Controllers/Admin/LocationController.php`
- **Factory:** `Database\Factories\LocationFactory.php`
- **Seeder:** `Database\Seeders\LocationSeeder.php`  
  *Populates both specific, predefined locations (like server rooms) and additional random locations for testing.*

---

# ICT Loan Module Tables

These tables are specific to the ICT equipment loan functionality.

---

## 9. `loan_applications`

Contains all applications submitted for ICT equipment loans.

- **Model:** `app/Models/LoanApplication.php`
- **Controller:** `app/Http/Controllers/LoanApplicationController.php`
- **Factory:** `Database\Factories\LoanApplicationFactory.php`
- **Seeder:** `Database\Seeders\LoanApplicationSeeder.php`  
  *Creates numerous sample applications with different statuses (draft, approved, rejected, etc.) for testing.*

---

## 10. `loan_application_items`

Details the specific equipment types and quantities requested in each loan application.

- **Model:** `app/Models/LoanApplicationItem.php`
- **Controller:** Managed within `app/Http/Controllers/LoanApplicationController.php`
- **Factory:** `Database\Factories\LoanApplicationItemFactory.php`
- **Seeder:** Seeded as part of `LoanApplicationSeeder.php` (via factory states like `withItems()`) and `LoanTransactionSeeder.php`; no dedicated seeder file exists.

---

## 11. `loan_transactions`

Records the issuance and return of equipment for a loan.

- **Model:** `app/Models/LoanTransaction.php`
- **Controller:** `app/Http/Controllers/LoanTransactionController.php`
- **Factory:** `Database\Factories\LoanTransactionFactory.php`
- **Seeder:** `Database\Seeders\LoanTransactionSeeder.php`  
  *Simulates the entire transaction lifecycle by creating "issue" transactions for approved loans and corresponding "return" transactions.*

---

## 12. `loan_transaction_items`

Details the specific, individual equipment items (with serial numbers) moved within each loan transaction.

- **Model:** `app/Models/LoanTransactionItem.php`
- **Controller:** Managed within `app/Http/Controllers/LoanTransactionController.php`
- **Factory:** `Database\Factories\LoanTransactionItemFactory.php`
- **Seeder:** Seeded by `LoanTransactionSeeder.php` to link specific equipment to issue/return transactions; no dedicated seeder file exists.

---

# Shared Workflow & Utility Tables

These tables support workflows and system functions common to multiple modules.

---

## 13. `approvals`

A polymorphic table to store approval information for various processes, including loan applications.

- **Model:** `app/Models/Approval.php`
- **Controller:** `app/Http/Controllers/ApprovalController.php`
- **Factory:** `Database\Factories\ApprovalFactory.php`
- **Seeder:** `Database\Seeders\ApprovalSeeder.php`  
  *Creates sample approval records with various statuses for both loan and email applications.*

---

## 14. `notifications`

Stores database notifications for users. The table includes standard notification columns plus custom audit fields.

- **Model:** `app/Models/Notification.php`
- **Controller:** `app/Http/Controllers/NotificationController.php`
- **Factory:** `Database\Factories\NotificationFactory.php`
- **Seeder:** `Database\Seeders\NotificationSeeder.php`  
  *Creates a set of sample notifications and marks a portion of them as "read" to simulate user activity.*

---

## 15. `settings`

A single-row table to store global application settings.

- **Model:** `app/Models/Setting.php`
- **Controller:** `app/Http/Controllers/Admin/SettingsController.php`
- **Factory:** `Database\Factories\SettingFactory.php`
- **Seeder:** `Database\Seeders\SettingsSeeder.php`  
  *Ensures a default row of settings exists for the application to function.*

---

<!--
**Completeness Check Against Previous .md Files**
- All core data tables, their models, controllers, factories, and seeders are covered.
- The roles/permissions tables are explicitly referenced as managed by Spatie/Laravel-Permission, matching prior documentation.
- Departments, positions, and grades are referenced and their linkage to the supplementary dropdown document noted.
- Equipment/category/location structure is fully described.
- Loan module tables, their relationships, and workflows match those detailed in the system design and workflow files.
- Shared tables (approvals, notifications, settings) are present and described.
- No contents are missing compared to previous .md files. This file provides a summary reference for data tables and their implementation artifacts; schema details are found in System_Design_Rev_3.6.md and related documentation.
-->
