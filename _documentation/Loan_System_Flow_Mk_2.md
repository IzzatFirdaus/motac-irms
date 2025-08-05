# ICT Loan Application System Flow (Mk. 2.5)

This document outlines the complete workflow for an ICT equipment loan, detailing the key stages, primary actors, and associated code components based on the final system architecture.

---

## 1. Application Initiation & Confirmation

The process begins when an applicant creates a new loan request. They can save it as a draft or formally submit it for approval, which includes certifying the request's details and agreeing to the terms.

- **Primary Actor:** Applicant

- **User Interface (UI):**
    - `App\Livewire\ResourceManagement\LoanApplication\ApplicationForm`: The primary Livewire component for the dynamic front-end form used for creating and editing applications.
    - `App\Livewire\ResourceManagement\MyApplications\Loan\Index`: Displays the applicant's list of their loan applications, showing current statuses.
    - `resources/views/livewire/resource-management/loan-application/application-form.blade.php`: The Blade template that renders the application form UI.

- **Core Logic:**
    - `app\Services\LoanApplicationService`: Contains the core business logic for creating and managing applications.
    - `App\Http\Controllers\LoanApplicationController`: Manages backend logic for non-Livewire flows, primarily showing application details (`show` method). The Livewire component handles most submission logic directly.
    - `app\Http\Requests\StoreLoanApplicationRequest` & `app\Http\Requests\UpdateLoanApplicationRequest`: Validate incoming data to ensure all fields are correctly formatted before processing.

- **Data & State Changes:**
    - `App\Models\LoanApplication`: A new record is created with a status of `draft`. Upon submission, the status is updated to `pending_support` and the `applicant_confirmation_timestamp` is recorded.
    - `App\Models\LoanApplicationItem`: Records are created for each type of equipment requested.

- **Authorization:**
    - `app\Policies\LoanApplicationPolicy`: The `create`, `update`, and `submit` policy methods ensure the user is authenticated and authorized to perform these actions.

- **Notifications & Communication:**
    - `app\Services\NotificationService`: Dispatches notifications to the applicant upon successful submission.
    - `App\Notifications\ApplicationSubmitted`: An email and database notification sent to the applicant confirming their submission.

---

## 2. Approval Workflow

Once submitted, the application is routed to a designated supporting officer for review and a decision. The system enforces grade-level requirements for approvers.

- **Primary Actor:** Supporting Officer (Grade 41+ as per config)

- **User Interface (UI):**
    - `App\Livewire\ResourceManagement\Approval\Dashboard`: Provides a centralized UI for approvers to view and manage all their pending approval tasks.
    - `resources/views/livewire/resource-management/approval/dashboard.blade.php`: The Blade view that renders the approver's dashboard.

- **Core Logic:**
    - `app\Services\ApprovalService`: Handles the core logic of finding the correct approver, creating/updating approval records, and processing the decision.
    - `App\Http\Controllers\ApprovalController`: The `recordDecision()` method processes the officer's approval or rejection action submitted from the UI.

- **Data & State Changes:**
    - `App\Models\Approval`: An Approval record is created or updated with the officer's decision, comments, and timestamp.
    - `App\Models\LoanApplication`: The application status is updated to `approved` or `rejected`.
    - `App\Models\LoanApplicationItem`: The `quantity_approved` field may be updated by the approver.

- **Authorization:**
    - `app\Policies\LoanApplicationPolicy`: The `recordDecision` policy method ensures the user is the designated approver for the current stage.
    - `app\Policies\ApprovalPolicy`: The `update` policy method governs the actions that can be performed on the Approval model records.

- **Notifications & Communication:**
    - `App\Notifications\ApplicationNeedsAction`: Notifies the designated officer of a pending task.
    - `App\Notifications\ApplicationApproved` / `App\Notifications\ApplicationRejected`: Notifies the applicant of the final decision.
    - `App\Notifications\LoanApplicationReadyForIssuanceNotification`: Notifies BPM staff that an application has been approved.

---

## 3. Equipment Issuance

Approved applications are processed by the BPM team, who select specific assets from inventory and issue the physical equipment to the applicant.

- **Primary Actor:** BPM Staff

- **User Interface (UI) & Core Logic:**
    - `App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance`: This Livewire component is the primary handler for the entire issuance process. Its `submitIssue` method orchestrates validation and calls the backend service.
    - `app\Services\LoanTransactionService`: The `processNewIssue()` method contains the core logic for creating the issue transaction, updating equipment statuses, and linking specific assets to the loan, all within a database transaction.
    - `resources/views/loan_transactions/issue.blade.php`: The main view that hosts the ProcessIssuance Livewire component.

- **Data & State Changes:**
    - `App\Models\LoanTransaction`: A new record is created with `type = 'issue'`, storing details like the issuing/receiving officers and an accessories checklist.
    - `App\Models\LoanTransactionItem`: Records are created to link specific Equipment serial numbers to this transaction.
    - `App\Models\Equipment`: The status of each issued asset is updated to `on_loan`.
    - `App\Models\LoanApplication`: The status is updated to `issued` or `partially_issued`.

- **Authorization:**
    - `app\Policies\LoanTransactionPolicy`: The `createIssue` policy method, checked within the ProcessIssuance component, ensures only authorized BPM staff can perform this action.

- **Notifications & Communication:**
    - `App\Notifications\EquipmentIssuedNotification`: Notifies the applicant that their equipment has been dispatched.

---

## 4. Equipment Return Process

