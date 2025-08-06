[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<p align="center">
  <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS">
    <h1 align="center">MOTAC ICT LOAN</h1>
  </a>
  <h2 align="center">MOTAC Integrated Resource Management System</h2>
  <p align="center">
    A system for managing Email/User ID Provisioning and ICT Equipment Loans at the Ministry of Tourism, Arts and Culture, Malaysia.<br />
    Built upon the amralsaleeh/HRMS template structure.<br /><br />
    <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues">Report Bug</a>
    ·
    <a href="https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues">Request Feature</a>
  </p>
</p>
<br />

---

## Overview

**MOTAC Integrated Resource Management System** is a Laravel-based web application designed to centralize and streamline two core functions for the Ministry of Tourism, Arts and Culture (MOTAC), Malaysia:
- **Email/User ID Provisioning**
- **ICT Equipment Loan Management**

It provides unified workflows, robust business rules, and a consistent user experience, enhancing operational efficiency, security, and accountability.

---

## Features

- **Email/User ID Provisioning Management**  
  Automates the application, certification, approval, and provisioning process for official MOTAC email accounts and user IDs, mirroring MyMail application workflows.

- **ICT Equipment Loan Management**  
  Facilitates requests, approvals, issuance, tracking, and return of ICT equipment (laptops, projectors, etc.) for official use.

- **Unified Data Management**  
  Consolidates users, applications, approvals, equipment inventory, and notifications in a single MySQL database.

- **Streamlined Workflows & Process Automation**  
  Standardizes and automates application and approval processes, reducing administrative burdens.

- **Role-Based Access Control (RBAC) & Security**  
  Robust access management for users, approvers, BPM staff, and IT Admins, including grade-based approval logic and standardized role names.

- **User and Organizational Data Management**  
  Centralized records for system users, department, position, and grade—vital for workflow and approvals.

- **Dynamic Forms**  
  Utilizes Livewire for complex, dynamic forms (e.g., conditional fields as in MyMail application).

- **Real-Time Reporting & Notifications**  
  Insights into resource utilization and application statuses. Users notified of critical events via email and in-app notifications.

- **ICT Equipment Inventory Management**  
  Tracks detailed inventory of ICT equipment, including categories, sub-categories, locations, and status.

- **Audit Trails**  
  Automatic logging of key user actions (created_by, updated_by) for accountability.

- **Localization Support**  
  Primary UI language is Bahasa Melayu, with support for language switching and localized date formats.

---

## Built With

- [Laravel](https://laravel.com)
- [Livewire](https://livewire.laravel.com)

---

## Getting Started

### Requirements

- PHP 8.1 or later
- Composer
- MySQL

### Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS
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

Initial admin users should be created via seeding or designated registration. If using the base HRMS template, default credentials for development may be:
```text
email: admin@demo.com
password: admin
```
*Change these for production use. Actual admin credentials should be securely established and managed.*

---

## Contribution

Contributions are welcome from authorized MOTAC developers and users.  
- Use the [issue tracker](https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues) for feature requests and bug reports.
- For security vulnerabilities, refer to `SECURITY.md`.

---

## Contact

**Information Management Division (BPM)**  
Ministry of Tourism, Arts and Culture (MOTAC), Malaysia  
Project Link: [https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS]  
*Official contact email to be provided by MOTAC/BPM.*

---

## License

This project uses the MIT License if adopted from the base template.  
See [`LICENSE.md`](https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/blob/master/LICENSE.md) for details.  
MOTAC reserves the right to define specific licensing terms.

---

[contributors-shield]: https://img.shields.io/github/contributors/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS.svg?style=flat-square
[contributors-url]: https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS.svg?style=flat-square
[forks-url]: https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/network/members
[stars-shield]: https://img.shields.io/github/stars/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS.svg?style=flat-square
[stars-url]: https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/stargazers
[issues-shield]: https://img.shields.io/github/issues/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS.svg?style=flat-square
[issues-url]: https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/issues
[license-shield]: https://img.shields.io/github/license/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS.svg?style=flat-square
[license-url]: https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS/blob/master/LICENSE.md
