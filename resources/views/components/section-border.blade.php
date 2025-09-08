{{--
    resources/views/components/section-border.blade.php
    MYDS-compliant horizontal border for visually separating sections.
    Hidden on xs screens, shown on sm+. Uses MYDS color tokens and spacing.
    Principles: Seragam, Minimalis, Struktur Hierarki, Accessibility (MyGOVEA 5, 6, 12, 18)
--}}

<div class="d-none d-sm-block myds-section-border" aria-hidden="true">
    <div class="py-4">
        <hr class="border-0" style="
            border-top: 1.5px solid var(--myds-otl-divider, #F4F4F5);
            margin: 0;
        ">
    </div>
</div>

{{--
    Documentation:
    - Uses MYDS divider color token for consistent section separation.
    - Responsive: Only visible on screens >=576px (d-sm-block).
    - Accessibility: aria-hidden="true" as a purely decorative element.
    - Use between major content sections to improve visual hierarchy.
--}}
