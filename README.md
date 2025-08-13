[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<p align="center">
  <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS">
    <h1 align="center">MOTAC IRMS</h1>
  </a>
  <h2 align="center">MOTAC Integrated Resource Management System</h2>
  <p align="center">
    A centralized system for managing ICT Equipment Loans and a Helpdesk/Ticketing System at the Ministry of Tourism, Arts and Culture, Malaysia.<br />
    Based on the amralsaleeh/HRMS template structure and enhanced for MOTAC's operational needs.<br /><br />
    <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues">Report Bug</a>
    ·
    <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues">Request Feature</a>
  </p>
</p>
<br />

---

## Overview

**MOTAC Integrated Resource Management System (MOTAC IRMS)** is a Laravel-based web application designed to centralize, automate, and streamline key operational processes for the Ministry of Tourism, Arts and Culture (MOTAC), Malaysia.

Version 4.0 focuses on two core modules:
- **ICT Equipment Loan Management**
- **Helpdesk & ICT Support Management**

MOTAC IRMS provides robust business rules, unified workflows, and a modern user experience to enhance efficiency, security, and accountability.

---

## Features

- **ICT Equipment Loan Management** Handles requests, approvals, issuance, tracking, and returns for ICT equipment (laptops, projectors, etc.) for official use.

- **Helpdesk & Ticketing System** Manages IT support tickets from creation and assignment to resolution and reporting, streamlining the support process for all ICT-related issues.

- **Unified Data Management** Consolidates users, applications, support tickets, approvals, equipment inventory, and notifications in a single secure database.

- **Automated Workflows & Process Standardization** Streamlines application/approval processes, minimizing manual steps and administrative workload for both loans and support tickets.

- **Role-Based Access Control (RBAC) & Security** Fine-grained permissions for users, approvers, BPM staff, and IT Admins. Includes grade-based approval logic and standardized roles.

- **Dynamic Forms with Livewire** Supports complex, conditional forms for both loan applications and helpdesk ticket submission.

- **Real-Time Reporting & Notifications** Provides insights into resource usage, loan statuses, and helpdesk performance. Sends email and in-app notifications for key events.

- **ICT Equipment Inventory Management** Maintains detailed inventory with categories, sub-categories, physical locations, and status (available, on_loan, under_maintenance).

- **Audit Trails & Accountability** Logs key actions (created_by, updated_by) for traceability and compliance across all modules.

- **Localization & Bahasa Melayu Support** Primary UI in Bahasa Melayu, with language switching and localized dates supported.

---

## Built With

- [Laravel](https://laravel.com) - Modern PHP web framework
- [Livewire](https://livewire.laravel.com) - Dynamic interfaces for Laravel
- [Jetstream](https://jetstream.laravel.com/) - Authentication & team management
- [Vuexy](https://pixinvent.com/demo/vuexy-laravel-admin-dashboard-template/landing/) - Admin dashboard template (integrated via Jetstream)
- [Spatie Activitylog & Permissions](https://spatie.be/open-source) - Logging and RBAC
- [Maatwebsite Excel](https://laravel-excel.com/) - Data import/export
- [Dompdf](https://github.com/barryvdh/laravel-dompdf) - PDF generation
- [Log Viewer](https://github.com/opcodesio/log-viewer) - Log management
- And more (see `composer.json`)

---

## Getting Started

### Requirements

- PHP 8.2 or later
- Composer
- MySQL

### Installation

1. **Clone the repository:**
    ```bash
    git clone [https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS](https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS)
    ```
2. **Navigate to the project folder:**
    ```bash
    cd MOTAC_ICT_LOAN_HRMS
    ```
3. **Install dependencies:**
    ```bash
    composer install
    ```
4. **Environment setup:**
    - Copy `.env.example` to `.env`
    - Edit `.env` to set:
        - Database (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
        - `APP_URL` (your application's URL)
        - `APP_TIMEZONE` (`Asia/Kuala_Lumpur`)
        - Mail settings (`MAIL_MAILER`, etc. per `config/mail.php`)
5. **Generate application key:**
    ```bash
    php artisan key:generate
    ```
6. **Link storage:**
    ```bash
    php artisan storage:link
    ```
7. **Run migrations and (optionally) seed MOTAC-specific data:**
    ```bash
    php artisan migrate --seed
    ```
    *Use `--seed` if you have MOTAC-specific seeders for departments, grades, roles, etc.*
8. **Start the development server:**
    ```bash
    php artisan serve
    ```
9. **Access the application:**
    - Visit `http://localhost:8000` or your configured `APP_URL`

### Default Admin Usage (Development Example)

Initial admin users should be created via seeding or designated registration.  
If using the base HRMS template, default credentials for development may be:
```text
email: admin@demo.com
password: admin
````

*Change these for production use\! Actual admin credentials should be securely established and managed.*

-----

## Contribution

Contributions are welcome from authorized MOTAC developers and users.

  - Use the [issue tracker](https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues) for feature requests and bug reports.
  - For security vulnerabilities, refer to [`SECURITY.md`](SECURITY.md).

-----

## Contact

**Information Management Division (BPM)** Ministry of Tourism, Arts and Culture (MOTAC), Malaysia  
Project Link: [https://github.com/IzzatFirdaus/MOTAC\_ICT\_LOAN\_HRMS]  
*Official contact email to be provided by MOTAC/BPM.*

-----

## License

This project uses the MIT License if adopted from the base template.  
See [`LICENSE.md`](https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/blob/master/LICENSE.md) for details.  
MOTAC reserves the right to define specific licensing terms.
