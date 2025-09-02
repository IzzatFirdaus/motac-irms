# Comprehensive Plan for Iterative Update of Translation Files (Bahasa Melayu → English)

This plan combines the original "Plan for Iterative Update of Translation Files (Bahasa Melayu → English)" and the "Updated Iterative Update Plan for English Translation Files" into a unified, actionable workflow. It is tailored for the MOTAC IRMS Laravel codebase and ensures robust bilingual coverage, citizen-centricity, accessibility, maintainability, and compliance with MYDS/MyGOVEA standards.

---

## 1. Objectives & Standards

- **Goal:** Achieve complete bilingual (Bahasa Melayu & English) coverage for MOTAC IRMS, ensuring all user-facing text is accessible, citizen-centric, and maintainable.
- **Compliance:** Align strictly with MYDS (Malaysia Government Design System) and MyGOVEA principles, especially Citizen-Centricity (Berpaksikan Rakyat), accessibility (WCAG, ARIA), and government digital standards.
- **Accessibility First:** All components must be keyboard navigable, have ARIA labels, and meet MYDS minimum contrast ratios.
- **MYDS Components:** Use MYDS standard components for UI elements (buttons, forms, tables, etc.).
- **Error Prevention:** Ensure confirmation for destructive actions and clear error messages.

---

## 2. Inventory & Audit

### A. Inventory Malay Keys

- Extract all unique keys from each `resources/lang/ms/*.php` file.
- Categorize by module (e.g., app, dashboard, common, approvals, forms, loan-applications, equipment, reports, contact-us, statuses, validation, passwords, policy, terms, transaction, menu, messages, pagination, auth).
- Include nested arrays, dropdown options, and all user-facing elements.

### B. Audit Existing English Files

- Compare each English file (`resources/lang/en/*.php`) to its Malay counterpart.
- For each file:
  - List missing keys/sections.
  - Identify keys present in Malay but absent/incomplete in English.
  - Flag partial or placeholder translations.

---

## 3. Standardize Translation Structure

- Enforce consistent key naming (`snake_case`, logical grouping, nested arrays).
- Resolve duplicate/conflicting keys, standardizing usage across modules.
- Match array and nesting structure between ms and en files for maintainability.

---

## 4. Draft & Review English Translations

- For every missing Malay key, draft a clear, contextually accurate English translation.
- Use plain, professional language aligned to MYDS tone.
- Add `TODO` or comments for keys needing clarification.
- Ensure accessibility: All interactive text (buttons, ARIA labels, dialogs, error summaries) must be present and clear.

---

## 5. Iterative, Module-by-Module Update

### **Phase 1**: Foundation/Core Files

- Update and sync: `app.php`, `common.php`, `dashboard.php`, `menu.php`, `auth.php`, `passwords.php`, `validation.php`.
- Add missing keys, maintain order.
- Add comments for context if needed.

### **Phase 2**: Main Application Modules

- Update: `approvals.php`, `forms.php`, `loan-applications.php`, `equipment.php`, `statuses.php`, `transaction.php`.
- Fill gaps, align nested arrays, ensure dropdowns/options match Malay.
- Add accessibility and ARIA-related keys.

### **Phase 3**: Supporting Modules

- Update: `reports.php`, `contact-us.php`, `messages.php`, `pagination.php`, `policy.php`, `terms.php`.
- Confirm completeness and clarity for all user-facing text.

### **Phase 4**: Final Review

- Validate shared/common keys and ARIA/accessibility labels.
- Cross-check that every interactive element is translatable.

---

## 6. Accessibility & MYDS Audit

- Confirm keyboard navigation for all interactive elements.
- Ensure all ARIA labels are present and descriptive.
- Validate that no text relies solely on color for meaning (see MYDS guidelines).
- Test responsive layouts using the MYDS 12-8-4 grid system.
- Confirm translation keys for navigation, footer, and responsive components are present.

---

## 7. Codebase Consistency

- Refactor Blade views, PHP, and JS to use translation keys (`__('key.path')`), not literals.
- Remove hard-coded Malay from English code paths.
- Standardize translation usage across the entire codebase.

---

## 8. Testing & Validation

- Switch app language to English; manually test all screens for missing/fallback keys.
- Review for context, clarity, and accessibility.
- Check for missing keys, fallback behavior, and ARIA label coverage.

---

## 9. Documentation & Contributor Guidance

- Document translation conventions: key naming, module structure, update workflow.
- Provide onboarding notes for future contributors (how to add/sync keys, accessibility requirements, MYDS compliance).

---

## 10. Git Best Practices & Collaboration

- **Feature Branch:** Use descriptive, lowercase branch names (e.g., `feature/translation-en-sync`).
- **Atomic Commits:** Each commit represents one logical change (e.g., "Add missing English keys in dashboard_en.php").
- **Commit Messages:** Present tense, subject ≤50 chars, body for details and linked issues.
- **Pull Requests:** Central hub for review, discussion, and QA. Get stakeholder feedback before merging.
- **Repository Hygiene:** Maintain `.gitignore`, exclude unnecessary files.
- **Always `git pull` before `git push`** to minimize conflicts.

---

## 11. Citizen-Centricity & MYDS Reminders

- **Every label, button, status, and error must be translatable.**
- **No hard-coded Malay in English code paths.**
- **Use MYDS components, patterns, and accessibility guidelines throughout.**
- **All ARIA labels, skiplinks, and error messages must be present and clear.**
- **Use MYDS standard components for forms, tables, dialogs, navigation.**

---

## 12. Next Steps

1. **Phase 1:** Audit and update foundational English files.
2. **Phase 2:** Update and review module files iteratively.
3. **Phase 3:** Accessibility and ARIA label audit.
4. **Phase 4:** Final documentation and handover for maintainers.

---

**This plan ensures MOTAC IRMS translation files are complete, robust, citizen-centric, and fully compliant with MYDS/MyGOVEA standards.**
