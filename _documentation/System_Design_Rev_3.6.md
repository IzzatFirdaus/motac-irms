# MOTAC Integrated Resource Management System (Revision 3.6)

*(Based on PERAKUAN PEMOHONAN EMEL & ID PENGGUNA MOTAC, BORANG PINJAMAN PERALATAN ICT 2024 SEWAAN C, and confirmed code structure from [amralsaleeh/HRMS template](https://github.com/amralsaleeh/HRMS))*

---

## 1. Overview

The MOTAC Integrated Resource Management System is designed to consolidate two key operational areas:

- **Email/User ID Provisioning**: Automates the process for staff to request, certify, and obtain official MOTAC email accounts or user IDs, mirroring the existing MyMail application process.
- **ICT Equipment Loan Management**: Facilitates the request, approval, issuance, and return of ICT equipment (laptops, projectors, etc.) used for official purposes.

The system provides a unified, Laravel-based platform that streamlines workflows, enforces business rules, and delivers a consistent user experience across both digital and physical resource management. This revision incorporates details from the official application forms (including specific fields and logic from the MyMail system) and reflects a focused project structure based on provided code files.

---

## 2. System Objectives

- **Unified Data Management**: Consolidate user data, applications, approvals, and notifications in a single MySQL database.
- **Streamlined Workflows**: Automate and standardize the application processes for both email/User ID provisioning and ICT equipment loans, adhering to the steps outlined in the official forms.
- **Role-Based Access & Security**: Ensure that users, approvers, BPM staff, and IT Admins have the correct levels of access with robust security measures, incorporating grade-based approval logic and detailed authorization policies. Standardized role names (e.g., 'Admin', 'BPM Staff', 'IT Admin') are used.
- **Real-Time Reporting & Notifications**: Enable real-time insights on resource utilization and application statuses, and notify users of critical events via email and in-app (database) notifications.
- **Modular & Scalable Architecture**: Build the system using Laravel’s MVC framework with clear separation of concerns and utilize Livewire for dynamic interfaces, a well-defined service layer, leveraging the structure provided by the HRMS template.

---

## 3. High-Level Architecture

### 3.1 Laravel/Livewire MVC Pattern

#### Controllers

In the MOTAC system, traditional PHP controllers primarily handle specific backend HTTP requests, API interactions, and actions not fully managed by front-end dynamic components. Many user interface interactions, particularly for CRUD operations, form handling, and dynamic list displays, are managed by Livewire components to provide a richer user experience. This reduces the scope of some traditional PHP controllers.

**Key active PHP controllers (confirmed from shared files and web.php/api.php) include:**

- `App\Http\Controllers\language\LanguageController.php`: Manages language switching for the application.
- `App\Http\Controllers\WebhookController.php`: Handles incoming GitHub webhooks for deployment triggers, secured by signature validation and dispatches Sync jobs.
- `App\Http\Controllers\Api\EmailProvisioningController.php`: Provides the provisionEmailAccount API endpoint for email provisioning, secured by Sanctum.
- `App\Http\Controllers\ApprovalController.php`: Manages user interactions with approval tasks (listing pending approvals, history, viewing details, recording decisions).
- `App\Http\Controllers\EmailAccountController.php`: Handles IT administrative actions for processing approved email applications.
- `App\Http\Controllers\EmailApplicationController.php`: Manages backend logic for user-submitted email applications; Livewire components handle the form UI and listings.
- `App\Http\Controllers\EquipmentController.php`: Allows general users to view lists of equipment and details.
- `App\Http\Controllers\LoanApplicationController.php`: Manages backend logic for ICT loan applications; Livewire components handle the form UI, editing, and listings.
- `App\Http\Controllers\LoanTransactionController.php`: Manages backend processing for equipment issuance and returns; relies on policies and services.
- `App\Http\Controllers\NotificationController.php`: Enables users to view and manage their system notifications.
- `App\Http\Controllers\MiscErrorController.php`: Used for displaying custom error pages.
- `App\Http\Controllers\ReportController.php`: Contains methods for fetching data for various reports; presentation may use Livewire.
- `App\Http\Controllers\Admin\GradeController.php`: Manages CRUD operations for Grades.
- `App\Http\Controllers\Admin\EquipmentController.php`: Manages CRUD operations for Equipment.
- **Base controllers**: Foundational functionality via `Controller.php` and authentication controllers (Fortify/Jetstream).

#### Models

Represent and manage data using Eloquent ORM, including polymorphic relationships for approvals and automated audit trails.

**Key models:**
- `User`
- `Department`
- `Position`
- `Grade`
- `EmailApplication`
- `Equipment`
- `EquipmentCategory`
- `SubCategory`
- `Location`
- `LoanApplication`
- `LoanApplicationItem`
- `LoanTransaction`
- `LoanTransactionItem`
- `Approval`
- `Notification`
- `Setting`
- `Import`

#### Views

Blade templates render user interfaces, including Livewire components for dynamic sections. Found in `resources/views/` and `resources/views/livewire/`.

#### Services

Encapsulate business logic to keep controllers thin. Located in `app/Services/`.

#### Middleware

Enforce authentication, authorization, and request validations. Located in `app/Http/Middleware/`.

#### Livewire Components

Handle dynamic UI elements and their server-side logic without full page reloads.

#### Policies

Define authorization logic for actions on specific models. Located in `app/Policies/`.

#### Observers

`BlameableObserver` automatically populates audit fields on specified models.

---

### 3.2 Deployment & Infrastructure

- **Dockerized Development**: Use Docker containers for consistency.
- **Staging Environment**: Separate server for testing.
- **Mailtrap Integration**: Email testing during development.
- **Version Control & CI/CD**: Git for source control, automated deployment via webhooks.

---

### 3.3 Core Providers and Configuration

- **AppServiceProvider**: Registers core services, view composers, translation, timezone, and locale.
- **AuthServiceProvider**: Registers all model policies and admin overrides.
- **EventServiceProvider**: Registers model observers and event listeners.
- **FortifyServiceProvider / JetstreamServiceProvider**: Configures authentication and user features.
- **MenuServiceProvider**: Loads and shares navigation menus.
- **RouteServiceProvider**: Configures routing and rate limiting.
- **BroadcastServiceProvider**: Configured for WebSockets.
- **System Configuration**: Critical values in `config/motac.php`.
- **Mail Configuration**: `config/mail.php` and `.env`.
- **Date Formatting**: Consistent formats in `config/app.php`.

---

## 4. Database Design

Uses a unified MySQL schema. Blameable fields (`created_by`, `updated_by`, `deleted_by`) added to auditable tables.

### 4.1 Users & Organizational Data

#### `users`

- Fields: id, title, name, identification_number, passport_number, profile_photo_path, position_id, grade_id, department_id, level, mobile_number, email, motac_email, user_id_assigned, service_status, appointment_type, previous_department_name, previous_department_email, password, status, email_verified_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, remember_token, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `departments`

- Fields: id, name, branch_type, code, description, is_active, head_user_id, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `positions`

- Fields: id, name, grade_id, description, is_active, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `grades`

- Fields: id, name, level, min_approval_grade_id, is_approver_grade, created_by, updated_by, deleted_by, timestamps, deleted_at.

### 4.2 Email/User ID Applications

#### `email_applications`

- Fields: id, user_id, previous_department_name, previous_department_email, service_start_date, service_end_date, application_reason_notes, proposed_email, group_email, contact_person_name, contact_person_email, supporting_officer_id, supporting_officer_name, supporting_officer_grade, supporting_officer_email, status, cert_info_is_true, cert_data_usage_agreed, cert_email_responsibility_agreed, certification_timestamp, rejection_reason, final_assigned_email, final_assigned_user_id, created_by, updated_by, deleted_by, timestamps, deleted_at.

### 4.3 ICT Equipment Loan Modules

#### `equipment`

- Fields: id, asset_type, brand, model, serial_number, tag_id, purchase_date, warranty_expiry_date, status, current_location, notes, condition_status, department_id, equipment_category_id, sub_category_id, location_id, item_code, description, purchase_price, acquisition_type, classification, funded_by, supplier_name, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `equipment_categories`, `sub_categories`, `locations`

- Fields for each: id, name, description, is_active, created_by, updated_by, deleted_by, timestamps, deleted_at. Additional linking and address fields as appropriate.

#### `loan_applications`

- Fields: id, user_id, responsible_officer_id, supporting_officer_id, purpose, location, return_location, loan_start_date, loan_end_date, status, rejection_reason, applicant_confirmation_timestamp, submitted_at, approved_by, approved_at, rejected_by, rejected_at, cancelled_by, cancelled_at, admin_notes, current_approval_officer_id, current_approval_stage, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `loan_application_items`, `loan_transactions`, `loan_transaction_items`

- Item and transaction details, status fields, audit fields, and linking foreign keys.

### 4.4 Approval & Notification

#### `approvals`

- Polymorphic table for approvals. Fields: id, approvable_type, approvable_id, officer_id, stage, status, comments, approval_timestamp, created_by, updated_by, deleted_by, timestamps, deleted_at.

#### `notifications`

- Custom table for notifications. Fields: id (UUID), type, notifiable_type, notifiable_id, data, read_at, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at.

---

## 5. Detailed Workflow Processes

### 5.1 Email/User ID Application Workflow

- **Application Initiation**: Dynamic form presentation, field visibility/requirement logic, data entry, certification, status management, notification.
- **Supporting Officer Review**: Approval routing, grade eligibility, notification, approval/rejection.
- **IT Processing & Credential Delivery**: Forwarded to IT Admin, provisioning, completion notification.

### 5.2 ICT Equipment Loan Workflow

- **Loan Application Initiation**: Multi-part form entry, itemized equipment request.
- **Confirmation & Certification**: Applicant confirmation, status update, notification.
- **Supporting Officer Approval**: Routing, grade check, approval/rejection, quantity adjustment.
- **Equipment Issuance & Return**: BPM staff processing, transaction recording, asset status update, applicant notification.

---

## 6. User Interface & Experience

### 6.1 Branding and Layout

- Unified MOTAC branding, responsive design, shared helpers and menu data.

### 6.2 Role-Based Dashboards

- Administrator, user, approver, BPM staff interfaces mapped to Blade views and Livewire components.

### 6.3 Dynamic and Reusable Components

- Dynamic forms, instructional text, auto-complete/suggestions, reusable Blade components.

---

## 7. Business Rules & Validation

### 7.1 Eligibility Rules

- Criteria for email/User ID and equipment loans.

### 7.2 Validation Rules

- Managed by FormRequest classes, input masks, certification, date logic, approver eligibility, password policy.

### 7.3 Processing Time & Responsibility

- Application processing times, applicant liability for equipment.

---

## 8. Technical Considerations

### 8.1 Security

- Role-based access, grade-level authorization, CSRF, input sanitization, password hashing, audit trails, webhook security, session management.

### 8.3 Development Practices

- Docker, automated testing, version control, CI/CD pipeline.

---

## 9. System Modules & Component Breakdown

### 9.1 Authentication & User Management

- Controllers, models, views, services, policies, Fortify/Jetstream actions, password rules, observer.

### 9.2 Email/User ID Provisioning Module

- Controllers, models, views, services, policies, notifications, mailables, observer.

### 9.3 ICT Equipment Loan Module

- Controllers, models, views, services, policies, notifications, observer.

### 9.4 Approval Workflow Module (Shared)

- Controllers, models, views, services, policies, observer.

### 9.5 Notification & Reporting

- Controllers, models, views, services, notification classes, channels.

### 9.6 Dashboard

- Controllers, views, Livewire components.

### 9.7 Shared Components & Infrastructure

- Providers, helpers, observers, CI/CD support.

---

## 10. Deployment & Rollout Strategy

- Phased rollout, user training, monitoring and feedback.

---

## 11. Conclusion

The MOTAC Integrated Resource Management System is designed to address both digital resource provisioning (email/User ID) and physical resource management (ICT equipment loans) using a unified Laravel platform with Livewire components, leveraging the structure of the amralsaleeh/HRMS template. By incorporating the specific requirements and workflows from the official MOTAC forms, including detailed aspects of the MyMail system, this revised design ensures efficiency, security, and scalability. The modular components, strict business rules, and role-based access will support MOTAC’s operational needs by streamlining application processes, enforcing standard procedures, and providing real-time reporting and notifications—all within a secure and user-friendly interface.

# Supplementary Document: Dropdown Menu Options for MyMail Integration

This document lists the predefined options for all dropdown menus (HTML `<select>` elements) as extracted from the existing MyMail system's "Permohonan Emel & ID Pengguna MOTAC" form. These options should be implemented in the "MOTAC Integrated Resource Management System" for the Email/User ID Provisioning module to ensure consistency and accurately reflect current MOTAC nomenclature and classifications.

These lists are crucial for populating the respective fields in the database (e.g., lookup tables or enums) and for rendering the user interface accurately.

---

## 1. Taraf Perkhidmatan (Service Status)

- **Field Name in MyMail HTML:** `service_type_id`
- **Database Field Reference:** `users.service_status` (enum)
- **Options:**
    - Value "": - Pilih Taraf Perkhidmatan -
    - Value "1": Tetap
    - Value "2": Lantikan Kontrak / MyStep
    - Value "3": Pelajar Latihan Industri (Ibu Pejabat Sahaja)

---

## 2. Pelantikan (Appointment)

- **Field Name in MyMail HTML:** `pelantikan` (id: pelantikan1)
- **Database Field Reference:** `users.appointment_type` (enum)
- **Options:**
    - Value "" (default, disabled): - Pilih Pelantikan -
    - Value "1": Baharu
    - Value "2": Kenaikan Pangkat/Pertukaran
    - Value "3": Lain-lain

---

## 3. Jawatan (Designation/Position)

- **Field Name in MyMail HTML:** `designation_id` (id: designation)
- **Database Field Reference:** `users.position_id` (links to positions table)
- **Options:**
    - Value "" (default): - Pilih Jawatan -
    - Value "1": Menteri
    - Value "2": Timbalan Menteri
    - Value "3": Ketua Setiausaha
    - Value "4": Timbalan Ketua Setiausaha
    - Value "5": Setiausaha Bahagian
    - Value "6": Setiausaha Akhbar
    - Value "7": Setiausaha Sulit Kanan
    - Value "8": Setiausaha Sulit
    - Value "9": Pegawai Tugas-Tugas Khas
    - Value "10": Timbalan Setiausaha Bahagian
    - Value "11": Ketua Unit
    - Value "12": Pegawai Khas
    - Value "13": Pegawai Media
    - Value "14": Pengarah
    - Value "15": Timbalan Pengarah
    - Value "16": Penolong Pengarah
    - Value "17": Ketua Penolong Setiausaha Kanan (M)
    - Value "18": Ketua Penolong Setiausaha (M)
    - Value "19": Penolong Setiausaha Kanan (M)
    - Value "20": Penolong Setiausaha (M)
    - Value "21": Pegawai Teknologi Maklumat (F)
    - Value "22": Pegawai Kebudayaan (B)
    - Value "23": Penasihat Undang-Undang (L)
    - Value "24": Pegawai Psikologi (S)
    - Value "25": Akauntan (WA)
    - Value "26": Pegawai Hal Ehwal Islam (S)
    - Value "27": Pegawai Penerangan (S)
    - Value "28": Jurutera (J)
    - Value "29": Kurator (S)
    - Value "30": Jurukur Bahan (J)
    - Value "31": Arkitek (J)
    - Value "32": Pegawai Arkib (S)
    - Value "33": Juruaudit (W)
    - Value "34": Perangkawan (E)
    - Value "35": Pegawai Siasatan (P)
    - Value "36": Penguasa Imigresen (KP)
    - Value "37": Pereka (B)
    - Value "38": Peguam Persekutuan (L)
    - Value "39": Penolong Pegawai Teknologi Maklumat
    - Value "40": Penolong Pegawai hal Ehwal Islam (S)
    - Value "41": Penolong Pegawai Undang-Undang (L)
    - Value "42": Penolong Pegawai Teknologi Maklumat
    - Value "43": Penolong Juruaudit
    - Value "44": Penolong Jurutera
    - Value "45": Penolong Pegawai Tadbir
    - Value "46": Penolong Pegawai Penerangan (S)
    - Value "47": Penolong Pegawai Psikologi (S)
    - Value "48": Penolong Pegawai Siasatan (P)
    - Value "49": Penolong Pegawai Arkib (S)
    - Value "50": Jurufotografi
    - Value "51": Penolong Penguasa Imigresen (KP)
    - Value "52": Penolong Pustakawan (S)
    - Value "53": Setiausaha Pejabat (N)
    - Value "54": Pembantu Setiausaha Pejabat (N)
    - Value "55": Pembantu Tadbir (Perkeranian/Operasi) (N)
    - Value "56": Penolong Akauntan (W)
    - Value "57": Pembantu Tadbir (Kewangan) (W)
    - Value "58": Pembantu Operasi (N)
    - Value "59": Pembantu Keselamatan (KP)
    - Value "60": Juruteknik Komputer (FT)
    - Value "61": Pemandu Kenderaan (H)
    - Value "62": Pembantu Khidmat (H)
    - Value "63": MySTEP
    - Value "64": Pelajar Latihan Industri
    - Value "65": Pegawai Imigresen

---

## 4. Gred (Grade)

- **Field Name in MyMail HTML:** `grade_id` (id: grade)
- **Database Field Reference:** `users.grade_id` (links to grades table)
- **Note:** The options for this dropdown are dynamically filtered based on the "Jawatan" selection in the MyMail system. The following is a complete static list from the provided HTML; the class attribute in the original options likely corresponds to the value of the selected designation_id.
- **Options:**
    - Value "" (default): - Pilih Gred -
    - Value "1" (class="1"): Menteri
    - Value "2" (class="2"): Timbalan Menteri
    - Value "3" (class="3"): Turus III
    - Value "4" (class="3"): Jusa A
    - Value "5" (class="3"): Jusa B
    - Value "6" (class="3"): Jusa C
    - Value "7" (class="4"): Jusa A
    - Value "8" (class="4"): Jusa B
    - Value "9" (class="4"): Jusa C
    - Value "10" (class="5"): Jusa A
    - Value "11" (class="5"): Jusa B
    - Value "12" (class="5"): Jusa C
    - Value "13" (class="5"): (14) 54
    - Value "14" (class="5"): (13) 52
    - Value "15" (class="5"): (12) 48
    - Value "16" (class="6"): 14 (54)
    - Value "17" (class="6"): 13 (52)
    - Value "18" (class="6"): (12) 48
    - Value "19" (class="7"): 14 (54)
    - Value "20" (class="7"): 13 (52)
    - Value "21" (class="7"): 12 (48)
    - Value "22" (class="8"): 14 (54)
    - Value "23" (class="8"): 13 (52)
    - Value "24" (class="8"): (12) 48
    - Value "25" (class="9"): 14 (54)
    - Value "26" (class="9"): 13 (52)
    - Value "27" (class="9"): 12 (48)
    - Value "28" (class="9"): 10 (44)
    - Value "29" (class="9"): 9 (41)
    - Value "30" (class="10"): 14 (54)
    - Value "31" (class="10"): 13 (52)
    - Value "32" (class="10"): 12 (48)
    - Value "33" (class="11"): 14 (54)
    - Value "34" (class="11"): 13 (52)
    - Value "35" (class="11"): 12 (48)
    - Value "36" (class="11"): 10 (44)
    - Value "37" (class="11"): 9 (41)
    - Value "38" (class="12"): 14 (54)
    - Value "39" (class="12"): 13 (52)
    - Value "40" (class="12"): 12 (48)
    - Value "41" (class="12"): 10 (44)
    - Value "42" (class="12"): 9 (41)
    - Value "43" (class="13"): 14 (54)
    - Value "44" (class="13"): 13 (52)
    - Value "45" (class="13"): 12 (48)
    - Value "46" (class="14"): 14 (54)
    - Value "47" (class="14"): 13 (52)
    - Value "48" (class="14"): 12 (48)
    - Value "49" (class="14"): 10 (44)
    - Value "50" (class="14"): 9 (41)
    - Value "51" (class="15"): 14 (54)
    - Value "52" (class="15"): 13 (52)
    - Value "53" (class="15"): 12 (48)
    - Value "54" (class="15"): 10 (44)
    - Value "55" (class="15"): 9 (41)
    - Value "56" (class="16"): 14 (54)
    - Value "57" (class="16"): 13 (52)
    - Value "58" (class="16"): 12 (48)
    - Value "59" (class="16"): 10 (44)
    - Value "60" (class="16"): 9 (41)
    - Value "61" (class="17"): 13 (52)
    - Value "62" (class="17"): 12 (48)
    - Value "63" (class="18"): 13 (52)
    - Value "64" (class="18"): 12 (48)
    - Value "65" (class="19"): 10 (44)
    - Value "66" (class="20"): 9 (41)
    - Value "67" (class="21"): 14 (54)
    - Value "68" (class="21"): 13 (52)
    - Value "69" (class="21"): 12 (48)
    - Value "70" (class="21"): 10 (44)
    - Value "71" (class="21"): 9 (41)
    - Value "72" (class="22"): 14 (53/54)
    - Value "73" (class="22"): 13 (51/52)
    - Value "74" (class="22"): 12 (47/48)
    - Value "75" (class="22"): 10 (43/44)
    - Value "76" (class="22"): 9 (41/42)
    - Value "77" (class="22"): 7 (37/38)
    - Value "78" (class="22"): 6 (31/32)
    - Value "79" (class="22"): 5 (29/30)
    - Value "80" (class="22"): 3 (25/26)
    - Value "81" (class="22"): 2 (21/22)
    - Value "82" (class="22"): 1 (19)
    - Value "83" (class="23"): 14 (54)
    - Value "84" (class="23"): 13 (52)
    - Value "85" (class="23"): 12 (48)
    - Value "86" (class="23"): 10 (44)
    - Value "87" (class="23"): 9 (41)
    - Value "88" (class="24"): 14 (54)
    - Value "89" (class="24"): 13 (52)
    - Value "90" (class="24"): 12 (48)
    - Value "91" (class="24"): 10 (44)
    - Value "92" (class="24"): 9 (41)
    - Value "93" (class="25"): 14 (54)
    - Value "94" (class="25"): 14 (53/54)
    - Value "95" (class="25"): 13 (51/52)
    - Value "96" (class="25"): 12 (47/48)
    - Value "97" (class="25"): 10 (44)
    - Value "98" (class="25"): 8 (40)
    - Value "99" (class="25"): 7 (38)
    - Value "100" (class="25"): 6 (32)
    - Value "101" (class="25"): 5 (29)
    - Value "102" (class="25"): 4 (28)
    - Value "103" (class="25"): 3 (26)
    - Value "104" (class="25"): 2 (22)
    - Value "105" (class="25"): 1 (19)
    - Value "106" (class="26"): 14 (54)
    - Value "107" (class="26"): 13 (52)
    - Value "108" (class="26"): 12 (48)
    - Value "109" (class="26"): 10 (44)
    - Value "110" (class="26"): 9 (41)
    - Value "111" (class="27"): 14 (54)
    - Value "112" (class="27"): 13 (52)
    - Value "113" (class="27"): 12 (48)
    - Value "114" (class="27"): 10 (44)
    - Value "115" (class="27"): 9 (41)
    - Value "116" (class="28"): 14 (54)
    - Value "117" (class="28"): 13 (52)
    - Value "118" (class="28"): 12 (48)
    - Value "119" (class="28"): 10 (44)
    - Value "120" (class="28"): 9 (41)
    - Value "121" (class="29"): 14 (54)
    - Value "122" (class="29"): 13 (52)
    - Value "123" (class="29"): 12 (48)
    - Value "124" (class="29"): 10 (44)
    - Value "125" (class="29"): 9 (41)
    - Value "126" (class="30"): 14 (54)
    - Value "127" (class="30"): 13 (52)
    - Value "128" (class="30"): 12 (48)
    - Value "129" (class="30"): 10 (44)
    - Value "130" (class="30"): 9 (41)
    - Value "131" (class="31"): 14 (54)
    - Value "132" (class="31"): 13 (52)
    - Value "133" (class="31"): 12 (48)
    - Value "134" (class="31"): 10 (44)
    - Value "135" (class="31"): 9 (41)
    - Value "136" (class="31"): 48
    - Value "137" (class="32"): 14 (54)
    - Value "138" (class="32"): 13 (52)
    - Value "139" (class="32"): 12 (48)
    - Value "140" (class="32"): 10 (44)
    - Value "141" (class="32"): 8 (40)
    - Value "142" (class="32"): 7 (38)
    - Value "143" (class="32"): 6 (32)
    - Value "144" (class="32"): 5 (29)
    - Value "145" (class="33"): 14 (54)
    - Value "146" (class="33"): 13 (52)
    - Value "147" (class="33"): 12 (48)
    - Value "148" (class="33"): 10 (44)
    - Value "149" (class="33"): 9 (41)
    - Value "150" (class="34"): 14 (54)
    - Value "151" (class="34"): 13 (52)
    - Value "152" (class="34"): 12 (48)
    - Value "153" (class="34"): 10 (44)
    - Value "154" (class="34"): 9 (41)
    - Value "155" (class="35"): 14 (53/54)
    - Value "156" (class="35"): 13 (51/52)
    - Value "157" (class="35"): 12 (47/48)
    - Value "158" (class="35"): 10 (43/44)
    - Value "159" (class="35"): 9 (41/42)
    - Value "160" (class="36"): 14 (54)
    - Value "161" (class="36"): 13 (52)
    - Value "162" (class="36"): 12 (48)
    - Value "163" (class="36"): 10 (44)
    - Value "164" (class="36"): 9 (41)
    - Value "165" (class="37"): 14 (53/54)
    - Value "166" (class="37"): 13 (51/52)
    - Value "167" (class="37"): 12 (47/48)
    - Value "168" (class="37"): 10 (43/44)
    - Value "169" (class="37"): 9 (41/42)
    - Value "170" (class="37"): 7 (37/38)
    - Value "171" (class="37"): 6 (31/32)
    - Value "172" (class="37"): 5 (29/30)
    - Value "173" (class="37"): 3 (25/26)
    - Value "174" (class="37"): 2 (21/22)
    - Value "175" (class="37"): 1 (19)
    - Value "176" (class="38"): 14 (54)
    - Value "177" (class="38"): 13 (52)
    - Value "178" (class="38"): 12 (48)
    - Value "179" (class="38"): 10 (44)
    - Value "180" (class="38"): 9 (41)
    - Value "181" (class="39"): 8 (40)
    - Value "182" (class="39"): 7 (38)
    - Value "183" (class="39"): 6 (32)
    - Value "184" (class="39"): 5 (29)
    - Value "185" (class="40"): 8 (40)
    - Value "186" (class="40"): 7 (38)
    - Value "187" (class="40"): 6 (32)
    - Value "188" (class="40"): 5 (29)
    - Value "189" (class="41"): 8 (40)
    - Value "190" (class="41"): 7 (38)
    - Value "191" (class="41"): 6 (32)
    - Value "192" (class="41"): 5 (29)
    - Value "193" (class="42"): 8 (40)
    - Value "194" (class="42"): 7 (38)
    - Value "195" (class="42"): 6 (32)
    - Value "196" (class="42"): 5 (29/30)
    - Value "197" (class="43"): 8 (40)
    - Value "198" (class="43"): 7 (38)
    - Value "199" (class="43"): 6 (32)
    - Value "200" (class="43"): 5 (29)
    - Value "201" (class="44"): 8 (40)
    - Value "202" (class="44"): 7 (38)
    - Value "203" (class="44"): 6 (32)
    - Value "204" (class="44"): 5 (29)
    - Value "205" (class="45"): 8 (40)
    - Value "206" (class="45"): 7 (38)
    - Value "207" (class="45"): 6 (32)
    - Value "208" (class="45"): 5 (29)
    - Value "209" (class="46"): 8 (40)
    - Value "210" (class="46"): 7 (38)
    - Value "211" (class="46"): 6 (32)
    - Value "212" (class="46"): 5 (29)
    - Value "213" (class="47"): 8 (40)
    - Value "214" (class="47"): 7 (38)
    - Value "215" (class="47"): 6 (32)
    - Value "216" (class="47"): 5 (29)
    - Value "217" (class="48"): 8 (40)
    - Value "218" (class="48"): 7 (38)
    - Value "219" (class="48"): 6 (32)
    - Value "220" (class="48"): 5 (29)
    - Value "221" (class="49"): 8 (40)
    - Value "222" (class="49"): 7 (38)
    - Value "223" (class="49"): 6 (32)
    - Value "224" (class="49"): 5 (29)
    - Value "225" (class="40"): 8 (40) (Note: This option has class="40", potentially conflicting with other "40" classes. Listed as per source.)
    - Value "226" (class="50"): 7 (38)
    - Value "227" (class="50"): 6 (32)
    - Value "228" (class="50"): 5 (29)
    - Value "229" (class="51"): 8 (40)
    - Value "230" (class="51"): 7 (38)
    - Value "231" (class="51"): 6 (32)
    - Value "232" (class="51"): 5 (29)
    - Value "233" (class="52"): 8 (40)
    - Value "234" (class="52"): 7 (38)
    - Value "235" (class="52"): 6 (32)
    - Value "236" (class="52"): 5 (29)
    - Value "237" (class="53"): 8 (40)
    - Value "238" (class="53"): 7 (38)
    - Value "239" (class="53"): 6 (32)
    - Value "240" (class="53"): 5 (29/30)
    - Value "241" (class="54"): 2 (22)
    - Value "242" (class="54"): 1 (19)
    - Value "243" (class="55"): 4 (28)
    - Value "244" (class="55"): 3 (26)
    - Value "245" (class="55"): 2 (22)
    - Value "246" (class="55"): 1 (19)
    - Value "247" (class="56"): 4 (28)
    - Value "248" (class="56"): 3 (26)
    - Value "249" (class="56"): 2 (22)
    - Value "250" (class="56"): 1 (19)
    - Value "251" (class="57"): 4 (28)
    - Value "252" (class="57"): 3 (26)
    - Value "253" (class="57"): 2 (22)
    - Value "254" (class="57"): 1 (19)
    - Value "255" (class="58"): 4 (28)
    - Value "256" (class="58"): 3 (26)
    - Value "257" (class="58"): 2 (22)
    - Value "258" (class="58"): 1 (19)
    - Value "259" (class="59"): 4 (28)
    - Value "260" (class="59"): 3 (26)
    - Value "261" (class="59"): 2 (22)
    - Value "262" (class="59"): 1 (19)
    - Value "263" (class="60"): 4 (28)
    - Value "264" (class="60"): 3 (26)
    - Value "265" (class="60"): 2 (22)
    - Value "266" (class="60"): 1 (19)
    - Value "267" (class="61"): 4 (28)
    - Value "268" (class="61"): 3 (26)
    - Value "269" (class="61"): 2 (22)
    - Value "270" (class="61"): 1 (19)
    - Value "271" (class="62"): 4 (28)
    - Value "272" (class="62"): 3 (26)
    - Value "273" (class="62"): 2 (22)
    - Value "274" (class="62"): 1 (19)
    - Value "275" (class="63"): 9 (41)
    - Value "276" (class="63"): 5 (29)
    - Value "277" (class="63"): 1 (19)
    - Value "278" (class="64"): Pelajar Latihan Industri
    - Value "279" (class="65"): 1 (19)
    - Value "280" (class="65"): 2 (22)
    - Value "281" (class="65"): 3 (26)
    - Value "282" (class="65"): 4 (28)

---

## 5. MOTAC Negeri/ Bahagian/ Unit (MOTAC State/ Division/ Unit)

- **Field Name in MyMail HTML:** `department_id` (id: department)
- **Database Field Reference:** `users.department_id` (links to departments table)
- **Options:**
    - Value "" (default): - Pilih MOTAC Negeri/Bahagian/Unit -
    - Value "1": Akaun
    - Value "2": Audit Dalam
    - Value "3": Dasar Kebudayaan
    - Value "4": Dasar Pelancongan dan Hubungan Antarabangsa
    - Value "22": Hubungan Antarabangsa Kebudayaaan
    - Value "5": Integriti
    - Value "6": Kewangan
    - Value "7": Komunikasi Korporat
    - Value "23": KPI
    - Value "33": MOTAC Johor
    - Value "27": MOTAC Kedah
    - Value "34": MOTAC Kelantan
    - Value "31": MOTAC Melaka
    - Value "32": MOTAC N. Sembilan
    - Value "36": MOTAC Pahang
    - Value "29": MOTAC Perak
    - Value "26": MOTAC Perlis
    - Value "28": MOTAC Pulau Pinang
    - Value "38": MOTAC Sabah
    - Value "37": MOTAC Sarawak
    - Value "30": MOTAC Selangor
    - Value "35": MOTAC Terengganu
    - Value "39": MOTAC WP Kuala Lumpur / Putrajaya
    - Value "40": MOTAC WP Labuan
    - Value "24": OSC MM2H
    - Value "8": Pejabat Ketua Setiausaha (KSU)
    - Value "9": Pejabat Menteri
    - Value "11": Pejabat Timbalan Ketua Setiausaha (Kebudayaan)
    - Value "12": Pejabat Timbalan Ketua Setiausaha (Pelancongan)
    - Value "10": Pejabat Timbalan Ketua Setiausaha (Pengurusan)
    - Value "13": Pejabat Timbalan Menteri
    - Value "14": Pelesenan dan Penguatkuasaan Pelancongan
    - Value "15": Pembangunan Industri
    - Value "16": Pembangunan Prasarana
    - Value "17": Pengurusan Acara
    - Value "18": Pengurusan Maklumat
    - Value "19": Pengurusan Sumber Manusia
    - Value "20": Pentadbiran
    - Value "21": Perundangan
    - Value "25": Sekretariat Visit Malaysia

---

## 6. Aras (Level/Floor)

- **Field Name in MyMail HTML:** `aras` (id: aras)
- **Database Field Reference:** `users.level`
- **Options:**
    - Value "" (default, disabled): -Pilih Aras -
    - Value "1": 1
    - Value "2": 2
    - Value "3": 3
    - Value "4": 4
    - Value "5": 5
    - Value "6": 6
    - Value "7": 7
    - Value "8": 8
    - Value "9": 9
    - Value "10": 10
    - Value "11": 11
    - Value "12": 12
    - Value "13": 13
    - Value "14": 14
    - Value "15": 15
    - Value "16": 16
    - Value "17": 17
    - Value "18": 181

---

## 7. Gred Penyokong (Supporting Officer's Grade)

- **Field Name in MyMail HTML:** `gred_penyokong` (id: gred_penyokong)
- **Database Field Reference:** `email_applications.supporting_officer_grade` (or links to grades table if storing structured grade info)
- **Options:**
    - Value "" (default): - Pilih Gred -
    - Value "Turus III": Turus III
    - Value "Jusa A": Jusa A
    - Value "Jusa B": Jusa B
    - Value "Jusa C": Jusa C
    - Value "14": 14
    - Value "13": 13
    - Value "12": 12
    - Value "10": 10
    - Value "9": 9

---

This supplementary document should be version-controlled and updated if the source MyMail system's dropdown options change. It will serve as a direct reference for development and data setup.
