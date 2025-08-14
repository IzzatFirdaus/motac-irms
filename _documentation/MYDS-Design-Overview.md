# Malaysia Government Design System (MYDS) Overview

## Introduction to MYDS

As digital government services become more widely used, maintaining high-quality, accessible platforms is essential. The **Malaysia Government Design System (MYDS)** supports this goal by providing standard tools, templates, and guidelines that enable agencies to build fast, user-friendly, and consistent digital services.

---

## What is MYDS?

MYDS is a comprehensive design system for Malaysian government websites and digital platforms. It includes:

- Components: Pre-built UI elements (such as buttons, forms, navigation bars) that promote consistency and accelerate interface development.
- Theme Customizer: A tool for tailoring colours and styles to match agency branding while preserving a unified visual identity.
- Patterns: Ready-made layouts and design patterns for common scenarios (e.g., login screens, data forms) that ensure usability and best practices.
- Design File: An all-in-one design asset library for prototyping and building according to MYDS standards.

---

## Why Use MYDS?

Adopting MYDS offers several advantages:

- Consistency: Ensures all government digital platforms share a cohesive look and feel, promoting trust and recognition.
- Rapid Development: Facilitates efficient design and development using reusable components and clear guidelines.
- User Experience: Allows designers and developers to focus more on improving user experience rather than manual styling.
- Scalability: Components can be customized for specific needs without sacrificing consistency.
- Accessibility: Follows WCAG (Web Content Accessibility Guidelines) to ensure inclusivity for all users, regardless of ability.

---

## Use Cases for MYDS

- Government Agency Websites: Platforms that disseminate information about policies, regulations, ministry profiles, activities, achievements, announcements, and media.
- Dashboards and Portals: Interactive dashboards for key metrics and user-friendly portals for citizens to access/manage government services.

---

## Resources for MYDS

- Figma: Interactive design canvas featuring MYDS components, templates, and guidelines, supporting efficient prototyping and collaboration.

---

## MYDS 12-8-4 Grid System

### Grid System Overview

The 12-8-4 grid system in MYDS provides a flexible, responsive layout structure for adaptable design across all screen sizes. This grid system ensures content remains consistently aligned and visually balanced, making designs polished and accessible on any device—from desktop to mobile.

### Grid System Containers and Content Types

- Content Container: Main area for arranging content in various layouts.
- Article Container: Designed for long-form content, with a maximum width of 640px for optimal readability.
- Images & Interactive Charts: Can span the full article container width (640px) or extend to a maximum width of 740px for visual impact.

### Grid System Breakpoints

Breakpoints allow layouts to adjust for different device sizes:

- Desktop (≥ 1024px):
  - 12-column grid
  - 24px column gaps and edge padding
  - Maximum content width: 1280px

  The 12-column layout offers maximum flexibility for arranging content on larger screens, enabling complex layouts while keeping elements aligned and uncluttered.

- Tablet (768px - 1023px):
  - 8-column grid
  - 24px gap and edge padding

  The 8-column layout simplifies design for medium screens, maintaining readability and usability.

- Mobile (≤ 767px):
  - 4-column grid
  - 18px gap and edge padding

  The 4-column layout is ideal for small screens, focusing on essential elements for quick and comfortable access.

### Grid System Usage Examples

#### Grid System Example: Content Section Layout

On the Ministry of Digital website, a content image slider section uses the grid as follows:

- Desktop: Title spans 3 columns, image spans 6 columns, with a 1-column space on each side and a 1-column gap between them for separation.
- Tablet: Title and image fill available space for optimal viewing.
- Mobile: Title and image are stacked vertically instead of side by side.

#### Grid System Example: Article Page Layout

The article page design prioritizes readability by using a maximum paragraph container width of 640px, ensuring easy reading without line fatigue.

- Images and Interactive Charts: Displayed at a maximum width of 740px to stand out while maintaining balanced content presentation.

---

## MYDS Colour System

### Colour System Overview

Colour in MYDS defines primary, danger, success, warning, and gray palettes, with guidelines for their use in backgrounds, buttons, and text to ensure contrast, readability, and consistency.

MYDS divides colour palettes into two categories:

- Primitive Colours: Base colours that remain consistent across both light and dark modes.
- Colour Tokens: Dynamic colours that adjust according to the mode or theme (light or dark).

### Colour System: Primitive Colours

Primitive colours are base colours used throughout the design system and do not have corresponding dark mode variants. For adaptive dark mode usage, refer to the Colour Tokens set.

Primitive Colours Include:

- White: Used for backgrounds that provide clarity.
- Gray: Base colour for headings, text, and backgrounds. Also used for placeholders, descriptions, dividers, outlines, and backgrounds with subtle effects.
- Primary: Used for selected links, tabs, primary buttons, and highlighted elements.
  Note: If the main product colour is not primary blue, create a custom palette.
- Danger: Indicates critical issues or errors, perfect for error messages, delete buttons, and urgent alerts.
- Success: Signals successful actions or outcomes, for confirmation messages, pop-ups, and progress indicators.
- Warning: Used for cautionary messages and alerts, suitable for banners, badges, and alert icons.

### Colour System: Colour Tokens Light Mode

Colour tokens provide purposeful styles optimized for high contrast and accessibility in both light and dark modes. They adhere to WCAG 2.1 contrast ratio guidelines.

Token Prefixes:

- bg-: Background
- txt-: Text
- otl-: Outline
- fr-: Focus ring

#### Colour System Example: Light Mode Tokens

- Background Tokens: Define foundational colours for surfaces and containers, maintaining contrast and hierarchy.
- Text Tokens: Used for all text elements, optimized for readability in various states.
- Outline Tokens: Set border and outline colours for clear separation and accents.
- Focus Ring Tokens: Emphasize interactive elements on keyboard focus.

### Colour System: Colour Tokens Dark Mode

Colour tokens also support dark interfaces, ensuring high contrast and readability, and maintaining accessibility.

#### Colour System Example: Dark Mode Tokens

- Background Tokens: Provide foundational colours for surfaces and containers in dark mode.
- Text Tokens: Maintain legibility for all text elements in low-light interfaces.
- Outline Tokens: Define border colours for separation and accents in dark themes.
- Focus Ring Tokens: Highlight interactive elements on keyboard focus in dark mode.

[update] more detailed information to be referred in MYDS-Colour-Reference.md

---

## MYDS Typography System

### Typography System Overview

MYDS typography provides clear standards for font style, size, and spacing, ensuring readability and consistency across government platforms. This supports accessible and user-friendly text presentation.

### Typography System: Headings

The Poppins font family is used for home page section titles, page headers, and important text elements to create a clear visual hierarchy and improve user navigation.
Note: Not applicable for rich-text content.

#### Typography System Example: Heading Sizes and Weights

| Name                | HTML Tag | Font Size      | Line Height     | Font Weight     |
|---------------------|----------|----------------|-----------------|-----------------|
| Heading Extra Large |          | 60px (3.75rem) | 72px (4.5rem)   | 400 / 500 / 600 |
| Heading Large       |          | 48px (3rem)    | 60px (3.75rem)  | 400 / 500 / 600 |
| Heading Medium      | `h1`     | 36px (2.25rem) | 44px (2.75rem)  | 400 / 500 / 600 |
| Heading Small       | `h2`     | 30px (1.875rem)| 38px (2.375rem) | 400 / 500 / 600 |
| Heading Extra Small | `h3`     | 24px (1.5rem)  | 32px (2rem)     | 400 / 500 / 600 |
| Heading 2X Small    | `h4`     | 20px (1.25rem) | 28px (1.75rem)  | 400 / 500 / 600 |
| Heading 3X Small    | `h5`     | 16px (1rem)    | 24px (1.5rem)   | 400 / 500 / 600 |
| Heading 4X Small    | `h6`     | 14px (0.875rem)| 20px (1.25rem)  | 400 / 500 / 600 |

### Typography System: Body Text

The Inter font family is used for paragraphs, descriptions, and general content to provide a comfortable reading experience.

#### Typography System Example: Body Text Sizes and Spacing

