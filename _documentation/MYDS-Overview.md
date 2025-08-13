# Malaysia Government Design System (MYDS) Overview

---

## MYDS Design System: Core Guidelines

Malaysia's official design system for government digital services, ensuring **accessibility**, **consistency**, and **user-centricity**.

---

## 1. Foundations

### A. Colors

- **Primary Palette:**
  - `MYDS Blue` (#2563EB) – Main government identity.
  - Supported by secondary colors, e.g., `Green` for success, `Red` for errors.
- **Accessibility:** Minimum contrast ratio of **4.5:1** (WCAG AA compliant).

### B. Typography

A clear standard for font style, size, and spacing to ensure readability and consistency across government platforms, supporting accessible and user-friendly text presentation.

#### Heading

- **Font Family:**  
  - `Poppins` is used for home page section titles, page headers, and important text elements to create a clear visual hierarchy and improve user navigation.  
  - Not applicable for rich-text.

- **Font Sizes and Weights:**  
  - All sizes include weights: Regular (400), Medium (500), Semibold (600).

| Name                   | HTML Tag | Font Size     | Line Height     |
|------------------------|----------|---------------|-----------------|
| Heading Extra Large    |          | 60px (3.75rem)| 72px (4.5rem)   |
| Heading Large          |          | 48px (3rem)   | 60px (3.75rem)  |
| Heading Medium         | `<h1>`   | 36px (2.25rem)| 44px (2.75rem)  |
| Heading Small          | `<h2>`   | 30px (1.875rem)| 38px (2.375rem)|
| Heading Extra Small    | `<h3>`   | 24px (1.5rem) | 32px (2rem)     |
| Heading 2X Small       | `<h4>`   | 20px (1.25rem)| 28px (1.75rem)  |
| Heading 3X Small       | `<h5>`   | 16px (1rem)   | 24px (1.5rem)   |
| Heading 4X Small       | `<h6>`   | 14px (0.875rem)| 20px (1.25rem) |

#### Body

- **Font Family:**  
  - `Inter` is used for paragraphs, descriptions, and general content to provide a comfortable reading experience for users.

- **Font Sizes and Weights:**  
  - All sizes include weights: Regular (400), Medium (500), Semibold (600).

| Name           | Font Size       | Line Height       | List Spacing     | Paragraph Spacing   |
|----------------|-----------------|-------------------|------------------|---------------------|
| Body 6X Large  | 60px (3.75rem)  | 72px (4.5rem)     | 6px (0.375rem)   | 12px (0.75rem)      |
| Body 5X Large  | 48px (3rem)     | 60px (3.75rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Body 4X Large  | 36px (2.25rem)  | 44px (2.75rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Body 3X Large  | 30px (1.875rem) | 38px (2.375rem)   | 6px (0.375rem)   | 12px (0.75rem)      |
| Body 2X Large  | 24px (1.5rem)   | 32px (2rem)       | 6px (0.375rem)   | 12px (0.75rem)      |
| Body Extra Large| 20px (1.25rem) | 28px (1.75rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Body Large     | 18px (1.125rem) | 26px (1.625rem)   | 6px (0.375rem)   | 12px (0.75rem)      |
| Body Medium    | 16px (1rem)     | 24px (1.5rem)     | 6px (0.375rem)   | 12px (0.75rem)      |
| Body Small     | 14px (0.875rem) | 20px (1.25rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Body Extra Small| 12px (0.75rem) | 18px (1.125rem)   | 6px (0.375rem)   | 12px (0.75rem)      |
| Body 2X Small  | 10px (0.625rem) | 12px (0.75rem)    | 6px (0.375rem)   | 12px (0.75rem)      |

#### Rich Text Format (RTF)

- **Font Family:**  
  - `Inter` is used for styling long form content such as an article.

- **Note:**  
  - The H1 tag in RTF differs from the standard H1 Heading tag and is intended only for formatting content within an article section.

- **Font Sizes and Weights:**  
  - All sizes include weights: Regular (400) and Semibold (600).

| Name        | HTML Tag | Font Size       | Line Height       | List Spacing     | Paragraph Spacing   |
|-------------|----------|-----------------|-------------------|------------------|---------------------|
| Heading 1   | `<h1>`   | 30px (1.875rem) | 38px (2.375rem)   | 6px (0.375rem)   | 12px (0.75rem)      |
| Heading 2   | `<h2>`   | 24px (1.5rem)   | 32px (2rem)       | 6px (0.375rem)   | 12px (0.75rem)      |
| Heading 3   | `<h3>`   | 20px (1.25rem)  | 28px (1.75rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Heading 4   | `<h4>`   | 18px (1.125rem) | 26px (1.625rem)   | 6px (0.375rem)   | 12px (0.75rem)      |
| Heading 5   | `<h5>`   | 16px (1rem)     | 24px (1.5rem)     | 6px (0.375rem)   | 12px (0.75rem)      |
| Heading 6   | `<h6>`   | 14px (0.875rem) | 20px (1.25rem)    | 6px (0.375rem)   | 12px (0.75rem)      |
| Paragraph   | `<p>`    | 16px (1rem)     | 28px (1.75rem)    | 6px (0.375rem)   | 28px (1.75rem)      |

---

### C. Spacing & Layout

- **Grid System:** 12-column layout with `24px` gutters.
- **Responsive Breakpoints:**
  - Mobile (`<768px`), Tablet (`768px–1024px`), Desktop (`>1024px`).

---

## 2. UI Components

### A. Navigation

- **Header:** Standardized layout with logo, search bar, and language toggle.
- **Sidebar:** Collapsible menu for multi-level content.

### B. Forms

- **Input Fields:**
  - Labels aligned top-left, error states in `Red`.
  - Placeholder text avoided (per accessibility guidelines).
- **Buttons:**
  - Primary (`Blue`), Secondary (`White`), Disabled (`Grey`).

### C. Data Display

- **Tables:** Zebra striping for readability.
- **Cards:** Consistent padding (`16px`) and shadow effects.

---

## 3. Accessibility (A11y)

- **Keyboard Navigation:** All components operable via `Tab`/`Enter`.
- **ARIA Labels:** Mandatory for interactive elements (e.g., buttons, icons).
- **Color Blindness:** Avoid color-only indicators (e.g., use icons + text for alerts).

---

## 4. Design Patterns

### A. Error Handling

- **Inline Validation:** Real-time feedback for form errors.
- **Error Pages:** Friendly messaging with recovery options (e.g., "404 – Page not found").

### B. Mobile Optimization

- **Touch Targets:** Minimum `48px × 48px` for buttons.
- **Gestures:** Swipe actions documented for lists.

---

## 5. Resources

- **Figma Kit:** Download components directly from [MYDS Figma](https://www.figma.com/design/svmWSPZarzWrJ116CQ8zpV/MYDS--Beta-).
- **Icons:** SVG-based `MYDS Icons` library (e.g., "download", "search").

---

## 6. Compliance & Governance

- **Alignment with MyGovEA:** Adheres to *Prinsip Reka Bentuk* (e.g., citizen-centricity).
- **Legal:** Follows PDPA (data privacy) and WCAG 2.1 standards.

---

## Existing Detailed Guidelines

### What is MYDS?

MYDS consists of several key elements:

- **Components:** Pre-built UI elements like buttons, forms, and navigation bars that help create consistent interfaces for government websites.
- **Theme Customizer:** A tool to adjust colors and styles to match an agency's branding, while maintaining a unified look and feel.
- **Patterns:** Ready-to-use layouts and design patterns for common interface scenarios, such as login screens and data forms.
- **Design File:** A comprehensive file containing all assets, components, and guidelines, helping designers prototype and build efficiently in line with MYDS standards.

### Why Use MYDS?

- **Consistency:** Ensures all government digital platforms have a cohesive visual identity, fostering trust and recognition among citizens.
- **Rapid Development:** Streamlines design and development with reusable components and clear guidelines.
- **Focus on User Experience:** Allows teams to spend less time on styling and more on improving user experience.
- **Scalability:** Components can be customized to meet specific project needs.
- **Accessibility for All:** Adheres to WCAG standards to ensure inclusivity, making services accessible to all citizens.

### Use Cases for MYDS

- **Government Agency Websites:** Informational sites about policies, regulations, profiles, activities, achievements, announcements, media, and more.
- **Dashboards and Portals:** Interactive platforms for visualizing key metrics and providing citizens with access to various government services.

### Resources

- **Figma:** Interactive design canvas for previewing components, templates, and guidelines.

---

## 12-8-4 Grid System

The **12-8-4 grid system** provides a flexible, responsive layout structure that encourages adaptable design across all screen sizes. This grid ensures content remains consistently aligned and visually balanced, making designs polished and accessible on any device, from desktop to mobile.

### Container Types

- **Content:** The main container for arranging content in various layouts.
- **Article:** Used for long-form content, such as articles. Its width is narrowed to a maximum of 640px for optimal reading comfort.
- **Images and Interactive Charts:** These can span the full width of the article container (640px) or extend to a maximum width of 740px for greater visual impact.

### Breakpoints

Breakpoints help websites adjust their layout for different devices.

| Device   | Width Range      | Grid Columns | Column Gap | Edge Padding | Max Width |
|----------|------------------|-------------|------------|--------------|-----------|
| Desktop  | ≥ 1024px         | 12          | 24px       | 24px         | 1280px    |
| Tablet   | 768px - 1023px   | 8           | 24px       | 24px         | —         |
| Mobile   | ≤ 767px          | 4           | 18px       | 18px         | —         |

#### Desktop

- **Grid:** 12 columns
- **Column gap & padding:** 24px
- **Max content width:** 1280px
- This layout offers maximum flexibility for arranging content on large screens, supporting complex designs while maintaining alignment and avoiding clutter.

#### Tablet

- **Grid:** 8 columns
- **Column gap & padding:** 24px
- Designed for medium-sized screens, the 8-column layout keeps layouts clean and organized, improving readability and usability.

#### Mobile

- **Grid:** 4 columns
- **Column gap & padding:** 18px
- On small screens, the 4-column layout provides ample space for content, focusing on essential elements for fast and comfortable navigation.

### Usage Examples

#### Content Section

- **Desktop:** Example - Ministry of Digital website uses an image slider section where the title spans 3 columns and the image spans 6 columns, with 1-column spaces on either side and a 1-column gap for separation.
- **Tablet:** Title and image fill available space for optimal viewing.
- **Mobile:** Title and image stack vertically instead of side by side.

#### Article Page

- The article page uses a maximum container width of 640px for paragraphs, ensuring easy reading without line fatigue.
- Images and interactive charts are displayed with a max width of 740px, helping them stand out while maintaining a balanced layout.

---

## Colour

Colour forms a fundamental part of MYDS, defining the primary, danger, success, warning, and gray palettes. These guidelines ensure contrast, readability, and consistency for backgrounds, buttons, and text across all interfaces.

MYDS divides colour palettes into two categories:

- **Primitive colour:** Base colours that remain consistent across both light and dark modes.
- **Colour tokens:** Dynamic colours that adjust according to the mode/theme (light or dark).

### Primitive Colour

Primitive colours are the foundational colours used throughout the design system and do not have dark mode variants. For adaptive colour usage in dark mode, use the Colour Tokens set.

### Primitive Colour Preview Table

| Primitive Colour Name | Primitive Colour HEX Value | Usage Example |
|----------------------|---------------------------|---------------|
| White                | #FFFFFF (Example)         | Backgrounds   |
| Gray                 | #6B7280 (Example)         | Headings, text, backgrounds, placeholders, dividers, outlines |
| Primary              | #2563EB (MYDS Blue)       | Selected links, tabs, primary buttons, highlighted elements |
| Danger               | #D32F2F (Example)         | Error messages, delete buttons, urgent alerts |
| Success              | #388E3C (Example)         | Success messages, confirmations, progress indicators |
| Warning              | #FFA000 (Example)         | Warning banners, notification badges, alert icons |

> **Note:** If your product does not use the primary blue as the main colour, consider creating a separate colour palette.

### Colour Tokens

Colour tokens are dynamic styles intended for accessibility and theme adaptation. They reference primitive colours and are optimized for both light and dark modes. Token naming follows this convention:

- `bg-`: Background
- `txt-`: Text
- `otl-`: Outline
- `fr-`: Focus ring

#### Light Mode

Colour tokens for light mode provide high contrast for readability and follow WCAG 2.1 contrast ratio guidelines. They support accessible design for all users.

**Examples of Light Mode Tokens:**

- **Background:** Provides foundational colours for surfaces and containers, ensuring proper contrast and hierarchy.
- **Text:** Ensures text legibility and accessibility in all states.
- **Outline:** Colours for borders and outlines, defining UI component separation and accents.
- **Focus Ring:** Highlights interactive elements when receiving keyboard focus.

#### Dark Mode

Colour tokens for dark mode are optimized for readability and accessibility in darker interfaces. They maintain high contrast ratios and support seamless theme switching.

**Examples of Dark Mode Tokens:**

- **Background:** Surfaces and containers adapted for dark mode.
- **Text:** Maintains legibility in all text elements.
- **Outline:** Borders and outlines optimized for dark backgrounds.
- **Focus Ring:** Ensures focused elements remain visible in dark mode.

---

## Typography

[See above for detailed standards, font families, sizes, and usage for headings, body, and rich text formatting.]

---

## Icon

Icons are visual symbols that communicate meaning quickly, guiding users through actions, statuses, and categories. They are crafted with consistent proportions, line weights, and styling to ensure a cohesive look across all components.

### Icon Set Types

There are 4 types of icon sets:

- **Generic Icon:**  
  Simple, universal icons used for common functions across websites.

- **WYSIWYG Icon:**  
  Icons that represent content formatting tools in text editors (e.g., bold, italic, list).

- **Social Media Icon:**  
  Icons used to link to social media platforms.

- **Media Icon:**  
  Icons that represent file types (e.g., PPTX, Excel, DOCX, PDF), often used in file uploaders or previews.

### Icon Design Guidelines

#### Grid Size

- Icons are designed within a **20x20 grid** as the base size, ensuring consistency.  
- Icons can be resized to fit any situation while keeping proportions.

#### Stroke Width

- All icons preserve a **1.5px stroke width** (at 20x20), even after export as SVG, giving developers flexibility for scaling in code.

#### Sizes

- Maintain visual balance and consistency by adjusting stroke width proportionally with icon size.

| Size (px) | Where to Use?         |
|-----------|----------------------|
| 16x16     | Small button         |
| 20x20     | Medium button        |
| 24x24     | Large button         |
| 32x32     | Alert dialog         |
| 42x42     | Alert dialog         |

#### Generic Icon

- 20x20 icons for common actions like search, add, edit, remove, settings.
- Designed for clarity and consistency, easily recognizable and functional.

- **Outline icon:**  
  Simple, single-line icons for clarity.
- **Filled icon:**  
  Solid icons for emphasis or active states.

#### WYSIWYG Icon

- Represent text editor tools, making formatting intuitive without code.

#### Social Media Icon

- Used to link to agency social profiles, typically in footers or navigation bars.

#### Media Icon

- Represent file types, helping users identify/interact with files in uploaders or previews.

### Usage Example

#### Web UI

- Use icons in buttons, text fields, alerts, and more to make actions clearer.

---

## Motion

Transform static elements in digital interfaces through purposeful movement and interaction with clear user feedback.

### Principle

- **Simple:** Motion should guide, not distract.
- **Harmony:** Sync productive and expressive motion for cohesive experiences.
- **Functional:** Every motion must serve a clear purpose.

### Motion Types

- **No transition (Instant):**  
  Used as the default transition when no motion effect is applied during transformations.  
  **Token name:** `instant`

- **Linear:**  
  Adds a CSS transition property allowing an element to interpolate from one state to another.  
  Not ideal for organic UI animation.  
  **Token name:** `linear`  
  `cubic-bezier(0, 0, 1, 1)`

- **Ease-Out:**  
  A smooth, natural curve moving directly to the target without overshooting. Ensures clarity and precision.  
  **Use Cases:** Functional interfaces requiring minimal distraction, such as charts, UI state transitions, background fade-out.  
  **Token name:** `easeout`  
  `cubic-bezier(0, 0, 0.58, 1)`

- **Ease-Out-Back (Custom):**  
  A dynamic, spring-like motion that overshoots the target before settling. Creates energy and adds personality.  
  **Use Cases:** Playful or attention-grabbing elements, like success animations or buttons.  
  **Token name:** `easeoutback`  
  `cubic-bezier(0.4, 1.4, 0.2, 1)`

### Transition Duration

| Token Variable | Duration | Use Case |
|---------------|----------|----------|
| short         | 200ms    | Small-size UI (buttons, dropdowns, micro-interactions) |
| medium        | 400ms    | Medium-size UI (callouts, alert dialogs, toasts) |
| long          | 600ms    | Large-size UI (page, section transitions) |

### Motion Tokens

Predefined values used to manage UI state transitions consistently.

Developers can customize the token name using the format `[motion-type].[transition]`. By default, transition durations are predefined as short, medium, and long, but can be replaced with custom values in rounded numbers for different speed durations.

| Token Variable        | CSS Code Example                                                    |
|----------------------|---------------------------------------------------------------------|
| easeoutback.short    | `.btn:hover { transition: 200ms cubic-bezier(0.4, 1.4, 0.2, 1); }`  |
| easeoutback.medium   | `.alert-dialog { transition: 400ms cubic-bezier(0.4, 1.4, 0.2, 1); }`|
| easeout.long         | `.slides { transition: 600ms cubic-bezier(0, 0, 0.58, 1); /* OR 600ms ease-out */ }` |
| easeout.1000         | `.circle { transition: 1000ms cubic-bezier(0, 0, 0.58, 1); /* OR 1000ms ease-out */ }`|
| easeoutback.1000     | `.circle { transition: 1000ms cubic-bezier(0.4, 1.4, 0.2, 1); }`    |

### Checkbox Usage Examples

Showcasing motion effects applied to UI components for smooth and dynamic interactions.

#### Toast

A simple demonstration of a Toast component appearing and disappearing with a delay in between.  
It features three animations:

- **Toast Enter:**  
  The Toast smoothly appears on the screen by sliding in from the bottom.  
  Token: `easeoutback.medium`  
  Motion type: `easeoutback`  
  Speed: 400ms (medium)

- **Delay Start / Progress Bar Start:**  
  After "Toast Enter" animation ends, a visual indicator (bar) counts down for about 3000ms, showing how long the Toast will remain visible before exiting.  
  Token: `linear.3000`  
  Motion type: `linear`  
  Speed: 3000ms

- **Progress Bar End / Toast Exit:**  
  After 3000ms "Progress bar" animation ends, the Toast disappears gracefully by sliding out to the bottom of the screen.  
  Token: `easeoutback.medium`  
  Motion type: `easeoutback`  
  Speed: 400ms (medium)

---

## Radius

A unified corner radius creates a smooth, modern aesthetic that enhances user experience through visual consistency. It softens element appearances, making them more approachable and easier to interact with.

![radius-preview.png](radius-preview.png) <!-- Add image for documentation and visual reference. -->

### Radius Specification

| Name         | Size   | Radius | Example (image)                | Where to use?                   |
|--------------|--------|--------|--------------------------------|---------------------------------|
| Extra Small  | 4px    | radius-xs-dark.png   | Context Menu Item                | Extra small radius for compact UI elements |
| Small        | 6px    | radius-s-dark.png    | Small Button                     | Small radius for compact buttons |
| Medium       | 8px    | radius-m-dark.png    | Button, CTA, Context Menu        | Medium radius for buttons and context menus |
| Large        | 12px   | radius-l-dark.png    | Content Card                     | Large radius for cards and containers |
| Extra Large  | 14px   | radius-xl-dark.png   | Context Menu with Search field   | Extra large radius for wide context menus |
| Full         | 9999px | radius-full-dark.png | Fully rounded (e.g., avatars)    | Full radius for circular elements |

---

## Shadow

Shadow adds depth and dimension to UI components, offering a sense of layering and hierarchy in digital interfaces.

![shadow-preview.png](shadow-preview.png) <!-- Visual reference for shadow specifications. -->

### Specification

| Name          | Shadow                    | CSS Code Example                                                                 |
|---------------|---------------------------|----------------------------------------------------------------------------------|
| None          | shadow-none-dark.png      | -                                                                                |
| Button        | shadow-button-dark.png    | `box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);`                               |
| Card          | shadow-card-dark.png      | `box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);` |
| Context Menu  | shadow-context-menu-dark.png | `box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);` |

---

## Spacing

The spacing guidelines define the consistent use of margins and paddings across all components, ensuring a harmonious and visually appealing layout.

| Size  | Preview Image        | Where to use?                                   |
|-------|----------------------|-------------------------------------------------|
| 4px   | space-4-dark.png     |                                                 |
| 8px   | space-8-dark.png     | Gap in buttons group, fields and labels         |
| 12px  | space-12-dark.png    |                                                 |
| 16px  | space-16-dark.png    |                                                 |
| 20px  | space-20-dark.png    |                                                 |
| 24px  | space-24-dark.png    | Gap between sub sections, cards                 |
| 32px  | space-32-dark.png    | Gap between main sections                       |
| 40px  | space-40-dark.png    |                                                 |
| 48px  | space-48-dark.png    |                                                 |
| 64px  | space-64-dark.png    |                                                 |

---

## Accordion

Organize and display content in a compact, collapsible format, commonly used for FAQ sections.

![accordion-anatomy.png](accordion-anatomy.png) <!-- Accordion anatomy visual reference -->

### Accordion Anatomy

The key elements that define an Accordion’s structure and function:

- **Header text:** The main title or label for the accordion section.
- **Body text:** The content that expands or collapses within the accordion.
- **Chevron Down (Default):** The default icon, displayed at a rotation of 0 degrees, indicating that the section is closed.
- **Chevron Down (Expanded):** The icon rotates to 180 degrees to indicate the section is open.

### State

![accordion-variation.png](accordion-variation.png) <!-- Accordion state visual reference -->

Defines how an Accordion visually responds to user actions like hover, focus, or click, providing feedback and enhancing usability.

- **Click trigger area (Header):** The clickable area on the header expands or collapses the accordion, toggling access to the body content.
- **Hover state:** Underline the Header text for interactive feedback.
- **Closed state (Default):** Body text is hidden (Opacity: 0%).
- **Opened state:** Click the trigger area (Header) to expand and show Body text.

### Demo

The example video below demonstrates the Accordion’s interaction and intended functionality in action.

---

## Alert Dialog

A modal pop-up designed to capture user attention for important actions or messages. It consists of three main components: Header, Content, and Footer. These elements together form a flexible and cohesive dialog structure that can accommodate different types of alerts or forms.

### Anatomy

**Imports:**

```javascript
import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogTitle,
  AlertDialogTrigger,
  AlertDialogAction,
  AlertDialogClose,
} from "@govtechmy/myds-react/alert-dialog";
```

**Component Structure Example:**

```jsx
export default () => (
  <AlertDialog>
    <AlertDialogTrigger />
    <AlertDialogBody>
      <AlertDialogHeader>
        <AlertDialogTitle />
      </AlertDialogHeader>
      <AlertDialogContent>
        <AlertDialogDescription />
      </AlertDialogContent>
      <AlertDialogFooter>
        <AlertDialogClose />
      </AlertDialogFooter>
    </AlertDialogBody>
  </AlertDialog>
);
```

#### Main Elements

- **Header text:** The main title or label for the dialog.
- **Content text:** The alert message, prompt, or form.
- **Footer:** Contains primary/secondary actions (e.g., Confirm, Cancel).

### Announce Bar Usage Examples

#### AlertDialog Variant

Set the `variant` prop to theme the alert dialog. Defaults to `default`. Available variants: `info`, `success`, `warning`, `danger`.

#### Trigger

Wrap the `AlertDialogTrigger` around the element that opens the dialog. Multiple triggers can open the same dialog.

#### Dialog State Management

- **Uncontrolled:** Use `defaultOpen` prop for initial state.
- **Controlled:** Use `open` and `onOpenChange` props to manage dialog state programmatically.

#### Dismissible (AlertDialog)

- **Dismissible (default):** Users can close the dialog.
- **Non-dismissible:** Set `dismissible={false}` on `AlertDialogContent` to prevent dismiss.
- Attach a callback to `onDismiss` for dismiss events.

#### AlertDialog Alignment

Set `align` on `DialogAction` to `start`, `end`, or `full` (default is `end`).

#### Action

Utilize the `action` prop on `DialogAction` to add special actions.

### AlertDialog Props

#### AlertDialog

| Prop         | Type                      | Default   |
|--------------|---------------------------|-----------|
| variant      | enum                      | success   |
| open         | boolean                   | false     |
| defaultOpen  | boolean                   | false     |
| onOpenChange | (open: boolean) => void   | -         |

#### AlertDialogContent

| Prop         | Type        | Default   |
|--------------|-------------|-----------|
| dismissible  | boolean     | true      |
| onDismiss    | () => void  | -         |

#### AlertDialogAction

| Prop         | Type                  | Default   |
|--------------|-----------------------|-----------|
| align        | start \| end \| full  | end       |
| action       | ReactNode             | -         |

---

## Announce Bar

Informs users about the service's development stage and includes a link for submitting feedback. The phase banner is typically placed below the navigation menu to display the current status of the service, such as Alpha, Beta, or Maintenance.

### Anatomy & Usage (Cookie Banner)

**Imports:**

```javascript
import {
  AnnounceBar,
  AnnounceBarTag,
  AnnounceBarDescription,
} from "@govtechmy/myds-react/announce-bar";
```

**Component Structure Example:**

```jsx
export default () => (
  <AnnounceBar>
    <AnnounceBarTag />
    <AnnounceBarDescription />
  </AnnounceBar>
);
```

### Announce Bar Examples

#### Announce Bar Variant

Use the `variant` prop to change the Announce Bar style.

```jsx
<AnnounceBar>
  <AnnounceBarTag variant="alpha" />
  <AnnounceBarDescription>
    This is a new service. Help us improve it. Send us your feedback here.
  </AnnounceBarDescription>
</AnnounceBar>
```

### AnnounceBar Props

#### AnnounceBar

| Prop    | Type      | Default |
|---------|-----------|---------|
| border  | boolean   | true    |
| children| ReactNode | -       |

#### AnnounceBarTag

| Prop    | Type   | Default |
|---------|--------|---------|
| variant | enum   | default |
| children| ReactNode | -    |

#### AnnounceBarDescription

| Prop    | Type      | Default |
|---------|-----------|---------|
| children| ReactNode | -       |

---

## Breadcrumb

A navigation aid that helps users understand their current location within a website or application and allows them to easily navigate back to previous levels.

### Anatomy & Usage (Data Table)

**Imports:**

```javascript
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbSeparator,
  BreadcrumbPage,
} from "@govtechmy/myds-react/breadcrumb";
```

**Component Structure Example:**

```jsx
export default () => (
  <Breadcrumb>
    <BreadcrumbItem>
      <BreadcrumbLink />
      <BreadcrumbPage />
    </BreadcrumbItem>
    <BreadcrumbSeparator />
  </Breadcrumb>
);
```

- `BreadcrumbLink` is used for parent pages (clickable).
- `BreadcrumbPage` is used for the current/active page (not clickable).

### Examples

#### Breadcrumb Variant

Use the `variant` prop to change the breadcrumb style.

#### Link vs Page

- Use `BreadcrumbLink` for navigable parent pages.
- Use `BreadcrumbPage` for the current page (not clickable).

#### Ellipsis Crumb

- Crumbs that are too long will be truncated with an ellipsis.
- Maximum width: 200px.
- Hovering over a crumb shows the full text.

### Breadcrumb Props

#### Breadcrumb Component Props

| Prop    | Type   | Default |
|---------|--------|---------|
| variant | enum   | default |

---

## Button

A fundamental UI element used to trigger actions or events. It ensures consistency, accessibility, and a clear call to action.

### Data Table Component Anatomy & Usage

**Imports:**

```javascript
import {
  Button,
  ButtonIcon,
  ButtonCounter,
} from "@govtechmy/myds-react/button";
```

**Component Structure Example:**

```jsx
export default () => (
  <Button>
    <ButtonIcon />
    <ButtonCounter />
  </Button>
);
```

- `<ButtonIcon />`: Adds an action icon to the button.
- `<ButtonCounter />`: Adds a numeric indicator (e.g., count) to the button.

### Button Examples

#### Button Variant

Use the `variant` prop to change the button style.

```jsx
<Button variant="primary">Primary Button</Button>
<Button variant="secondary">Secondary Button</Button>
<Button variant="danger">Danger Button</Button>
```

#### Size

Use the `size` prop to change the button size.

```jsx
<Button size="small">Small</Button>
<Button size="medium">Medium</Button>
<Button size="large">Large</Button>
```

#### With Icon

Add an icon using `<ButtonIcon>`.

```jsx
<Button>
  <ButtonIcon>🔍</ButtonIcon>
  Search
</Button>
```

#### Icon Only

For buttons with only an icon, set `iconOnly={true}` and use `<ButtonIcon>` as the only child.

```jsx
<Button iconOnly>
  <ButtonIcon>🔍</ButtonIcon>
</Button>
```

#### With Counter

Add a numeric counter using `<ButtonCounter>`.

```jsx
<Button>
  Notifications
  <ButtonCounter>3</ButtonCounter>
</Button>
```

### Button Props

#### Button Component Props

| Prop      | Type      | Default         |
|-----------|-----------|-----------------|
| variant   | enum      | default-outline |
| size      | enum      | small           |
| iconOnly  | boolean   | false           |
| children  | ReactNode | -               |
| className | string    | -               |

#### ButtonIcon

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

#### ButtonCounter

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

---

## Callout

Notifies users about important information related to their actions inside Forms. Callouts can indicate success, warnings, errors, or provide additional information depending on the context inside a form.

### Anatomy & Implementation

**Imports:**

```javascript
import {
  Callout,
  CalloutAction,
  CalloutTitle,
  CalloutContent,
} from "@govtechmy/myds-react/callout";
```

**Component Structure Example:**

```jsx
export default () => (
  <Callout>
    <CalloutTitle />
    <CalloutContent />
    <CalloutAction>
      <CalloutClose />
    </CalloutAction>
  </Callout>
);
```

- `<CalloutTitle />`: Displays the callout's headline (e.g., "Success", "Warning", "Info", "Error").
- `<CalloutContent />`: Shows the description or supporting information.
- `<CalloutAction />`: Contains actionable elements, such as a close button with `<CalloutClose />`.

### Callout Examples

#### Callout Variant

Use the `variant` prop to change the callout style (e.g., info, success, warning, error).

```jsx
<Callout variant="success">
  <CalloutTitle>Success</CalloutTitle>
  <CalloutContent>Operation completed successfully.</CalloutContent>
</Callout>
```

#### Layout

- If only the title and action are present, the callout is displayed inline.
- If title, content, and action are present, the callout is displayed in a stacked manner.

#### Callout Dismissible

- To make the callout dismissible, use the `dismissible` prop.
- Attach `onDismiss` to capture the dismiss event (e.g., log to console).
- To dismiss via event listener, wrap the target element with `<CalloutClose />`; it also triggers `onDismiss` if defined.

### Callout Props

#### Callout Component Props

| Prop        | Type      | Default |
|-------------|-----------|---------|
| variant     | enum      | info    |
| dismissible | boolean   | -       |
| onDismiss   | () => void| -       |

---

## Checkbox

Allows users to make a binary choice, such as selecting or not selecting an option. Commonly used in forms, settings, and filters to capture user input and preferences.

### Data Table Anatomy & Usage

**Imports:**

```javascript
import { Checkbox } from "@govtechmy/myds-react/checkbox";
```

**Component Structure Example:**

```jsx
export default () => <Checkbox />;
```

### Checkbox Examples

#### Checkbox Size

Use the `size` prop to change the checkbox size.

```jsx
<Checkbox size="small" />
<Checkbox size="medium" />
<Checkbox size="large" />
```

#### Checkbox State Management

- **Uncontrolled:** Do not provide the `checked` prop; the checkbox manages its own state.
- **Controlled:** Provide the `checked` prop and handle state via the `onCheckedChange` event.

```jsx
// Uncontrolled
<Checkbox defaultChecked={true} />

// Controlled
<Checkbox checked={isChecked} onCheckedChange={setIsChecked} />
```

#### Checkbox Disabled

Set the `disabled` prop to `true` to make the checkbox non-interactive.

```jsx
<Checkbox disabled />
```

#### Indeterminate

Use the `checked={indeterminate}` or `defaultChecked={indeterminate}` to indicate a partial selection, commonly used for parent checkboxes in groups.

```jsx
<Checkbox checked="indeterminate" />
```

### Checkbox Props

#### Checkbox Component Props

| Prop            | Type                           | Default        |
|-----------------|--------------------------------|----------------|
| size            | enum                           | small          |
| defaultChecked  | boolean \| indeterminate       | false          |
| checked         | boolean \| indeterminate       | false          |
| onCheckedChange | (checked: boolean \| indeterminate) => void | false          |

---

## Cookies Banner

Notifies users that the website uses cookies to improve their experience and gives them the option to manage their consent preferences through cookie settings. The banner typically appears when a user lands on the website for the first time.

### Anatomy & Usage (Cookies Banner)

**Imports:**

```javascript
import {
  CookieBanner,
  CookieBannerClose,
  CookieBannerDescription,
  CookieBannerAction,
  CookieBannerPreferences,
  CookieBannerTitle,
  CookieBannerPreferencesTrigger,
} from "@govtechmy/myds-react/cookie-banner";
```

**Component Structure Example:**

```jsx
export default () => (
  <CookieBanner>
    <CookieBannerTitle />
    <CookieBannerDescription />
    <CookieBannerPreferences />
    <CookieBannerAction>
      <CookieBannerClose />
    </CookieBannerAction>
  </CookieBanner>
);
```

- `<CookieBannerTitle />`: Displays the title or headline of the cookie banner.
- `<CookieBannerDescription />`: Provides a brief description about cookies usage.
- `<CookieBannerPreferences />`: Optionally includes a preferences menu for cookie settings.
- `<CookieBannerAction />`: Contains CTAs for user actions, such as closing or managing preferences.
- `<CookieBannerClose />`: A button to dismiss/close the banner.

### Cookie Banner Examples

#### CookieBanner State Management

- **Uncontrolled:** Use the `defaultOpen` prop to define the initial state.
- **Controlled:** Use `open` and `onOpenChange` props to manage the banner's state programmatically.

> **Note:** It is recommended to keep `CookieBanner` as an uncontrolled component (via `defaultOpen`). Use client-side storage APIs (e.g., `localStorage` or `sessionStorage`) to store and retrieve the state when the user revisits the website.

#### Preference Call-to-Action (CTA)

- For letting users choose their cookie preferences, pass the `preferences` prop to `CookieBannerAction` to display preference CTAs.
- Use `CookieBannerPreferencesTrigger` to toggle visibility of cookie preference options.

### CookieBanner Props

#### CookieBanner

| Prop         | Type                   | Default |
|--------------|------------------------|---------|
| defaultOpen  | boolean                | false   |
| open         | boolean                | false   |
| onOpenChange | (value: boolean) => void | -     |

#### CookieBannerTitle

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### CookieBannerDescription

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### CookieBannerPreferences

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### CookieBannerPreferencesTrigger

| Prop      | Type      | Default |
|-----------|-----------|---------|
| asChild   | boolean   | -       |
| children  | ReactNode | -       |

#### CookieBannerAction

| Prop        | Type      | Default |
|-------------|-----------|---------|
| preferences | boolean   | -       |
| children    | ReactNode | -       |

#### CookieBannerClose

| Prop      | Type      | Default |
|-----------|-----------|---------|
| asChild   | boolean   | -       |
| children  | ReactNode | -       |

---

## Data Table

Organizes information into rows and columns for easy readability. Accommodates various data types, including text, numbers, codes, call-to-action elements, and links, enabling efficient presentation and comparison.

### Component Anatomy & Usage

**Imports:**

```javascript
import { DataTable } from "@govtechmy/myds-react/data-table";
```

**Component Structure Example:**

```jsx
export default () => <DataTable />;
```

### Data Table Examples

#### Data & Columns

- `data`: Array of objects, each representing a row in the table.
- `columns`: Array of `ColumnDef` objects, each representing a column in the table.

#### Expanding Columns

Set the `expandable` property to `true` in the `meta` object of the column definition to allow the cell's content to expand.  
Example:  

- Expand icon appears on expandable columns (e.g., Name and Position).

#### Tooltip on Column Headers

Set the `tooltip` property in the `meta` object of the column definition to display a tooltip on column headers.  
Example:  

- Hover over Age or Position column headers to view their tooltips.

#### Sorting

Set the `sortable` property to `true` in the `meta` object of the column definition to enable sorting for a column.  
Example:  

- Click the sort icon on the Position column to toggle sorting.

#### Skeleton Loading

Enable the `loading` prop to display skeleton loading state for the table while data is being fetched.

#### Empty Table

Passing an empty array to the `data` prop will render an empty state with "No data available" message.

#### Row Selection

Enable selection mode by passing a `selection` prop to the DataTable.  

- `mode`: Can be either `checkbox` or `radio`.
- `onSelectionChange`: Callback triggered on selection change.
- `rowId`: Unique identifier for each row.

#### Grouped Header

Group columns together by setting the `columns` property to an array of `ColumnGroupDef` objects.  

- Each `ColumnGroupDef` represents a group of columns.

#### Fixed/Sticky Header

Table header is sticky by default. To demonstrate, set a fixed height using the `className` prop (e.g., `max-h-[300px]`).

#### Nested Rows

DataTable can nest rows.  

- `data`: Contains a nested array object matching the `id_by` key in the `nest` object.
- `columns`: Use `Cell.Expand` for the column that expands/collapses nested rows.
- `nest`: Object prop with `{ id_by: string }`.

#### Pinned Columns

Pin columns to the left or right by setting the `pin` prop:  

- `left`: Array of column IDs to pin left.
- `right`: Array of column IDs to pin right.

#### Table Footer

Add a footer per column by setting the `footer` property in the `ColumnDef` of a column definition.

### DataTable Props

#### DataTable

| Prop     | Type                                    | Default |
|----------|-----------------------------------------|---------|
| columns  | ColumnDef                               | -       |
| data     | Array<T = {}>                           | -       |
| nest     | { id_by: string }                       | -       |
| pin      | `{ left: Array&lt;string&gt;, right: Array&lt;string&gt; }` | - |

#### DataTable ColumnDef

| Prop            | Type                                      | Default   |
|-----------------|-------------------------------------------|-----------|
| id              | string                                    | -         |
| accessorKey     | string                                    | -         |
| accessorFn      | (row) => any                              | -         |
| header          | string \| (context) => ReactNode          | -         |
| footer          | string \| (context) => ReactNode          | -         |
| size            | number                                    | -         |
| cell            | (context) => ReactNode                    | -         |
| meta.expandable | boolean                                   | -         |
| meta.sortable   | boolean                                   | -         |
| meta.tooltip    | ReactNode                                 | -         |

---

## Date Field

Allows users to input a date in a structured format using separate fields for day, month, and year.

### Anatomy & Usage (Date Field)

**Imports:**

```javascript
import { DateField } from "@govtechmy/myds-react/date-field";
```

**Component Structure Example:**

```jsx
export default () => <DateField />;
```

### Date Field Examples

#### Date Field Size

Use the `size` prop to change the date field size.

```jsx
<DateField size="small" />
<DateField size="medium" />
<DateField size="large" />
```

#### Uncontrolled vs Controlled State (DateField)

- **Uncontrolled:** Use the `defaultValue` prop to set the initial value.
- **Controlled:** Set the `value` prop and use the `onChange` event to manage changes.

```jsx
// Uncontrolled
<DateField defaultValue="17/06/2025" />

// Controlled
<DateField value={dateValue} onChange={setDateValue} />
```
<!--
The uncontrolled example will allow the component to manage its own value.
The controlled example will require you to manage the value in your app state.
-->

#### DateField Disabled

Set the `disabled` prop to `true` to make the date field non-interactive.

```jsx
<DateField disabled />
```

#### Invalid

Set the `invalid` prop to `true` to indicate validation failure or an error state.

```jsx
<DateField invalid />
```

### DateField Props

#### DateField Component Props

| Prop         | Type                       | Default |
|--------------|----------------------------|---------|
| size         | enum                       | small   |
| defaultValue | string                     | -       |
| value        | string                     | -       |
| onChange     | (date: string) => void     | -       |
| disabled     | boolean                    | false   |
| invalid      | boolean                    | false   |

---

## Date Picker

Allows users to select a date from an interactive calendar.

### Anatomy & Usage (Date Range Picker Component)

**Imports:**

```javascript
import { DatePicker } from "@govtechmy/myds-react/date-picker";
```

**Component Structure Example:**

```jsx
export default () => <DatePicker />;
// Renders a date picker component where users can select a date from a calendar.
```

### Date Picker Examples

#### Date Picker Size

Use the `size` prop to change the date picker size.

```jsx
<DatePicker size="small" />
<DatePicker size="medium" />
<DatePicker size="large" />
```

#### Uncontrolled vs Controlled State (DatePicker)

- **Uncontrolled:** Use the `defaultValue` prop to set the initial value.
- **Controlled:** Set the `value` prop and use the `onValueChange` event to handle changes.

```jsx
// Uncontrolled
<DatePicker defaultValue={new Date("2025-06-17")} />

// Controlled
<DatePicker value={selectedDate} onValueChange={setSelectedDate} />
```
<!--
The uncontrolled example lets the component manage its own value.
The controlled example lets your app manage the selected date.
-->

#### DatePicker Disabled

- **Entirely Disabled:** Set the `disabled` prop to `true` to make the picker non-interactive.
- **Custom Disabled Dates:** Pass a Matcher or Matcher[] to the `disabled` prop to disable specific dates or ranges.

```jsx
// Disable all dates
<DatePicker disabled={true} />

// Disable dates before today
<DatePicker disabled={{ before: new Date() }} />

// Disable dates after today
<DatePicker disabled={{ after: new Date() }} />

// Disable dates outside of range
<DatePicker disabled={[{ before: minDate }, { after: maxDate }]} />

// Disable weekends (Saturday and Sunday)
<DatePicker disabled={date => date.getDay() === 0 || date.getDay() === 6} />

// Disable every 13th of the month
<DatePicker disabled={date => date.getDate() === 13} />
```
<!--
You can disable the entire picker or specific dates using matchers or callback functions.
-->

#### Maximum and Minimum Year (DatePicker)

Set the maximum and minimum year with the `minYear` and `maxYear` props.

```jsx
<DatePicker minYear={2000} maxYear={2030} />
// Limits the selectable years in the dropdown.
```

#### Year Dropdown Sort Order

Set the sorting order of years in the dropdown with the `yearOrder` prop.

```jsx
<DatePicker yearOrder="asc" /> // Ascending order
<DatePicker yearOrder="desc" /> // Descending order
```
<!--
Controls whether the year dropdown sorts years ascending or descending.
-->

#### Locale (DatePicker)

Support different locales by passing the `locale` prop.  
Default is English (`en`). For Bahasa Melayu, use `ms`.

```jsx
<DatePicker locale="en" />
<DatePicker locale="ms" />
```
<!--
Locale changes the language/format of the calendar.
-->

### DatePicker Props

#### DatePicker Component Props

| Prop        | Type                             | Default         |
|-------------|----------------------------------|-----------------|
| size        | enum                             | small           |
| defaultValue| Date                             | -               |
| value       | Date                             | -               |
| onValueChange| (date: Date) => void            | -               |
| placeholder | string                           | dd / mm / yyyy  |
| disabled    | boolean \| Matcher \| Matcher[]  | false           |
| maxYear     | number                           | 2099            |
| minYear     | number                           | 1900            |
| yearOrder   | asc \| desc                      | asc             |
| locale      | en \| ms                         | en              |

---

## Date Range Picker

Allows users to select a date range from an interactive calendar.

### Anatomy & Usage (Date Range Picker)

**Imports:**

```javascript
import { DateRangePicker } from "@govtechmy/myds-react/date-picker";
```

**Component Structure Example:**

```jsx
export default () => <DateRangePicker />;
// Renders a date range picker component for selecting a start and end date via calendar.
```

### Date Range Picker Examples

#### Date Range Picker Size

Use the `size` prop to change the date range picker size.

```jsx
<DateRangePicker size="small" />
<DateRangePicker size="medium" />
<DateRangePicker size="large" />
```
<!--
Choose the visual size of the date range picker to fit your layout.
-->

#### Dialog State: Uncontrolled vs Controlled

- **Uncontrolled:** Use the `defaultValue` prop to set the initial range.
- **Controlled:** Set the `value` prop and use the `onValueChange` event to handle changes.

```jsx
// Uncontrolled
<DateRangePicker defaultValue={{ from: new Date("2025-06-17"), to: new Date("2025-06-20") }} />

// Controlled
<DateRangePicker value={dateRange} onValueChange={setDateRange} />
```
<!--
Uncontrolled lets the picker manage its own range. Controlled lets you manage the range in your app state.
-->

#### DateRangePicker Disabled

- **Entirely Disabled:** Set the `disabled` prop to `true` to make the picker non-interactive.
- **Custom Disabled Dates:** Pass a Matcher or Matcher[] to disable specific dates or ranges.

```jsx
// Disable all dates
<DateRangePicker disabled={true} />

// Disable dates before yesterday
<DateRangePicker disabled={{ before: yesterday }} />

// Disable dates after tomorrow
<DateRangePicker disabled={{ after: tomorrow }} />

// Disable dates outside of range
<DateRangePicker disabled={[{ before: minDate }, { after: maxDate }]} />

// Disable weekends (Saturday and Sunday)
<DateRangePicker disabled={date => date.getDay() === 0 || date.getDay() === 6} />

// Disable every 13th of the month
<DateRangePicker disabled={date => date.getDate() === 13} />
```
<!--
You can disable all, ranges, or custom rules using callback functions.
-->

#### Maximum and Minimum Year

Set the maximum and minimum year with the `minYear` and `maxYear` props.

```jsx
<DateRangePicker minYear={2000} maxYear={2030} />
// Limits the selectable years for range selection.
```

#### Year Sort Order

Set the sorting order of years with the `yearOrder` prop.

```jsx
<DateRangePicker yearOrder="asc" /> // Ascending years
<DateRangePicker yearOrder="desc" /> // Descending years
```
<!--
Controls the sort order for year dropdowns.
-->

#### Locale

Support different locales by passing the `locale` prop.  
Default is English (`en`). For Bahasa Melayu, use `ms`.

```jsx
<DateRangePicker locale="en" />
<DateRangePicker locale="ms" />
```
<!--
Locale changes the language and formatting of the calendar.
-->

### DateRangePicker Props

#### DateRangePicker Component Props

| Prop         | Type                                 | Default         |
|--------------|--------------------------------------|-----------------|
| size         | enum                                 | small           |
| defaultValue | { from: Date, to: Date }             | -               |
| value        | { from: Date, to: Date }             | -               |
| onValueChange| (range: { from: Date, to: Date }) => void | -          |
| placeholder  | string                               | dd / mm / yyyy  |
| disabled     | boolean \| Matcher \| Matcher[]      | false           |
| maxYear      | number                               | 2099            |
| minYear      | number                               | 1900            |
| yearOrder    | asc \| desc                          | asc             |
| locale       | en \| ms                             | en              |

---

## Dialog

A modal pop-up designed to capture user attention for important actions or messages. It consists of three main components: Header, Content, and Footer.

### Anatomy & Usage (Dropdown)

**Imports:**

```javascript
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
  DialogClose,
} from "@govtechmy/myds-react/dialog";
```

**Component Structure Example:**

```jsx
export default () => (
  <Dialog>
    <DialogTrigger />
    <DialogBody>
      <DialogHeader>
        <DialogTitle />
      </DialogHeader>
      <DialogContent>
        <DialogDescription />
      </DialogContent>
      <DialogFooter>
        <DialogClose />
      </DialogFooter>
    </DialogBody>
  </Dialog>
);
// This component creates a dialog with a trigger, header, content, and footer.
// DialogTrigger opens the dialog. Header and Title show the modal's heading.
// Content and Description provide details. Footer includes a close button.
```

### Dialog Examples

#### Dialog Trigger

Wrap the `DialogTrigger` around the element (e.g., button) that opens the dialog.  
You can use multiple triggers to open the same dialog.

```jsx
<Dialog>
  <DialogTrigger>
    <Button>Open Dialog</Button>
  </DialogTrigger>
  {/* ...dialog body here... */}
</Dialog>
```
<!--
Multiple triggers can be used to open a single dialog instance.
-->

#### Dialog Open/Close State Management

- **Uncontrolled:** Use the `defaultOpen` prop to set the initial state.
- **Controlled:** Use the `open` prop and handle state via the `onOpenChange` callback.

```jsx
// Uncontrolled
<Dialog defaultOpen={true} />

// Controlled
<Dialog open={isOpen} onOpenChange={setIsOpen} />
```
<!--
Uncontrolled lets the component manage its own open/close state.
Controlled lets you manage state in your app.
-->

#### Dismissible (Dialog)

- By default, dialogs are dismissible.
- To prevent dismissal, set the `dismissible` prop to `false` on `DialogBody`.
- You can handle the dismiss event with the `onDismiss` prop.

```jsx
<DialogBody dismissible={false} onDismiss={() => { /* handle close */ }} />
```
<!--
Set dismissible to false to make the dialog not closable by the user.
-->

#### Border

Add a border to the `DialogHeader` and `DialogFooter` by setting their `border` prop to `true`.

```jsx
<DialogHeader border={true}>...</DialogHeader>
<DialogFooter border={true}>...</DialogFooter>
```
<!--
Borders help visually separate header and footer from content.
-->

#### Alignment

Set the `align` prop on `DialogFooter` to control alignment of its children.

```jsx
<DialogFooter align="start">...</DialogFooter>
<DialogFooter align="full">...</DialogFooter>
<DialogFooter align="end">...</DialogFooter>
```
<!--
Use "start", "full", or "end" to choose left, full-width, or right alignment.
-->

#### Dialog Actions

Add special actions to the dialog by passing elements to the `action` prop of `DialogFooter`.

```jsx
<DialogFooter action={<Button>Save Changes</Button>}>...</DialogFooter>
```
<!--
You can add custom actions (e.g., Save, Cancel) in the footer.
-->

### Dialog Props

#### Dialog Component Props

| Prop         | Type                       | Default |
|--------------|----------------------------|---------|
| open         | boolean                    | false   |
| defaultOpen  | boolean                    | false   |
| onOpenChange | (open: boolean) => void    | -       |

#### DialogBody

| Prop         | Type           | Default |
|--------------|----------------|---------|
| dismissible  | boolean        | true    |
| hideClose    | boolean        | false   |
| onDismiss    | () => void     | -       |

#### DialogHeader

| Prop         | Type           | Default |
|--------------|----------------|---------|
| border       | boolean        | false   |

#### DialogFooter

| Prop         | Type                   | Default |
|--------------|------------------------|---------|
| border       | boolean                | false   |
| align        | start \| end \| full   | end     |
| action       | ReactNode              | -       |

---

## Dropdown

Dropdowns are toggleable, contextual overlays for displaying lists of links and more.

### Anatomy & Usage

**Imports:**

```javascript
import {
  Dropdown,
  DropdownContent,
  DropdownItem,
  DropdownItemIcon,
  DropdownTrigger,
} from "@govtechmy/myds-react/dropdown";
```

**Component Structure Example:**

```jsx
export default () => (
  <Dropdown>
    <DropdownTrigger />
    <DropdownContent>
      <DropdownItem>
        <DropdownItemIcon />
      </DropdownItem>
    </DropdownContent>
  </Dropdown>
);
// This component sets up a dropdown menu with a trigger, content area, and items.
// DropdownItemIcon can be used to display an icon next to each item.
```

### Dropdown Examples

#### Dropdown Trigger

Wrap the `DropdownTrigger` around the element (e.g., button) that opens the dropdown menu.

```jsx
<Dropdown>
  <DropdownTrigger>
    <Button>Open Dropdown</Button>
  </DropdownTrigger>
  {/* ...dropdown content here... */}
</Dropdown>
```
<!--
The trigger element can be any clickable UI element.
-->

#### Uncontrolled vs Controlled State

- **Uncontrolled:** Use the `defaultOpen` prop to set the initial state.
- **Controlled:** Use the `open` prop and handle state via the `onOpenChange` callback.

```jsx
// Uncontrolled
<Dropdown defaultOpen={true} />

// Controlled
<Dropdown open={isOpen} onOpenChange={setIsOpen} />
```
<!--
Uncontrolled lets the dropdown manage open/close state on its own.
Controlled lets your app manage the dropdown state.
-->

#### Dropdown Item With Icon

Add an icon to `DropdownItem` using the `DropdownItemIcon` component to provide visual cues.

```jsx
<DropdownItem>
  <DropdownItemIcon>
    <Icon name="edit" />
  </DropdownItemIcon>
  Edit
</DropdownItem>
```
<!--
Icons help users quickly recognize actions in dropdown menus.
-->

#### Dropdown Item Variant

Set the `variant` prop to `danger` on `DropdownItem` to indicate destructive actions.

```jsx
<DropdownItem variant="danger">
  <DropdownItemIcon>
    <Icon name="delete" />
  </DropdownItemIcon>
  Delete
</DropdownItem>
```
<!--
"danger" variant visually distinguishes destructive actions.
-->

#### Dropdown Item Disabled

Disable a dropdown item by setting its `disabled` prop to `true`. Disabled items are non-interactive.

```jsx
<DropdownItem disabled>
  <DropdownItemIcon>
    <Icon name="lock" />
  </DropdownItemIcon>
  Locked Item
</DropdownItem>
```
<!--
Disabled items appear faded and cannot be selected.
-->

#### Dropdown Alignment

Set the `align` prop on `DropdownContent` to control where the dropdown content aligns relative to the trigger element.

```jsx
<DropdownContent align="start">...</DropdownContent>
<DropdownContent align="center">...</DropdownContent>
<DropdownContent align="end">...</DropdownContent>
```
<!--
Choose from "start", "center", or "end" for alignment.
-->

#### Side

Set the `side` prop on `DropdownContent` to position the dropdown content relative to the trigger.

```jsx
<DropdownContent side="bottom">...</DropdownContent>
<DropdownContent side="top">...</DropdownContent>
<DropdownContent side="left">...</DropdownContent>
<DropdownContent side="right">...</DropdownContent>
```
<!--
Position dropdown content above, below, or beside the trigger element.
-->

### Dropdown Props

#### Dropdown Component Props

| Prop         | Type                       | Default |
|--------------|----------------------------|---------|
| open         | boolean                    | false   |
| defaultOpen  | boolean                    | false   |
| onOpenChange | (open: boolean) => void    | -       |

#### DropdownTrigger

| Prop         | Type           | Default |
|--------------|----------------|---------|
| asChild      | boolean        | false   |

#### DropdownContent

| Prop         | Type           | Default |
|--------------|----------------|---------|
| align        | enum           | end     |
| side         | enum           | bottom  |

#### DropdownItem

| Prop         | Type                      | Default |
|--------------|---------------------------|---------|
| disabled     | boolean                   | false   |
| variant      | default \| danger         | default |
| onSelect     | (event: Event) => void    | -       |

---

## Footer

The Footer component provides a standardized footer for Malaysian government websites and services.

### Anatomy & Usage (Footer)

**Imports:**

```javascript
import {
  Footer,
  SiteInfo,
  FooterSection,
  SiteLinkGroup,
  SiteLink,
  FooterLogo,
} from "@govtechmy/myds-react/footer";
```
<!--
Imports all necessary subcomponents to build a government-standard footer.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Footer>
    <FooterSection>
      <SiteInfo>
        <FooterLogo />
      </SiteInfo>
      <SiteLinkGroup>
        <SiteLink />
      </SiteLinkGroup>
    </FooterSection>
  </Footer>
);
// This example shows a typical footer layout with information, logo, and grouped links.
```

### Footer Examples

#### Footer Layout Section

`FooterSection` is a grid-based layout container that organizes and partitions footer content and provides responsive behavior.

```jsx
<Footer>
  <FooterSection>
    All Rights Reserved © 2025
    |
    <SiteLink>Disclaimer</SiteLink>
    <SiteLink>Privacy Policy</SiteLink>
    Last updated: 11th March 2025
  </FooterSection>
</Footer>
```
<!--
Use FooterSection for legal, copyright, and policy info.
-->

#### Footer SiteInfo

`SiteInfo` is an aside HTML component designed to contain brand information, contact details, and social media links in the footer.

```jsx
<SiteInfo>
  <FooterLogo logoTitle="Kementerian Digital" />
  Aras 13, 14 & 15, Blok Menara, Menara Usahawan, No. 18, Persiaran Perdana, Presint 2, Pusat Pentadbiran Kerajaan Persekutuan, 62000 Putrajaya, Malaysia
  Follow us
</SiteInfo>
```
<!--
SiteInfo is ideal for address, contact, and social links.
-->

#### Footer SiteLinkGroup

`SiteLinkGroup` structures a collection of links (`SiteLink`) into a titled group with responsive column layout.

```jsx
<SiteLinkGroup groupTitle="Super long title" linkCount={8}>
  <SiteLink>Link 1 super super long super super long long long long</SiteLink>
  <SiteLink>Link 2</SiteLink>
  <SiteLink>Link 3</SiteLink>
  <SiteLink>Link 4</SiteLink>
  <SiteLink>Link 5</SiteLink>
  <SiteLink>Link 6</SiteLink>
  <SiteLink>Link 7</SiteLink>
  <SiteLink>Link 8</SiteLink>
</SiteLinkGroup>
```
<!--
SiteLinkGroup is used for grouped navigation or resource links.
-->

### Footer Props

#### Footer Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

#### FooterSection Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

#### SiteInfo Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

#### SiteLinkGroup Component Props

| Prop       | Type      | Default |
|------------|-----------|---------|
| children   | ReactNode | -       |
| className  | string    | -       |
| groupTitle | ReactNode | -       |
| linkCount  | number    | 8       |

#### FooterLogo Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| logoTitle | ReactNode | -       |
| logo      | ReactNode | -       |

#### SiteLink Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |
| asChild   | boolean   | false   |

---

## Input

The input component provides a basic text input field for users to enter text. It supports various sizes, states, icons, and addons for flexible form designs.

### Anatomy & Usage (Input)

**Imports:**

```javascript
import { Input } from "@govtechmy/myds-react/input";
```
<!--
Import the core Input component for text fields in forms.
-->

**Component Structure Example:**

```jsx
<Input />
// Renders a basic text input field for user input.
```

### Input Examples

#### Input Size

Use the `size` prop to change the size of the input field. The default size is `small`.

```jsx
<Input size="small" placeholder="Small" />
<Input size="medium" placeholder="Medium" />
<Input size="large" placeholder="Large" />
```
<!--
Choose input size for different UI layouts.
-->

#### Input Disabled

Disable the input by setting the `disabled` prop to `true`.

```jsx
<Input disabled placeholder="Disabled" />
```
<!--
The input field is non-interactive when disabled.
-->

#### Input State Management (Uncontrolled vs Controlled)

- **Uncontrolled:** Use the `defaultValue` prop to set the initial value.
- **Controlled:** Use the `value` prop and `onChange` handler to manage the input state programmatically.

```jsx
// Uncontrolled
<Input defaultValue="Uncontrolled" />

// Controlled
<Input value={inputValue} onChange={setInputValue} />
```
<!--
Uncontrolled input manages its own value; controlled input is managed by your app.
-->

#### Input with Icon

Add icons to the input field using the `InputIcon` component. Icons can be positioned to the left, right, or both sides.

```jsx
<Input>
  <InputIcon position="left">
    <Icon name="search" />
  </InputIcon>
</Input>

<Input>
  <InputIcon position="right">
    <Icon name="eye" />
  </InputIcon>
</Input>

<Input>
  <InputIcon position="left">
    <Icon name="search" />
  </InputIcon>
  <InputIcon position="right">
    <Icon name="eye" />
  </InputIcon>
</Input>
```
<!--
Icons provide visual cues for input fields (e.g., search, visibility).
-->

#### Input with Addon

Use the `InputAddon` component to append or prepend controls, such as currency symbols or buttons, to the input field.

```jsx
<Input>
  <InputAddon>RM</InputAddon>
</Input>

<Input>
  <InputAddon>Type something here</InputAddon>
</Input>

<Input>
  <InputAddon>
    <Button>Submit</Button>
  </InputAddon>
</Input>
```
<!--
Addons extend input functionality with extra controls or information.
-->

### Input Props

#### Input Component Props

| Prop         | Type      | Default |
|--------------|-----------|---------|
| size         | enum      | small   |
| placeholder  | string    | -       |
| defaultValue | string    | -       |
| value        | string    | -       |
| onChange     | function  | -       |
| disabled     | boolean   | false   |

#### InputIcon Component Props

| Prop      | Type               | Default |
|-----------|--------------------|---------|
| position  | left \| right      | -       |
| children  | ReactNode          | -       |

#### InputAddon Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

---

## Input OTP

The Input OTP (One-Time Password) component enables users to enter a code (typically sent to email or phone) for verifying identity. Common use cases include two-factor authentication (2FA) and account recovery.

### Anatomy & Usage (Input OTP)

**Imports:**

```javascript
import { InputOTP, InputOTPSlot } from "@/components/myds";
```
<!--
Imports the InputOTP and InputOTPSlot components for building OTP input forms.
-->

**Component Structure Example:**

```jsx
<InputOTP maxLength={4}>
  <InputOTPSlot index={0} />
  <InputOTPSlot index={1} />
  <InputOTPSlot index={2} />
  <InputOTPSlot index={3} />
</InputOTP>
// This setup creates a 4-digit OTP input, with each slot representing a digit/character.
```

### Input OTP Examples

#### Input OTP Invalid State

Set the `invalid` prop to `true` to mark the entire OTP input as invalid.

```jsx
<InputOTP maxLength={4} invalid>
  <InputOTPSlot index={0} />
  <InputOTPSlot index={1} />
  <InputOTPSlot index={2} />
  <InputOTPSlot index={3} />
</InputOTP>
```
<!--
Indicates an incorrect or failed OTP entry.
-->

#### Input OTP Disabled State

Disable the OTP input by setting the `disabled` prop to `true`.

```jsx
<InputOTP maxLength={4} disabled>
  <InputOTPSlot index={0} />
  <InputOTPSlot index={1} />
  <InputOTPSlot index={2} />
  <InputOTPSlot index={3} />
</InputOTP>
```
<!--
Prevents user interaction when OTP entry is not allowed.
-->

#### Input OTP Controlled Component

Control the OTP value using `value` and `onChange` props.  
In this example, the value is managed in state and converted to uppercase on change.

```jsx
const [otp, setOtp] = useState("");

<InputOTP value={otp} onChange={val => setOtp(val.toUpperCase())} maxLength={4}>
  <InputOTPSlot index={0} />
  <InputOTPSlot index={1} />
  <InputOTPSlot index={2} />
  <InputOTPSlot index={3} />
</InputOTP>
```
<!--
Controlled OTP input supports custom value handling (e.g. uppercase).
-->

#### Input OTP Pattern Restriction

Use the `pattern` prop to restrict OTP input to a specific regex pattern.  
For example, only allow numeric OTP:

```jsx
<InputOTP pattern="\d*" maxLength={4}>
  <InputOTPSlot index={0} />
  <InputOTPSlot index={1} />
  <InputOTPSlot index={2} />
  <InputOTPSlot index={3} />
</InputOTP>
```
<!--
Pattern prop ensures OTP consists only of numbers (or other custom formats).
-->

### Input OTP Props

#### InputOTP Component Props

| Prop        | Type      | Default |
|-------------|-----------|---------|
| maxLength   | number    | -       |
| defaultValue| string    | -       |
| value       | string    | -       |
| onChange    | function  | -       |
| disabled    | boolean   | false   |
| invalid     | boolean   | false   |
| pattern     | string    | -       |

#### InputOTPSlot Component Props

| Prop  | Type   | Default |
|-------|--------|---------|
| index | number | -       |

---

## Label

The Label component is a standardized form element used to associate descriptive text with controls such as checkboxes and toggles. It ensures accessibility and consistent styling for Malaysian government websites.

### Anatomy & Usage (Label)

**Imports:**

```javascript
import { Label } from "@govtechmy/myds-react/label";
import { Checkbox } from "@govtechmy/myds-react/checkbox";
import { Toggle, ToggleThumb } from "@govtechmy/myds-react/toggle";
```
<!--
Import Label for form text, Checkbox for selection, and Toggle for switch controls.
-->

**Component Structure Example:**

```jsx
export default () => (
  <>
    <div className="flex items-center justify-between">
      <Label htmlFor="checkbox">Auto-delete notifications</Label>
      <Checkbox id="checkbox" />
    </div>
    <div className="flex items-center justify-between">
      <Label htmlFor="toggle">Enable 2FA security?</Label>
      <Toggle id="toggle">
        <ToggleThumb />
      </Toggle>
    </div>
  </>
);
// This example shows Label paired with Checkbox and Toggle for accessible form controls.
```

### Label Examples

#### Label Size

Use the `size` prop to change the label size. The default size is `medium`.

```jsx
<Label size="small" htmlFor="toggle">Enable 2FA security?</Label>
<Label size="medium" htmlFor="toggle">Enable 2FA security?</Label>
<Label size="large" htmlFor="toggle">Enable 2FA security?</Label>
```
<!--
Choose label size for different form layouts.
-->

#### Label Rendered as Slot

Use the `asChild` prop to render the label using the Slot component for custom rendering needs.

```jsx
<Label asChild>
  <span className="custom-label">Custom Rendered Label</span>
</Label>
```
<!--
Renders label as a different HTML element for advanced use cases.
-->

### Label Props

#### LabelProps Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| asChild   | boolean   | false   |
| size      | enum      | medium  |
| className | string    | -       |

---

## Link

The Link component extends the `<a>` HTML anchor element and is used to display hyperlinks with standardized styling for Malaysian government websites.

### Anatomy & Usage (Link)

**Imports:**

```javascript
import { Link } from "@govtechmy/myds-react/link";
```
<!--
Imports the Link component for creating styled hyperlinks.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Link href="/" primary>
    {/* The link's destination should be indicated here. */}
    Malaysia's Official Design System
  </Link>
);
// This example shows a primary link to the homepage.
```

### Link Examples

#### Link Using asChild for Custom Links

If you need to use the Link component provided by a different package (e.g., Next.js), use the `asChild` prop and wrap the component.

```jsx
import { Link as LinkPrimitive } from "@govtechmy/myds-react/link";
import NextLink from "next/link";

export const Link = (props) => (
  <LinkPrimitive asChild>
    <NextLink {...props} />
  </LinkPrimitive>
);
// This allows you to use Next.js's <Link> with MYDS styling.
```
<!--
Use asChild to wrap another link component and inherit MYDS styling.
-->

#### Link Open in a New Tab

Open the link in a new tab by setting `newTab` or `target="_blank"`.

```jsx
<Link href="https://digital.gov.my" newTab>
  Ministry of Digital
</Link>
```
<!--
Links with newTab open in a new browser tab or window.
-->
> **Note:**  
> All current versions of major browsers automatically use the behaviour of `rel="noopener"` for any links with `target="_blank"`.

#### Link Primary Style

Use the `primary` prop to toggle the link text colour.

```jsx
<Link href="/" primary>
  Primary
</Link>
<Link href="/">
  Inherit from parent (default)
</Link>
```
<!--
Primary links use a distinct color to highlight important navigation.
-->

#### Link Underline Styles

Control when the link should have an underline using the `underline` prop.

```jsx
<Link href="/" underline="always">
  Always
</Link>
<Link href="/" underline="hover">
  Hover
</Link>
<Link href="/" underline="none">
  None
</Link>
```
<!--
Underline options: always, hover (default), or none.
-->

### Link Props

#### Link Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| asChild   | boolean   | -       |
| href      | string    | -       |
| newTab    | boolean   | false   |
| primary   | boolean   | false   |
| underline | enum      | hover   |

---

## Masthead

The Masthead component is a standardized header for Malaysian government websites, providing consistent branding and navigation at the top of the page.

### Anatomy & Usage (Masthead)

**Imports:**

```javascript
import {
  Masthead,
  MastheadHeader,
  MastheadContent,
  MastheadTitle,
  MastheadTrigger,
  MastheadSection,
} from "@govtechmy/myds-react/masthead";
```
<!--
Imports all necessary subcomponents for building a government-standard masthead.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Masthead>
    <MastheadHeader>
      <MastheadTitle>Official Malaysian Government Website</MastheadTitle>
      <MastheadTrigger />
    </MastheadHeader>
    <MastheadContent>
      <MastheadSection title="Important Updates" icon={<Icon name="info" />}>
        {/* Content or navigation goes here */}
      </MastheadSection>
    </MastheadContent>
  </Masthead>
);
// This example shows a masthead with a title and trigger in the header, and a content section with an icon and title.
```

### Masthead Props

#### Masthead Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### MastheadHeader Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### MastheadTitle Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### MastheadTrigger Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### MastheadContent Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

#### MastheadSection Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| title     | ReactNode | -       |
| icon      | ReactNode | -       |
| children  | ReactNode | -       |

---

## Navbar

The Navbar component establishes a clear navigation menu for users to browse the site. It provides site branding, navigation links, dropdown menus, and action controls in a standardized header for Malaysian government websites.

### Anatomy & Usage (Navbar)

**Imports:**

```javascript
import {
  Navbar,
  NavbarLogo,
  NavbarMenu,
  NavbarMenuItem,
  NavbarMenuDropdown,
  NavbarAction,
} from "@govtechmy/myds-react/navbar";
```
<!--
Imports all necessary subcomponents for building a government-standard navigation bar.
-->

**Component Structure Example:**

```jsx
export default () => {
  return (
    <Navbar>
      <NavbarLogo src="/logo.png" alt="MYDS" href="/" />
      {/* Displays the site logo as a link to the homepage */}
      <NavbarMenu>
        <NavbarMenuItem href="/menu1">Menu 1</NavbarMenuItem>
        <NavbarMenuItem href="/menu2">Menu 2</NavbarMenuItem>
        {/* Menu dropdown for sub-navigation */}
        <NavbarMenuDropdown title="More">
          <NavbarMenuItem href="/menu3">Menu 3</NavbarMenuItem>
        </NavbarMenuDropdown>
      </NavbarMenu>
      <NavbarAction>
        {/* Place action buttons or links here, e.g., login/logout */}
      </NavbarAction>
    </Navbar>
  );
};
// This example provides branding, navigation, and actions, following MYDS standards.
```

### Navbar Examples

#### Navbar Logo Example

The logo section displays site branding, typically linking to the homepage.

```jsx
<NavbarLogo src="/logo.png" alt="MYDS" href="/" />
```
<!--
Use NavbarLogo for official site branding. The src and alt props define logo image and accessibility text.
-->

#### Navbar Menu Example

Menu items provide primary navigation links. Use `NavbarMenuItem` for standard links and `NavbarMenuDropdown` for grouped or sub-menu links.

```jsx
<NavbarMenu>
  <NavbarMenuItem href="/menu1">Menu 1</NavbarMenuItem>
  <NavbarMenuItem href="/menu2">Menu 2</NavbarMenuItem>
  <NavbarMenuDropdown title="More">
    <NavbarMenuItem href="/menu3">Menu 3</NavbarMenuItem>
  </NavbarMenuDropdown>
</NavbarMenu>
```
<!--
Menu items and dropdowns organize navigation in a clear, accessible manner.
-->

#### Navbar Action Example

Action controls (e.g., login, profile, language selector) can be placed in the action area.

```jsx
<NavbarAction>
  <Button>Login</Button>
</NavbarAction>
```
<!--
Use NavbarAction for user actions or controls at the end of the Navbar.
-->

### Navbar Props

#### Navbar Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| className | string    | -       |

#### NavbarLogo Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| href      | string    | -       |
| src       | string    | -       |
| alt       | string    | -       |

#### NavbarMenu Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | string    | -       |

#### NavbarMenuItem Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| href      | string    | -       |
| className | string    | -       |

#### NavbarMenuDropdown Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |
| title     | string    | -       |
| className | boolean   | -       |

#### NavbarAction Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| children  | ReactNode | -       |

---

## Pagination

The Pagination component allows users to navigate through a large set of content divided into discrete pages. It provides both simple and advanced configurations for flexible navigation experiences.

### Anatomy & Usage (Pagination)

#### AutoPagination (Simple Usage)

For most cases, `AutoPagination` can be used directly. It is a pre-assembled component that handles common pagination use cases such as page navigation, page count display, and different layout types.

**Imports:**

```javascript
import { AutoPagination } from "@govtechmy/myds-react/pagination";
```
<!--
AutoPagination is the easiest way to add page navigation. Just configure page, count, limit, and type.
-->

**Component Structure Example:**

```jsx
<AutoPagination count={20} limit={5} page={2} type="default" />
// Renders a paginated navigation bar with 20 pages, showing 5 per view, starting on page 2.
```

#### Manual Pagination (Advanced Usage)

For more advanced scenarios, you can assemble the pagination manually using subcomponents. This allows full customization of layout and navigation.

**Imports:**

```javascript
import {
  Pagination,
  PaginationContext,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationNumber,
  PaginationPrevious,
} from "@/components/myds";
```
<!--
Manual assembly provides low-level control for custom layouts, labels, and navigation handling.
-->

**Component Structure Example:**

```jsx
<Pagination count={20} limit={5} page={2} onPageChange={handlePageChange}>
  <PaginationContent>
    <PaginationItem>
      <PaginationPrevious />
    </PaginationItem>
    <PaginationItem>
      <PaginationNumber page={1} />
    </PaginationItem>
    <PaginationItem>
      <PaginationEllipsis />
    </PaginationItem>
    <PaginationItem>
      <PaginationNext />
    </PaginationItem>
  </PaginationContent>
</Pagination>
// This example shows custom navigation with previous/next, page numbers, and ellipsis for skipped pages.
```

### Pagination Examples

#### AutoPagination Types Example

Use the `type` prop to change the AutoPagination layout:

- **default:** Allows jumping to any page, ideal for large datasets.
- **simple:** For forward/back navigation only.
- **full:** Shows current page/total left, navigation buttons far right.

```jsx
<AutoPagination type="default" count={20} page={2} />
<AutoPagination type="simple" count={20} page={2} />
<AutoPagination type="full" count={20} page={2} fullText="Page 2 of 20" />
```
<!--
Choose the type that best fits your navigation needs and content size.
-->

#### Pagination Configuration Example

Configure page, limit, and count for both AutoPagination and manual assembly. These props control the visible page, number of items per page, and total item count.

```jsx
// AutoPagination example
<AutoPagination count={50} limit={10} page={3} />

// Manual pagination example
<Pagination count={50} limit={10} page={3}>
  <PaginationContent>
    {/* ...items... */}
  </PaginationContent>
</Pagination>
```
<!--
Configuring these props ensures the pagination controls match your data set.
-->

### Pagination Props

#### AutoPagination Component Props

| Prop        | Type       | Default |
|-------------|------------|---------|
| count       | number     | -       |
| limit       | number     | -       |
| page        | number     | -       |
| maxDisplay  | number     | 4       |
| type        | enum       | default |
| onPageChange| function   | -       |
| fullText    | string     | -       |
| next        | ReactNode  | -       |
| previous    | ReactNode  | -       |

#### Pagination Component Props

| Prop        | Type       | Default |
|-------------|------------|---------|
| count       | number     | -       |
| limit       | number     | -       |
| page        | number     | -       |
| type        | enum       | default |
| onPageChange| function   | -       |

#### PaginationContent Component

- Arranges `PaginationItem` components in a horizontal row as an unordered list (`ul`).

#### PaginationItem Component

- Wraps individual pagination controls (`li`), such as numbers, next, previous, or ellipsis.

#### PaginationNext Component

- Button for navigating to the next page.
- Can use `asChild` to substitute the underlying component.

#### PaginationPrevious Component

- Button for navigating to the previous page.
- Can use `asChild` to substitute the underlying component.

#### PaginationLabel Component Props

| Prop    | Type     | Default |
|---------|----------|---------|
| content | string   | -       |

#### PaginationEllipsis Component

- Visual indicator for skipped page numbers, rendered as three dots.

---

## Pill

The Pill component represents tags or categories in a textfield, UI, or form. Pills can contain text and may include a trailing "x" button for easy removal.

### Anatomy & Usage (Pill)

**Imports:**

```javascript
import { Pill } from "@govtechmy/myds-react/pill";
```
<!--
Import the Pill component to display tags or categories with optional remove functionality.
-->

**Component Structure Example:**

```jsx
export default () => <Pill>Trending</Pill>;
// Renders a pill with the text "Trending".
```

### Pill Examples

#### Pill Size Example

Use the `size` prop to change the pill size. The default size is `small`.

```jsx
<Pill size="small">New</Pill>
<Pill size="medium">Trending</Pill>
<Pill size="large">Popular</Pill>
```
<!--
Choose pill size for visual hierarchy or layout needs.
-->

#### Pill with Trailing "x" Button Example

Pass a callback function to the `onDismiss` prop to show a trailing "x" button. Clicking the button removes the pill.

```jsx
<Pill onDismiss={() => alert('Removed!')}>With trailing "x" button</Pill>
```
<!--
The "x" button appears to allow users to remove the pill. The callback handles the remove action.
-->

#### Pill Disabled State Example

Set the `disabled` prop to `true` to make the pill non-interactive.  
*Note: If `onDismiss` is set, the "x" button will be hidden when disabled.*

```jsx
<Pill>Enabled</Pill>
<Pill disabled>Disabled</Pill>
```
<!--
Disabled pills do not respond to user actions and cannot be dismissed.
-->

### Pill Props

#### Pill Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| size      | enum      | small   |
| disabled  | boolean   | false   |
| onDismiss | function  | -       |

---

## Radio

Radio buttons allow users to select exactly one choice from a group. They are commonly used in forms and settings where a single option must be chosen.

### Anatomy & Usage (Radio)

**Imports:**

```javascript
import {
  Radio,
  RadioButton,
  RadioHintText,
  RadioItem,
  RadioLabel,
} from "@govtechmy/myds-react/radio";
```
<!--
Import all radio subcomponents for building radio groups with hints and labels.
-->

**Component Structure Example:**

```jsx
<Radio>
  <RadioItem>
    <RadioButton value="apple" id="apple" />
    <RadioLabel htmlFor="apple">Apple</RadioLabel>
  </RadioItem>
  <RadioItem>
    <RadioButton value="banana" id="banana" />
    <div>
      <RadioLabel htmlFor="banana">Banana</RadioLabel>
      <RadioHintText htmlFor="banana">Comes pre-packaged.</RadioHintText>
    </div>
  </RadioItem>
</Radio>
// This example shows a radio group with labeled choices, and hint text for additional info.
```

### Radio Examples

#### Radio Size Example

Change the size of radio buttons using the `size` prop on the Radio component.

```jsx
<Radio size="small">
  <RadioItem>
    <RadioButton value="email" id="email" />
    <RadioLabel htmlFor="email">Email</RadioLabel>
    <RadioHintText htmlFor="email">
      We will send notifications to your registered email address.
    </RadioHintText>
  </RadioItem>
  <RadioItem>
    <RadioButton value="phone" id="phone" />
    <RadioLabel htmlFor="phone">Phone Call</RadioLabel>
    <RadioHintText htmlFor="phone">
      Our representative will call you on your provided phone number.
    </RadioHintText>
  </RadioItem>
</Radio>
```
<!--
Use size="small", "medium", or "large" to adjust radio visuals.
-->

#### Radio Disabled State Examples

Disable all radio items by setting the `disabled` prop to `true` on the Radio component:

```jsx
<Radio disabled>
  <RadioItem>
    <RadioButton value="email" id="email" />
    <RadioLabel htmlFor="email">Email</RadioLabel>
    <RadioHintText htmlFor="email">
      We will send notifications to your registered email address.
    </RadioHintText>
  </RadioItem>
  <RadioItem>
    <RadioButton value="phone" id="phone" />
    <RadioLabel htmlFor="phone">Phone Call</RadioLabel>
    <RadioHintText htmlFor="phone">
      Our representative will call you on your provided phone number.
    </RadioHintText>
  </RadioItem>
</Radio>
```
<!--
Disables all items in the radio group.
-->

Disable individual radio items by setting the `disabled` prop to `true` on the RadioItem component:

```jsx
<Radio>
  <RadioItem>
    <RadioButton value="email" id="email" />
    <RadioLabel htmlFor="email">Email</RadioLabel>
    <RadioHintText htmlFor="email">
      We will send notifications to your registered email address.
    </RadioHintText>
  </RadioItem>
  <RadioItem disabled>
    <RadioButton value="postal" id="postal" />
    <RadioLabel htmlFor="postal">Postal Mail</RadioLabel>
    <RadioHintText htmlFor="postal">
      Not available at the moment.
    </RadioHintText>
  </RadioItem>
</Radio>
```
<!--
Only disables specified item, others remain enabled.
-->

#### Radio Controlled Component Example

To use Radio as a controlled component, set the `value` and `onValueChange` props.

```jsx
const [value, setValue] = useState("");

<Radio value={value} onValueChange={setValue}>
  <RadioItem>
    <RadioButton value="email" id="email" />
    <RadioLabel htmlFor="email">Email</RadioLabel>
    <RadioHintText htmlFor="email">
      We will send notifications to your registered email address.
    </RadioHintText>
  </RadioItem>
  <RadioItem>
    <RadioButton value="phone" id="phone" />
    <RadioLabel htmlFor="phone">Phone Call</RadioLabel>
    <RadioHintText htmlFor="phone">
      Our representative will call you on your provided phone number.
    </RadioHintText>
  </RadioItem>
</Radio>
```
<!--
Controlled radio group lets you manage selected value in your app state.
-->

### Radio Props

#### Radio Component Props

| Prop         | Type      | Default |
|--------------|-----------|---------|
| value        | string    | -       |
| onValueChange| function  | -       |
| size         | enum      | medium  |
| disabled     | boolean   | false   |

#### RadioButton Component Props

- Abstracted from Radix UI's RadioGroup.Item. Refer to API for full details.

#### RadioItem Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| disabled  | boolean   | false   |

#### RadioHintText Component Props

- Uses same props as a regular `div` element.

#### RadioLabel Component Props

- Uses same props as a regular `label` element.

---

## Search Bar

The Search Bar component allows users to enter a query or keyword to search through content within a website. It supports search suggestions, result grouping, hints, and a clear button for improved UX.

### Anatomy & Usage (Search Bar)

**Imports:**

```javascript
import {
  SearchBar,
  SearchBarInput,
  SearchBarInputContainer,
  SearchBarSearchButton,
  SearchBarResults,
  SearchBarResultsList,
  SearchBarResultsItem,
  SearchBarClearButton,
  SearchBarHint,
} from "@govtechmy/myds-react/search-bar";
```
<!--
Imports all subcomponents required for building a feature-rich search bar.
-->

**Component Structure Example:**

```jsx
<SearchBar size="large">
  <SearchBarInputContainer>
    <SearchBarInput value={query} onValueChange={setQuery} />
    <SearchBarHint>
      Press <Pill size="small">/</Pill> to search
    </SearchBarHint>
    <SearchBarClearButton />
    <SearchBarSearchButton />
  </SearchBarInputContainer>
  <SearchBarResults open={true}>
    <SearchBarResultsList>
      <SearchBarResultsItem value="foo" onSelect={() => {}}>
        Foo
      </SearchBarResultsItem>
      <SearchBarResultsItem value="bar" onSelect={() => {}}>
        Bar
      </SearchBarResultsItem>
    </SearchBarResultsList>
  </SearchBarResults>
</SearchBar>
// Example: Large search bar with input, hint, clear, search button, and results list.
```

### Search Bar Examples

#### Search Bar Size Example

Use the `size` prop to change the search bar's size. The default size is `medium`.

```jsx
<SearchBar size="small">
  <SearchBarInput placeholder="Search by name" />
</SearchBar>
<SearchBar size="medium">
  <SearchBarInput placeholder="Search by name" />
</SearchBar>
<SearchBar size="large">
  <SearchBarInput placeholder="Search by name" />
</SearchBar>
```
<!--
Select small, medium, or large for different layouts.
-->

#### Search Bar Clear Button Example

The `SearchBarClearButton` shows a button to clear the search input.

```jsx
<SearchBar>
  <SearchBarInputContainer>
    <SearchBarInput value={query} onValueChange={setQuery} />
    <SearchBarClearButton />
  </SearchBarInputContainer>
</SearchBar>
```
<!--
Clear button lets users quickly reset their search query.
-->

#### Search Bar Hint Example

Show a hint with `SearchBarHint` to guide users on using the search bar.

```jsx
<SearchBar>
  <SearchBarInputContainer>
    <SearchBarInput placeholder="Search by name" />
    <SearchBarHint>
      Press <Pill size="small">/</Pill> to search
    </SearchBarHint>
  </SearchBarInputContainer>
</SearchBar>
```
<!--
Hints improve discoverability of keyboard shortcuts and search functionality.
-->

#### Search Bar Results and Suggestions Example

Display search results or suggestions with `SearchBarResults`, `SearchBarResultsList` and `SearchBarResultsItem`.

```jsx
<SearchBar>
  <SearchBarInputContainer>
    <SearchBarInput value={query} onValueChange={setQuery} />
  </SearchBarInputContainer>
  <SearchBarResults open={true}>
    <SearchBarResultsList>
      <SearchBarResultsItem value="michelle-yeoh" onSelect={() => {}}>
        Michelle Yeoh<br />Internationally acclaimed actress
      </SearchBarResultsItem>
      <SearchBarResultsItem value="p-ramlee" onSelect={() => {}}>
        P. Ramlee<br />Iconic actor, director, and musician
      </SearchBarResultsItem>
    </SearchBarResultsList>
  </SearchBarResults>
</SearchBar>
```
<!--
Results list provides instant feedback or suggestions as users type.
-->

#### Grouped Search Results Example

Group search results with `SearchBarResultsGroup` for better organization.

```jsx
<SearchBar>
  <SearchBarInputContainer>
    <SearchBarInput value={query} onValueChange={setQuery} />
  </SearchBarInputContainer>
  <SearchBarResults open={true}>
    <SearchBarResultsGroup heading="Arts">
      <SearchBarResultsList>
        <SearchBarResultsItem value="michelle-yeoh" onSelect={() => {}}>
          Michelle Yeoh<br />Internationally acclaimed actress
        </SearchBarResultsItem>
        <SearchBarResultsItem value="harith-iskander" onSelect={() => {}}>
          Harith Iskander<br />Comedian and actor
        </SearchBarResultsItem>
      </SearchBarResultsList>
    </SearchBarResultsGroup>
    <SearchBarResultsGroup heading="Business">
      <SearchBarResultsList>
        <SearchBarResultsItem value="tony-fernandes" onSelect={() => {}}>
          Tony Fernandes<br />Founder of AirAsia
        </SearchBarResultsItem>
      </SearchBarResultsList>
    </SearchBarResultsGroup>
  </SearchBarResults>
</SearchBar>
```
<!--
Grouping makes it easier to browse results by category.
-->

#### Controlled Search Bar Example

Control the search bar using props:

- Set `value` and `onValueChange` for `SearchBarInput`
- Set `open` for `SearchBarResults`
- Track focus state for more refined behavior

```jsx
const [query, setQuery] = useState("");
const [hasFocus, setHasFocus] = useState(false);

<SearchBar>
  <SearchBarInputContainer>
    <SearchBarInput
      value={query}
      onValueChange={setQuery}
      onFocus={() => setHasFocus(true)}
      onBlur={() => setHasFocus(false)}
    />
    <SearchBarHint>
      Press <Pill size="small">/</Pill> to search
    </SearchBarHint>
    <SearchBarClearButton />
    <SearchBarSearchButton />
  </SearchBarInputContainer>
  <SearchBarResults open={query.length > 0 && hasFocus}>
    {/* ...results here... */}
  </SearchBarResults>
</SearchBar>
```
<!--
Controlled usage enables advanced features like dynamic opening/closing of results.
-->

### Search Bar Props

#### SearchBar Component Props

| Prop | Type | Default |
|------|------|---------|
| size | enum | medium  |

#### SearchBarInput Component Props

| Prop          | Type     | Default |
|---------------|----------|---------|
| value         | string   | -       |
| onValueChange | function | -       |

#### SearchBarInputContainer Component

- Uses same props as a regular `div` element.

#### SearchBarSearchButton Component

- Uses same props as MYDS' Button.

#### SearchBarClearButton Component

- Uses same props as MYDS' Button.

#### SearchBarHint Component

- Uses same props as a regular `div` element.

#### SearchBarResults Component Props

| Prop | Type    | Default |
|------|---------|---------|
| open | boolean | -       |

#### SearchBarResultsList Component

- Abstracted from cmdk's Command.List.

#### SearchBarResultsItem Component

- Abstracted from cmdk's Command.Item.

#### SearchBarResultsGroup Component

- Abstracted from cmdk's Command.Group.

---

## Select

The Select component allows users to choose from a list of options. It typically appears as a popup menu that expands when the user clicks the select button, showing a list of options for selection.

### Anatomy & Usage (Select)

**Imports:**

```javascript
import {
  Select,
  SelectGroup,
  SelectGroupTitle,
  SelectValue,
  SelectTrigger,
  SelectLabel,
  SelectContent,
  SelectHeader,
  SelectFooter,
  SelectItem,
  SelectSeparator,
} from "@govtechmy/myds-react/select";
```
<!--
Imports all subcomponents for building flexible and accessible select menus.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Select>
    <SelectTrigger>
      <SelectValue />
    </SelectTrigger>
    <SelectContent>
      <SelectHeader />
      <SelectGroup>
        <SelectGroupTitle />
        <SelectItem />
      </SelectGroup>
      <SelectSeparator />
      <SelectFooter />
    </SelectContent>
  </Select>
);
// This example shows a select dropdown with a trigger, value display, header, grouped items, separator, and footer.
```

### Select Examples

#### Select Size Example

Use the `size` prop to change the select size. Available sizes are `small`, `medium`, and `large`. The default size is `small`.

```jsx
<Select size="small" />
<Select size="medium" />
<Select size="large" />
```
<!--
Choose select size for different interface densities or layouts.
-->

#### Select Variant Example

Use the `variant` prop to change the select style. Available variants are `outline` and `ghost`. The default variant is `outline`.

```jsx
<Select variant="outline" />
<Select variant="ghost" />
```
<!--
Variants control border and background styling of the select menu.
-->

#### Select Disabled State Example

Disable the entire select by setting the `disabled` prop to `true`:

```jsx
<Select disabled />
```

Disable individual options by setting the `disabled` prop to `true` on `SelectItem`:

```jsx
<Select>
  <SelectContent>
    <SelectItem value="option1">Option 1</SelectItem>
    <SelectItem value="option2" disabled>Option 2 (disabled)</SelectItem>
  </SelectContent>
</Select>
```
<!--
Disabling prevents selection or interaction as needed.
-->

#### Grouped Options Example

Group related options for better structure using `SelectGroup` and `SelectGroupTitle`:

```jsx
<Select>
  <SelectContent>
    <SelectGroup>
      <SelectGroupTitle>Fruits</SelectGroupTitle>
      <SelectItem value="apple">Apple</SelectItem>
      <SelectItem value="banana">Banana</SelectItem>
    </SelectGroup>
    <SelectSeparator />
    <SelectGroup>
      <SelectGroupTitle>Vegetables</SelectGroupTitle>
      <SelectItem value="carrot">Carrot</SelectItem>
      <SelectItem value="spinach">Spinach</SelectItem>
    </SelectGroup>
  </SelectContent>
</Select>
```
<!--
Grouping improves navigation and organization for long lists.
-->

#### Select With Header Example

Add a header to provide extra context or instructions:

```jsx
<Select>
  <SelectContent>
    <SelectHeader>Choose your favorite</SelectHeader>
    <SelectItem value="apple">Apple</SelectItem>
    <SelectItem value="banana">Banana</SelectItem>
  </SelectContent>
</Select>
```
<!--
Headers can display instructions or information above the option list.
-->

#### Select With Footer Example

Add a footer to show additional info or actions:

```jsx
<Select>
  <SelectContent>
    <SelectItem value="apple">Apple</SelectItem>
    <SelectFooter>More options coming soon!</SelectFooter>
  </SelectContent>
</Select>
```
<!--
Footers are useful for extra details or links at the bottom of the select menu.
-->

#### Multiple Selection Example

Enable multiple selection by setting the `multiple` prop to `true`:

```jsx
<Select multiple>
  <SelectContent>
    <SelectItem value="apple">Apple</SelectItem>
    <SelectItem value="banana">Banana</SelectItem>
    <SelectItem value="carrot">Carrot</SelectItem>
  </SelectContent>
</Select>
```
<!--
Multiple select allows users to choose more than one option.
-->

#### Custom Value Display Example

Customize how selected values are displayed using the `SelectValue` component:

```jsx
<Select>
  <SelectTrigger>
    <SelectValue>
      {value => <span>Selected: {Array.isArray(value) ? value.join(", ") : value}</span>}
    </SelectValue>
  </SelectTrigger>
  <SelectContent>
    <SelectItem value="apple">Apple</SelectItem>
    <SelectItem value="banana">Banana</SelectItem>
  </SelectContent>
</Select>
```
<!--
Custom value display can show selected items in any format you want.
-->

### Select Props

#### Select Component Props

| Prop         | Type                    | Default |
|--------------|-------------------------|---------|
| multiple     | boolean                 | false   |
| size         | enum                    | small   |
| variant      | enum                    | outline |
| defaultValue | string \| string[]      | false   |
| value        | string \| string[]      | false   |
| onValueChange| (value: string \| string[]) => void | -       |
| disabled     | boolean                 | false   |

#### SelectValue Component Props

| Prop        | Type                                   | Default |
|-------------|----------------------------------------|---------|
| label       | string                                 | -       |
| children    | (value: string \| string[]) => ReactNode | -     |
| placeholder | ReactNode                              | -       |

#### SelectItem Component Props

| Prop     | Type    | Default |
|----------|---------|---------|
| value    | string  | string  |
| disabled | boolean | false   |

---

## Skiplink Example

The Skiplink component enables users to bypass repetitive navigation links and jump directly to the main content. It improves accessibility for keyboard and screen reader users by allowing efficient navigation—especially on sites with large menus or complex layouts.

### Anatomy & Usage (Skiplink) – Example

**Imports:**

```javascript
import { Skiplink } from "@govtechmy/myds-react/skiplink";
```
<!--
Import the Skiplink component to provide an accessible way for users to skip navigation and reach the main content quickly.
-->

**Component Structure Example:**

```jsx
export default () => (
  <>
    {/* Skiplink is typically hidden until focused via Tab key */}
    <Skiplink href="#main-content">
      <span>Skip to main content</span>
    </Skiplink>
    {/* Main content should have a matching id for skiplink to jump to */}
    <main id="main-content">
      <span className="text-slate-600">
        Main content here
      </span>
    </main>
  </>
);
// This setup allows keyboard users to skip navigation and reach #main-content directly for improved accessibility.
```

### How Skiplink Works (Example)

- After clicking on the page, press the **Tab** key.
- The skip link will appear at the top of the viewport, overlapping the navigation/menu bar.
- Press **Enter** while the skip link is focused to jump directly to the main content area.
- Especially useful for keyboard navigation and screen readers to avoid repeatedly tabbing through navigation menus.

### Skiplink Example Props

#### Skiplink Example Component Props

| Prop | Type   | Default |
|------|--------|---------|
| href | string | -       |

---

## Skiplink

The Skiplink component enables users to bypass repetitive navigation links and jump directly to the main content. It improves accessibility for keyboard and screen reader users by allowing efficient navigation—especially on sites with large menus or complex layouts.

### Anatomy & Usage (Skiplink)

**Imports:**

```javascript
import { Skiplink } from "@govtechmy/myds-react/skiplink";
```
<!--
Import the Skiplink component to provide an accessible way for users to skip navigation and reach the main content quickly.
-->

**Component Structure Example:**

```jsx
export default () => (
  <>
    {/* Skiplink is typically hidden until focused via Tab key */}
    <Skiplink href="#main-content">
      <span>Skip to main content</span>
    </Skiplink>
    {/* Main content should have a matching id for skiplink to jump to */}
    <main id="main-content">
      <span className="text-slate-600">
        Main content here
      </span>
    </main>
  </>
);
// This setup allows keyboard users to skip navigation and reach #main-content directly for improved accessibility.
```

### How Skiplink Works

- After clicking on the page, press the **Tab** key.
- The skip link will appear at the top of the viewport, overlapping the navigation/menu bar.
- Press **Enter** while the skip link is focused to jump directly to the main content area.
- Especially useful for keyboard navigation and screen readers to avoid repeatedly tabbing through navigation menus.

### Skiplink Props

#### Skiplink Component Props

| Prop | Type   | Default |
|------|--------|---------|
| href | string | -       |

---

## Spinner

The Spinner component is a customizable loading indicator used to provide visual feedback for loading states in your application.

### Anatomy & Usage (Spinner)

**Imports:**

```javascript
import { Spinner } from "@govtechmy/myds-react/spinner";
```
<!--
Import the Spinner component to display a loading indicator anywhere in your app.
-->

**Component Structure Example:**

```jsx
export default () => <Spinner />;
// Renders a default spinner, useful for showing loading states.
```

### Spinner Examples

#### Spinner Variant Example

Use the `color` prop to change the spinner style.  
Available colors: e.g., `gray` (default), `primary`, `success`, `warning`, `danger` (check your library for supported colors).

```jsx
<Spinner color="gray" />
<Spinner color="primary" />
<Spinner color="success" />
<Spinner color="warning" />
<Spinner color="danger" />
```
<!--
Change color to reflect different loading contexts or states.
-->

#### Spinner Size Example

Use the `size` prop to adjust the spinner's size.  
Available sizes: `small` (default), `medium`, `large`.

```jsx
<Spinner size="small" />
<Spinner size="medium" />
<Spinner size="large" />
```
<!--
Choose the spinner size that best fits your UI layout.
-->

#### Spinner Show/Hide Example

Use the `show` prop to control spinner visibility.

```jsx
<Spinner show={true} />  {/* Spinner is visible */}
<Spinner show={false} /> {/* Spinner is hidden */}
```
<!--
Control visibility for conditional loading states.
-->

### Spinner Props

#### Spinner Component Props

| Prop | Type   | Default |
|------|--------|---------|
| color| enum   | gray    |
| size | enum   | small   |
| show | boolean| true    |

---

## Summary List

The Summary List component displays information in a structured key-value format, making it ideal for presenting form summaries, application details, or any data that needs to be reviewed. It supports headers, action buttons, and custom styling for enhanced usability and accessibility.

### Anatomy & Usage (Summary List)

**Imports:**

```javascript
import {
  SummaryList,
  SummaryListAction,
  SummaryListBody,
  SummaryListHeader,
  SummaryListTerm,
  SummaryListDetail,
  SummaryListRow,
  SummaryListAddition,
} from "@govtechmy/myds-react/summary-list";
```
<!--
Import all subcomponents to build structured summary tables and rows.
-->

**Component Structure Example:**

```jsx
export default () => (
  <SummaryList>
    <SummaryListHeader>Government Subsidy Application</SummaryListHeader>
    <SummaryListBody>
      <SummaryListRow>
        <SummaryListTerm>Application ID</SummaryListTerm>
        <SummaryListDetail>SUB12345</SummaryListDetail>
        <SummaryListAction>
          <button>View</button>
        </SummaryListAction>
        <SummaryListAddition>Additional Info</SummaryListAddition>
      </SummaryListRow>
      <SummaryListRow>
        <SummaryListTerm>Applicant Name</SummaryListTerm>
        <SummaryListDetail>Lee Ming Wei</SummaryListDetail>
      </SummaryListRow>
    </SummaryListBody>
  </SummaryList>
);
// This example displays a summary table for an application, including rows, actions, and additions.
```

### Summary List Components and Structure

#### Main Wrapper: SummaryList

The `SummaryList` is the primary container for presenting a summary of items in a structured and visually consistent format.  
It serves as the parent component, wrapping all subcomponents.

```jsx
<SummaryList>
  {/* Subcomponents go here */}
</SummaryList>
```
<!--
Use SummaryList to wrap all summary rows and headers.
-->

#### SummaryListHeader

Displays the title of the summary list.

```jsx
<SummaryListHeader>Government Subsidy Application</SummaryListHeader>
```
<!--
Header provides context or a label for the summary table.
-->

#### SummaryListBody

Wrapper for the tabular data within the SummaryList. It contains multiple or single `SummaryListRow` components.

```jsx
<SummaryListBody>
  {/* SummaryListRow components */}
</SummaryListBody>
```
<!--
Body organizes all rows in the summary list.
-->

#### SummaryListRow

Defines individual rows within the table, grouping terms, details, actions, and additions.

```jsx
<SummaryListRow>
  {/* Term, Detail, Action, Addition */}
</SummaryListRow>
```
<!--
Each row represents a key-value pair and optional action/addition.
-->

#### SummaryListTerm

Represents the term or label in the row.

```jsx
<SummaryListTerm>Application ID</SummaryListTerm>
```
<!--
Label for the row, e.g., field name.
-->

#### SummaryListDetail

Displays the detailed information corresponding to the term.

```jsx
<SummaryListDetail>SUB12345</SummaryListDetail>
```
<!--
Value for the row, e.g., field data.
-->

#### SummaryListAction

For adding actions (e.g., buttons or links) associated with a row.

```jsx
<SummaryListAction>
  <button>View</button>
</SummaryListAction>
```
<!--
Add actions such as edit, view, or delete per row.
-->

#### SummaryListAddition

Provides extra information or actions in the row.

```jsx
<SummaryListAddition>Additional Info</SummaryListAddition>
```
<!--
Use for supplementary details or controls in a row.
-->

### Summary List Props

#### SummaryList Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListHeader Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListBody Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListRow Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListTerm Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListDetail Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListAction Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

#### SummaryListAddition Component Props

| Prop      | Type      | Default |
|-----------|-----------|---------|
| className | string    | -       |
| children  | ReactNode | -       |

---

## Table

The Table component organizes information into rows and columns for easy readability. It accommodates various data types, including text, numbers, code, call-to-action, and links, enabling efficient presentation and comparison.

### Anatomy & Usage (Table)

**Imports:**

```javascript
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableFooter,
  TableHead,
  TableHeader,
  TableRow,
  TableSkeleton,
  TableEmpty,
} from "@govtechmy/myds-react/table";
```
<!--
Imports all necessary subcomponents for building structured tables with headers, rows, cells, captions, footers, skeleton loading, and empty states.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>Timestamp</TableHead>
        <TableHead>Activity</TableHead>
        <TableHead>Action Taken</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow>
        <TableCell>2021-09-01 12:00:00</TableCell>
        <TableCell>Login attempt successful</TableCell>
        <TableCell>Ali</TableCell>
      </TableRow>
      {/* Add more TableRow/TableCell as needed */}
    </TableBody>
    <TableCaption>Audit log</TableCaption>
  </Table>
);
// Example: Simple audit log table with header, body, and caption.
```

### Table Examples

#### Basic Table Example

Create a basic table with headers, rows, and cells using Table, TableHeader, TableRow, TableHead, TableBody, and TableCell.

```jsx
<Table>
  <TableHeader>
    <TableRow>
      <TableHead>Timestamp</TableHead>
      <TableHead>Activity</TableHead>
      <TableHead>Action Taken</TableHead>
    </TableRow>
  </TableHeader>
  <TableBody>
    <TableRow>
      <TableCell>2021-09-01 12:00:00</TableCell>
      <TableCell>Login attempt successful</TableCell>
      <TableCell>Ali</TableCell>
    </TableRow>
    {/* Additional rows */}
  </TableBody>
</Table>
```
<!--
Use TableHead for column headers and TableCell for data cells.
-->

#### Row Span and Col Span Example

Use `rowSpan` and `colSpan` attributes to merge cells across rows or columns.

```jsx
<Table>
  <TableHeader>
    <TableRow>
      <TableHead>Category</TableHead>
      <TableHead>Chargeable Income</TableHead>
      <TableHead>Calculation (RM)</TableHead>
      <TableHead>Rate</TableHead>
      <TableHead>Tax</TableHead>
    </TableRow>
  </TableHeader>
  <TableBody>
    <TableRow>
      <TableCell rowSpan={2}>A</TableCell>
      <TableCell colSpan={2}>0 - 5,000</TableCell>
      <TableCell>0</TableCell>
      <TableCell>RM 0</TableCell>
    </TableRow>
    <TableRow>
      <TableCell>B</TableCell>
      <TableCell>5,000 - 20,000</TableCell>
      <TableCell>On the First 5,000</TableCell>
      <TableCell>1%</TableCell>
      <TableCell>RM 0</TableCell>
    </TableRow>
  </TableBody>
</Table>
```
<!--
Merge cells for clearer data grouping and summaries.
-->

#### Table with Footer Example

Add a footer to the table using the TableFooter component for summary or totals.

```jsx
<Table>
  {/* ...header and body... */}
  <TableFooter>
    <TableRow>
      <TableCell colSpan={4}>Total Tax</TableCell>
      <TableCell>RM 600</TableCell>
    </TableRow>
  </TableFooter>
</Table>
```
<!--
Footer is useful for displaying totals or aggregate info.
-->

#### Table Skeleton Loading Example

Show a skeleton loading state while data is being fetched with TableSkeleton.

```jsx
<TableSkeleton />
// Renders a placeholder skeleton for loading state.
```
<!--
Use TableSkeleton to indicate that data is loading.
-->

#### Empty Table Example

Show an empty state when there is no data using TableEmpty.

```jsx
<Table>
  <TableHeader>
    <TableRow>
      <TableHead>Timestamp</TableHead>
      <TableHead>Activity</TableHead>
      <TableHead>Action Taken</TableHead>
    </TableRow>
  </TableHeader>
  <TableEmpty>No data available</TableEmpty>
</Table>
```
<!--
Use TableEmpty to inform users when there's no content to display.
-->

### Table Props

#### TableHead Component Props

| Prop    | Type                                      | Default |
|---------|-------------------------------------------|---------|
| rowSpan | boolean                                   | false   |
| colSpan | boolean                                   | false   |
| scope   | "col" \| "row" \| "colgroup" \| "rowgroup"| -       |

#### TableCell Component Props

| Prop    | Type                                      | Default |
|---------|-------------------------------------------|---------|
| rowSpan | boolean                                   | false   |
| colSpan | boolean                                   | false   |
| scope   | "col" \| "row" \| "colgroup" \| "rowgroup"| -       |

---

## Tabs

The Tabs component allows users to navigate between different views or content sections within the same context by clicking on a tab. It supports multiple visual styles, sizes, icons, and counters for enhanced organization and usability.

### Anatomy & Usage (Tabs)

**Imports:**

```javascript
import {
  Tabs,
  TabsList,
  TabsTrigger,
  TabsContent,
  TabsIcon,
  TabsCounter,
} from "@govtechmy/myds-react/tabs";
```
<!--
Import all subcomponents to build interactive and accessible tab navigation.
-->

**Component Structure Example:**

```jsx
export default () => (
  <Tabs variant="line" size="small">
    <TabsList>
      <TabsTrigger value="novel">Novel</TabsTrigger>
      <TabsTrigger value="short-story">Short Story</TabsTrigger>
      <TabsTrigger value="poetry">Poetry</TabsTrigger>
      <TabsTrigger value="drama">Drama</TabsTrigger>
    </TabsList>
    <TabsContent value="novel">
      A novel is a long work of fiction that presents a complete story with characters, plot, and setting. It typically encompasses various themes such as love, family, life struggles, and societal challenges. Novels allow readers to immerse themselves in new worlds through the author's imagination.
    </TabsContent>
    {/* Add more TabsContent for each trigger */}
  </Tabs>
);
// This example shows a basic tabs setup with four categories and content panels.
```

### Tabs Examples

#### Tabs Variant Example

Use the `variant` prop to change the tabs' style. There are three visual variants:

- **Pill:** Pill-shaped tabs with active tab highlighted.
- **Enclosed:** All tabs in a container with a distinct active pill style.
- **Line:** Active tab indicated by a line beneath it (default).

```jsx
<Tabs variant="pill">...</Tabs>
<Tabs variant="enclosed">...</Tabs>
<Tabs variant="line">...</Tabs>
```
<!--
Choose a variant that matches your design and user experience needs.
-->

#### Tabs Size Example

Use the `size` prop to change the size of the tabs for different contexts:

- **small:** Good for mobile or compact layouts (default).
- **medium:** Suitable for tablets, desktops, or larger components.

```jsx
<Tabs size="small">...</Tabs>
<Tabs size="medium">...</Tabs>
```
<!--
Size variants change tab and icon dimensions for different device sizes.
-->

#### Tabs with Leading Icons Example

Use `TabsIcon` inside `TabsTrigger` to add visual indicators at the start (or end) of tab labels.

```jsx
<Tabs>
  <TabsList>
    <TabsTrigger value="novel">
      <TabsIcon>{/* <BookIcon /> */}</TabsIcon>
      Novel
    </TabsTrigger>
    <TabsTrigger value="short-story">
      <TabsIcon>{/* <StoryIcon /> */}</TabsIcon>
      Short Story
    </TabsTrigger>
    {/* ... */}
  </TabsList>
  {/* ... */}
</Tabs>
```
<!--
Icons help users quickly recognize tab categories.
-->

#### Tabs with Counter Example

Add `TabsCounter` inside `TabsTrigger` to display the number of items in each category.

```jsx
<Tabs>
  <TabsList>
    <TabsTrigger value="novel">
      Novel <TabsCounter>1</TabsCounter>
    </TabsTrigger>
    <TabsTrigger value="short-story">
      Short Story <TabsCounter>2</TabsCounter>
    </TabsTrigger>
  </TabsList>
  {/* ... */}
</Tabs>
```
<!--
Counters provide at-a-glance information about item counts per tab.
-->

### Tabs Props

#### Tabs Component Props

| Prop    | Type  | Default |
|---------|-------|---------|
| variant | enum  | line    |
| size    | enum  | small   |

#### TabsList Component Props

| Prop  | Type | Default |
|-------|------|---------|
| width | enum | fit     |

#### TabsTrigger Component

- Interactive element that toggles the visibility of its corresponding tab panel.

#### TabsContent Component

- Panel displaying content when its corresponding trigger is selected.

#### TabsIcon Component

- Wrapper for icons within `TabsTrigger`, applies consistent styling and sizing based on context (`small` or `medium` variants).

#### TabsCounter Component

- Displays numerical indicators or badges within `TabsTrigger`, adapts text size based on parent tabs' context.

---

> For more details:
>
> - [MYDS Design Guidelines](https://design.digital.gov.my/en/docs/design)
> - [MYDS Development Docs](https://design.digital.gov.my/en/docs/develop)
> - [MYDS Figma Kit](https://www.figma.com/design/svmWSPZarzWrJ116CQ8zpV/MYDS--Beta-)

---

<!--
## Next Section: Tag
-->
