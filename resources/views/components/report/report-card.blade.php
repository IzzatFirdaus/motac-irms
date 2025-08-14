{{--
    resources/views/components/report/report-card.blade.php

    MYDS-compliant reusable Report Card Component
    - For dashboard/report tiles with icon, title, description, and a view button.
    - Follows MYDS component anatomy, grid, typography, color tokens, radius, shadow, and accessibility.
    - Implements MyGOVEA Principles: citizen-centric, minimal, consistent, clear feedback, accessible, error prevention, documentation.

    Props:
    - $icon: SVG/icon class (e.g. 'bi-bar-chart')
    - $title: Card title (string)
    - $description: Card description (string)
    - $route: Route/URL for the "View Report" button

    Usage Example:
    <x-report.report-card :icon="'bi-bar-chart'" :title="'Some Title'" :description="'Desc...'" :route="route('something')" />
--}}

@props([
    'icon' => 'bi-bar-chart',
    'title' => '',
    'description' => '',
    'route' => '#'
])

<div class="myds-col-4">
    <div
        class="myds-card myds-shadow-card myds-radius-l h-100 d-flex flex-column"
        role="region"
        aria-label="{{ $title }}"
        tabindex="0"
        style="
            background: var(--myds-bg-white, #FFF);
            box-shadow: 0 2px 6px rgba(0,0,0,0.05), 0 6px 24px rgba(0,0,0,0.05);
            border-radius: 12px;
        "
    >
        <div class="myds-card-body d-flex flex-column p-4" style="flex:1 1 auto;">
            {{-- Icon at the top, uses MYDS icon size and color --}}
            <div class="mb-2" aria-hidden="true">
                <i class="bi {{ $icon }}"
                   style="font-size:2.2rem; color:var(--myds-primary-600, #2563EB);"></i>
            </div>
            {{-- Title, MYDS heading (Poppins, semibold, correct size) --}}
            <h3
                class="myds-card-title mb-2"
                style="font-family:'Poppins',Arial,sans-serif; font-size:1.5rem; font-weight:600; color:var(--myds-primary-800, #1E40AF); line-height:2rem;">
                {{ $title }}
            </h3>
            {{-- Description, MYDS body text (Inter, muted, correct size/spacing) --}}
            <p
                class="myds-card-desc mb-4"
                style="font-family:'Inter',Arial,sans-serif; font-size:1rem; color:var(--myds-gray-500, #71717A); line-height:1.5; margin-bottom:1.5rem;">
                {{ $description }}
            </p>
            {{-- "View Report" Button at the bottom (MYDS Primary, accessible, semantic icon, focus ring) --}}
            <a
                href="{{ $route }}"
                class="myds-btn myds-btn-primary btn-sm mt-auto d-inline-flex align-items-center"
                style="
                    background: var(--myds-primary-600, #2563EB);
                    color: #FFF;
                    border-radius: 8px;
                    font-family:'Inter',Arial,sans-serif;
                    font-weight:600;
                    padding: 0.5em 1.2em;
                    outline: none;
                    border: none;
                    box-shadow: 0 1px 3px rgba(25,99,235,0.07);
                    transition: background 0.2s;
                "
                aria-label="Lihat Laporan: {{ $title }}"
                tabindex="0"
                onfocus="this.style.outline='2px solid var(--myds-primary-400, #6394FF)';this.style.outlineOffset='2px';"
                onblur="this.style.outline='none';"
            >
                <i class="bi bi-eye-fill me-1" aria-hidden="true"></i>
                <span>
                    {{-- Use raw string for button label to avoid PHP error if __() is unavailable --}}
                    View Report
                </span>
            </a>
        </div>
    </div>
</div>

{{--
    === MYDS & MyGOVEA Principles Applied ===
    - Uses MYDS grid system (.myds-col-4), shadow, radius, color tokens.
    - Typography: Poppins for headings, Inter for body, correct sizes/weights.
    - Accessible: ARIA attributes, keyboard navigation, focus ring.
    - Minimalist: Only essential info shown, avoids clutter.
    - Consistent: All UI elements use MYDS design tokens and anatomy.
    - Citizen-centric: Clear title, description, and action.
    - Error prevention: Button is clear, semantic, and accessible.
    - Documentation: Comments explain structure, anatomy, and standards mapping.
--}}
