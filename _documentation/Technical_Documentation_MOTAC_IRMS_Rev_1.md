# A Simple Guide to the MOTAC Integrated Resource Management System (IRMS)

---

## Who is this guide for?

This guide is for all MOTAC staff members. It explains our new system in simple terms, so you can understand what it does, why it’s useful, and how it will make our daily work easier.

---

## 1. What is the MOTAC IRMS?

Think of the IRMS as a new central online hub for handling common staff requests.  
Instead of using paper forms or sending emails back and forth for certain tasks, you'll now use this one simple, modern website.

**Initially, the system handles two main areas:**
- **Getting a New Email or User ID:** For new staff, or when you need a specific User ID for a system.
- **Borrowing ICT Equipment:** When you need to loan a laptop, projector, or other tech gear for official work.

The goal is to bring these processes online, making them faster, easier to track, and more organized for everyone.

---

## 2. What Problems Does It Solve?

The IRMS is designed to fix common headaches and improve how we work.

- **Everything in One Place:** No more hunting for the right paper form or wondering who to email. All your applications and their statuses are in one secure system.
- **Smarter, Faster Online Forms:** The online forms are interactive. They only show you the questions you need to answer. This reduces errors and saves you time.
- **Clear, Automated Steps:** The system automatically sends your request to the right person for approval (like your supporting officer). You don't have to guess the next step.
- **Instant Updates and Reports:** You can log in anytime to see the status of your request—whether it's pending, approved, or ready for pickup. Managers can also get quick reports on equipment usage.
- **The Right Access for the Right People:** The system knows your role. An applicant, an approver, and an IT administrator will each see a different dashboard tailored to their tasks, ensuring security and simplicity.

---

## 3. A Quick Tour: The Journey of a Request

Let's walk through how a typical request moves through the system. Both applying for an email and borrowing equipment follow a similar, simple path.

**Step 1: You Fill Out the Form**  
You log into the IRMS and choose the form you need. As you fill it out, you’ll notice it’s smart. For example, if you say you’re a contract staff, it might ask for your contract end date. You attach any needed documents and submit it.

**Step 2: It Goes for Approval**  
Once you submit, the system automatically sends a notification (an email and an in-app alert) to the correct supporting officer. The officer can review all the details online.

**Step 3: A Decision is Made**  
Your supporting officer reviews your request and either approves or rejects it directly in the system. They can also add comments. You immediately get a notification letting you know the outcome.

**Step 4: The IT Team Takes Action**  
- **For Email/ID Requests:** If approved, the request goes to the IT team. They create your account and the system notifies you once it's ready, along with your new credentials.
- **For Equipment Loans:** If approved, the request goes to the BPM (ICT Equipment) staff. They prepare the equipment for you.

**Step 5: You Get Your Item**  
- **For Equipment Loans:** You receive a notification that your item is ready. BPM staff will record when you pick it up (the "issuance"). They’ll check off accessories like the charger and bag. When you bring it back, they’ll check it in and the loan is marked as "completed."

> At any point in this journey, you can log in and see exactly where your application is.

---

## 4. What Information Does the System Keep?

To make all this work, the system needs to store information securely. Think of it like a set of digital filing cabinets.

- **Your User Profile:** Your name, position, grade, and department. This helps auto-fill forms for you.
- **Equipment Inventory:** A complete, up-to-date list of all our ICT equipment, including its model, serial number, and current status (e.g., Available, On Loan).
- **Your Application Details:** A secure record of every request you’ve ever made, including the purpose, dates, and the final outcome.
- **Approval History:** A log of who approved your request and when, creating a clear digital paper trail.

---

## 5. Key Features You'll Use

