---
applyTo: '**'
---

# Malaysia Government Design System (MYDS) AI Development Instructions (2025)

_Last updated: August 2025_

- This file defines repository-specific rules for AI assistants when generating UI code that must comply with the Malaysia Government Design System (MYDS).
- Do not rely on out-of-date knowledge — fetch the official MYDS docs when in doubt:
	- MYDS Home: https://design.digital.gov.my/en
	- Design Guidelines: https://design.digital.gov.my/en/docs/design
	- Development Docs: https://design.digital.gov.my/en/docs/develop
	- Colour guidance: https://design.digital.gov.my/en/docs/design/color

## About This File

- Location: `.github/instructions/` — the VS Code AI agent will read this file when active in the repository.
- Purpose: Force AI-assisted UI work to behave as an MYDS expert and ensure generated UI code follows official tokens, components, and accessibility rules.

## Design Overview

This section imports the repository's MYDS design overview so agents have a concise reference for system goals, patterns, and component guidance.

### Introduction to MYDS

As digital government services become more widely used, maintaining high-quality, accessible platforms is essential. The Malaysia Government Design System (MYDS) supports this goal by providing standard tools, templates, and guidelines that enable agencies to build fast, user-friendly, and consistent digital services.

Refer to the official design docs for the latest guidance: https://design.digital.gov.my/en/docs/design

### What is MYDS?

MYDS is a comprehensive design system for Malaysian government websites and digital platforms. It includes:

- Components: Pre-built UI elements (buttons, forms, navigation, etc.) that promote consistency and speed development.
- Theme Customizer: Tools for tailoring colours and styles while preserving a unified identity.
- Patterns: Reusable layouts and patterns for common scenarios (login, data entry, dashboards).
- Design Asset Library: Centralized design files for prototyping and consistent implementation.

### Why Use MYDS?

- Consistency: Creates a cohesive look and feel across government services.
- Rapid Development: Reusable components reduce design and engineering effort.
- Accessibility: Adheres to WCAG guidance to support inclusive access.
- Scalability: Components and tokens enable safe customization per agency needs.

### Use Cases

- Agency websites (information pages, announcements).
- Dashboards and portals for citizen services and internal tools.

### Resources

- Figma and design asset libraries (use official MYDS files when prototyping).

### MYDS Grid System (12-8-4)

The MYDS grid is responsive and supports 12/8/4 columns across desktop, tablet, and mobile:

- Desktop (≥ 1024px): 12 columns, 24px gutters.
- Tablet (768–1023px): 8 columns, 24px gutters.
- Mobile (≤ 767px): 4 columns, 18px gutters.

Usage notes:

- Article content typically constrains to a readable max width (about 640px).
- Images and visualisations can extend slightly beyond the article width for impact (up to ~740px) where appropriate.

### MYDS Colour System (overview)

Colour guidance in MYDS defines primary, semantic, and primitive tokens for light and dark modes. Use the colour tokens in `MYDS-Colour-Reference.md` and prefer semantic tokens (e.g., `bg-primary-600`, `txt-danger`) in code so themes map correctly.

Key points:

- Primitive colours: Base swatches used across tokens.
- Semantic tokens: Role-based tokens for backgrounds, text, outlines, and focus rings.
- Ensure contrast meets WCAG minimums (4.5:1 for body text where applicable).

### Typography

- Headings: Poppins for page and section headers.
- Body: Inter for paragraphs and long-form content.
- Follow the size/weight/line-height scale defined in the design assets for headings and body text.

### Icon, Motion, Radius, Shadow, and Spacing Systems (summary)

- Icons: Designed on a 20×20 grid; stroke and filled variants are available. Keep stroke widths consistent when exporting SVGs.
- Motion: Use motion tokens (instant, linear, easeout, easeoutback) and recommended durations (short 200ms, medium 400ms, long 600ms).
- Radius: Standard radius scale (xs, s, m, l, xl, full) for consistent corner rounding.
- Shadow: Use system shadow tokens for buttons, cards, and overlays to provide depth without excessive contrast.
- Spacing: Use the spacing scale (4, 8, 12, 16, 24, 32, etc.) and prefer gap utilities for lists and flex layouts.

### Component Guidance (high-level)

The design overview contains detailed component examples; agents should reference specific component sections elsewhere in this file (for example: Accordion, Alert Dialog, Button, Callout, Date Picker, File Upload). When generating code:

- Prefer MYDS components (`@govtechmy/myds-react`) when available.
- Keep components accessible: keyboard focus, ARIA attributes, and colour contrast.
- Reuse tokens and avoid hard-coded hex values unless implementing a new primitive that must be added to the token list.

### Notes

- This overview is a concise reference; the full design details are available in `MYDS-Design-Overview.md` and in the official docs at https://design.digital.gov.my/en/docs/design.

## Development Overview

This section provides implementation-focused guidance for developers working with MYDS components, tokens, and patterns. For full developer docs see: https://design.digital.gov.my/en/docs/develop

### MYDS Design System: Core Guidelines (Developer-facing)

- Purpose: Provide consistent, accessible, and maintainable UI primitives for government digital services.
- Focus: Implementation details (component imports, props, tokens, theming, and accessibility hooks).

### 1. Foundations (Developer)

