---
applyTo: '**'
---

# MyGovEA Design Principles (Prinsip Reka Bentuk MyGOVEA)

This repository follows the MyGovEA design principles which guide citizen-centric digital services. See the official source: [MyGovEA design principles](https://mygovea.jdn.gov.my/page-prinsip-reka-bentuk/)

## Core Principles (Senarai Prinsip Reka Bentuk)

1. Berpaksikan Rakyat (Citizen-Centric)
2. Berpacukan Data
3. Kandungan Terancang
4. Teknologi Bersesuaian
5. Antara Muka Minimalis dan Mudah
6. Seragam
7. Paparan/Menu Jelas
8. Realistik
9. Kognitif
10. Fleksibel
11. Komunikasi
12. Struktur Hierarki
13. Komponen Antara Muka dan Pengalaman Pengguna (UI/UX)
14. Tipografi
15. Tetapan Lalai
16. Kawalan Pengguna
17. Pencegahan Ralat
18. Panduan dan Dokumentasi

### Guidance and Short Explanations

Below are concise, developer-friendly explanations and actions to follow when implementing these principles in code and UI:

- Berpaksikan Rakyat (Citizen-Centric)
  - Focus on the user's needs and context. Conduct user research, include users in testing, and design flows that minimise errors. Prioritise accessibility and privacy. Make language clear and localised where appropriate.

- Berpacukan Data
  - Design data models and APIs for clear ownership, secure sharing, and consent-based access. Use consistent naming and document contracts (OpenAPI / API resources).

- Kandungan Terancang
  - Plan content and user journeys. Use structured content types and guardrails for authors (character limits, field guidance, validation messages).

- Teknologi Bersesuaian
  - Choose frameworks and stacks appropriate for the team's skills and project scale. Prefer long-term maintainable solutions and document rationale.

- Antara Muka Minimalis dan Mudah
  - Keep UIs simple. Use MYDS components and avoid visual clutter. Ensure clear affordances and consistent language.

- Seragam
  - Reuse tokens, components, and styles. Centralise theme overrides. Encourage a single source of truth for design tokens (`MYDS-Colour-Reference.md`).

- Paparan/Menu Jelas
  - Ensure navigation, labels, and menus are predictable. Use consistent ordering, truncation/ellipsis for long labels, and provide tooltips where helpful.

- Realistik
  - Account for device constraints, network variability, and development/runtime limits. Provide progressive enhancement and fallbacks.

- Kognitif
  - Reduce cognitive load: chunk information, prioritise primary actions, and avoid excessive choices. Use inline help and progressive disclosure.

- Fleksibel
  - Build modular, configurable components and expose sensible configuration options. Keep APIs backward compatible when possible.

- Komunikasi
  - Maintain clear channels between product, design, and engineering. Document decisions and rationale in the repo (CHANGELOG, design notes).

- Struktur Hierarki
  - Use semantic HTML and ARIA landmarks to expose document structure. Ensure headings follow a logical order and present clear visual hierarchy.

- Komponen UI/UX
  - Use MYDS React components when available; wrap them in local components if project-specific behaviour is required. Provide thorough examples and Storybook stories where possible.

- Tipografi
  - Load approved fonts (Poppins/Inter) and map typography tokens to CSS vars or Tailwind classes. Use the scale defined in `MYDS-Develop-Overview.md`.

- Tetapan Lalai
  - Choose secure, privacy-preserving defaults. Document defaults in configs and allow users to override in settings where appropriate.

- Kawalan Pengguna
  - Make controls discoverable and consistent. Ensure touch targets meet minimum size and interactive states are visible (hover/focus/active).

- Pencegahan Ralat
  - Validate early, show inline feedback, and require confirmations for destructive actions. Use `Callout` or `AlertDialog` for critical messages.

- Panduan dan Dokumentasi
  - Ship developer guides, examples, and user help. Keep docs versioned and close to the code (in-repo `docs/` or MD files).

### Implementation Checklist (for PR reviewers)

- Reference the principle(s) the change implements in the PR description.
- Ensure components use design tokens or tokens fallback to the `MYDS-Colour-Reference.md` values.
- Add accessibility checks: keyboard navigation, ARIA attributes, and color-contrast validation.
- Add or update Storybook/examples to demonstrate component usage in the intended context.

---