- **Your Personal Dashboard:** When you log in, you'll see a simple screen showing your recent requests, their status, and any notifications waiting for you. Approvers will see a list of applications that need their attention.
- **Smart, Interactive Forms:** Forms that guide you through the application process, making sure you provide all the necessary information.
- **Automatic Notifications:** You'll get emails and alerts on the website for key events, like when your request is submitted, approved, or when your loaned laptop is due for return.
- **Print to PDF:** For the ICT loan form, you can generate a professional, printable PDF copy of your application at any time, which looks just like the official government paper form.

---

## 6. Keeping Your Information Safe

Your privacy and the security of MOTAC's data are our top priorities.

- **Secure Login:** You'll access the system with your own secure password. The system is protected against common cyber threats.
- **Permissions and Roles:** You can only see what you're supposed to see. An applicant cannot see the administrator's dashboard, and one user cannot see another user's private application details.
- **Digital Trail (Audit Log):** The system keeps a record of important actions, such as who approved a request and when. This ensures accountability and transparency.

---

# Technical Documentation: MOTAC Integrated Resource Management System (Revision 1)

**Document Version:** 1.0  
**Revision Date:** June 14, 2025  
**Based On:** PERAKUAN PEMOHONAN EMEL & ID PENGGUNA MOTAC, BORANG PINJAMAN PERALATAN ICT 2024 SEWAAN C, and confirmed code structure from amralsaleeh/HRMS template.

---

## 1. Overview

The MOTAC Integrated Resource Management System is designed to consolidate two key operational areas:

- **Email/User ID Provisioning:** Automates the process for staff to request, certify, and obtain official MOTAC email accounts or user IDs, mirroring the existing MyMail application process.
- **ICT Equipment Loan Management:** Facilitates the request, approval, issuance, and return of ICT equipment (laptops, projectors, etc.) used for official purposes.

The system provides a unified, Laravel-based platform that streamlines workflows, enforces business rules, and delivers a consistent user experience across both digital and physical resource management. This revision incorporates details from the official application forms (including specific fields and logic from the MyMail system) and reflects a focused project structure based on provided code files.

---

## 2. System Objectives

- **Unified Data Management:** Consolidate user data, applications, approvals, and notifications in a single MySQL database.
- **Streamlined Workflows:** Automate and standardize the application processes for both email/User ID provisioning and ICT equipment loans, adhering to the steps outlined in the official forms.
- **Role-Based Access & Security:** Ensure that users, approvers, BPM staff, and IT Admins have the correct levels of access with robust security measures, incorporating grade-based approval logic and detailed authorization policies. Standardized role names (e.g., 'Admin', 'BPM Staff', 'IT Admin') are used.
- **Real-Time Reporting & Notifications:** Enable real-time insights on resource utilization and application statuses, and notify users of critical events via email and in-app (database) notifications.
- **Modular & Scalable Architecture:** Build the system using Laravel’s MVC framework with clear separation of concerns and utilize Livewire for dynamic interfaces, a well-defined service layer, leveraging the structure provided by the HRMS template.

---

## 3. High-Level Architecture

The system is built upon the Laravel framework, employing the Model-View-Controller (MVC) pattern enhanced with Livewire for dynamic user interfaces.

### 3.1 Laravel/Livewire MVC Pattern

**Controllers:**  
Traditional PHP controllers handle backend HTTP requests, API interactions, and actions not fully managed by front-end dynamic components. Many UI interactions (CRUD, forms, lists) are managed by Livewire components.

**Key Controllers:**
- `App\Http\Controllers\language\LanguageController.php`
- `App\Http\Controllers\WebhookController.php`
- `App\Http\Controllers\Api\EmailProvisioningController.php`
- `App\Http\Controllers\ApprovalController.php`
- `App\Http\Controllers\EmailAccountController.php`
- `App\Http\Controllers\EmailApplicationController.php`
- `App\Http\Controllers\EquipmentController.php`
- `App\Http\Controllers\LoanApplicationController.php`
- `App\Http\Controllers\LoanTransactionController.php`
- `App\Http\Controllers\NotificationController.php`
- `App\Http\Controllers\ReportController.php`
- `App\Http\Controllers\Admin\*Controller.php`

