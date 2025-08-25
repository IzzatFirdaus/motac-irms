# Project Directory Structure

This document lists all key PHP Controller files in `app/Http/Controllers`, all Livewire PHP component files in `app/Livewire`, and all Blade view files in `resources/views` (including Livewire views), organized by their respective directories and subdirectories.

---

## 1. Controller Classes (`app/Http/Controllers`)

```text
app/Http/Controllers/
├── Admin/
│   ├── DepartmentController.php
│   ├── EquipmentController.php
│   ├── GradeController.php
│   ├── PositionController.php
│   └── UserController.php
├── Api/
│   └── HelpdeskApiController.php
├── Helpdesk/
│   └── TicketController.php
├── language/
│   └── LanguageController.php
├── ApprovalController.php
├── Controller.php
├── DashboardController.php
├── EquipmentController.php
├── LegalController.php
├── LoanApplicationController.php
├── LoanTransactionController.php
├── MiscErrorController.php
├── NotificationController.php
├── ReportController.php
└── WebhookController.php
```

---

## 2. Livewire Component Classes (`app/Livewire`)

```text
app/Livewire/
├── Charts/
│   └── LoanSummaryChart.php
├── ContactUs.php
├── Dashboard/
│   ├── AdminDashboard.php
│   ├── ApproverDashboard.php
│   ├── BpmDashboard.php
│   ├── Dashboard.php
│   ├── ItAdminDashboard.php
│   └── UserDashboard.php
├── EquipmentChecklist.php
├── Helpdesk/
│   ├── Admin/
│   │   ├── TicketManagement.php
│   │   └── TicketReport.php
│   ├── CreateTicketForm.php
│   ├── MyTicketsIndex.php
│   ├── TicketDetail.php
│   ├── TicketDetails.php
│   ├── TicketForm.php
│   └── TicketList.php
├── HumanResource/
│   └── Structure/
│       ├── Departments.php
│       ├── EmployeeInfo.php
│       └── Positions.php
├── LoanRequestForm.php
├── Misc/
│   └── ComingSoon.php
├── ResourceManagement/
│   ├── Admin/
│   │   ├── BPM/
│   │   │   ├── IssuedLoans.php
│   │   │   ├── OutstandingLoans.php
│   │   │   ├── ProcessIssuance.php
│   │   │   └── ProcessReturn.php
│   │   ├── Equipment/
│   │   │   ├── EquipmentForm.php
│   │   │   └── EquipmentIndex.php
│   │   ├── Grades/
│   │   │   └── GradeIndex.php
│   │   ├── Reports/
│   │   │   ├── EquipmentInventoryReport.php
│   │   │   ├── EquipmentReport.php
│   │   │   ├── LoanApplicationsReport.php
│   │   │   └── UserActivityReport.php
│   │   └── Users/
│   │       └── UserIndex.php
│   ├── Approval/
│   │   ├── ApprovalDashboard.php
│   │   └── ApprovalHistory.php
│   ├── LoanApplication/
│   │   └── LoanApplicationForm.php
│   ├── MyApplications/
│   │   └── Loan/
│   │       └── LoanApplicationsIndex.php
│   └── Reports/
│       ├── EquipmentReport.php
│       ├── LoanApplicationsReport.php
│       ├── ReportsIndex.php
│       └── UserActivityReport.php
├── Sections/
│   ├── Footer/
│   │   └── Footer.php
│   ├── Menu/
│   │   └── VerticalMenu.php
│   └── Navbar/
│       ├── Navbar.php
│       └── NotificationsDropdown.php
├── Settings/
│   ├── Departments/
│   │   └── DepartmentsIndex.php
│   ├── Permissions/
│   │   └── PermissionsIndex.php
│   ├── Roles/
│   │   └── RolesIndex.php
│   └── Users/
│       ├── UsersCreate.php
│       ├── UsersEdit.php
│       ├── UsersIndex.php
│       └── UsersShow.php
├── Shared/
│   ├── Notifications/
│   │   └── NotificationsList.php
│   └── TableFilters.php
```

## 3. Blade View Files (`resources/views`)