#### A. Colors

- Use semantic tokens from `MYDS-Colour-Reference.md` (e.g., `bg-primary-600`, `otl-divider`) rather than hard-coded hex values.
- Respect light/dark mappings and prefer token names so theme switching works automatically.
- Dev link: https://design.digital.gov.my/en/docs/develop

#### B. Typography

- Load fonts (Poppins and Inter) via the approved method in your app (font-display: swap recommended).
- Map typography tokens to CSS variables or Tailwind theme values to ensure consistent sizing and responsive adjustments.

#### C. Spacing & Layout

- Implement the 12-8-4 grid using CSS grid utilities or Tailwind classes. Provide container, article, and content layout helpers.
- Use the spacing scale (4, 8, 12, 16, 24, 32, etc.) as design tokens.

### 2. UI Components (Developer guidance)

- Preferred package: `@govtechmy/myds-react` (import components from the package or local wrappers).
- Component composition: follow the official component anatomy (for example: `AlertDialog` must use `AlertDialogContent`, `AlertDialogHeader`, `AlertDialogFooter`).
- Accessibility: provide `aria-*` attributes, keyboard handlers, and focus management exactly as in the docs.

### 3. Implementation Patterns

- Theming: centralise token overrides in a theme file. Map tokens to CSS variables at root to enable runtime theme switching.
- Controlled vs uncontrolled: Follow documented component conventions (`defaultOpen` vs `open` + `onOpenChange`).
- State: prefer lifting state into parent components for complex flows and use the component's controlled API when available.

### 4. Component Examples & Props (summary)

- AlertDialog: `variant`, `open`, `defaultOpen`, `onOpenChange`, `dismissible`.
- Button: `variant`, `size`, `iconOnly`, `className`.
- DataTable: `columns`, `data`, `nest`, `pin`, and features like sorting, selection, and expandable rows.
- DatePicker/DateField: support controlled/uncontrolled modes; provide `disabled` matchers for date constraints.

### 5. Accessibility (A11y) for Developers

- Keyboard navigation: ensure all interactive elements are reachable and operable via keyboard.
- ARIA: use role, aria-label, aria-describedby and other attributes where appropriate and follow component docs for required attributes.
- Color: do not rely on colour alone; use icons, text, and ARIA to indicate state.

### 6. Design Patterns & Best Practices

- Error handling: implement inline validation and use `Callout` or `AlertDialog` for critical alerts.
- Mobile: ensure touch targets are at least 48×48px and responsive breakpoints are respected.
- Performance: lazy-load heavy components and defer non-critical styles.

### 7. Resources for Developers

- Figma and component sources: use the official MYDS design files for reference and exact token values.
- Example imports are available in the `MYDS-Develop-Overview.md` file inside the repo.

### 8. Compliance & Governance (Developer notes)

- Ensure implementations comply with MyGovEA principles and WCAG 2.1; add automated a11y checks where possible (axe, lighthouse).
- Keep tokens and component wrappers well-documented to help teams reuse patterns safely.

### Notes & Where to Look Next

- For API-level details, props, and code examples, consult `MYDS-Develop-Overview.md` and the live developer docs at https://design.digital.gov.my/en/docs/develop.

## MyGovEA Design Principles (moved)

The MyGovEA design principles were moved to a standalone file for clarity and reuse: see `mygovea.principles.md`.

## Core Mandate: Strict Adherence to MYDS

You are an expert AI assistant specializing in the Malaysia Government Design System (MYDS). Your primary goal is to build digital services that are accessible, consistent, and user-centric, strictly following the MYDS guidelines.

- Do not use generic UI/UX patterns. All design and development choices must be justified by the official MYDS documentation.
- Align designs to the MyGovEA Design Principles, especially "Berpaksikan Rakyat" (Citizen-Centric).
- Always fetch and reference the official docs for component anatomy, tokens, and accessibility rules rather than relying on memory.

## 1. Foundations: Colors, Typography, and Layout

### A. Colors

