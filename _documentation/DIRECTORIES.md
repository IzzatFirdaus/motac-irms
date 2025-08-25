# Project Directory Structure

This document provides a cleaned, lint-friendly index of key application directories and representative files. Use it as a quick reference; run `git ls-files` for a full inventory.

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
````

## 2\. Livewire Component Classes (`app/Livewire`)

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

## 3\. Blade View Files (`resources/views`)

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
│   │   └── modal-sub-category.blade.php
│   ├── macros.blade.php
│   ├── rocket.blade.php
│   └── stat-card.blade.php
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

## Notes

- This file is intended as a quick index for maintainers. For a full file list, run `git ls-files` or inspect the repository in your IDE.

- If you want, I can add a small script or a README in `_documentation/` that auto-lists files and links into this index.
