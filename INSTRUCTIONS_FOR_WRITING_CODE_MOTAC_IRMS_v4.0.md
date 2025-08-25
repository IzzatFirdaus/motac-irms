# Instructions for Writing Code in MOTAC IRMS v4.0 Project

This instruction set guides all developers on **how to write, structure, and document code** for the MOTAC Integrated Resource Management System (IRMS) v4.0, **referring to** the migration/integration plan (`MOTAC_IRMS_v4.0_Migration_and_Helpdesk_Integration_Plan.md`) and the full technical documentation (`Dokumentasi_*` files).

---

## 1. **General Coding Principles**

- **Follow the Migration & Integration Plan:**  
  All additions, deletions, and modifications must be guided by the step-by-step instructions in `MOTAC_IRMS_v4.0_Migration_and_Helpdesk_Integration_Plan.md`.
- **Reference Documentation:**  
  For details on models, controllers, workflows, UI, and business rules, always refer to the relevant `Dokumentasi_*` files.
- **Compliance:**  
  Ensure all code aligns with [MYDS](https://design.digital.gov.my/en/docs/design) standards and **18 Prinsip MyGOVEA**.
- **Atomic Commits:**  
  Each logical change should have its own commit with a clear, present-tense message.

---

## 2. **File Creation and Organization**

- **Models:**  
  - Place Eloquent models in `/app/Models/`, following naming conventions (`Ticket.php`, `LoanApplication.php`).
  - Use fields and relationships as described in the documentation (e.g., audit stamps, status fields, relationships).
- **Controllers:**  
  - Place controllers in `/app/Http/Controllers/`.
  - For feature modules, use subfolders like `/Helpdesk/`, `/Admin/`, `/ResourceManagement/`.
- **Livewire Components:**  
  - Place Livewire components in `/app/Livewire/`, organized by module (`ResourceManagement`, `Helpdesk`, etc).
  - Blade views for each component go in `/resources/views/livewire/`.
- **Services:**  
  - Business logic/service classes go in `/app/Services/`.
- **Policies:**  
  - Place authorization policies in `/app/Policies/`.
- **Observers:**  
  - Place observers in `/app/Observers/` (e.g., `BlameableObserver.php`).
- **Notifications/Mailables:**  
  - Use `/app/Notifications/` and `/app/Mail/` for notification/email classes.
  - Blade templates for emails go in `/resources/views/emails/`.
- **Factories/Seeders:**  
  - Place factories in `/database/factories/` and seeders in `/database/seeders/`.
- **Migrations:**  
  - Create migrations in `/database/migrations/`.
  - Follow the field specifications in documentation, including audit fields.
- **Helpers:**  
  - Shared utility functions go in `/app/Helpers/Helpers.php`.

---

## 3. **Coding Standards and Best Practices**

- **Laravel Standards:**  
  - Use Eloquent ORM for models and relationships.
  - Use FormRequest classes for validation.
  - Use resource controllers for CRUD and RESTful endpoints.
- **Naming:**  
  - Use singular for models (`Ticket`, `Equipment`) and plural for tables (`tickets`, `equipment`).
  - Use descriptive names for functions and variables.
- **Documentation:**  
  - Add docblocks for all classes, methods, and complex logic.
  - Inline comments for non-trivial code.
- **Testing:**  
  - Write feature and unit tests for all major workflows.
  - Place tests in `/tests/Feature/` and `/tests/Unit/`.
- **Accessibility & UI:**  
  - For Blade templates and Livewire, use MYDS-compliant classes, components, and ARIA attributes.
  - Ensure forms are accessible, responsive, and follow design guidelines.

---

## 4. **Migration/Refactoring Instructions**

Follow the **exact steps** in the migration plan:

- **Remove Legacy Email Application Module:**  
  - Delete all code, migrations, views, policies, notifications, and tests related to email provisioning.
  - Update existing models/tables to remove legacy fields.
- **Integrate Helpdesk System:**  
  - Add new models, migrations, controllers, policies, Livewire components, and views as described.
  - Use relationships and field definitions from documentation.
- **Database Changes:**  
  - For every new/modified model, create appropriate migrations.
  - Always include audit fields (`created_by`, `updated_by`, `deleted_by`, timestamps).
- **Shared Components:**  
  - Use shared helpers, observers, and notification mechanisms for consistency.
- **Configuration:**  
  - Update `/config/motac.php` for new settings and remove unused keys.
- **Menu/UI Updates:**  
  - Update navigation menu files (`verticalMenu.json`, sidebar Blade templates) to reflect only current modules (Pinjaman ICT, Helpdesk).

---

## 5. **Documentation and Comments**

- **Update README.md:**  
  - Document all new modules, workflows, and changes.
- **User Manual:**  
  - Update with new workflows for Pinjaman ICT and Helpdesk.
- **Inline Code Documentation:**  
  - Use clear comments in code, especially for business logic and validation.
- **System Design Document:**  
  - Keep the design document (`Dokumentasi_Reka_Bentuk_*`) updated with any architectural changes.

---

## 6. **Quality Assurance**

- **Review Against Compliance Checklist:**  
  - Ensure code meets all MYDS and MyGOVEA principles.
- **Browser Testing:**  
  - Test on all supported browsers and devices.
- **Performance:**  
  - Optimize queries and UI for fast load times.
- **Accessibility:**  
  - Audit forms and dashboards for WCAG 2.1 AA compliance.

---

## 7. **Sample Workflow for Writing New Code**

1. **Read the Migration Plan & Documentation.**
2. **Identify the module/component to be implemented or refactored.**
3. **Create/modify files as per structure and naming conventions.**
4. **Write code following Laravel and MYDS standards.**
5. **Add detailed comments and docblocks.**
6. **Write/Update relevant tests.**
7. **Update documentation and user manuals.**
8. **Commit with atomic, present-tense message.**

---

## 8. **Commit Message Guidelines**

- Use present tense (e.g., "Add helpdesk ticket model", "Remove legacy email provisioning code").
- Keep subject under 50 characters.
- Use body for detailed explanation and link related issues (e.g., "Closes #42").
- Make small, atomic commits.

---

## 9. **Final Validation**

- **Before merging:**  
  - Run all tests.
  - Check for documentation completeness.
  - Confirm compliance with MYDS and MyGOVEA principles.

---

## References

- `MOTAC_IRMS_v4.0_Migration_and_Helpdesk_Integration_Plan.md`
- All `Dokumentasi_*` files
- [MYDS Design Guidelines](https://design.digital.gov.my/en/docs/design)
- [Prinsip Reka Bentuk MyGOVEA](https://mygovea.jdn.gov.my/page-prinsip-reka-bentuk/)

---

**For any new feature, bugfix, or refactor, always start by reading the migration/integration plan and the relevant technical documentation.**