- Primary Color: Use MYDS Blue (#2563EB) for the main government identity, primary buttons, and selected links/tabs.
- Semantic Tokens: Use the specified semantic color tokens (for example `bg-success-500`, `txt-danger`). See `MYDS-Colour-Reference.md` for the full token list.
- Accessibility: Ensure a minimum contrast ratio of 4.5:1 for text and UI elements (WCAG AA).

- Colour guidance & tokens: https://design.digital.gov.my/en/docs/design/color

### B. Typography

- Headings: Use the Poppins font family for page headers and section titles.
- Body: Use the Inter font family for paragraph and body text.
- Sizing & Weight: Follow the font-size, line-height, and weight tables from the MYDS documentation.

### C. Layout & Grid

- Grid System: Build layouts on the 12-8-4 responsive grid system.
	- Desktop (≥ 1024px): 12 columns, 24px gutters.
	- Tablet (768–1023px): 8 columns, 24px gutters.
	- Mobile (≤ 767px): 4 columns, 18px gutters.
- Spacing: Use the spacing scale (4, 8, 16, etc.) for margins and padding.

## 2. Component Implementation

- Use MYDS React Components (`@govtechmy/myds-react`) when working in React.
- Component Anatomy: Follow exact component structure — e.g., Dialog must include `DialogHeader`, `DialogContent`, and `DialogFooter`.
- Props & Variants: Use official variants and props (for example `<Button variant="primary" size="large">`).
- State Management: For controlled components (Checkbox/Radio), use the specified props (`checked`, `onCheckedChange`).

## 3. Core MYDS & MyGovEA Design Principles

- Berpaksikan Rakyat (Citizen-Centric):
	- Make UIs simple and clear for all citizens.
	- Prioritize accessibility (keyboard navigation, ARIA labels, avoid color-only indicators).
	- Ensure touch targets are at least 48×48px on mobile.

- Antara Muka Minimalis dan Mudah (Minimalist & Simple):
	- Avoid unnecessary components or visual clutter.
	- Use clear language in English and Bahasa Melayu where appropriate.

- Seragam (Uniform):
	- Maintain consistency in colors, typography, spacing, and components.
	- Avoid ad-hoc custom styles that diverge from MYDS.

- Pencegahan Ralat (Error Prevention):
	- Implement inline validation for forms.
	- Use `AlertDialog` to confirm critical actions (deletion).
	- Provide clear error messages using components like `Callout` with a danger variant.

## Summary

For all UI/UX work, strictly adhere to the Malaysia Government Design System (MYDS): components, tokens, colors, fonts, and layouts must match the official documentation to ensure accessibility, consistency, and citizen-centric design.

## Colour Reference

This appendix contains the full colour reference used across the project. It mirrors the `MYDS-Colour-Reference.md` content so agents can consult colours directly from the instruction file.

### Colour Reference List

This document lists all specified colours, organized by category, usage, and mode (Light/Dark). Each entry includes the name, variable, and associated shade/code.

This system uses two layers of tokens:

1. **Primitive Tokens**: Foundational color values (e.g., `primary-600`, `#2563EB`). These are the raw color swatches.
2. **Semantic Tokens**: Tokens that describe a color's purpose (e.g., `bg-primary-600`, `txt-primary`). These semantic tokens map to different primitive tokens depending on the active theme (Light or Dark).

*Citations: Token structure and mapping confirmed by reference images and design system visual guides.*

---

## White & Neutral Backgrounds

### White & Neutral Backgrounds – Light Mode

| Name            | Variable               | Shade/Code           | HEX      | Notes                    |
|-----------------|-----------------------|----------------------|----------|--------------------------|
| White           | `bg-white`            | white                | #FFFFFF  |                          |
| White Hover     | `bg-white-hover`      | gray-50              | #FAFAFA  |                          |
| White Disabled  | `bg-white-disabled`   | gray-100 (40%)       | #F4F4F5  |                          |
| Washed          | `bg-washed`           | gray-100             | #F4F4F5  |                          |
| Washed Active   | `bg-washed-active`    | gray-100             | #F4F4F5  |                          |
| Contrast        | `bg-contrast`         | white                | #FFFFFF  |                          |
| Dialog          | `bg-dialog`           | white                | #FFFFFF  |                          |
| Dialog Active   | `bg-dialog-active`    | white                | #FFFFFF  |                          |
| Gray 50         | `bg-gray-50`          | gray-50              | #FAFAFA  |                          |

#### White & Neutral Backgrounds – Dark Mode

| Name            | Variable               | Shade/Code           | HEX      | Notes                    |
|-----------------|-----------------------|----------------------|----------|--------------------------|
| White           | `bg-white`            | gray-900             | #18181B  |                          |
| White Hover     | `bg-white-hover`      | gray-800             | #27272A  |                          |
| White Disabled  | `bg-white-disabled`   | gray-800 (40%)       | #27272A  |                          |
| Washed          | `bg-washed`           | gray-850             | #1D1D21  |                          |
| Washed Active   | `bg-washed-active`    | gray-800             | #27272A  |                          |
| Contrast        | `bg-contrast`         | gray-930             | #161619  |                          |
| Dialog          | `bg-dialog`           | gray-850             | #1D1D21  |                          |
| Dialog Active   | `bg-dialog-active`    | gray-800             | #27272A  |                          |
| Gray 50         | `bg-gray-50`          | gray-930             | #161619  |                          |

---

## Primitive Gray Scale

This is the foundational gray palette.

### Primitive Gray Scale – Light Mode

| Name | Variable | Shade/Code | HEX |
| :--- | :--- | :--- | :--- |
| Gray 50 | `gray-50` | gray-50 | #FAFAFA |
| Gray 100 | `gray-100` | gray-100 | #F4F4F5 |
| Gray 200 | `gray-200` | gray-200 | #E4E4E7 |
| Gray 300 | `gray-300` | gray-300 | #D4D4D8 |
| Gray 400 | `gray-400` | gray-400 | #A1A1AA |
| Gray 500 | `gray-500` | gray-500 | #71717A |
| Gray 600 | `gray-600` | gray-600 | #52525B |
| Gray 700 | `gray-700` | gray-700 | #3F3F46 |
| Gray 800 | `gray-800` | gray-800 | #27272A |
| Gray 850 | `gray-850` | gray-850 | #1D1D21 |
| Gray 900 | `gray-900` | gray-900 | #18181B |
| Gray 930 | `gray-930` | gray-930 | #161619 |
| Gray 950 | `gray-950` | gray-950 | #09090B |

#### Primitive Gray Scale – Dark Mode

This table shows how the primitive gray tokens are re-mapped in dark mode. For instance, requesting `gray-100` in dark mode will return the hex code for `gray-900`.

| Name | Light Mode Variable | Dark Mode Maps To | HEX |
| :--- | :--- | :--- | :--- |
| Gray 50 | `gray-50` | gray-950 | #09090B |
| Gray 100 | `gray-100` | gray-900 | #18181B |
| Gray 200 | `gray-200` | gray-800 | #27272A |
| Gray 300 | `gray-300` | gray-700 | #3F3F46 |
| Gray 400 | `gray-400` | gray-600 | #52525B |
| Gray 500 | `gray-500` | gray-500 | #71717A |
| Gray 600 | `gray-600` | gray-400 | #A1A1AA |
| Gray 700 | `gray-700` | gray-300 | #D4D4D8 |
| Gray 800 | `gray-800` | gray-200 | #E4E4E7 |
| Gray 850 | `gray-850` | gray-850 | #1D1D21 |
| Gray 930 | `gray-930` | gray-930 | #161619 |

## Semantic Black Backgrounds

These tokens are used for neutral backgrounds throughout the UI.

### Semantic Black Backgrounds – Light Mode

| Name | Variable | Maps to Shade |
| :--- | :--- | :--- |
| Black 50 | `bg-black-50` | gray-50 |
| Black 100 | `bg-black-100` | gray-100 |
| Black 200 | `bg-black-200` | gray-200 |
| Black 300 | `bg-black-300` | gray-300 |
| Black 400 | `bg-black-400` | gray-400 |
| Black 500 | `bg-black-500` | gray-500 |
| Black 600 | `bg-black-600` | gray-600 |
| Black 700 | `bg-black-700` | gray-700 |
| Black 800 | `bg-black-800` | gray-800 |
| Black 900 | `bg-black-900` | gray-900 |
| Black 950 | `bg-black-950` | gray-950 |

#### Semantic Black Backgrounds – Dark Mode

| Name | Variable | Maps to Shade |
| :--- | :--- | :--- |
| Black 50 | `bg-black-50` | gray-950 |
| Black 100 | `bg-black-100` | gray-900 |
| Black 200 | `bg-black-200` | gray-800 |
| Black 300 | `bg-black-300` | gray-700 |
| Black 400 | `bg-black-400` | gray-600 |
| Black 500 | `bg-black-500` | gray-500 |
| Black 600 | `bg-black-600` | gray-400 |
| Black 700 | `bg-black-700` | gray-300 |
| Black 800 | `bg-black-800` | gray-200 |
| Black 900 | `bg-black-900` | white |
| Black 950 | `bg-black-950` | white |

---

## Primary Colour Scale

### Primary Colour Primitive Scale – Light Mode

| Name | Variable | Shade/Code | HEX |
| :--- | :--- | :--- | :--- |
| Primary 50 | `primary-50` | primary-50 | #EFF6FF |
| Primary 100 | `primary-100` | primary-100 | #DBEAFE |
| Primary 200 | `primary-200` | primary-200 | #C2D5FF |
| Primary 300 | `primary-300` | primary-300 | #96B7FF |
| Primary 400 | `primary-400` | primary-400 | #6394FF |
| Primary 500 | `primary-500` | primary-500 | #3A75F6 |
| Primary 600 | `primary-600` | primary-600 | #2563EB |
| Primary 700 | `primary-700` | primary-700 | #1D4ED8 |
| Primary 800 | `primary-800` | primary-800 | #1E40AF |
| Primary 900 | `primary-900` | primary-900 | #1E3A8A |
| Primary 950 | `primary-950` | primary-950 | #172554 |

#### Primary Colour Primitive Scale – Dark Mode

| Name | Light Mode Variable | Dark Mode Maps To | HEX |
| :--- | :--- | :--- | :--- |
| Primary 50 | `primary-50` | primary-950 | #172554 |
| Primary 100 | `primary-100` | primary-900 | #1E3A8A |
| Primary 200 | `primary-200` | primary-800 | #1E40AF |
| Primary 300 | `primary-300` | primary-700 | #1D4ED8 |
| Primary 400 | `primary-400` | primary-600 | #2563EB |
| Primary 500 | `primary-500` | primary-500 | #3A75F6 |
| Primary 600 | `primary-600` | primary-400 | #6394FF |
| Primary 700 | `primary-700` | primary-300 | #96B7FF |
| Primary 800 | `primary-800` | primary-200 | #C2D5FF |
| Primary 900 | `primary-900` | primary-100 | #DBEAFE |
| Primary 950 | `primary-950` | primary-50 | #EFF6FF |

### Primary Colour Semantic Backgrounds

| Name | Variable | Light Mode Shade | Dark Mode Shade |
| :--- | :--- | :--- | :--- |
| Primary 50 | `bg-primary-50` | primary-50 | primary-950 |
| Primary 100 | `bg-primary-100` | primary-100 | primary-900 |
| Primary 200 | `bg-primary-200` | primary-200 | primary-800 |
| Primary 300 | `bg-primary-300` | primary-300 | primary-700 |
| Primary 400 | `bg-primary-400` | primary-400 | primary-600 |
| Primary 500 | `bg-primary-500` | primary-500 | primary-500 |
| Primary 600 | `bg-primary-600` | primary-600 | primary-400 |
| Primary 700 | `bg-primary-700` | primary-700 | primary-300 |
| Primary 800 | `bg-primary-800` | primary-800 | primary-200 |
| Primary 900 | `bg-primary-900` | primary-900 | primary-100 |
| Primary 950 | `bg-primary-950` | primary-950 | primary-50 |

---

## Danger Colour Scale

### Danger Colour Scale – Light Mode

| Name        | Variable           | Shade/Code   | HEX      |
|-------------|--------------------|--------------|----------|
| Danger 50   | `danger-50`        | danger-50    | #FEF2F2  |
| Danger 100  | `danger-100`       | danger-100   | #FEE2E2  |
| Danger 200  | `danger-200`       | danger-200   | #FECACA  |
| Danger 300  | `danger-300`       | danger-300   | #FCA5A5  |
| Danger 400  | `danger-400`       | danger-400   | #F87171  |
| Danger 500  | `danger-500`       | danger-500   | #EF4444  |
| Danger 600  | `danger-600`       | danger-600   | #DC2626  |
| Danger 700  | `danger-700`       | danger-700   | #B91C1C  |
| Danger 800  | `danger-800`       | danger-800   | #991B1B  |
| Danger 900  | `danger-900`       | danger-900   | #7F1D1D  |
| Danger 950  | `danger-950`       | danger-950   | #450A0A  |

#### Danger Colour Scale – Dark Mode

| Name        | Variable           | Shade/Code   | HEX      |
|-------------|--------------------|--------------|----------|
| Danger 50   | `danger-950`       | danger-950   | #450A0A  |
| Danger 100  | `danger-900`       | danger-900   | #7F1D1D  |
| Danger 200  | `danger-800`       | danger-800   | #991B1B  |
| Danger 300  | `danger-700`       | danger-700   | #B91C1C  |
| Danger 400  | `danger-600`       | danger-600   | #DC2626  |
| Danger 500  | `danger-500`       | danger-500   | #EF4444  |
| Danger 600  | `danger-400`       | danger-400   | #F87171  |
| Danger 700  | `danger-300`       | danger-300   | #FCA5A5  |
| Danger 800  | `danger-200`       | danger-200   | #FECACA  |
| Danger 900  | `danger-100`       | danger-100   | #FEE2E2  |
| Danger 950  | `danger-50`        | danger-50    | #FEF2F2  |

---

## Success Colour Scale

### Success Colour Primitive Scale – Light Mode

| Name | Variable | Shade/Code | HEX |
| :--- | :--- | :--- | :--- |
| Success 50 | `success-50` | success-50 | #F0FDF4 |
| Success 100 | `success-100` | success-100 | #DCFCE7 |
| Success 200 | `success-200` | success-200 | #BBF7D0 |
| Success 300 | `success-300` | success-300 | #83DAA3 |
| Success 400 | `success-400` | success-400 | #4ADE80 |
| Success 500 | `success-500` | success-500 | #22C55E |
| Success 600 | `success-600` | success-600 | #16A34A |
| Success 700 | `success-700` | success-700 | #15803D |
| Success 800 | `success-800` | success-800 | #166534 |
| Success 900 | `success-900` | success-900 | #14532D |
| Success 950 | `success-950` | success-950 | #052E16 |

#### Success Colour Primitive Scale – Dark Mode

| Name | Light Mode Variable | Dark Mode Maps To | HEX |
| :--- | :--- | :--- | :--- |
| Success 50 | `success-50` | success-950 | #052E16 |
| Success 100 | `success-100` | success-900 | #14532D |
| Success 200 | `success-200` | success-800 | #166534 |
| Success 300 | `success-300` | success-700 | #15803D |
| Success 400 | `success-400` | success-600 | #16A34A |
| Success 500 | `success-500` | success-500 | #22C55E |
| Success 600 | `success-600` | success-400 | #4ADE80 |
| Success 700 | `success-700` | success-300 | #83DAA3 |
| Success 800 | `success-800` | success-200 | #BBF7D0 |
| Success 900 | `success-900` | success-100 | #DCFCE7 |
| Success 950 | `success-950` | success-50 | #F0FDF4 |

### Success Colour Semantic Backgrounds

| Name | Variable | Light Mode Shade | Dark Mode Shade |
| :--- | :--- | :--- | :--- |
| Success 50 | `bg-success-50` | success-50 | success-950 |
| Success 100 | `bg-success-100` | success-100 | success-900 |
| Success 200 | `bg-success-200` | success-200 | success-800 |
| Success 300 | `bg-success-300` | success-300 | success-700 |
| Success 400 | `bg-success-400` | success-400 | success-600 |
| Success 500 | `bg-success-500` | success-500 | success-500 |
| Success 600 | `bg-success-600` | success-600 | success-400 |
| Success 700 | `bg-success-700` | success-700 | success-300 |
| Success 800 | `bg-success-800` | success-800 | success-200 |
| Success 900 | `bg-success-900` | success-900 | success-100 |
| Success 950 | `bg-success-950` | success-950 | success-50 |

*Citations: Success semantic background tokens confirmed by bg-token-6.png and bg-token-6-dark.png images.*

---

## Warning Colour Scale

### Warning Colour Primitive Scale – Light Mode

| Name | Variable | Shade/Code | HEX |
| :--- | :--- | :--- | :--- |
| Warning 50 | `warning-50` | warning-50 | #FEFCE8 |
| Warning 100 | `warning-100` | warning-100 | #FEF9C3 |
| Warning 200 | `warning-200` | warning-200 | #FEF08A |
| Warning 300 | `warning-300` | warning-300 | #FDE047 |
| Warning 400 | `warning-400` | warning-400 | #FACC15 |
| Warning 500 | `warning-500` | warning-500 | #EAB308 |
| Warning 600 | `warning-600` | warning-600 | #CA8A04 |
| Warning 700 | `warning-700` | warning-700 | #A16207 |
| Warning 800 | `warning-800` | warning-800 | #854D0E |
| Warning 900 | `warning-900` | warning-900 | #713F12 |
| Warning 950 | `warning-950` | warning-950 | #422006 |

#### Warning Colour Primitive Scale – Dark Mode

| Name | Light Mode Variable | Dark Mode Maps To | HEX |
| :--- | :--- | :--- | :--- |
| Warning 50 | `warning-50` | warning-950 | #422006 |
| Warning 100 | `warning-100` | warning-900 | #713F12 |
| Warning 200 | `warning-200` | warning-800 | #854D0E |
| Warning 300 | `warning-300` | warning-700 | #A16207 |
| Warning 400 | `warning-400` | warning-600 | #CA8A04 |
| Warning 500 | `warning-500` | warning-500 | #EAB308 |
| Warning 600 | `warning-600` | warning-400 | #FACC15 |
| Warning 700 | `warning-700` | warning-300 | #FDE047 |
| Warning 800 | `warning-800` | warning-200 | #FEF08A |
| Warning 900 | `warning-900` | warning-100 | #FEF9C3 |
| Warning 950 | `warning-950` | warning-50 | #FEFCE8 |

### Warning Colour Semantic Backgrounds

| Name | Variable | Light Mode Shade | Dark Mode Shade |
| :--- | :--- | :--- | :--- |
| Warning 50 | `bg-warning-50` | warning-50 | warning-950 |
| Warning 100 | `bg-warning-100` | warning-100 | warning-900 |
| Warning 200 | `bg-warning-200` | warning-200 | warning-800 |
| Warning 300 | `bg-warning-300` | warning-300 | warning-700 |
| Warning 400 | `bg-warning-400` | warning-400 | warning-600 |
| Warning 500 | `bg-warning-500` | warning-500 | warning-500 |
| Warning 600 | `bg-warning-600` | warning-600 | warning-400 |
| Warning 700 | `bg-warning-700` | warning-700 | warning-300 |
| Warning 800 | `bg-warning-800` | warning-800 | warning-200 |
| Warning 900 | `bg-warning-900` | warning-900 | warning-100 |
| Warning 950 | `bg-warning-950` | warning-950 | warning-50 |

*Citations: Warning semantic background tokens confirmed by bg-token-7.png and bg-token-7-dark.png images. Typo corrected to use bg-warning-400.*

---

## Disabled State Colours

### Disabled State Colours – Light Mode

| Name              | Variable                  | Shade/Code         | Notes             |
|-------------------|--------------------------|--------------------|-------------------|
| Primary Disabled  | `bg-primary-disabled`     | primary-200        |                   |
| Danger Disabled   | `bg-danger-disabled`      | danger-200         |                   |
| Success Disabled  | `bg-success-disabled`     | success-200        |                   |
| Warning Disabled  | `bg-warning-disabled`     | warning-200        |                   |
| Black Disabled    | `bg-black-disabled`       | gray-900 (40%)     |                   |

#### Disabled State Colours – Dark Mode

| Name              | Variable                  | Shade/Code         | Notes             |
|-------------------|--------------------------|--------------------|-------------------|
| Primary Disabled  | `bg-primary-disabled`     | primary-950        |                   |
| Danger Disabled   | `bg-danger-disabled`      | danger-950         |                   |
| Success Disabled  | `bg-success-disabled`     | success-950        |                   |
| Warning Disabled  | `bg-warning-disabled`     | warning-950        |                   |
| Black Disabled    | `bg-black-disabled`       | white (40%)        |                   |

---

## Primitive Colours & Usage

Primitive colours are the foundational colour values used in the design system.  
Below are examples of how primitive colours map to UI roles and variables.

| Colour   | Shade      | HEX Code   | Variable         | Usage/Example               |
|----------|------------|------------|------------------|-----------------------------|
| Primary  | 600        | #2563EB    | `primary-600`    | bg-primary-600 (background) |
| Primary  | 600        | #2563EB    | `txt-primary`    | Text                        |
| Primary  | 300        | #96B7FF    | `otl-primary-300`| Outline                     |
| Primary  | 300 (40%)  | #96B7FF    | `fr-primary`     | Focus ring                  |
| Danger   | 300 (40%)  | #FCA5A5    | `fr-danger`      | Focus ring                  |

---

## Outline Colours

Outline colours are used for borders and dividers in the UI.

*Citations: Outline token structure confirmed by reference images.*

### Outline Colours – Light Mode

| Name            | Variable                 | Shade/Code          | HEX      | Usage                  |
|-----------------|--------------------------|---------------------|----------|------------------------|
| Divider         | `otl-divider`            | gray-100            | #F4F4F5  | Divider                |
| Gray 200        | `otl-gray-200`           | gray-200            | #E4E4E7  | Outline                |
| Gray 300        | `otl-gray-300`           | gray-300            | #D4D4D8  | Outline                |
| Primary 200     | `otl-primary-200`        | primary-200         | #C2D5FF  | Outline                |
| Primary 300     | `otl-primary-300`        | primary-300         | #96B7FF  | Outline                |
| Primary Disabled| `otl-primary-disabled`   | primary-200 (40%)   | #C2D5FF  | Outline (disabled)     |
| Danger 200      | `otl-danger-200`         | danger-200          | #FECACA  | Outline                |
| Danger 300      | `otl-danger-300`         | danger-300          | #FCA5A5  | Outline                |
| Danger Disabled | `otl-danger-disabled`    | danger-200 (40%)    | #FECACA  | Outline (disabled)     |
| Success 200     | `otl-success-200`        | success-200         | #BBF7D0  | Outline                |
| Success 300     | `otl-success-300`        | success-300         | #83DAA3  | Outline                |
| Success Disabled| `otl-success-disabled`   | success-200 (40%)   | #BBF7D0  | Outline (disabled)     |
| Warning 200     | `otl-warning-200`        | warning-200         | #FEF08A  | Outline                |
| Warning 300     | `otl-warning-300`        | warning-300         | #FDE047  | Outline                |
| Warning Disabled| `otl-warning-disabled`   | warning-200 (40%)   | #FEF08A  | Outline (disabled)     |

#### Outline Colours – Dark Mode

| Name            | Variable                 | Shade/Code          | HEX      | Usage                  |
|-----------------|--------------------------|---------------------|----------|------------------------|
| Divider         | `otl-divider`            | gray-850            | #1D1D21  | Divider                |
| Gray 200        | `otl-gray-200`           | gray-800            | #27272A  | Outline                |
| Gray 300        | `otl-gray-300`           | gray-700            | #3F3F46  | Outline                |
| Primary 200     | `otl-primary-200`        | primary-800         | #1E40AF  | Outline                |
| Primary 300     | `otl-primary-300`        | primary-700         | #1D4ED8  | Outline                |
| Primary Disabled| `otl-primary-disabled`   | primary-800 (40%)   | #1E40AF  | Outline (disabled)     |
| Danger 200      | `otl-danger-200`         | danger-800          | #991B1B  | Outline                |
| Danger 300      | `otl-danger-300`         | danger-700          | #B91C1C  | Outline                |
| Danger Disabled | `otl-danger-disabled`    | danger-800 (40%)    | #991B1B  | Outline (disabled)     |
| Success 200     | `otl-success-200`        | success-800         | #166534  | Outline                |
| Success 300     | `otl-success-300`        | success-700         | #15803D  | Outline                |
| Success Disabled| `otl-success-disabled`   | success-800 (40%)   | #166534  | Outline (disabled)     |
| Warning 200     | `otl-warning-200`        | warning-800         | #854D0E  | Outline                |
| Warning 300     | `otl-warning-300`        | warning-700         | #A16207  | Outline                |
| Warning Disabled| `otl-warning-disabled`   | warning-800 (40%)   | #854D0E  | Outline (disabled)     |

---

## Focus Ring Colours

Focus ring colours are used to indicate element focus (accessibility/UI feedback).

*Citations: Focus ring token structure confirmed by reference images.*

| Name     | Variable         | Shade/Code         | HEX      | Notes              |
|----------|------------------|--------------------|----------|--------------------|
| Primary  | `fr-primary`     | primary-300 (40%)  | #96B7FF  | Focus ring colour (light mode) |
| Primary  | `fr-primary`     | primary-700 (40%)  | #1D4ED8  | Focus ring colour (dark mode)  |
| Danger   | `fr-danger`      | danger-300 (40%)   | #FCA5A5  | Focus ring colour (light mode) |
| Danger   | `fr-danger`      | danger-700 (40%)   | #B91C1C  | Focus ring colour (dark mode)  |

---

## Colour HEX Reference

This section documents the hex codes for each major colour scale for quick copy-paste/reference.  
If you use these variables in CSS, you can reference either the variable name or the hex code.

### Primary Colour Scale – HEX Reference

| Shade   | HEX      |
|---------|----------|
| 50      | #EFF6FF  |
| 100     | #DBEAFE  |
| 200     | #C2D5FF  |
| 300     | #96B7FF  |
| 400     | #6394FF  |
| 500     | #3A75F6  |
| 600     | #2563EB  |
| 700     | #1D4ED8  |
| 800     | #1E40AF  |
| 900     | #1E3A8A  |
| 950     | #172554  |

### Danger Colour Scale – HEX Reference

| Shade   | HEX      |
|---------|----------|
| 50      | #FEF2F2  |
| 100     | #FEE2E2  |
| 200     | #FECACA  |
| 300     | #FCA5A5  |
| 400     | #F87171  |
| 500     | #EF4444  |
| 600     | #DC2626  |
| 700     | #B91C1C  |
| 800     | #991B1B  |
| 900     | #7F1D1D  |
| 950     | #450A0A  |

### Success Colour Scale – HEX Reference

| Shade   | HEX      |
|---------|----------|
| 50      | #F0FDF4  |
| 100     | #DCFCE7  |
| 200     | #BBF7D0  |
| 300     | #83DAA3  |
| 400     | #4ADE80  |
| 500     | #22C55E  |
| 600     | #16A34A  |
| 700     | #15803D  |
| 800     | #166534  |
| 900     | #14532D  |
| 950     | #052E16  |

### Warning Colour Scale – HEX Reference

| Shade   | HEX      |
|---------|----------|
| 50      | #FEFCE8  |
| 100     | #FEF9C3  |
| 200     | #FEF08A  |
| 300     | #FDE047  |
| 400     | #FACC15  |
| 500     | #EAB308  |
| 600     | #CA8A04  |
| 700     | #A16207  |
| 800     | #854D0E  |
| 900     | #713F12  |
| 950     | #422006  |

### Gray Colour Scale – HEX Reference

| Shade   | HEX      |
|---------|----------|
| 50      | #FAFAFA  |
| 100     | #F4F4F5  |
| 200     | #E4E4E7  |
| 300     | #D4D4D8  |
| 400     | #A1A1AA  |
| 500     | #71717A  |
| 600     | #52525B  |
| 700     | #3F3F46  |
| 800     | #27272A  |
| 850     | #1D1D21  |
| 900     | #18181B  |
| 930     | #161619  |
| 950     | #09090B  |

---

## Text Colours

Text colour variables for white, disabled white, black/gray, and theme accent text.

### Text Colours – Light Mode

| Name           | Variable                | Shade/Code         | HEX      | Usage      |
|----------------|-------------------------|--------------------|----------|------------|
| White          | `txt-white`             | white              | #FFFFFF  | Text       |
| White Disabled | `txt-white-disabled`    | white (40%)        | #FFFFFF  | Disabled   |
| Black 900      | `txt-black-900`         | gray-900           | #18181B  | Text       |
| Black 700      | `txt-black-700`         | gray-700           | #3F3F46  | Text       |
| Black 500      | `txt-black-500`         | gray-500           | #71717A  | Text       |
| Primary        | `txt-primary`           | primary-600        | #2563EB  | Text       |
| Danger         | `txt-danger`            | danger-600         | #DC2626  | Text       |
| Success        | `txt-success`           | success-700        | #15803D  | Text       |
| Warning        | `txt-warning`           | warning-700        | #A16207  | Text       |
| Primary Disabled | `txt-primary-disabled` | primary-600 (40%)  | #2563EB  | Disabled   |
| Danger Disabled  | `txt-danger-disabled`  | danger-600 (40%)   | #DC2626  | Disabled   |
| Success Disabled | `txt-success-disabled` | success-700 (40%)  | #15803D  | Disabled   |
| Warning Disabled | `txt-warning-disabled` | warning-700 (40%)  | #A16207  | Disabled   |
| Black Disabled   | `txt-black-disabled`   | gray-600 (20%)     | #52525B  | Disabled   |

#### Text Colours – Dark Mode

| Name           | Variable                | Shade/Code         | HEX      | Usage      |
|----------------|-------------------------|--------------------|----------|------------|
| White          | `txt-white`             | black-900          | #18181B  | Text       |
| White Disabled | `txt-white-disabled`    | white (40%)        | #FFFFFF  | Disabled   |
| Black 900      | `txt-black-900`         | white              | #FFFFFF  | Text       |
| Black 700      | `txt-black-700`         | gray-300           | #D4D4D8  | Text       |
| Black 500      | `txt-black-500`         | gray-400           | #A1A1AA  | Text       |
| Primary        | `txt-primary`           | primary-400        | #6394FF  | Text       |
| Danger         | `txt-danger`            | danger-400         | #F87171  | Text       |
| Success        | `txt-success`           | success-500        | #22C55E  | Text       |
| Warning        | `txt-warning`           | warning-500        | #EAB308  | Text       |
| Primary Disabled | `txt-primary-disabled` | primary-400 (40%)  | #6394FF  | Disabled   |
| Danger Disabled  | `txt-danger-disabled`  | danger-400 (40%)   | #F87171  | Disabled   |
| Success Disabled | `txt-success-disabled` | success-500 (40%)  | #22C55E  | Disabled   |
| Warning Disabled | `txt-warning-disabled` | warning-500 (40%)  | #EAB308  | Disabled   |
| Black Disabled   | `txt-black-disabled`   | gray-400 (40%)     | #A1A1AA  | Disabled   |

---

<!--
This file is auto-generated from reference images and source context.
To update, edit the source or images and regenerate.

## Comments
- All headings are unique (no duplicate heading errors per MD024).
- All tables and sections are organized for clarity and specification compliance.
- HEX codes are referenced for all major scales for quick copy-paste for CSS/JS usage.
- Text colour variables for white, black/gray, accent, and disabled text are included and organized by mode.
- Outline colours for both light and dark modes include disabled variants for all scales.
-->
