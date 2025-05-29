[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
<p align="center">
  <a href="[https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS]">
    <h1 align="center">MOTAC ICT LOAN</h1>
  </a>

  <h2 align="center">MOTAC Integrated Resource Management System</h2>

  <p align="center">
    A system for managing Email/User ID Provisioning and ICT Equipment Loans at the Ministry of Tourism, Arts and Culture, Malaysia.
    <br />
    Built upon the amralsaleeh/HRMS template structure.
    <br />
    <br />
    <a href="[MOTAC_GIT_REPOSITORY_URL]/issues">Report Bug</a>
    ·
    <a href="[MOTAC_GIT_REPOSITORY_URL]/issues">Request Feature</a>
  </p>
</p>
<br />

The **MOTAC Integrated Resource Management System** is a web application designed to streamline and consolidate two key operational areas for the Ministry of Tourism, Arts and Culture (MOTAC), Malaysia: Email/User ID Provisioning and ICT Equipment Loan Management. It provides a unified, Laravel-based platform to optimize workflows, enforce business rules, and ensure a consistent user experience.

This system aims to enhance efficiency through automated processes, centralized data management, role-based access, real-time notifications, and comprehensive reporting capabilities tailored to MOTAC's specific needs.

### Built With
* [Laravel](https://laravel.com)
* [Livewire](https://livewire.laravel.com)

## Features

- **Email/User ID Provisioning Management:** Automates the application, certification, approval, and provisioning process for official MOTAC email accounts and user IDs, mirroring existing MyMail application workflows.
- **ICT Equipment Loan Management:** Facilitates the request, approval, issuance, tracking, and return of ICT equipment (laptops, projectors, etc.) for official use.
- **Unified Data Management:** Consolidates user data, applications, approvals, equipment inventory, and notifications in a single MySQL database.
- **Streamlined Workflows & Process Automation:** Automates and standardizes application and approval processes, reducing administrative burdens.
- **Role-Based Access Control (RBAC) & Security:** Ensures users, approvers, BPM staff, and IT Admins have appropriate access levels with robust security measures, including grade-based approval logic. Standardized role names (e.g., 'Admin', 'BPM Staff', 'IT Admin') are utilized.
- **User and Organizational Data Management:** Maintains centralized records of system users, including their department, position, and grade, crucial for workflows and approvals.
- **Dynamic Forms:** Utilizes Livewire for complex, dynamic application forms, such as replicating the conditional field logic of the MyMail application form.
- **Real-Time Reporting & Notifications:** Enables insights into resource utilization and application statuses. Users are notified of critical events via email and in-app (database) notifications.
- **ICT Equipment Inventory Management:** Manages a detailed inventory of ICT equipment, including categories, sub-categories, locations, and status.
- **Audit Trails:** Automatically records user actions for key data (created_by, updated_by) for accountability and tracking.
- **Support for Localization:** Adapts the system for use in Bahasa Melayu as the primary language, including UI text and date formats. The system architecture supports language switching.

## Getting Started

### Requirements
- PHP 8.1 or later.
- Composer.
- MySQL.

### Installation

1.  Download the source code using the following command:
    ```bash
    git clone [https://github.com/IzzatFirdaus/MOTAC_ICT_LOAN_HRMS]
    ```
2.  Navigate to the project folder:
    ```bash
    cd MOTAC_Integrated_Resource_Management_System # Or your chosen project folder name
    ```
3.  Install dependencies using Composer:
    ```bash
    composer install
    ```
4.  Set up the database and necessary configurations:
    * Copy the `.env.example` to `.env` file in the root of your project.
    * Open the `.env` file in the root of your project.
    * Set the database connection details: `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
    * Set the `APP_URL` to your application's URL.
    * Set the `APP_TIMEZONE` to 'Asia/Kuala_Lumpur'.
    * Configure `MAIL_MAILER` and other mail settings as per `config/mail.php` for notifications.
5.  Run the key generate command:
    ```bash
    php artisan key:generate
    ```
6.  Run the storage link command:
    ```bash
    php artisan storage:link
    ```
7.  Run the migration command. It is recommended to use specific seeders developed for the MOTAC system to populate necessary data like departments, grades, roles, equipment categories, etc.
    ```bash
    php artisan migrate # Add --seed if MOTAC-specific seeders are available
    ```
8.  Run the development server:
    ```bash
    php artisan serve
    ```
9.  Open your browser and go to `http://localhost:8000` (or your configured `APP_URL`) to see the application.

### Default Admin Usage (Example)
The system uses role-based access. Initial administrative users should be created through seeding or a designated registration process defined by MOTAC. For development, if based on the original HRMS template, example credentials might be:
    ```bash
    email: admin@demo.com
    password: admin
    ```
    *(Note: These credentials are for illustrative purposes if derived from the base template. Actual admin credentials for the MOTAC system will be established during setup and should be kept secure.)*

## Contribution
We welcome contributions from authorized developers and users within MOTAC. If you have ideas for improving the system or discover issues, please use the project's issue tracker.
For reporting security vulnerabilities, please refer to the `SECURITY.md` file.

## Contact

Information Management Division (BPM)
Ministry of Tourism, Arts and Culture (MOTAC), Malaysia
Project Link: `[MOTAC_GIT_REPOSITORY_URL]`
*(Official contact email for project queries to be provided by MOTAC/BPM)*

## License
This project may use the MIT License if adopted from the base template. Please see the `LICENSE.md` file for specific license information applicable to the MOTAC Integrated Resource Management System. MOTAC reserves the right to define the licensing terms.

[contributors-shield]: https://img.shields.io/github/contributors/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO].svg?style=flat-square
[contributors-url]: https://github.com/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO]/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO].svg?style=flat-square
[forks-url]: https://github.com/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO]/network/members
[stars-shield]: https://img.shields.io/github/stars/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO].svg?style=flat-square
[stars-url]: https://github.com/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO]/stargazers
[issues-shield]: https://img.shields.io/github/issues/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO].svg?style=flat-square
[issues-url]: https://github.com/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO]/issues
[license-shield]: https://img.shields.io/github/license/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO].svg?style=flat-square
[license-url]: https://github.com/[MOTAC_GITHUB_USER]/[MOTAC_GITHUB_REPO]/blob/master/LICENSE.md
