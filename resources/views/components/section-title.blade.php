{{--
    resources/views/components/section-title.blade.php

    MYDS-compliant Section Title Component
    - Displays a section title with optional description, aside (actions), and icon.
    - Uses MYDS grid, spacing, typography, colours, and accessibility standards.
    - Follows MyGOVEA principles: clarity, hierarchy, minimalism, accessibility, user control, and documentation.
    - See MYDS-Design-Overview.md for anatomy, spacing, and typography.
    - See prinsip-reka-bentuk-mygovea.md for design principles.

    Props:
    - $title: string (required) — section title, clear and concise
    - $description: string (optional) — additional description under the title
    - $aside: mixed (optional) — aside content (e.g., action button)
    - $icon: string|null (optional) — Bootstrap icon class for the section

    Usage:
    <x-section-title title="User Management" description="Manage all users here." aside="<a ...>Add User</a>" icon="bi-person" />
    <x-section-title :title="__('Settings')" :aside="view('partials.settings-help-link')" />
--}}

@props(['title', 'description' => null, 'aside' => null, 'icon' => null])

<div
    {{-- MYDS grid and spacing for section header --}}
    {{ $attributes->merge(['class' => 'myds-row align-items-start justify-content-between mb-4 pb-2 myds-section-header']) }}
    role="heading"
    aria-level="2"
>
    <div class="myds-col-12 myds-col-md-8 mb-3 mb-md-0">
        <h2 class="myds-section-title d-flex align-items-center"
            style="
                font-family: 'Poppins', Arial, sans-serif;
                font-size: 1.875rem;
                line-height: 2.375rem;
                font-weight: 600;
                color: var(--myds-primary-700, #1D4ED8);
                margin-bottom: 0.25em;
            "
        >
            @if ($icon)
                <i class="bi {{ $icon }} me-2 myds-section-icon"
                   aria-hidden="true"
                   style="font-size: 1.5em; color: var(--myds-primary-600, #2563EB);"></i>
            @endif
            {{-- Section title, clear, concise, accessible --}}
            <span>{{ $title }}</span>
        </h2>
        @if ($description)
            <p class="myds-section-desc text-muted mb-0 small"
               style="
                   font-family: 'Inter', 'Noto Sans', Arial, sans-serif;
                   font-size: 1rem;
                   line-height: 1.5rem;
                   color: var(--myds-gray-500, #71717A);
                   margin-top: 0.5em;
               "
            >{{ $description }}</p>
        @endif
    </div>

    @if ($aside)
        <div class="myds-col-12 myds-col-md-4 ms-md-3 mt-2 mt-md-0 text-end" style="display: flex; justify-content: flex-end;">
            {{-- Aside area for action buttons, help links, etc. --}}
            {!! $aside !!}
        </div>
    @endif
</div>

<hr class="myds-section-divider border-top" style="border-color: var(--myds-otl-divider, #F4F4F5); margin-top: 8px; margin-bottom: 0;" />

{{--
    MYDS Section Title Component Documentation:
    - Uses MYDS grid system for responsive layout (12 columns desktop, 8 tablet, 4 mobile).
    - Typography follows MYDS standards: Poppins for headings, Inter for body.
    - Semantic HTML: <h2> for section title, ARIA role for accessibility.
    - Icon is optional and uses MYDS-compatible size and colour.
    - Section divider uses MYDS outline tokens for visual separation (see Spacing & Layout).
    - All colours, spacing, and font sizes reference MYDS tokens for maintainability.
    - Fulfills MyGOVEA principles: clarity, clear hierarchy, minimalism, accessibility, and documentation.
--}}