The applicant, or a designated officer, returns the equipment. BPM staff inspect the items against the original checklist, note their condition, and record the return in the system.

- **Primary Actors:** Applicant/Returning Officer, BPM Staff

- **User Interface (UI) & Core Logic:**
    - `App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn`: This Livewire component is the primary handler for the return process, managing the form, accessories checklist, and condition reporting.
    - `App\Http\Controllers\LoanTransactionController`: May process submissions from the Livewire component and is used to show transaction details (`show` method).
    - `app\Services\LoanTransactionService`: The `processExistingReturn()` method contains the core logic for creating the return transaction and updating equipment status based on its condition.
    - `resources/views/loan_transactions/return.blade.php`: The main view that hosts the ProcessReturn Livewire component.
    - `resources/views/livewire/resource-management/admin/bpm/process-return.blade.php`: The specific Blade view for the Livewire component.

- **Data & State Changes:**
    - `App\Models\LoanTransaction`: A new record is created with `type = 'return'`, storing officers, checklist, notes, and timestamps.
    - `App\Models\LoanTransactionItem`: Records are updated to reflect the return status and condition of individual items.
    - `App\Models\Equipment`: The status of each returned asset is updated to `available`, `under_maintenance`, etc.
    - `App\Models\LoanApplication`: The status is updated to `returned` once all items have been processed.

- **Authorization:**
    - `app\Policies\LoanTransactionPolicy`: The `processReturn` policy method ensures only authorized BPM staff can record a return.

- **Notifications & Communication:**
    - `app\Services\NotificationService`: Dispatches all notifications for the return process.
    - `App\Notifications\EquipmentReturnedNotification`: Notifies the applicant that their return has been successfully processed.
    - `App\Notifications\EquipmentReturnReminderNotification`: Proactively sent to users before their loan is due.
    - `App\Notifications\EquipmentOverdueNotification`: Sent if equipment is overdue.
    - `App\Notifications\EquipmentIncidentNotification`, `EquipmentLostNotification.php`, `EquipmentDamagedNotification.php`: Used to notify relevant parties if an item is returned damaged or reported lost.

- **Mailables & Email Views:**
    - Associated Mailables (e.g., `App\Mail\EquipmentReturnedNotification`) build the email content.
    - Email templates are stored in `resources/views/emails/`.

---

## 5. Shared Components & Infrastructure

These components are used throughout the loan workflow to provide core functionality and a consistent architecture.

- **Core Models:**  
  `User.php`, `Department.php`, `Position.php`, and `Grade.php` provide essential user context for applicants, approvers, and staff.

- **Observers:**  
  `app\Observers\BlameableObserver.php` automatically populates the `created_by`, `updated_by`, and `deleted_by` fields on auditable models like `LoanApplication` and `LoanTransaction`.

- **Middleware:**
    - **Authentication:** `auth:sanctum` and `config('jetstream.auth_session')`.
    - **Authorization:** `can:` for policy checks, Spatie's role/permission, and custom middleware like `check.gradelevel`.
    - **General:** Standard Laravel middleware like `EncryptCookies`, `StartSession`, and `VerifyCsrfToken` are used.

- **Routing:**  
  All web-based requests are defined and managed in `routes/web.php`.

- **Service Providers:**
    - `AppServiceProvider`: Registers core services like `LoanApplicationService`, `LoanTransactionService`, and `NotificationService`.
    - `AuthServiceProvider`: Registers all model policies (`LoanApplicationPolicy`, `LoanTransactionPolicy`, etc.).
    - `EventServiceProvider`: Registers model observers like `BlameableObserver`.

- **Configuration:**  
  The `config/motac.php` file stores system-wide settings, such as `motac.approval.min_loan_support_grade_level` and the `motac.loan_accessories_list`.

- **Shared Views & Helpers:**
    - Reusable Blade components are located in `resources/views/components/` (e.g., for status badges).
    - `app/Helpers/Helpers.php` may contain utility functions used across the module.

---

### Shared Components & Infrastructure (Used Throughout the Loan Workflow)

- **Models:**  
  `User.php`, `Department.php`, `Position.php`, `Grade.php` provide essential user context.

- **Observers:**  
  `app\Observers\BlameableObserver.php` automatically populates `created_by`, `updated_by`, `deleted_by` fields for models like `LoanApplication`, `LoanTransaction`, `Equipment`.

- **Middleware:**
    - **Authentication:** `auth:sanctum`, `config('jetstream.auth_session')`.
    - **Authorization:** `can:` middleware for policy checks, role/permission (Spatie), `check.gradelevel`, `check.usergrade`.
    - **General web middleware:** `EncryptCookies`, `StartSession`, `VerifyCsrfToken`, etc.

- **Routing:**  
  Defined in `routes/web.php` using Laravel's routing functions.

- **Service Providers:**
    - `AppServiceProvider.php`: Registers core services like `LoanApplicationService`, `LoanTransactionService`, `EquipmentService`, `NotificationService`.
    - `AuthServiceProvider.php`: Registers model policies like `LoanApplicationPolicy`, `LoanTransactionPolicy`, `EquipmentPolicy`.
    - `EventServiceProvider.php`: Registers observers like `BlameableObserver`.

- **Views (Shared UI Elements):**
    - Reusable Blade components in `resources/views/components/` (e.g., status badges, user info cards).
    - Main application layout views.

- **Helpers:**  
  `app/Helpers/Helpers.php` may contain utility functions used across the module.