```text
├── _partials/
│   ├── \_alerts/
│   │   └── alert-general.blade.php
│   ├── \_modals/
│   │   ├── modal-category-info.blade.php
│   │   ├── modal-category.blade.php
│   │   ├── modal-department.blade.php
│   │   ├── modal-import.blade.php
│   │   ├── modal-leave-with-employee.blade.php
│   │   ├── modal-motac-generic.blade.php
│   │   ├── modal-position.blade.php
│   │   └── modal-sub-category.blade.php
│   ├── macros.blade.php
│   ├── rocket.blade.php
│   └── stat-card.blade.php

```text
app/Livewire/
├── Charts/


├── welcome.blade.php
```text
│   └── LoanSummaryChart.php
├── Dashboard/
│   ├── AdminDashboard.php
│   ├── ApproverDashboard.php
│   ├── BpmDashboard.php
│   ├── Dashboard.php
│   ├── ItAdminDashboard.php
│   └── UserDashboard.php
├── EquipmentChecklist.php
├── Helpdesk/
│   ├── Admin/
│   │   └── TicketManagement.php
│   ├── CreateTicketForm.php
│   ├── MyTicketsIndex.php
│   ├── TicketDetail.php
│   ├── TicketDetails.php
│   ├── TicketForm.php
│   └── TicketList.php
├── HumanResource/
│   └── Structure/
│       ├── Departments.php
│       ├── EmployeeInfo.php
│       └── Positions.php
├── LoanRequestForm.php
├── Misc/
│   └── ComingSoon.php
├── ResourceManagement/
│   ├── Admin/
│   │   ├── BPM/
│   │   │   ├── IssuedLoans.php
│   │   │   ├── OutstandingLoans.php
│   │   │   ├── ProcessIssuance.php
│   │   │   └── ProcessReturn.php
│   │   ├── Equipment/
│   │   │   ├── EquipmentForm.php
│   │   │   └── EquipmentIndex.php
│   │   ├── Grades/
│   │   │   └── GradeIndex.php
│   │   ├── Reports/
│   │   │   ├── EquipmentInventoryReport.php
│   │   │   ├── EquipmentReport.php
│   │   │   ├── LoanApplicationsReport.php
│   │   │   └── UserActivityReport.php
│   │   └── Users/
│   │       └── UserIndex.php
│   ├── Approval/
│   │   ├── ApprovalDashboard.php
│   │   └── ApprovalHistory.php
│   ├── LoanApplication/
│   │   └── LoanApplicationForm.php
│   ├── MyApplications/
│   │   └── Loan/
│   │       └── LoanApplicationsIndex.php
│   └── Reports/
│       ├── EquipmentReport.php
│       ├── LoanApplicationsReport.php
│       ├── ReportsIndex.php
│       └── UserActivityReport.php
├── Sections/
│   ├── Footer/
│   │   └── Footer.php
│   ├── Menu/
│   │   └── VerticalMenu.php
│   └── Navbar/
│       ├── Navbar.php
│       └── NotificationsDropdown.php
├── Settings/
│   ├── Departments/
│   │   └── DepartmentsIndex.php
│   ├── Permissions/
│   │   └── PermissionsIndex.php
│   ├── Roles/
│   │   └── RolesIndex.php
│   └── Users/
│       ├── UsersCreate.php
│       ├── UsersEdit.php
│       ├── UsersIndex.php
│       └── UsersShow.php
├── Shared/
│   ├── Notifications/
│   │   └── NotificationsList.php
│   └── TableFilters.php
```text

---

## 3. Blade View Files (`resources/views`)