**Models:**  
Represent and manage data using Eloquent ORM, include:
- `User`, `Department`, `Position`, `Grade`, `EmailApplication`, `Equipment`, `EquipmentCategory`, `LoanApplication`, `LoanTransaction`, `Approval`, `Notification`

**Views:**  
Blade templates render UIs, including Livewire components.
- `resources/views/`
- `resources/views/livewire/`
- `resources/views/emails/`
- `resources/views/_partials/_modals/`
- `resources/views/loan-applications/pdf/print-form.blade.php` (PDF export)

**Services:**  
Encapsulate business logic (thin controllers).
- `app/Services/`: `ApprovalService`, `EmailApplicationService`, `LoanApplicationService`, `LoanTransactionService`, `EquipmentService`, `NotificationService`

**Middleware:**  
Enforce authentication, authorization, and request validations.
- **Global:** `TrustHosts`, `TrustProxies`, `HandleCors`, `PreventRequestsDuringMaintenance`, `ValidatePostSize`, `TrimStrings`, `ConvertEmptyStringsToNull`
- **Web Group:** `EncryptCookies`, `StartSession`, `VerifyCsrfToken`, `SubstituteBindings`, `LocaleMiddleware`
- **API Group:** `ThrottleRequests:api`, `auth:sanctum`
- **Aliases:** Standard Laravel, Spatie Permission (`role`, `permission`), Custom (`allow_admin_during_maintenance`, `validate.webhook.signature`, `check.gradelevel`, `check.usergrade`)

**Livewire Components:**  
Dynamic UI elements (app/Livewire/)  
Examples: settings management (Settings\Users\Index), application forms (ResourceManagement\EmailAccount\ApplicationForm), loan processing (ResourceManagement\Admin\BPM\ProcessIssuance)

**Policies:**  
Authorization logic for models (app/Policies/), e.g. `UserPolicy`, `LoanApplicationPolicy`, registered in `AuthServiceProvider`.

**Observers:**  
`BlameableObserver` auto-fills audit fields on specified models.

---

### 3.2 Deployment & Infrastructure

- **Dockerized Development:** Use Docker for consistency.
- **Staging Environment:** Separate server for testing.
- **Mailtrap Integration:** Use Mailtrap for email testing during development.
- **Version Control & CI/CD:** Git for source control; automated deployment via webhooks.

---

### 3.3 Core Providers and Configuration

- **AppServiceProvider:** Registers core services, global view data, localization, timezone.
- **AuthServiceProvider:** Registers model policies, admin overrides.
- **EventServiceProvider:** Registers model observers and event listeners.
- **FortifyServiceProvider / JetstreamServiceProvider:** Authentication actions and Jetstream features.
- **MenuServiceProvider:** Loads/shares navigation menu data.
- **RouteServiceProvider:** Route model binding, rate limiting, loads route files.
- **config/motac.php:** Stores application-specific values (approval grades, accessories).
- **config/app.php:** Application-wide defaults (date/time formats).

---

## 4. Database Design

The system utilizes a unified MySQL database. Auditable tables include `created_by`, `updated_by`, and `deleted_by` fields, managed automatically by the BlameableObserver.

### 4.1 Users & Organizational Data

- **users:** id, title, name, identification_number (unique), passport_number (unique), profile_photo_path, position_id, grade_id, department_id, level, mobile_number, email (unique), motac_email (unique), user_id_assigned (unique), service_status (enum), appointment_type (enum), password, status (enum), email_verified_at, two-factor fields, audit stamps, timestamps.
- **departments:** id, name, branch_type (enum), code, description, is_active, head_user_id, audit stamps, timestamps.
- **positions:** id, name, grade_id, description, is_active, audit stamps, timestamps.
- **grades:** id, name, level (integer), min_approval_grade_id, is_approver_grade, audit stamps, timestamps.