| Name             | Font Size      | Line Height     | List Spacing    | Paragraph Spacing | Font Weight     |
|------------------|----------------|-----------------|-----------------|-------------------|-----------------|
| Body 6X Large    | 60px (3.75rem) | 72px (4.5rem)   | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body 5X Large    | 48px (3rem)    | 60px (3.75rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body 4X Large    | 36px (2.25rem) | 44px (2.75rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body 3X Large    | 30px (1.875rem)| 38px (2.375rem) | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body 2X Large    | 24px (1.5rem)  | 32px (2rem)     | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body Extra Large | 20px (1.25rem) | 28px (1.75rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body Large       | 18px (1.125rem)| 26px (1.625rem) | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body Medium      | 16px (1rem)    | 24px (1.5rem)   | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body Small       | 14px (0.875rem)| 20px (1.25rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body Extra Small | 12px (0.75rem) | 18px (1.125rem) | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |
| Body 2X Small    | 10px (0.625rem)| 12px (0.75rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 500 / 600 |

### Typography System: Rich Text Format (RTF)

The Inter font family is also used for styling long-form content such as articles.
Note: The `h1` tag in RTF differs from the standard Heading Medium `h1` and is used only for article content.

#### Typography System Example: Rich Text Format Sizes

| Name      | HTML Tag | Font Size      | Line Height     | List Spacing    | Paragraph Spacing | Font Weight |
|-----------|----------|----------------|-----------------|-----------------|-------------------|-------------|
| Heading 1 | `h1`     | 30px (1.875rem)| 38px (2.375rem) | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Heading 2 | `h2`     | 24px (1.5rem)  | 32px (2rem)     | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Heading 3 | `h3`     | 20px (1.25rem) | 28px (1.75rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Heading 4 | `h4`     | 18px (1.125rem)| 26px (1.625rem) | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Heading 5 | `h5`     | 16px (1rem)    | 24px (1.5rem)   | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Heading 6 | `h6`     | 14px (0.875rem)| 20px (1.25rem)  | 6px (0.375rem)  | 12px (0.75rem)    | 400 / 600   |
| Paragraph | `p`      | 16px (1rem)    | 28px (1.75rem)  | 6px (0.375rem)  | 28px (1.75rem)    | 400 / 600   |

---

## MYDS Icon System

### Icon System Overview

Icons are visual symbols that communicate meaning quickly, guiding users through actions, statuses, and categories. They are crafted with consistent proportions, line weights, and styling to ensure a cohesive look across all components.

### Icon System: Types of Icons

MYDS provides four main icon sets:

- Generic Icons: Simple, universal icons for common functions (search, add, edit, remove, settings).
- WYSIWYG Icons: Icons for text editor tools, representing formatting actions.
- Social Media Icons: Used to link to social media platforms, typically in footers or navigation bars.
- Media Icons: Represent file types (PPTX, Excel, DOCX, PDF), commonly used in file uploaders or previews.

### Icon System: Design Guidelines

#### Icon System Grid Size

- Icons are designed on a 20x20 grid as the base size. They can be resized to fit various UI scenarios.

#### Icon System Stroke Width

- Standard stroke width is 1.5px at 20x20 size. This remains consistent after SVG export for easy adjustment in code.

#### Icon System Sizes & Usage

Stroke width should adjust proportionally with icon size for visual consistency.

| Size (px) | Recommended Usage |
|-----------|-------------------|
| 16x16     | Small button      |
| 20x20     | Medium button     |
| 24x24     | Large button      |
| 32x32     | Alert dialog      |
| 42x42     | Alert dialog      |

#### Icon System: Variants

- Outline Icons: 20x20 icons with stroke outlining the glyph.
- Filled Icons: 20x20 icons with filled style for the glyph.

#### Icon System Example: Usage in UI

Icons are used in buttons, text fields, alerts, and other UI elements to clarify actions and improve usability.

---

## MYDS Motion System

### Motion System Overview

Motion brings interfaces to life, transforming static elements through purposeful movement and interaction. It provides clear user feedback and enhances usability.

### Motion System: Principles

- Simple: Motion should guide users, not distract them.
- Harmony: Productive and expressive motions should be in sync for a cohesive experience.
- Functional: Every motion must serve a clear purpose.

### Motion System: Types of Motion

#### Motion System Type: No Transition (Instant)

- The default state with no motion effect during transformations.
- Token name: instant

#### Motion System Type: Linear

- Uses the CSS transition property for direct interpolation between states.
- Token name: linear
- CSS: cubic-bezier(0, 0, 1, 1)
- Not ideal for organic UI animation.

#### Motion System Type: Ease-Out

- Smooth, natural curve to the target state, ensuring clarity and precision.
- Token name: easeout
- CSS: cubic-bezier(0, 0, 0.58, 1)
- Use cases: Charts, UI state transitions, background fade-outs.

#### Motion System Type: Ease-Out-Back (Custom)

- Dynamic, spring-like motion that overshoots before settling.
- Token name: easeoutback
- CSS: cubic-bezier(0.4, 1.4, 0.2, 1)
- Use cases: Playful or attention-grabbing elements, success animations, buttons.

### Motion System: Transition Duration

| Token Variable | Duration | Recommended Usage                                  |
|----------------|----------|---------------------------------------------------|
| short          | 200ms    | Small UI (buttons, dropdowns, micro-interactions) |
| medium         | 400ms    | Medium UI (callouts, alert dialogs, toast)        |
| long           | 600ms    | Large UI (page, section transitions)              |

### Motion System: Motion Tokens

Motion tokens are predefined values for managing UI state transitions.
Token naming follows [motion-type].[transition] format.

| Token Variable     | Example CSS                                                   |
|--------------------|---------------------------------------------------------------|
| easeoutback.short  | .btn:hover { transition: 200ms cubic-bezier(0.4, 1.4, 0.2, 1); }    |
| easeoutback.medium | .alert-dialog { transition: 400ms cubic-bezier(0.4, 1.4, 0.2, 1); } |
| easeout.long       | .slides { transition: 600ms cubic-bezier(0, 0, 0.58, 1); }          |
| easeout.1000       | .circle { transition: 1000ms cubic-bezier(0, 0, 0.58, 1); }         |
| easeoutback.1000   | .circle { transition: 1000ms cubic-bezier(0.4, 1.4, 0.2, 1); }      |

Motion tokens can be customized for different speeds and durations.

### Motion System Example: Toast Component

A Toast demonstrates motion effects for smooth, dynamic UI interactions:

1. Toast Enter:
   - Slides in from the bottom using easeoutback.medium (400ms).
2. Progress Bar:
   - A visual indicator counts down for 3000ms using linear.3000, showing visibility duration.
3. Toast Exit:
   - Slides out to the bottom using easeoutback.medium (400ms) after the progress bar ends.

---

## MYDS Radius System

### Radius System Overview

A unified corner radius creates a smooth, modern aesthetic that enhances user experience through visual consistency. It softens element appearances, making them more approachable and easier to interact with.

### Radius System: Specification

| Name        | Size  | Radius      | Recommended Usage                          |
|-------------|-------|-------------|--------------------------------------------|
| Extra Small | 4px   | radius-xs   | Context Menu Item                          |
| Small       | 6px   | radius-s    | Small Button                               |
| Medium      | 8px   | radius-m    | Button, CTA, Context Menu                  |
| Large       | 12px  | radius-l    | Content Card                               |
| Extra Large | 14px  | radius-xl   | Context Menu with Search field             |
| Full        | 9999px| radius-full | Fully rounded elements (e.g. avatars, chips)|

---

## MYDS Shadow System

### Shadow System Overview

Shadow adds depth and dimension to UI components, offering a sense of layering and hierarchy in digital interfaces.

### Shadow System: Specification

| Name         | Preview                 | CSS                                                                                         | Recommended Usage |
|--------------|-------------------------|----------------------------------------------------------------------------------------------|-------------------|
| None         | shadow-none.png         | -                                                                                            | No shadow         |
| Button       | shadow-button.png       | box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);                                             | Buttons           |
| Card         | shadow-card.png         | box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);       | Cards             |
| Context Menu | shadow-context-menu.png | box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);       | Context Menus     |

---

## MYDS Spacing System

### Spacing System Overview

The spacing guidelines define the consistent use of margins and paddings across all components, ensuring a harmonious and visually appealing layout.

### Spacing System: Specification

| Size | Preview       | Recommended Usage                         |
|------|---------------|-------------------------------------------|
| 4px  | space-4.png   | Micro spacing                             |
| 8px  | space-8.png   | Gap in button groups, fields, labels      |
| 12px | space-12.png  | General component spacing                 |
| 16px | space-16.png  | General component spacing                 |
| 20px | space-20.png  | General component spacing                 |
| 24px | space-24.png  | Gap between sub-sections, cards           |
| 32px | space-32.png  | Gap between main sections                 |
| 40px | space-40.png  | Large blocks, outer margins               |
| 48px | space-48.png  | Extra large blocks, outer margins         |
| 64px | space-64.png  | Page-level or major section separation    |

---

## MYDS Accordion Component

### Accordion Component Overview

The Accordion component organizes and displays content in a compact, collapsible format—commonly used for FAQ sections or to streamline lengthy documentation.

### Accordion Component Anatomy

- Header Text: The main title or label for the accordion section.
- Body Text: The content that expands or collapses within the accordion.
- Chevron Down (Default): Default icon (rotation: 0°), indicates closed section.
- Chevron Down (Expanded): Icon rotates to 180° when section is open.

### Accordion Component State and Interaction

- Click Trigger Area: The header is clickable; clicking expands or collapses the accordion to reveal or hide the body text.
- Hover State: Header text is underlined to signify interactivity.
- Closed State (Default): Body text is hidden (opacity: 0%).
- Opened State: Clicking the header shows the body text (opacity: 100%).

### Accordion Component Demo

A demo showcases the Accordion's interaction:

- Closed: Chevron at 0°, body text hidden.
- Hover: Header underlined.
- Opened: Chevron at 180°, body text visible.

Accordion is ideal for managing large content sections, improving readability and navigation by allowing users to expand only what they need.

---

## MYDS Alert Dialog Component

### Alert Dialog Component Overview

An Alert Dialog is a modal pop-up designed to capture user attention for important actions or messages. It consists of three main components—Header, Content, and Footer—forming a flexible, cohesive structure for alerts or forms.

### Alert Dialog Component Anatomy

- Header: Sticky top section with a title, leading icon, and optional elements (close icon or last updated status). Includes a divider below.
- Content: Main area for forms, messages, or other information.
- Footer: Sticky bottom section with action buttons for user decisions: primary action, secondary actions, and optional disclaimer.
- Callout: Displays error messages if form validation fails.
- Background Overlay: 50% black opacity overlay behind the dialog to focus attention and block background interaction.

### Alert Dialog Component Size

#### Alert Dialog Size: Desktop/Tablet

- Fixed at center of the screen.
- Maximum width: 400px (alert type), 800px (form type).

#### Alert Dialog Size: Mobile

- Centered on the screen, stretches to fit width.
- Minimum width: 300px; maximum width: 100% of screen with 18px margins on all sides.

### Alert Dialog Component Sections

#### Alert Dialog Section: Header

- Sticky header for long, scrollable modals.
- Includes title, optional last updated status, optional tab navigation, optional close icon, divider.

#### Alert Dialog Section: Footer

- Positioned at bottom, contains action buttons:
  - Primary Action: Main button, always visible (right-aligned desktop, centered mobile).
  - Action 1: Secondary, next to primary (left).
  - Cancel Action: Next to Action 1, cancels the primary action.
  - Action 2: Far left, for additional actions (e.g., Delete Draft).
  - Disclaimer: Shown before primary action (e.g., By submitting, you agree to Terms of Use and Privacy Policy).

#### Alert Dialog Section: Footer Layout

- Side-by-Side: Horizontal button layout (desktop/larger modals).
- Stacked: Vertical button layout (mobile/smaller modals).

#### Alert Dialog Section: Footer Alignment

- Right: Default, aligns actions right (desktop/larger modals).
- Left: Actions aligned left.
- Full: Actions stretch to fill container (mobile/small alert dialog).

### Alert Dialog Component Usage Examples

#### Alert Dialog Example: Forms

- Used for applications, settings, or interactive content needing user input.

#### Alert Dialog Example: Alerts

- Success: Feedback for successful actions.
- Warning: Alerts for issues needing attention.
- Destructive: For irreversible actions (e.g., deletion).

Alert Dialog is crucial for key actions, feedback, and confirmations, improving user experience and safety.

---

## MYDS Backlink Component

### Backlink Component Overview

The Backlink component helps users return to the previous page in a multi-page flow. Contextual backlinks also improve internal linking, which assists search engines in crawling and indexing your site more effectively—distributing link equity and improving overall SEO.

### Backlink Component State and Usage

- Backlink Format: Contextual backlink should follow the format: <- Back to Page title.
- Hover State: The backlink displays an underline on hover to indicate it's clickable.
- Usage Example: Used at the top of forms, dialogs, or any multi-step navigation to allow users to return to the previous step or overview.

Backlink is essential for guiding users through complex flows and improving navigation efficiency.

---

## MYDS Breadcrumb Component

### Breadcrumb Component Overview

The Breadcrumb component is a navigation aid that helps users understand their current location within a website or application, and allows easy navigation back to previous levels.

### Breadcrumb Component Anatomy

- Parent Page: Link to the preceding page or section, helping users navigate back through the hierarchy.
- Current Page: Indicates the user’s current location in the navigation path (typically not clickable).
- Wrapper: Container holding the breadcrumb trail for consistent spacing and alignment.

### Breadcrumb Component State and Interaction

- Trigger Area: Parent page links are clickable and receive underline on hover to indicate interactivity.
- Wrapper Appearance: Can be default (no wrapper) or with a visual wrapper for layout consistency.

### Breadcrumb Component Title Length Management

- Limit Title Length: The current page title should have a maximum width of 200px. If the title exceeds this, display an ellipsis (e.g., Long Page Title...) to maintain a clean, organized layout.

Breadcrumb improves navigation, orientation, and access to higher levels of the site or app hierarchy.

---

## MYDS Button Component

### Button Component Overview

A Button is a core UI element used to trigger actions or events. It provides consistency, accessibility, and a clear call to action across platforms.

### Button Component Types

MYDS provides several button types for various contexts:

- Primary: Main action button, most prominent.
- Secondary: Alternative action, visually less prominent.
- Secondary Colour: Secondary action with accent colour.
- Tertiary: Minimal emphasis, often used for less critical or supporting actions.
- Tertiary Colour: Tertiary variant with accent colour.
- Danger Primary: Main destructive action (e.g., delete).
- Danger Secondary: Secondary destructive action.
- Danger Tertiary: Least prominent destructive action.

### Button Component Sizes

Each button size is designed to fit different forms or actions, optimizing readability and accessibility:

| Size Name | Usage Example                    |
|-----------|----------------------------------|
| Small     | Compact UI, toolbars, inline forms |
| Medium    | Standard forms, dialogs, pages   |
| Large     | Prominent CTAs, hero sections    |

### Button Component Appearance States

Buttons visually respond to user interaction and accessibility needs:

- Default: Initial appearance, not interacted with.
- Hover: Mouse pointer over button; provides feedback (often colour change or underline).
- Focused: Selected/active via keyboard (TAB) or mouse; crucial for accessibility.
- Disabled: Not available for interaction; visually muted.

### Button Component Leading & Trailing Icons

- Leading Icon: Placed before text, visually indicates purpose (e.g., download, back arrow, trash).
- Trailing Icon: Placed after text, indicates further action or direction (e.g., arrow forward).

### Button Component Counter

A counter is a small numeric indicator within a button, showing a related quantity (e.g., number of notifications, items, filters).

### Button Component Icon Only

Some buttons contain only an icon (no text), clearly indicating the action:

- Examples: Plus icon, pencil icon, hamburger menu, trash icon.

### Button Component Demo Notes

When clicked, a button may visually move down by 0.5px for tactile feedback, enhancing the sense of interaction.

---

## MYDS Callout Component

### Callout Component Overview

The Callout component notifies users about important information related to their actions, especially inside forms. Callouts can indicate success, warnings, errors, or provide additional information based on context.

### Callout Component Anatomy

- Leading Icon: Indicates the type of Callout (e.g., success, error, info).
- Title: Primary text conveying the main message.
- Description: Additional details or context.
- Optional Close Icon: Allows users to dismiss the callout manually (non-critical callouts).
- Optional Primary Action: Button for key actions (e.g., Retry, Continue).
- Optional Secondary Action: Additional button for extra tasks (e.g., Learn More, Cancel).

### Callout Component Types

| Type        | Purpose / Example                               |
|-------------|--------------------------------------------------|
| Success     | User action completed successfully (Form submitted) |
| Warning     | Reminders or important notes (Session will expire)  |
| Information | Non-critical helpful info (Updates available)       |
| Error       | Error requiring resolution (Submission failed)      |

### Callout Component Close Icon

- Users may click the close icon to dismiss non-critical callouts.
- Useful for clearing notifications manually.

### Callout Component Action Buttons

- Primary: Usually styled as a Secondary Button (important actions).
- Secondary: Often styled as a Tertiary Button (supportive/optional actions).

### Callout Component Usage Examples

#### Callout Usage Example: Forms

- Location: Appears inside forms or modals when user performs an action.
- Purpose: Indicates outcome (success, error, warning, info).
- Width: Adapts to container or screen size for clear visibility.

#### Callout Usage Example: Page

- Location: At the beginning of page content for instant visibility.
- Purpose: Notifies users before they engage with the rest of the content.
- Width: Full-width, adapts responsively to container or screen size.

---

## MYDS Character Count Component

### Character Count Component Overview

The Character Count component tracks the number of characters in a text area, providing real-time feedback to help users stay within a set limit. It displays warnings near the limit and errors when exceeded, improving form usability and compliance.

### Character Count Component Anatomy

- Text Area Input: Character count is integrated below the Text Area Input, displaying the character limit as a hint.
- Hint Under: Dynamic hint text shows the remaining character count below the input.
- Counter: The counter begins with the maximum allowed value and decrements as the user types, showing the remaining characters (e.g., 200, 199...0).

### Character Count Component States

- Default: Displays remaining characters in descending order as the user types (e.g., 200 characters remaining).
- Warning: When the user is close to the limit, the counter changes colour (yellow) and message (e.g., 10 characters left).
- Error: If the user exceeds the limit, the counter shows negative numbers (e.g., -100 characters over the limit) and prevents form submission due to validation failure.

### Character Count Component Usage Example

- Form Integration: Character count is typically shown within forms, especially for text area inputs with limits.
- Validation: Prevents submission if character count exceeds the limit.
- Accessibility: Provides clear feedback for users to adjust their input.

---

## MYDS Checkbox Component

### Checkbox Component Overview

The Checkbox component allows users to make a binary choice, such as selecting or not selecting an option. It is used in forms, settings, and filters to capture user input and preferences.

### Checkbox Component Size

Defines the dimensions of the checkbox for different contexts, ensuring accessibility and clarity.

- ![checkbox-size.png](checkbox-size.png)

### Checkbox Component State

Indicates the current status of the checkbox, helping users understand and interact with options.

- ![checkbox-state.png](checkbox-state.png)

### Checkbox Component Property: Checked

Controls whether the checkbox displays a checkmark (selected) or not (unselected).

- Checked: Boolean property. If true, checkbox is selected and shows a checkmark.
- Unchecked: If false, no checkmark is shown.
- Trigger Area: Add 5px extra padding around checkbox for easier clicking.

- ![checkbox-checked.png](checkbox-checked.png)

### Checkbox Component Property: Indeterminate

Represents a mixed or partial selection state, often used in hierarchical selection scenarios.

- Indeterminate: Checkbox shows a dash instead of a checkmark to indicate partial selection.

- ![checkbox-intermediate.png](checkbox-intermediate.png)

### Checkbox Component Label Size

Defines the size of the checkbox and its text label to fit different form sizes.

- ![checkbox-label-size.png](checkbox-label-size.png)

### Checkbox Component Hint Text

Hint text below the checkbox label provides extra information or guidance about the option.

- ![checkbox-hint-text.png](checkbox-hint-text.png)

### Checkbox Component Usage

- Use checkboxes in forms, settings pages, and filter options.
- Use indeterminate state for parent checkboxes in hierarchical lists when only some children are selected.
- Add hint text to clarify the consequence or meaning of an option.
- Ensure trigger area is large enough for accessibility.

---

## MYDS Cookies Banner Component

### Cookies Banner Component Overview

The Cookies Banner notifies users that the website uses cookies to improve their experience and offers options to manage consent preferences. It appears when a user lands on the website for the first time.

### Cookies Banner Component Anatomy

- Description: Message informing users about the website's use of cookies. Example: Cookies on {website}.gov.my.
- Accept or Reject Cookies Button: Lets users accept all cookies or reject cookies.
- Customize Button: Opens cookie settings so users can choose preferences.
- Close Icon: Allows users to manually dismiss the banner.
- Necessary Cookies Checkbox: Required for site functionality. Cannot be unchecked.
- Analytics Cookies Checkbox: Tracks visitor count, bounce rate, traffic sources.
- Performance Cookies Checkbox: Used to analyze key metrics for improved UX.
- Accept Cookies Button: Accepts cookies selected in Manage settings.
- Reject All Cookies Button: Rejects all non-essential cookies.

### Cookies Banner Component State

- Default: Banner appears with description and options to Accept All, Reject all, or Customize.
- Customize: User can select/deselect Analytics/Performance cookies (all cookies checked by default except Necessary, which is always checked). Preferences save immediately when changed.

### Cookies Banner Component Mobile View

- Desktop: 24px padding by default, fixed at bottom left with 24px margin, width 500px.
- Mobile: 18px padding, banner centered at bottom with 18px margin, stretches to fit screen width.

### Cookies Banner Component Location

- Desktop/Tablet: Fixed bottom left, 24px margin, width 500px.
- Mobile: Fixed bottom center, 18px margin on sides, fills screen width.

### Cookies Banner Component Usage Example

- Banner shows automatically on page load.
- Users manage settings via Customize, Accept, or Reject.
- Remains visible until user interacts (accepts, rejects, customizes, or closes).

---

## MYDS Date Field Component

### Date Field Component Overview

The Date Field component allows users to input a date in a structured format using separate fields for day, month, and year. This ensures clarity, proper validation, and a user-friendly experience.

### Date Field Component Anatomy

- Leading Icon (Calendar): A calendar icon placed before input fields, signaling the field is for date entry.
- Day Field (dd): 2-digit field, accepts values from 1–31.
- Month Field (mm): 2-digit field, accepts values from 1–12.
- Year Field (yyyy): 4-digit field, accepts year values.
- Separator (/): Visual separator between fields, clearly indicating the format (e.g., dd/mm/yyyy).
- Label Text: Labels the date field (e.g., Date of Birth).
- Hint Text: Placed below the field, displays errors if the entered date is invalid.

![date-field-anatomy.png](date-field-anatomy.png)

### Date Field Component Field Highlight

- Day: Highlighted when focused or filled.
- Month: Highlighted when focused or filled.
- Year: Highlighted when focused or filled.

![date-field-highlight.png](date-field-highlight.png)

### Date Field Component Sizes

- Each size caters to different form requirements, ensuring readability and usability.

![date-field-size.png](date-field-size.png)

### Date Field Component States

- Default: Ready for input, not interacted with.
- Filled: User has entered values in the field.
- Focused: Field is active, ready to receive input.
- Disabled: Field is unavailable due to context or permissions.

![date-field-state.png](date-field-state.png)

### Date Field Component Error State

- Triggered when the field has an error condition (e.g., invalid date).
- Displays a red border for visual feedback.
- Remains highlighted when focused to emphasize the error.

![date-field-error.png](date-field-error.png)

---

## MYDS Date Picker Component

### Date Picker Component Overview

The Date Picker component allows users to select a date from an interactive calendar, supporting both single date and range selection. It enhances user experience for date entry, filtering, and planning tasks.

### Date Picker Component Anatomy

- Date Selection Trigger Button: Opens the calendar for date selection.
- Month and Year Header: Displays current month and year; acts as navigation control.
- Previous and Next Navigation: Buttons to move between months and years.
- Day Abbreviation: Short names for days of the week.
- Date Selection: Clickable dates for user to select.

![date-picker-anatomy.png](date-picker-anatomy.png)

### Date Picker Component Month and Year Picker

To support easy selection far in the past or future, the Date Picker provides three views:

- Day View: Shows days of the current month.
- Month View: Shows all months of the year.
- Year View: Shows a group of 18 years for quick navigation.

How to navigate:

- Click the {Month} header for Month View.
- Click the {YYYY} header for Year View.
- Click {Month} or a month name to go back to Day View.
- Click {YYYY} or a year to go back to Day View.

![date-picker-month-year-picker.png](date-picker-month-year-picker.png)

### Date Picker Component Navigation Between Views

- Day View: Use navigation buttons for previous/next month.
- Month View: Use navigation buttons for previous/next year.
- Year View: Scroll up/down to navigate groups of 18 years.

![date-picker-navigate-views.png](date-picker-navigate-views.png)

### Date Picker Component Language Support

- Supports both English and Malay for localization.

![date-picker-language.png](date-picker-language.png)

### Date Picker Component Single Date Selection

- Trigger Button: Secondary button for month navigation (enabled/disabled as needed).
- Date Buttons:
  - Secondary (default/disabled) for navigation.
  - Tertiary (default/hover/disabled) for interactive dates.
  - Primary (default) for currently selected date.
- Disabled Dates: Prevent selection ahead of latest allowed date.
- Visual Feedback: Hover, selected, and disabled states for clear interaction.

![date-picker-single-date.png](date-picker-single-date.png)

### Date Picker Component Range Date Selection

- Trigger Buttons: From and To for selecting date range.
- Range Highlight: Visual highlight for selected range (use bg-primary-200 for highlight).
- Hover State: Date hover in highlighted range for feedback.
- Multiple Selections: Supports picking start and end dates.

![date-picker-date-range.png](date-picker-date-range.png)

### Date Picker Component Demo

- Demonstrates interactive selection, navigation between views, localization, and range selection.

---

## MYDS Details Component

### Details Component Overview

The Details component provides additional information or explanations that users can reveal by expanding the section. It is used in forms, modals, pages, or articles to display more details without cluttering the interface.

### Details Component Anatomy

- Header Text: The clickable title for the details section.
- Body Text: The hidden content that appears when the section is expanded.
- Chevron Right Opaque (Default): Chevron icon at rotation 0° indicating closed state.
- Chevron Right Opaque (Opened): Chevron icon rotates 90° when opened, indicating expanded state.

![details-anatomy.png](details-anatomy.png)

### Details Component State and Interaction

- Click Trigger Area: The header acts as the clickable trigger to expand/collapse details.
- Hover State: Underlines header text to show interactivity.
- Closed State (Default): Body text is hidden (opacity 0%).
- Opened State: Clicking the header reveals body text (opacity 100%).

![details-state.png](details-state.png)

### Details Component Demo

- Shows interactive expansion/collapse, chevron rotation, and transition between states.

---

## MYDS File Upload Component

### File Upload Component Overview

The File Upload component allows users to upload and preview files. It features a title, upload guidelines, an upload button, and a list displaying uploaded items, with built-in error handling for file uploads.

### File Upload Component Preview

When a file is uploaded, its details are previewed, including:

- Thumbnail/Icon: Displays a thumbnail for images/videos, or an icon for other file types (PDF, DOCX, PPTX, XLSX, etc.).
- File Name: Name of the uploaded file, truncated if too long.
- File Extension: Displayed at the end of the file name (e.g., .pdf, .jpeg, .docx).
- Trailing Icon: Close icon to allow the user to remove the uploaded file.

![file-preview-anatomy.png](file-preview-anatomy.png)

### File Upload Component Preview State

- Default: File is displayed after upload.
- Hover: Shows remove action (e.g., close icon); optional tooltip guides user.
- Hover on Remove Icon: Highlights remove button; tooltip may explain action.
- Disabled: File cannot be removed/edited.
- Loading: Upload in progress; spinner shown, file name replaced with Uploading....

![file-preview-state.png](file-preview-state.png)
![file-preview-loading.png](file-preview-loading.png)

### File Upload Component

Typically used in forms requiring single or multiple file uploads. Key elements:

- Title: Indicates purpose (e.g., Supporting Documents).
- File Format: Supported file types.
- Max File Size: Maximum allowed size.
- Upload Button: For selecting files to upload.
- Hint/Error Message: Informs about issues (file size/format).
- Uploaded File List: Shows preview component for each file uploaded.
- Optional Previously Uploaded File List: (Rare) Shows old files for comparison.

![file-upload-anatomy.png](file-upload-anatomy.png)

### File Upload Component State

- Default: Ready for file upload.
- Disabled: Cannot interact/upload files.
- Loading: Shows spinner while uploading.
- Uploaded: Files successfully uploaded and displayed.
- Error: Message displayed for problems (size, format, etc).

![file-upload-state.png](file-upload-state.png)

### File Upload Component Usage Example

- Location: Used in forms or modals where documents/files are required (e.g., applications, support tickets).
- Width: Full width of container (form/modal) for adaptability.

![file-upload-usage-example.png](file-upload-usage-example.png)

### File Upload Component Demo

- Shows upload, preview, remove, loading, and error handling interactions.

---

## MYDS Inset Text Component

### Inset Text Component Overview

The Inset Text component highlights key information—such as quotes or important messages—by indenting content and adding a distinct visual boundary. It is ideal for emphasizing content while maintaining a clean, readable layout.

### Inset Text Component Anatomy

- Inset Description: Main body text, typically styled as Body/M/500.
- 4px Left Border: A distinct left border visually separates the inset from main content.
- Author Name: (Optional) Used for block quotes to indicate the source or author.

![inset-text-anatomy.png](inset-text-anatomy.png)

### Inset Text Component Types

- Inset Text: Used to highlight key information or call out specific details within the main content; stands out due to indentation.
- Block Quote: Used to quote text from a source or author, often formatted with an indent and author attribution.

![inset-text-type.png](inset-text-type.png)

### Inset Text Component Usage Examples

- Forms and Instructions: Place inset text in forms to highlight important instructions, deadlines, requirements, or clarifications. Guides users without interrupting form flow.
- Documentation and Guidelines: Use inset text for key takeaways, essential rules, or important information in documentation, helping users locate critical points quickly.
- Articles or Blog Posts: Call out quotes, statistics, or main ideas, ensuring readers notice important content without needing to read the entire section.

By using inset text in these scenarios, you improve readability, emphasize important content, and enhance overall user experience.

![inset-text-usage-example.png](inset-text-usage-example.png)

---

## MYDS Pagination Component

### Pagination Component Overview

The Pagination component allows users to navigate through a large set of content divided into discrete pages, improving usability and access to information.

### Pagination Component Types

![pagination-type.png](pagination-type.png)

- Type 1: Ideal for cases where users want to jump to a specific page within a large number of pages.
- Type 2: Designed for simpler navigation, mainly for moving forward or backward.
- Type 3: Displays the current page and total pages on the left, with navigation buttons on the far right.

### Pagination Component Previous & Next Button

![pagination-buttons.png](pagination-buttons.png)

- Use Button Type=Secondary for navigation buttons.
- Disabled state removes box-shadow under the button for accessibility.
- Default: Button is not interacted with.
- Hover: Button responds visually when pointer is over it.
- Focused: Button is selected or active (keyboard/mouse).
- Previous Button: Disabled on first page, enabled otherwise.
- Next Button: Disabled on last page, enabled otherwise.
- Both Disabled: When only one page is present.

### Pagination Component Page Numbers

![pagination-numbers.png](pagination-numbers.png)

- Use Button Type=Tertiary for pagination numbers.
- States: Default, Hover, Selected, Focused, Not Interactive, Focused & Selected.
- Numbers allow users to jump to a specific page in large datasets.

### Pagination Component Usage

- Use pagination for tables, lists, search results, or any content requiring navigation across multiple pages.
- Choose the appropriate type based on user needs and content volume.

---

## MYDS Panel Component

### Panel Component Overview

The Panel component is a visible container designed to emphasize important content, typically placed at the top section of confirmation or results pages. Its primary purpose is to ensure key information stands out clearly for users.

### Panel Component Anatomy

- Panel Title: A clear, concise heading summarizing the panel's content, helping users quickly understand its purpose and importance.
- Placeholder Description: Main body text, typically styled as Body/M/400.
- Detail Section: Additional details displayed in Body/XL/600 for enhanced emphasis.

![panel-anatomy.png](panel-anatomy.png)

### Panel Component Colour Types

Panels use colour to visually indicate the status or category of the information:

- Blue (Informational): For process updates or ongoing status.
- Green (Success): For completed and successful processes.
- Yellow (Warning): For situations requiring user input or attention.
- Red (Danger): For failed or rejected processes.

![panel-color.png](panel-color.png)

### Panel Component Usage Examples

#### Panel Component Usage Example: Informational Blue

- Used to display informational messages when a process is in progress but not yet completed.
- Common for submissions being processed or applications under review.
- Include useful details such as Date submitted or instructions for next steps.

![panel-usage-blue.png](panel-usage-blue.png)

#### Panel Component Usage Example: Success Green

- Indicates that a process is complete and successful.
- Confirms to the user that their application, payment, or task is finalized.
- Use a check-circle icon for completion indication.
- Provide reference numbers or key information for future use.
- Use larger typography (Body/5XL/600) for reference numbers.
- Include QR codes, barcodes, or images as proof of completion.

![panel-usage-green.png](panel-usage-green.png)

#### Panel Component Usage Example: Warning Yellow

- Brings attention to processes that are incomplete, under review, or approaching deadlines.
- Guides users toward next steps, estimated response time, and expected resolution.
- Include payment requirements or any action needed for resolution.

![panel-usage-yellow.png](panel-usage-yellow.png)

#### Panel Component Usage Example: Danger Red

- Indicates failed or rejected processes, overdue payments, or errors.
- Provides clear feedback on what went wrong.
- Include the reason for rejection and error codes for user understanding and resolution guidance.

![panel-usage-red.png](panel-usage-red.png)

---

## MYDS Password Input Component

### Password Input Component Overview

The Password Input component provides a secure field for users to enter passwords, masking the characters to ensure privacy. It typically includes an option to toggle password visibility for easier input verification.

### Password Input Component Anatomy

- Label: Text label for the password field, indicating its purpose (e.g., Password).
- Default Placeholder: Enter password guides users on what to input.
- Show/Hide Icon: Eye icon changes based on visibility state:
  - eye-show: Displays when password is hidden.
  - eye-hide: Displays when password is visible.
- Show/Hide Icon Trigger Area: The clickable area for toggling password visibility.
- Tooltip: Displays guidance when hovering over the icon:
  - Show password if hidden.
  - Hide password if visible.

![password-input-anatomy.png](password-input-anatomy.png)
![password-input-trigger-area.png](password-input-trigger-area.png)

### Password Input Component Show/Hide Icon Trigger Area

- The trigger area for the Show/Hide icon should be increased to match the input field's height, ensuring easy and accessible clicking/tapping.
- Tooltip should display Show password when hidden, and Hide password when visible.

### Password Input Component State

![password-input-state.png](password-input-state.png)

- Default: Password hidden, field ready for input.
- Focused: Field is active and ready to receive input (via click or tab).
- Filled: User has entered a password.
  - Eye-show: Trailing icon to reveal password.
- Filled (Password visible): Password displayed in plain text.
  - Eye-hide: Trailing icon to hide password.
- Error: Outlined in otl-danger-300 and shows error hint text when password input is empty.
- Error Focused: Error state remains even when focused.

### Password Input Component Size

- Multiple sizes are available to suit different form requirements and optimize readability and usability.

![password-input-size.png](password-input-size.png)

---

## MYDS Phase Banner Component

### Phase Banner Component Overview

The Phase Banner informs users about the service’s development stage (e.g., Alpha, Beta, Maintenance) and provides a link to submit feedback. It is typically placed below the navigation menu to communicate the current status of the service.

### Phase Banner Component Anatomy

- Tag (UI/Tag): Displays the current phase label. Recommended size M by default.
- Description: A brief sentence explaining the current phase and what it means for users.
- Feedback Link: A link inviting users to submit feedback on the service.

![phase-banner-anatomy.png](phase-banner-anatomy.png)

### Phase Banner Component Responsive View

- Desktop & Tablet:
  - Tag size: M
  - Horizontal padding: 24px on left and right
- Mobile:
  - Tag size: S
  - Horizontal padding: 18px on left and right
  - The tag remains on the left. Wrap/break the description text as needed for smaller screens.

![phase-banner-responsive.png](phase-banner-responsive.png)

### Phase Banner Component Usage Examples

#### Phase Banner Usage Example: Status Types

Use the phase banner to communicate the service’s development stage and encourage appropriate user actions:

- Alpha: Introduces a new service and encourages early feedback to improve the experience.
- Beta: Indicates the service is nearly complete and requests user input for final adjustments.
- Public Beta: Shows the service is available for wider use; invites all users to test and share feedback.
- Maintenance: Indicates the service is temporarily impacted due to updates or fixes.
- Retired: Communicates that the service is no longer active.

![phase-banner-example.png](phase-banner-example.png)

### Phase Banner Component Placement

- Primary placement is directly below the navigation menu to ensure users immediately understand the service status.
- Alternatively, place in another prominent area where visibility is high and contextually appropriate.

### Phase Banner Component Scroll Behaviour

- The phase banner is not sticky and should scroll off the screen with page content. This ensures it remains informative yet non-intrusive as users navigate.

---

## MYDS Pill Component

### Pill Component Overview

Pills represent tags or categories within a text field. They can contain text and may include an "x" button to allow for easy removal, making them ideal for filtering or selection tasks.

### Pill Component Size

Size properties ensure that pills are appropriately proportioned and visually consistent with other UI elements in the form or interface.

![pills-size.png](pills-size.png)

### Pill Component Disabled State

When a pill is disabled, the "x" or remove icon is hidden to indicate that it is not interactive. This is useful for displaying tags that cannot be removed by the user.

![pills-disabled.png](pills-disabled.png)

### Pill Component Trailing Icon

A trailing icon (typically a close 'x' icon) is an optional button within the pill that allows users to remove it from the text field or selection.

![pills-trailing-icon.png](pills-trailing-icon.png)

---

## MYDS Radio Component

### Radio Component Overview

Radio buttons allow users to select exactly one choice from a group of options, ensuring clear, mutually exclusive selections in forms and interfaces.

### Radio Component Size

Radio buttons are available in multiple sizes to suit different UI densities and form requirements:

- **Small:** Compact and space-efficient, suitable for dense interfaces.
- **Medium:** Balanced size that works well in most interfaces.

![radio-size.png](radio-size.png)

### Radio Component State

Radio buttons provide visual cues based on user interaction and accessibility:

- **Default:** The initial state before any interaction.
- **Hover:** Subtle border colour change when hovered, indicating interactivity.
- **Focused:** Highlighted when navigated via keyboard or clicked with mouse, supporting accessibility.
- **Disabled:** The radio button is unavailable for interaction.

![radio-size-state.png](radio-size-state.png)

### Radio Component Checked State

Indicates whether the radio button is selected:

- **False:** Initial state, not selected.
- **True:** Selected by the user.

![radio-size-checked.png](radio-size-checked.png)

### Radio Component Group

#### Radio Group Overview

Radio buttons are typically grouped together to present multiple options, allowing users to select only one.

#### Radio Group Label Size

Each group and its label can be sized to fit form requirements, ensuring readability and consistency.

![radio-group-label-size.png](radio-group-label-size.png)

#### Radio Group Hint Text

Hint text provides additional information or context about the radio options, assisting users in making informed choices.

![radio-group-hint-text.png](radio-group-hint-text.png)

#### Radio Group State

Each radio button in a group visually reflects its current status and interaction possibilities:

- **Checked:** Radio selected by the user.
- **Default:** Ready for interaction.
- **Disabled:** Option not available due to permissions or context.

![radio-group-state.png](radio-group-state.png)

---

## MYDS Search Bar Component

### Search Bar Component Overview

The Search Bar allows users to enter a query or keyword to search through content within a website or application. It is a critical component for improving navigation and discoverability of information.

### Search Bar Component Size

The Search Bar is available in multiple sizes to fit different UI contexts:

- **Small:** Used in section headers alongside small buttons and tabs for compact layouts.
- **Medium:** Used in section headers with medium buttons and tabs for balanced layouts.
- **Large:** Used in hero sections or main areas where search is a primary action, prominently centered.

![search-size.png](search-size.png)

### Search Bar Component States

The Search Bar provides clear visual feedback for different user interactions:

- **Default (Empty Field):** Shows shortcut hint (e.g., “Press / to search”). Placeholder and button at 20% opacity.
- **Hover:** Border colour changes to `otl-gray-300` to indicate interactivity.
- **Filled:** Entered text displayed in `txt-black-900` for strong readability; shortcut hint hidden and “Clear” or “X” symbol appears for easy clearing of input.
- **Focused:** Shows a focused ring when activated (e.g., after pressing “/” or clicking into the field).
- **Clear Action:** Clicking the “Clear” or “X” icon removes the text and restores the default state.

![search-states.png](search-states.png)

### Search Bar Component Search Result Dropdown

When users type in the Search Bar, a dropdown displays relevant results, categories, and highlights matching text:

- **Focus Ring:** Visible while the input is focused; hidden when not focused.
- **Search Result Hover State:** Hovering on a result highlights it with `bg-washed`.
- **Category Label:** Results are grouped by category; from the second item onwards, each category label has 16px padding-top for separation.
- **Inline Text Highlight:** Matching query text in results is highlighted with the default brand colour and `semibold` font weight.
- **Max Height:** Dropdown is scrollable with a maximum height of 400px to accommodate many results without overwhelming the view.

![search-dropdown.png](search-dropdown.png)

---

## MYDS Select Component

### Select Component Overview

The Select component enables users to choose from a list of options. It typically appears as a popup menu when the user clicks a select button, revealing a list of choices. This is commonly used for filtering, selecting items, or performing actions in forms and toolbars.

### Select Component Button Types

The Select button supports multiple visual types for various use cases:

- **Default:** Standard select button appearance.
- **With Label:** Displays a filter label next to the button, for clarity in filters or forms.
- **Tertiary:** Minimal style for less prominent actions.
- **Icon:** Select button with leading or trailing icon.
- **Icon Tertiary:** Minimal style with icon only.

![select-button-type.png](select-button-type.png)

### Select Component Button Sizes

Each select button size is designed to fit different form or action requirements, ensuring optimal readability and user experience.

![select-button-size.png](select-button-size.png)

### Select Component Button States

Select buttons provide clear feedback for user interaction:

- **Default:** Ready for interaction, no user action yet.
- **Hover:** Button visually responds (e.g., background or border colour change) when hovered.
- **Focused:** Shows focus ring when navigated by keyboard or clicked, supporting accessibility.
- **Disabled:** Visually muted and not available for interaction.

![select-button-state.png](select-button-state.png)

### Select Component Counter

A counter is a small numeric indicator displayed within the select button. It provides a visual cue about the count related to the button’s function, such as:

- Number of selected filters.
- Number of notifications.
- Items in a cart.

![select-button-counter.png](select-button-counter.png)

### Select Component Icon Only

Some select buttons use only an icon, clearly indicating the action or function without text (e.g., three-dots for more options, plus icon for adding items).

![select-button-icon-only.png](select-button-icon-only.png)

### Select Menu Component

When a select button is clicked, a popup menu appears showing available options.

#### Select Menu List Types

- **Dropdown Menu:** Standard dropdown for single or multiple selections.
- **Context Menu:** Popup with additional options or actions.
- **Select Multiple:** Allows users to choose multiple options.
- **Select Single:** Users can choose only one option.

![select-menu-list-type.png](select-menu-list-type.png)

#### Select Menu List States

Each menu option provides feedback based on interaction:

- **Default:** Ready for selection.
- **Hover:** Highlighted as the cursor moves over it.
- **Disabled:** Option is unavailable for selection.
- **Selected:** Indicates the option currently chosen.

![select-menu-list-state.png](select-menu-list-state.png)

#### Select Dropdown Menu Features

- **"More" Option Menu:** Provides additional actions.
- **Search Function:** Can be placed at the top or bottom of the menu for filtering options.
- **Clear Button:** In multi-select context menus, a "Clear" button appears overlapped at the bottom when at least one option is selected. It is hidden when no options are selected.

![context-menu-type.png](context-menu-type.png)

#### Grouped Context Menu Items

Menu items can be organized into groups for better navigation. Each group is separated and titled for clarity.

![grouped-context-menu-items.png](grouped-context-menu-items.png)

### Select Component Dropdown Menu Sizes

Context menus come in different sizes to match form needs and optimize readability:

![context-menu-size.png](context-menu-size.png)

### Select Component Demo

A demo should showcase interaction states, grouped items, search filtering, counter updates, and clear button behavior.

---

## MYDS Skip Link Component

### Skip Link Component Overview

The Skip Link component allows users to bypass repetitive navigation links and jump directly to the main content of a page, greatly enhancing accessibility for keyboard and screen reader users. It is typically hidden until focused and provides a fast way to reach the main section.

### Skip Link Component Usage Placement

- **Standard Placement:**  
  Every `gov.my` page should have a Skip Link placed immediately after the `<body>` tag.
- **With Cookie Banner:**  
  If a cookie banner is present, place the Skip Link immediately after the cookie banner.
- **With Header:**  
  If a `<header>` is present, wrap the Skip Link inside the header.  
  Do **not** wrap Skip Links inside `<nav>`.

Example HTML:

```html
<body>
  <!-- Cookie banner -->
  <cookie-banner />
  
  <!-- 1. Place skip link here -->
  <a href="#main-content" class="myds-skip-link">
    Skip to main content
  </a>
  
  <masthead />
  <nav />
  
  <!-- 2. Main content -->
  <div id="main-content" class="container">
    <input class="myds-search-bar" tabindex="0">
  </div>
</body>
```

- The Skip Link should point to the main content section's ID (e.g., `href="#main-content"`).
- To make any element focusable within the section, add `tabindex="0"`.

### Skip Link Component How It Works

- **Hidden by Default:**  
  The Skip Link is visually hidden until a user presses the `TAB` key after page load.
- **Focus State:**  
  When focused, the Skip Link appears in a fixed position at the top left of the screen.
- **Shadow:**  
  Apply a context-menu style shadow to indicate it is above other menus.
- **Dismissal:**  
  Clicking anywhere on the page hides the Skip Link button.

![skip-link-how-it-works.png](skip-link-how-it-works.png)

#### Keyboard Interaction

- **Press TAB:**  
  The Skip Link appears and receives focus.
- **Press ENTER:**  
  The Skip Link hides, the page scrolls to the main content section, and focus moves to the first focusable element (e.g., input, button) in the main content.
- **Press TAB Again:**  
  Focus moves sequentially to the next interactive elements on the page, following the natural tab order.

Example:

- **TAB → Enter:**  
  Skip to main content.
- **TAB → TAB → TAB ...:**  
  Focus sequentially on links/buttons in the navigation and main content.

![skip-link-press-tab.png](skip-link-press-tab.png)

### Skip Link Component Accessibility

- The Skip Link is essential for accessibility and should be implemented on all government web pages.
- It improves navigation efficiency for users relying on keyboards and assistive technologies.

---

## MYDS Summary List Component

### Summary List Component Overview

The Summary List component presents a concise overview of key information as a collection of key-value pairs, making it ideal for reviewing user responses or form data before completion or submission. Its style and structure are borrowed from the Table component for clarity and consistency.

### Summary List Component Anatomy

- **Key (`<dt>`):**  
  The first column displays the item or attribute being described. It acts as a label or identifier for the value in the next column.
- **Value (`<dd>`):**  
  The second column contains the corresponding data or details for each key. This is the main information that the summary list highlights.
- **Actions (optional) (`<dd>`):**  
  The third column, if present, provides interaction options (such as edit or delete), allowing users to take action on specific items in the list.

![summary-list-anatomy.png](summary-list-anatomy.png)

### Summary List Component Usage Examples

- **Subsidy Application Review:**  
  Summarizes details of a citizen's subsidy application, including personal information, submitted documents, and application status, before final submission.
- **Tax Filing Summary:**  
  Provides a summary of a citizen’s tax filing, including income details, deductions, and tax amount owed or refunded, for review before filing.
- **Permit Application Overview:**  
  Displays key information related to a government permit application, such as applicant details, permit type, and application fee, before proceeding to payment or approval.

![summary-list-usage.png](summary-list-usage.png)

---

## MYDS Switch Component

### Switch Component Overview

The Switch component allows users to toggle between two states, such as ON or OFF, providing an intuitive control for settings, features, and preferences in forms and interfaces.

### Switch Component Size

Switches are available in multiple sizes to suit different interface requirements:

- **Medium:** A compact design, ideal for interfaces with limited space.
- **Large:** A versatile size, fitting comfortably in most layouts.

![toggle-size.png](toggle-size.png)

### Switch Component State

Switches provide clear visual feedback for various user interactions:

- **Default:** The initial state before any interaction.
- **Hover:** Background colour changes to indicate interactivity.
- **Focused:** Highlighted when navigated via keyboard or mouse click, supporting accessibility.
- **Disabled:** The toggle is inactive and cannot be interacted with.

![toggle-state.png](toggle-state.png)

### Switch Component Checked State

Indicates whether the switch is ON or OFF:

- **False:** Initial state, switched OFF.
- **True:** Switched ON by the user.

![toggle-checked.png](toggle-checked.png)

### Switch Component Group

Switches can be paired with a setting label on the left, providing simple and intuitive control for toggling settings.

### Switch Component Label Size

The dimensions of the toggle and its label can be adjusted to fit different form sizes, maintaining readability and consistency across layouts.

![toggle-label-size.png](toggle-label-size.png)

---

## MYDS Table Component

### Table Component Overview

The Table component organizes information into rows and columns for easy readability and comparison. It accommodates various data types, including text, numbers, codes, call-to-action buttons, and links, enabling efficient presentation and analysis.

### Table Component Anatomy

- **Header:**  
  The title of each column, providing clear context for the data beneath.
- **Expand Column Button:**  
  Hidden by default, appears when the user hovers over a column. Clicking expands the column horizontally to reveal longer descriptions for improved readability.
- **Tooltip:**  
  Hovering on the tooltip icon shows explanatory text for the column’s purpose or data role.
- **Sort Column Button:**  
  Hidden by default, appears on hover. Clicking toggles sorting between ascending and descending order.
- **Cell for Text:**  
  Left-aligned by default for easy scanning.
- **Cell for Button:**  
  Contains action buttons aligned left, allowing users to take specific actions per row.
- **Cell for Status Pill:**  
  Displays a colour-coded status indicator (pill), providing a quick visual reference of each row’s status.
- **Skeleton Loader:**  
  An animated placeholder element that mimics the table layout while data is loading. This improves perceived loading time and reduces abrupt content transitions.

![table-anatomy.png](table-anatomy.png)

### Table Component Usage Examples

- **Cell with Text Aligned Center:**  
  Used for headings or key information for visual balance and emphasis.
- **Cell with Numbers Aligned Right:**  
  Right-aligns numerical data for easy comparison and ensures decimal points line up, especially in financial or statistical tables.
- **Cell with Row Span:**  
  Occupies two or more rows vertically, consolidating related data and reducing redundancy. Useful for representing information relevant to multiple rows and improving clarity.

![table-usage.png](table-usage.png)

### Table Component Expand Column

- When hovering over a column, the expand button appears.
- Clicking expands the column horizontally for longer content.
- When expanded, keep the header text underlined and show a collapse button to revert.

### Table Component Sort Column

- On hover, the sort button appears.
- By default, the first click sorts the column in descending order.
- The next click sorts in ascending order.
- When sorted, keep the header underlined and show the sort button to indicate active sorting.

---

## MYDS Tabs Component

### Tabs Component Overview

The Tabs component enables users to navigate between different views or sections within the same context by clicking or tapping on a tab. It improves organization and user experience by grouping related content and providing a clear, interactive way to switch between panels.

### Tabs Component Type

Tabs are available in several visual styles to suit different contexts and branding needs:

- **Pill:**  
  Features a pill-shaped design. The active tab is highlighted for clear selection.
- **Enclosed:**  
  Encases the entire tab group in a container, with the active tab highlighted using a distinct pill style.
- **Line:**  
  Uses a line beneath the active tab, typically in a brand color, to indicate which tab is selected.

![tabs-type.png](tabs-type.png)

### Tabs Component Size

Tabs come in multiple sizes to fit various UI layouts and devices:

- **Small:**  
  Ideal for small components or mobile devices.
- **Medium:**  
  Commonly used for medium or large components, such as on tablets and desktops.

![tabs-size.png](tabs-size.png)

### Tabs Component State

Tabs provide visual cues based on user interaction, accessibility, and selection:

- **Default:**  
  The initial state, ready for user action but not currently interacted with.
- **Focused:**  
  Indicates the tab is selected or active, usually via keyboard navigation (TAB) or mouse click. This state is vital for accessibility.
- **Hover:**  
  Visual feedback when the mouse pointer moves over a tab, highlighting its interactivity.
- **Active:**  
  The tab is clicked and selected, showing it is currently in view.
- **Active + Focused:**  
  Occurs when a user clicks again on the active tab, confirming its selection and focus.

![tabs-state.png](tabs-state.png)

### Tabs Component Leading Icon

A leading icon can be placed before the text inside a tab, visually enhancing the button and indicating the tab's purpose at a glance.

- Common examples: Table icon, Profile icon, Settings icon, List icon

![tabs-leading-icon.png](tabs-leading-icon.png)

### Tabs Component Counter

A counter can be positioned after the text within a tab. It typically displays the number of new or current items in that tab, such as unread messages or items in a list.

![tabs-counter.png](tabs-counter.png)

---

## MYDS Tag Component

### Tag Component Overview

A Tag is used to display the current state or status of an item, process, or entity. Tags are visually distinct elements that help users quickly identify information such as status, category, or type within lists, cards, and interfaces.

### Tag Component Status Types

Tags are available in various status types to communicate different states:

- **Gray:** Default status tag, used for neutral or inactive states.
- **Brand:** Indicates a tag related to a product's brand.
- **Success:** Represents a successful status or outcome.
- **Danger:** Indicates a critical issue or error.
- **Warning:** Alerts the user to a potential issue or cautionary state.

![tag-status-type.png](tag-status-type.png)

### Tag Component Styles

Tags provide two distinct styles for flexible placement in different UI scenarios:

- **Style 1:**  
  Used to display a status independently, often positioned absolutely on an image or container. Features a fully rounded border radius (`9999px`) for a "pill" appearance.
- **Style 2:**  
  Used to display a status inline, positioned side-by-side with text. Features a slightly rounded border radius (`6px`), blending into text or lists.

![tag-style.png](tag-style.png)

### Tag Component Sizes

Tags come in multiple sizes to suit different components and device contexts:

- **Small:** For small components or on mobile devices.
- **Medium:** For medium-sized components or tablets.
- **Large:** For large components or on desktops and laptops.

![tag-size.png](tag-size.png)

### Tag Component Status Dot (Optional)

An optional status dot provides a compact visual indicator of the current state or status of an item. The dot size adjusts according to the tag size:

- **Small:** 6x6px
- **Medium:** 8x8px
- **Large:** 10x10px

![tag-status-dot.png](tag-status-dot.png)

---

## MYDS Task List Component

### Task List Component Overview

The Task List component presents information or calls to action in a structured format, making it easy to distinguish between completed and pending tasks. It enables users to track progress and prioritize actions efficiently.

### Task List Component Anatomy

- **Title:**  
  Provides the task title, describing what needs to be done.
- **Hint Text:**  
  Clearly and briefly describes the task requirements or context to guide the user.
- **Status (Completed):**  
  Uses the UI/Tag component to visually indicate a completed task.
- **Status (Incomplete):**  
  Uses the UI/Tag component to indicate an incomplete or pending task.

![task-list-anatomy.png](task-list-anatomy.png)

### Task List Component State

Tasks in the list provide interactive visual feedback:

- **Default:**  
  No background fill is applied by default.
- **Hover:**  
  When hovered, adds `bg-gray-50` and underlines the title to indicate interactivity.

![task-list-state.png](task-list-state.png)

### Task List Component Usage Example

The Task List is suitable for presenting user actions, step-by-step processes, or requirements on a page. Each item can be marked as completed or pending using the Tag component for clear progress tracking.

### Task List Component Grouping Tasks

When multiple tasks are present, grouping them helps users better understand and plan their next steps. Organize related tasks into separate task lists, each with a clear and concise heading that explains the task group.

![task-list-usage.png](task-list-usage.png)

---

## MYDS Toast Component

### Toast Component Overview

The Toast component provides non-intrusive notifications that appear temporarily on the screen to give feedback or alert users about events. Toasts enhance user experience by delivering timely information without disrupting workflow.

### Toast Component Anatomy

- **Leading Icon:**  
  Represents the type of toast, such as success, error, or info.
- **Title:**  
  The primary message or key feedback of the toast.
- **Description:**  
  Additional context or details about the event.
- **Progress Bar:**  
  A visual indicator that defaults to a 3-second duration, showing how long before the toast auto-dismisses.
- **Optional Close Icon:**  
  Allows users to manually dismiss the toast.

![toast-anatomy.png](toast-anatomy.png)

### Toast Component Types

Toasts can represent different types of notifications:

- **Success:**  
  Indicates a successful action, such as submitting a form or completing a request.
- **Warning:**  
  Alerts the user to take notice, typically for reminders or required actions.
- **Information:**  
  Provides extra details or keeps the user informed.
- **Error:**  
  Notifies the user about an error that must be resolved.

![toast-type.png](toast-type.png)

### Toast Component Close Icon State

- **Default State:**  
  During the default 3-second duration, the Close Icon is enabled, allowing manual dismissal.
- **Disabled State:**  
  Once the toast is being dismissed, the Close Icon is disabled.

![toast-close-icon.png](toast-close-icon.png)

### Toast Component Location

- **Desktop/Tablet:**  
  Toasts are fixed at the bottom right of the screen, with a 24px margin. Minimum width is 300px, maximum width is 600px.
- **Mobile:**  
  Toasts are centered at the bottom of the screen, with 18px margins on both sides, stretching to fit screen width.

![toast-location.png](toast-location.png)

### Toast Component with Progress Bar

- **Progress Bar Animation:**  
  For successful scenarios, a progress bar appears with a 200ms ease-in-out slide up animation.
  - The loading bar animates from 100% to 0% width over 3 seconds.
  - The toast auto-dismisses after 3 seconds with a 200ms ease-in-out slide down animation, along with the progress bar.

![toast-progress-bar.png](toast-progress-bar.png)

---

## MYDS Tooltip Component

### Tooltip Component Overview

The Tooltip component provides brief, contextual information when users hover or focus on an element, enhancing clarity without cluttering the interface. Tooltips are typically used to explain icons, buttons, or any interactive elements whose meaning might not be immediately clear.

### Tooltip Component Anatomy

- **Trigger Area:**  
  The region that activates the tooltip when hovered or focused. It is recommended to widen the trigger space for easier user interaction.
- **Tooltip:**  
  Displays additional context or guidance, helping users understand the function or meaning of an element.

![tooltip-anatomy.png](tooltip-anatomy.png)

### Tooltip Component Minimum Trigger Size

- **Small Trigger Area (Don't):**  
  Avoid using a small trigger area, as it makes accessing the tooltip difficult and negatively affects user experience.
- **Large Trigger Area (Do):**  
  Use a larger trigger area to improve usability, making it easier for users to hover and access the tooltip content.

![tooltip-trigger-size.png](tooltip-trigger-size.png)

### Tooltip Component Direction

Tooltips can appear in different directions relative to the trigger area:

- **Left**
- **Top**
- **Bottom**
- **Right**

Always choose the direction that ensures the tooltip remains visible on the screen.  
*Note: The blue outline in illustrations shows the trigger area. Remove it in the actual UI.*

![tooltip-direction.png](tooltip-direction.png)

### Tooltip Component Alignment

Tooltip alignment ensures content stays within the visible screen area:

- **Align Left:**  
  When the trigger is at the left edge of the screen, keeps the tooltip visible.
- **Align Center:**  
  For triggers in the central area, centers the tooltip.
- **Align Right:**  
  When the trigger is at the right edge, keeps the tooltip visible.

Always select an alignment that maintains visibility within the screen boundaries.  
*Note: The blue outline is for demo purposes only.*

![tooltip-alignment.png](tooltip-alignment.png)

### Tooltip Component Description Length

- The tooltip's maximum description length is **250px** to prevent overflow and maintain readability.
- Always ensure the tooltip remains visible within the screen area.
- *Note: The blue outline is used to illustrate the trigger area in diagrams only.*

![tooltip-description.png](tooltip-description.png)

### Tooltip Component Demo

- **Small Trigger Area:**  
  Demonstrates poor usability—harder for users to access tooltips.
- **Large Trigger Area:**  
  Demonstrates improved usability—easier for users to access tooltips.

---

> By following MYDS, agencies can deliver digital services that embody transparency, inclusivity, and efficiency, ultimately improving access and experience for all Malaysians.
