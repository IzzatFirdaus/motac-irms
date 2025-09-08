{{--
    resources/views/components/application-mark.blade.php

    MYDS-compliant MOTAC application mark/icon.
    - Uses currentColor for dynamic theming and accessibility.
    - Scalable SVG, responsive for any container size.
    - Follows MyGOVEA Principle 13 (UI Component Consistency), 14 (Typography visual balance), and 5 (Minimal, clear interface).
    - Ensures contrast and clarity for all device themes (light/dark).
    - Use for icon-only branding in navigation, sidebar, small cards, badges, etc.
--}}

<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true" focusable="false"
    {{ $attributes->merge([
        'role' => 'img',
        'tabindex' => '-1',
        'class' => 'myds-logo-mark', // Add a class for consistent theming if needed
    ]) }}>
    {{--
        Left main curve - represents foundation/legacy.
        Right main curve - represents innovation/progress.
        Both use currentColor for dynamic theme support.
        Responsive to parent's color (MYDS: txt-primary, txt-black-900, etc).
    --}}
    <path d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0a23.891 23.891 0 0113.997 4.502c-.2 17.907-11.097 33.245-26.602 39.926z"
        fill="currentColor"/>
    <path d="M14.134 45.885A23.914 23.914 0 0024 48c13.255 0 24-10.745 24-24 0-3.516-.756-6.856-2.115-9.866-4.659 15.143-16.608 27.092-31.75 31.751z"
        fill="currentColor"/>
    {{--
        Accessibility note:
        This mark has no interactive role; aria-hidden ensures screen readers skip it.
        For logos with branding, use <title> or aria-label.
    --}}
</svg>