```text
---
├── \_partials/
│   ├── \_alerts/
│   │   └── alert-general.blade.php
│   ├── \_modals/
│   │   ├── modal-category-info.blade.php
│   │   ├── modal-category.blade.php
│   │   ├── modal-department.blade.php
│   │   ├── modal-import.blade.php

## 3. Blade View Files (`resources/views`)

```text
resources/views/
├── _partials/
│   ├── _alerts/
│   │   └── alert-general.blade.php
│   ├── _modals/
│   │   ├── modal-category-info.blade.php
│   │   ├── modal-category.blade.php
│   │   ├── modal-department.blade.php
│   │   ├── modal-import.blade.php
│   │   ├── modal-leave-with-employee.blade.php
│   │   ├── modal-motac-generic.blade.php
│   │   ├── modal-position.blade.php
│   │   └── modal-sub-category.blade.php
│   ├── macros.blade.php
│   ├── rocket.blade.php
│   └── stat-card.blade.php
├── admin/
│   ├── departments/
│   │   ├── department-index.blade.php
│   │   ├── department-show.blade.php
│   │   ├── departments-create.blade.php
│   │   └── departments-edit.blade.php
│   ├── equipment/
│   │   ├── equipment-create.blade.php
│   │   ├── equipment-edit.blade.php
│   │   ├── equipment-index.blade.php
│   │   └── equipment-show.blade.php
│   ├── grades/
│   │   ├── grade-create.blade.php
│   │   ├── grade-edit.blade.php
│   │   ├── grade-index.blade.php
│   │   └── grade-show.blade.php
│   ├── positions/
│   │   ├── position-create.blade.php
│   │   ├── position-edit.blade.php
│   │   ├── position-index.blade.php
│   │   └── position-show.blade.php
│   ├── profiles/
│   │   ├── partials/
│   │   │   ├── input-email.blade.php
│   │   │   ├── input-password.blade.php
│   │   │   └── input-text.blade.php
│   │   └── profile-edit.blade.php
│   └── users/
│       ├── user-create.blade.php
│       ├── user-edit.blade.php
│       ├── user-index.blade.php
│       └── user-show.blade.php
├── api/
│   ├── api-token-manager-index.blade.php
│   └── api-token-manager-page.blade.php
├── approvals/
│   ├── approvals-history.blade.php
│   ├── approvals-index.blade.php
│   ├── approvals-show.blade.php
│   └── comments.blade.php
├── auth/
│   ├── confirm-password-page.blade.php
│   ├── confirm-password.blade.php
│   ├── forgot-password-page.blade.php
│   ├── forgot-password.blade.php
│   ├── login-page.blade.php
│   ├── login.blade.php
│   ├── register-page.blade.php
│   ├── register.blade.php
│   ├── reset-password-page.blade.php
│   ├── reset-password.blade.php
│   ├── two-factor-challenge-page.blade.php
│   ├── two-factor-challenge.blade.php
│   ├── verify-email-page.blade.php
│   └── verify-email.blade.php
├── components/
│   ├── report/
│   │   └── report-card.blade.php
│   ├── action-message.blade.php
│   ├── action-section.blade.php
│   ├── alert.blade.php
│   ├── applicant-details-readonly.blade.php
│   ├── application-logo.blade.php
│   ├── application-mark.blade.php
│   ├── approval-status-badge.blade.php
│   ├── authentication-card-logo.blade.php
│   ├── authentication-card.blade.php
│   ├── back-button.blade.php
│   ├── banner.blade.php
│   ├── boolean-badge.blade.php
│   ├── button.blade.php
│   ├── card.blade.php
│   ├── checkbox.blade.php
│   ├── confirmation-modal.blade.php
│   ├── confirms-password.blade.php
│   ├── danger-button.blade.php
│   ├── dialog-modal.blade.php
│   ├── dropdown-link.blade.php
│   ├── dropdown.blade.php
│   ├── email-application-status-badge.blade.php
│   ├── equipment-status-badge.blade.php
│   ├── form-section.blade.php
│   ├── input-error.blade.php
│   ├── input.blade.php
│   ├── label.blade.php
│   ├── loan-application-status-badge.blade.php
│   ├── loan-transaction-status-badge.blade.php
│   ├── modal.blade.php
│   ├── nav-link.blade.php
│   ├── resource-status-panel.blade.php
│   ├── responsive-nav-link.blade.php
│   ├── secondary-button.blade.php
│   ├── section-border.blade.php
│   ├── section-title.blade.php
│   ├── sort-icon.blade.php
│   ├── switchable-team.blade.php
│   ├── user-info-card.blade.php
│   ├── user-status-badge.blade.php
│   └── validation-errors.blade.php
├── content/
│   └── pages-misc-error-page.blade.php
├── dashboard/
│   ├── admin-dashboard.blade.php
│   ├── approver-dashboard.blade.php
│   ├── bpm-dashboard.blade.php
│   ├── itadmin-dashboard.blade.php
│   └── user-dashboard.blade.php
├── emails/
│   ├── helpdesk/
│   │   ├── ticket-assigned.blade.php
│   │   ├── ticket-comment-added.blade.php
│   │   ├── ticket-created.blade.php
│   │   └── ticket-status-updated.blade.php
│   ├── notifications/
│   │   └── motac_default_notification.blade.php
│   ├── _partials/
│   │   └── email-header.blade.php
│   ├── application-approved.blade.php
│   ├── application-needs-action.blade.php
│   ├── application-rejected.blade.php
│   ├── application-submitted-notification.blade.php
│   ├── equipment-return-reminder.blade.php
│   ├── equipment-returned.blade.php
│   ├── loan-application-issued.blade.php
│   ├── loan-application-overdue-reminder.blade.php
│   ├── loan-application-ready-for-issuance.blade.php
│   ├── loan-application-rejected.blade.php
│   ├── loan-application-returned.blade.php
│   ├── team-invitation.blade.php
│   └── test-email.blade.php
├── equipment/
│   ├── partials/
│   │   └── equipment-session-messages.blade.php
│   ├── equipment-create.blade.php
│   ├── equipment-edit.blade.php
│   ├── equipment-index.blade.php
│   └── equipment-show.blade.php
├── errors/
│   ├── 401.blade.php
│   ├── 403.blade.php
│   ├── 404.blade.php
│   ├── 419.blade.php
│   ├── 422.blade.php
│   ├── 429.blade.php
│   ├── 500.blade.php
│   └── 503.blade.php
├── helpdesk/
│   ├── ticket-create.blade.php
│   ├── ticket-edit.blade.php
│   ├── ticket-index.blade.php
│   └── ticket-show.blade.php
├── layouts/
│   ├── sections/
│   │   ├── footer/
│   │   │   └── footer-section.blade.php
│   │   ├── menu/
│   │   │   ├── submenu-partial.blade.php
│   │   │   └── submenu.blade.php
│   │   ├── layout-scripts-includes.blade.php
│   │   ├── layout-scripts.blade.php
│   │   └── layout-styles.blade.php
│   ├── app.blade.php
│   ├── commonMaster.blade.php
│   ├── email.blade.php
│   ├── layout-blank.blade.php
│   ├── layout-content-navbar.blade.php
│   └── layout-master.blade.php
├── loan-applications/
│   ├── pdf/
│   │   └── loan-application-print-form.blade.php
│   ├── loan-application-create.blade.php
│   ├── loan-application-edit.blade.php
│   ├── loan-application-index.blade.php
│   └── loan-application-show.blade.php
├── loan-transactions/
│   ├── loan-transaction-index.blade.php
│   ├── loan-transaction-issue.blade.php
│   ├── loan-transaction-issued-list.blade.php
```markdown
# Project Directory Structure

This document provides a single, cleaned reference of key application directories and notable files. It lists main controller classes in `app/Http/Controllers`, Livewire component namespaces in `app/Livewire`, and the top-level organization of Blade views in `resources/views`.

Where possible this file documents directory trees and representative files — use it as a quick index, not a complete file inventory.

---

## 1. Controller Classes (`app/Http/Controllers`)

```text
app/Http/Controllers/
├── Admin/
│   ├── DepartmentController.php
│   ├── EquipmentController.php
│   ├── GradeController.php
│   ├── PositionController.php
│   └── UserController.php
├── Api/
│   └── HelpdeskApiController.php
├── Helpdesk/
│   └── TicketController.php
├── language/
│   └── LanguageController.php
├── ApprovalController.php
├── Controller.php
├── DashboardController.php
├── EquipmentController.php
├── LegalController.php
├── LoanApplicationController.php
├── LoanTransactionController.php
├── MiscErrorController.php
├── NotificationController.php
├── ReportController.php
└── WebhookController.php
```

---

## 2. Livewire Component Classes (`app/Livewire`)

```text
app/Livewire/
├── Charts/
│   └── LoanSummaryChart.php
├── ContactUs.php
├── Dashboard/
│   ├── AdminDashboard.php
│   ├── ApproverDashboard.php
│   ├── BpmDashboard.php
│   ├── Dashboard.php
│   ├── ItAdminDashboard.php
│   └── UserDashboard.php
├── EquipmentChecklist.php
├── Helpdesk/
│   ├── Admin/
│   │   ├── TicketManagement.php
│   │   └── TicketReport.php
│   ├── CreateTicketForm.php
│   ├── MyTicketsIndex.php
│   ├── TicketDetail.php
│   ├── TicketDetails.php
│   ├── TicketForm.php
│   └── TicketList.php
├── HumanResource/
│   └── Structure/
│       ├── Departments.php
│       ├── EmployeeInfo.php
│       └── Positions.php
├── LoanRequestForm.php
├── Misc/
│   └── ComingSoon.php
├── ResourceManagement/
│   ├── Admin/
│   │   ├── BPM/
│   │   │   ├── IssuedLoans.php
│   │   │   ├── OutstandingLoans.php
│   │   │   ├── ProcessIssuance.php
│   │   │   └── ProcessReturn.php
│   │   ├── Equipment/
│   │   │   ├── EquipmentForm.php
│   │   │   └── EquipmentIndex.php
│   │   ├── Grades/
│   │   │   └── GradeIndex.php
│   │   ├── Reports/
│   │   │   ├── EquipmentInventoryReport.php
│   │   │   ├── EquipmentReport.php
│   │   │   ├── LoanApplicationsReport.php
│   │   │   └── UserActivityReport.php
│   │   └── Users/
│   │       └── UserIndex.php
│   ├── Approval/
│   │   ├── ApprovalDashboard.php
│   │   └── ApprovalHistory.php
│   ├── LoanApplication/
│   │   └── LoanApplicationForm.php
│   ├── MyApplications/
│   │   └── Loan/
│   │       └── LoanApplicationsIndex.php
│   └── Reports/
│       ├── EquipmentReport.php
│       ├── LoanApplicationsReport.php
│       ├── ReportsIndex.php
│       └── UserActivityReport.php
├── Sections/
│   ├── Footer/
│   │   └── Footer.php
│   ├── Menu/
│   │   └── VerticalMenu.php
│   └── Navbar/
│       ├── Navbar.php
│       └── NotificationsDropdown.php
├── Settings/
│   ├── Departments/
│   │   └── DepartmentsIndex.php
│   ├── Permissions/
│   │   └── PermissionsIndex.php
│   ├── Roles/
│   │   └── RolesIndex.php
│   └── Users/
│       ├── UsersCreate.php
│       ├── UsersEdit.php
│       ├── UsersIndex.php
│       └── UsersShow.php
└── Shared/
	├── Notifications/
	│   └── NotificationsList.php
	└── TableFilters.php
