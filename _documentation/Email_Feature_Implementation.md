# MOTAC IRMS: Email Feature Implementation

This document explains how the "email system" works within the **MOTAC Integrated Resource Management System (IRMS)**, referencing the overall system design and related modules.

---

## 1. Email Account/User ID Creation Workflow

The system provides a structured workflow for staff to apply for an email account or User ID.

### Steps Overview

1. **Applicant Logs In & Finds Form**
2. **Applicant Fills Form**  
   - Provides personal details and purpose.
3. **Applicant Certifies Information**  
   - Ticks mandatory checkboxes.
4. **System Validates Input**  
   - Status set: `pending_support`.
5. **System Routes Application to Supporting Officer**  
   - Officer must be Grade 41+.
6. **Supporting Officer Reviews Application**
7. **Supporting Officer Approves or Rejects**

   - **If Approved:**
     - Status set: `pending_admin`.
     - System notifies IT Administrator.
     - IT Admin provisions Email Account/User ID (external system/manual/script).
     - IT Admin updates system record with assigned Email/ID.
     - System sets status: `completed`.
     - System notifies applicant (email & in-app).
     - **End.**

   - **If Rejected:**
     - Status set: `rejected`.
     - System notifies applicant (email & in-app).
     - **End.**

### Process Diagram

```mermaid
graph TD
    A[Applicant Logs In & Finds Form] --> B[Applicant Fills Form]
    B --> C[Applicant Certifies Information]
    C --> D[System Validates Input & Sets Status: Pending Support]
    D --> E[Route to Supporting Officer (Grade 41+)]
    E --> F[Officer Reviews]
    F -->|Approved| G[Status: Pending IT Admin]
    G --> H[Alert IT Admin]
    H --> I[IT Admin Provisions Email/User ID (External)]
    I --> J[IT Admin Updates Record in System]
    J --> K[Status: Completed]
    K --> L[Notify Applicant (Email & In-app)]
    F -->|Rejected| M[Status: Rejected]
    M --> N[Notify Applicant (Email & In-app)]
```

### Notes

- The actual creation of the email account or User ID occurs **outside** the application, typically handled manually by IT administrators or through scripts that interact with MOTAC's mail servers (Exchange, Google Workspace, etc.).
- The system tracks every workflow step, status changes, and participant actions.

---

## 2. Email Notifications for Loan Applications

- The system tracks the lifecycle of ICT equipment loan applications through various statuses (`draft`, `pending_support`, `approved`, `issued`, `returned`, etc.).
- A **Notification module** alerts users about important events (application status changes, loan issuance, returns, etc.).
- Notifications are triggered automatically by workflow events.
- Delivery methods:
  - **Email** (using mail server: Mailtrap for development, MOTAC's server for production)
  - **In-app alerts** (notification center/dashboard)
- This keeps applicants informed about progress/status without needing to manually check the system.

---

## 3. Summary: Email System Roles

- **Workflow Management:** The system manages the request, approval, and notification process for email accounts/User IDs.
- **Provisioning:** Actual account creation is performed externally by IT staff after system approval.
- **Notifications:** Workflow events trigger email and in-app notifications to keep all parties informed.

---

## 4. Logical Components Mapping

Based on the design document (esp. Section 9), the components related to **Email/User ID Provisioning**, **ICT Equipment Loan**, and **Approval/Notification** are described in terms of logical modules, controllers, models, and views.

### 4.1 Email/User ID Provisioning Module

| Type        | Example File/Component                       |
|-------------|---------------------------------------------|
| Controller  | `EmailAccountController`                    |
| Model       | `EmailApplication`                          |
<<<<<<< HEAD
| Views       | `email-accounts/create.blade.php`<br>`email-accounts/show.blade.php` |
| Services    | `EmailApplicationService`<br>`EmailProvisioningService`              |
=======
| Views       | `email-accounts/create.blade.php`, `email-accounts/show.blade.php` |
| Services    | `EmailApplicationService`, `EmailProvisioningService`                |
>>>>>>> release/v4.0

### 4.2 ICT Equipment Loan Module

| Type        | Example File/Component                       |
|-------------|---------------------------------------------|
<<<<<<< HEAD
| Controller  | `LoanApplicationController`<br>`EquipmentController`<br>`LoanTransactionController` |
| Model       | `Equipment`<br>`LoanApplication`<br>`LoanApplicationItem`<br>`LoanTransaction`      |
| Views       | `loans/create.blade.php`<br>`loans/show.blade.php`<br>`transactions/issue.blade.php`<br>`transactions/return.blade.php` |
=======
| Controller  | `LoanApplicationController`, `EquipmentController`, `LoanTransactionController` |
| Model       | `Equipment`, `LoanApplication`, `LoanApplicationItem`, `LoanTransaction`      |
| Views       | `loans/create.blade.php`,`loans/show.blade.php`,`transactions/issue.blade.php`,`transactions/return.blade.php` |
>>>>>>> release/v4.0

### 4.3 Approval Workflow Module (Shared)

| Type        | Example File/Component       |
|-------------|-----------------------------|
| Controller  | `ApprovalController`         |
| Model       | `Approval`                  |
<<<<<<< HEAD
| Views       | `approvals/pending.blade.php`<br>`approvals/history.blade.php`<br>`approvals/show.blade.php` |
=======
| Views       | `approvals/pending.blade.php`,`approvals/history.blade.php`,`approvals/show.blade.php` |
>>>>>>> release/v4.0

### 4.4 Notification & Reporting

| Type        | Example File/Component                       |
|-------------|---------------------------------------------|
<<<<<<< HEAD
| Controller  | `NotificationController`<br>`ReportController` |
| Model       | `Notification`                              |
| Views       | `notifications/index.blade.php`<br>`reports/equipment.blade.php`<br>`reports/email-accounts.blade.php` |
=======
| Controller  | `NotificationController`,`ReportController` |
| Model       | `Notification`                              |
| Views       | `notifications/index.blade.php`,`reports/equipment.blade.php`,`reports/email-accounts.blade.php` |
>>>>>>> release/v4.0
| Services    | `NotificationService`                       |

---

### Laravel Project Structure Mapping

In a standard Laravel project (as per amralsaleeh/HRMS template), these components map to:

- **Controllers:** `/app/Http/Controllers/`
- **Models:** `/app/Models/`
- **Views:** `/resources/views/` (subfolders for each module)
- **Services:** `/app/Services/` (or similar)
- **Notifications:** `/app/Notifications/` (for Mailable/Notification classes)

Create files matching the controller, model, service, and view names listed above within the appropriate directories in your project.

---

## 5. Completeness Check

This document reflects and summarizes all relevant email feature details as described in:

- [System_Design_Rev_3.6.md](System_Design_Rev_3.6.md)
- [Loan_System_Flow_Mk_2.md](Loan_System_Flow_Mk_2.md)
- [Design_Document_Mk_2_MOTAC_IRMS.md](Design_Document_Mk_2_MOTAC_IRMS.md)
- [Core_User_&_Organizational_Data_Tables_Mk_2.md](Core_User_&_Organizational_Data_Tables_Mk_2.md)

All workflow steps, logical components, notification mechanisms, and project structure conventions are included.  
No key design elements or implementation requirements are missing compared to previous markdown documentation.

---
