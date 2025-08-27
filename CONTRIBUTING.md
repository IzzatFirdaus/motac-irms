# Contributing to MOTAC IRMS

Thank you for your interest in contributing to the MOTAC Integrated Resource Management System (MOTAC_IRMS)!  
We welcome and appreciate all community contributions—whether you found a bug, have a feature request, want to improve documentation, or wish to contribute code.

---

## Ways to Contribute

- **Reporting Bugs:**  
  If you find a bug, please [create an issue](../../issues) describing:
  - Steps to reproduce
  - Expected vs. actual behavior
  - Environment (OS, browser, device, etc.)

- **Suggesting Features or Improvements:**  
  [Open an issue](../../issues) and explain:
  - The improvement or feature proposal
  - Its benefit to MOTAC IRMS users

- **Submitting Pull Requests:**  
  We encourage direct code contributions! See below for workflow details.

- **Improving Documentation:**  
  Clear, up-to-date documentation is crucial. Feel free to suggest edits or submit PRs for documentation improvements.

---

## Pull Request Workflow

1. **Fork the Repository**

2. **Clone Your Fork Locally**

    ```bash
    git clone https://github.com/YOUR_USERNAME/MOTAC_IRMS.git
    ```

3. **Create a Descriptive Branch**

    ```bash
    git checkout -b feature/your-feature-name
    ```

4. **Make Your Changes**
    - Follow project coding standards and best practices.
    - Write clear, well-documented code.
    - Add or update tests as appropriate.

5. **Run Tests & Lint Code**
    - Ensure all tests pass (`php artisan test` or `npm run test` as applicable).
    - Check code style with Pint or Stylelint (`./vendor/bin/pint`, `npm run lint:css`).

6. **Commit with a Descriptive Message**

    ```bash
    git commit -m "Add feature: user can export reports as PDF"
    ```

7. **Push to Your Fork**

    ```bash
    git push origin feature/your-feature-name
    ```

8. **Open a Pull Request**
    - Go to your fork on GitHub and click "Compare & pull request".
    - Use the [Pull Request Template](./pull_request_template.md).
    - Reference any related issues (e.g., `Closes #123`).

---

## Coding & Documentation Guidelines

- Follow the existing **coding style** (`laravel` preset for PHP, PSR-12, Pint, and Stylelint for CSS).
- **Document** complex logic with inline comments.
- Write **clear, meaningful commit messages**.
- **Test** your changes—automated tests are preferred where possible.
- For UI or translations, ensure both English and Bahasa Melayu are updated.

---

## Communication & Collaboration

- Be polite and professional.
- Use clear and concise language.
- If your contribution is significant or architectural, consider opening a Discussion or Issue first to get feedback.
- Be patient—maintainers may need time to review and respond.

---

## Attribution & Licensing

- This project is developed by MOTAC Malaysia and based on the HRMS template by amralsaleeh.
- By contributing, you agree your contributions will be licensed under the MIT License.

---

## Need Help?

- Check existing [issues](../../issues) and [discussions](../../discussions).
- If you have questions, open a new discussion or contact a maintainer.

---

Terima kasih kerana menyumbang kepada MOTAC IRMS!  
Thank you for contributing to MOTAC IRMS!