### 4.2 Email/User ID Applications

- **email_applications:** id, user_id, application_reason_notes, proposed_email, supporting_officer_id, status (draft, pending_support, approved, rejected, completed, etc.), certification booleans, certification_timestamp, rejection_reason, final_assigned_email, audit stamps, timestamps.

### 4.3 ICT Equipment Loan Modules

- **equipment:** id, asset_type (enum), brand, model, serial_number (unique), tag_id (unique), status (available, on_loan, under_maintenance, etc.), condition_status (new, good, fair, lost, etc.), department_id, equipment_category_id, sub_category_id, location_id, audit stamps, timestamps.
- **equipment_categories & sub_categories:** id, name, description, is_active, audit stamps, timestamps.
- **locations:** id, name, address, city, state, is_active, audit stamps, timestamps.
- **loan_applications:** id, user_id, responsible_officer_id, purpose, location, loan_start_date, loan_end_date, status (draft, pending_support, approved, rejected, issued, returned, completed, etc.), rejection_reason, submitted_at, audit stamps, timestamps.
- **loan_application_items:** id, loan_application_id, equipment_type, quantity_requested, quantity_approved, quantity_issued, quantity_returned, audit stamps, timestamps.
- **loan-transactions:** id, loan_application_id, type (issue, return), transaction_date, issuing_officer_id, receiving_officer_id, accessories_checklist_on_issue (json), issue_notes, accessories_checklist_on_return (json), return_notes, status (pending, issued, returned_good, etc.), audit stamps, timestamps.
- **loan_transaction_items:** id, loan_transaction_id, equipment_id, status (issued, returned_good, reported_lost, etc.), condition_on_return, audit stamps, timestamps.

### 4.4 Approval & Notification

- **approvals:** id, approvable_type, approvable_id, officer_id, stage, status (pending, approved, rejected), comments, approval_timestamp, audit stamps, timestamps.
- **notifications:** id (UUID), type, notifiable_type, notifiable_id, data (text), read_at, audit stamps, timestamps.

---

## 5. Detailed Workflow Processes

### 5.1 Email/User ID Application Workflow

**Application Initiation:**  
- **Actor:** Applicant  
- **UI:** Dynamic application form (Livewire), adapts based on selections (Service Status, Appointment)  
- **Component:** `App\Livewire\ResourceManagement\EmailAccount\ApplicationForm`

**Form Entry & Certification:**  
- **Actor:** Applicant  
- **UI:** Fill details, tick three mandatory checkboxes  
- **Data:** Saved as draft, cert_* fields, certification_timestamp

**Submission & Supporting Officer Review:**  
- **Actors:** Applicant, Supporting Officer  
- **Logic:** Status changes to pending_support. Routed via ApprovalService (officer must meet grade requirement).  
- **Notifications:** `ApplicationSubmitted` to applicant, `ApplicationNeedsAction` to approver

**IT Processing & Credential Delivery:**  
- **Actors:** Supporting Officer, IT Admin  
- **Logic:** Approve → pending_admin. IT Admin notified, provisions account/ID, updates record.  
- **Notifications:** `EmailApplicationReadyForProcessingNotification` to IT Admin. Upon completion, `EmailProvisionedNotification` (with credentials) to applicant. `ApplicationApproved` or `ApplicationRejected` as appropriate.

### 5.2 ICT Equipment Loan Workflow

**Application Initiation & Confirmation:**  
- **Actor:** Applicant  
- **UI:** `App\Livewire\ResourceManagement\LoanApplication\ApplicationForm`  
- **Logic:** Fill out form, confirm/certify, status: pending_support  
- **Notifications:** `ApplicationSubmitted` to applicant