```

---

## 3. Blade View Files (`resources/views`)

This is a representative top-level layout of `resources/views`. Use the real project tree for exact filenames.

```text
resources/views/
├── _partials/
│   ├── _alerts/
│   │   └── alert-general.blade.php
│   ├── _modals/
│   │   ├── modal-category-info.blade.php
│   │   ├── modal-category.blade.php
│   │   ├── modal-department.blade.php
│   │   ├── modal-import.blade.php
│   │   ├── modal-leave-with-employee.blade.php
│   │   ├── modal-motac-generic.blade.php
│   │   ├── modal-position.blade.php
+│   │   └── modal-sub-category.blade.php
├── admin/
│   ├── departments/
│   ├── equipment/
│   ├── grades/
│   ├── positions/
│   ├── profiles/
│   └── users/
├── auth/
├── components/
├── dashboard/
├── emails/
├── equipment/
├── errors/
├── helpdesk/
├── layouts/
├── loan-applications/
├── loan-transactions/
├── notifications/
├── pages/
├── partials/
├── profile/
├── reports/
├── transactions/
├── users/
└── welcome.blade.php
```

---

Notes
- This file is intended as a quick index for maintainers. For a full file list, run `git ls-files` or inspect the repository in your IDE.
- If you want, I can add a small script or a README in `_documentation/` that auto-lists files and links into this index.