**Supporting Officer Approval:**  
- **Actor:** Supporting Officer  
- **UI:** `App\Livewire\ResourceManagement\Approval\Dashboard`  
- **Logic:** Routed via ApprovalService to officer meeting grade level. Approver can adjust quantities, approve/reject. Logged in approvals table.  
- **Notifications:** `ApplicationNeedsAction` to approver, `ApplicationApproved`/`ApplicationRejected` to applicant, `LoanApplicationReadyForIssuanceNotification` to BPM staff

**Equipment Issuance:**  
- **Actor:** BPM Staff  
- **UI & Logic:** `App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance` selects assets. `LoanTransactionService::processNewIssue()` creates issue transaction, links equipment, updates status.  
- **Notifications:** `EquipmentIssuedNotification` to applicant

**Equipment Return Process:**  
- **Actors:** Applicant/Returning Officer, BPM Staff  
- **UI & Logic:** `App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn` component. Staff inspect/check accessories, record condition. `LoanTransactionService::processExistingReturn()` creates return transaction, updates equipment status.  
- **Notifications:** `EquipmentReturnReminderNotification` for due dates, `EquipmentReturnedNotification` to applicant, `EquipmentIncidentNotification` for lost/damaged items

**Supporting Actions:**  
- **Print to PDF:** Generate printable PDF using `LoanApplicationController::printPdf()` for official documentation.

---

## 6. User Interface & Experience

- **Branding and Layout:** Consistent MOTAC brand identity, responsive/mobile-friendly
- **Role-Based Dashboards:**  
  - Administrator: System-wide statistics, approvals, resource usage  
  - User: Personalized application statuses, notifications  
  - Approver: List of pending approval tasks  
  - BPM Staff: Interfaces for equipment issuance/returns
- **Dynamic and Reusable Components:**  
  - Dynamic Forms: Livewire components with conditional fields, validation, auto-complete  
  - Placeholders/Instructions: UI guidance from official forms  
  - Reusable Blade Components: Standardized UI elements (e.g., `<x-approval-status-badge>`)

---

## 7. Business Rules & Validation

- **Eligibility Rules:**  
  - Email: Full account vs. User ID based on service status  
  - Loans: Only active staff, subject to availability
- **Validation Rules:**  
  - FormRequest classes enforce mandatory fields, formats, authorization  
  - Input masks for NRIC, mobile numbers  
  - Date logic for loan periods
  - Approver grade requirements (config/motac.php)
  - Password policy (CustomPasswordValidationRules.php)
- **Processing Time & Liability:**  
  - ICT Loans processed within 3 working days  
  - Applicant liable for loss/damage

---

## 8. Technical Considerations

- **Security:**  
  - Authentication/Authorization: Laravel Fortify/Jetstream, RBAC via spatie/laravel-permission, custom grade middleware  
  - Data Protection: CSRF, input sanitization  
  - Audit Trails: BlameableObserver auto-logs actions  
  - Webhook Security: Signature validation for CI/CD
- **Development Practices:**  
  - Dockerized environment  
  - Testing: Unit, integration, feature tests  
  - Version Control: Git, CI/CD pipeline

---

## 9. System Modules & Component Breakdown

### 9.1 Authentication & User Management

- **Logic:** Fortify Actions, Jetstream Actions, UserService
- **UI:** Livewire components (`App\Livewire\Settings\...`)
- **Models:** User, Department, Position, Grade, Role, Permission
- **Policies:** UserPolicy, GradePolicy

### 9.2 Email/User ID Provisioning Module

- **Controllers:** EmailApplicationController, EmailAccountController, Api\EmailProvisioningController
- **UI:** Livewire components (ApplicationForm, MyApplications\Email\Index)
- **Models:** EmailApplication
- **Services:** EmailApplicationService, EmailProvisioningService
- **Policy:** EmailApplicationPolicy
- **Notifications:** ApplicationSubmitted, ApplicationNeedsAction, EmailApplicationReadyForProcessingNotification, EmailProvisionedNotification, etc.

### 9.3 ICT Equipment Loan Module

- **Controllers:** LoanApplicationController, EquipmentController, LoanTransactionController, Admin\EquipmentController
- **UI:** Livewire components for forms, listings, BPM issuance/return
- **Models:** Equipment, LoanApplication, LoanApplicationItem, LoanTransaction, LoanTransactionItem
- **Services:** EquipmentService, LoanApplicationService, LoanTransactionService
- **Policies:** EquipmentPolicy, LoanApplicationPolicy, LoanTransactionPolicy
- **Notifications:** LoanApplicationReadyForIssuanceNotification, EquipmentIssuedNotification, EquipmentReturnReminderNotification, etc.

### 9.4 Shared Modules (Approval, Notification, Reporting)

- **Approval:** ApprovalController, ApprovalService, ApprovalPolicy, Approval model, Approval\Dashboard Livewire component
- **Notification:** NotificationController, NotificationService, Notification model, App\Notifications classes
- **Reporting:** ReportController serving views for Livewire reporting components

---

## 10. Deployment & Rollout Strategy

- **Phased Rollout:** Data migration scripts, import existing users/equipment
- **User Training & Documentation:** Comprehensive guides, training, online help
- **Monitoring & Feedback:** System performance, user feedback, scheduled updates

---

## 11. Conclusion

The MOTAC Integrated Resource Management System is designed to address both digital resource provisioning (email/User ID) and physical resource management (ICT equipment loans) using a unified Laravel platform with Livewire components. By incorporating the specific requirements and workflows from the official MOTAC forms, including detailed aspects of the MyMail system, this revised design ensures efficiency, security, and scalability. The modular components, strict business rules, and role-based access will support MOTAC’s operational needs by streamlining application processes, enforcing standard procedures, and providing real-time reporting and notifications—all within a secure and user-friendly interface.

---

## Supplementary Document: Dropdown Menu Options for MyMail Integration

This document lists the predefined options for all dropdown menus (`<select>`) as extracted from the existing MyMail system's "Permohonan Emel & ID Pengguna MOTAC" form.  
These options must be implemented in the system to ensure data consistency.

1. **Taraf Perkhidmatan (Service Status)**
   - **DB Field:** `users.service_status` (enum)
   - **Options:** Tetap, Lantikan Kontrak / MyStep, Pelajar Latihan Industri (Ibu Pejabat Sahaja)

2. **Pelantikan (Appointment)**
   - **DB Field:** `users.appointment_type` (enum)
   - **Options:** Baharu, Kenaikan Pangkat/Pertukaran, Lain-lain

3. **Jawatan (Designation/Position)**
   - **DB Field:** `users.position_id` (links to positions table)
   - **Options:** Predefined list of 65 positions (Menteri, Ketua Setiausaha, Pegawai Teknologi Maklumat (F), Pembantu Tadbir (N), MySTEP, etc.)

4. **Gred (Grade)**
   - **DB Field:** `users.grade_id` (links to grades table)
   - **Options:** Comprehensive list of grades (Jusa A, 54, 41, N19), dynamically filtered based on selected Jawatan

5. **MOTAC Negeri/ Bahagian/ Unit (Department)**
   - **DB Field:** `users.department_id` (links to departments table)
   - **Options:** All divisions, units, state offices (Dasar Kebudayaan, MOTAC Johor, Pengurusan Maklumat, etc.)

6. **Aras (Level/Floor)**
   - **DB Field:** `users.level`
   - **Options:** Numerical list (1-18)

7. **Gred Penyokong (Supporting Officer's Grade)**
   - **DB Field:** `email_applications.supporting_officer_grade`
   - **Options:** Eligible senior grades (Turus III, Jusa A, 14, 13, 12, 10, 9)

---

<!--
**Completeness Check Against Previous .md Files**
- All major sections and features are present and consistent with previously edited markdown documentation.
- No content is missing.
- The guide gives both a simple user-facing overview and a technical breakdown aligned with the system design docs.
-->
