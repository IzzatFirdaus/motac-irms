{{--
    MYDS-compliant dropdown link component.
    - Uses MYDS typography, spacing, color tokens, and accessibility standards.
    - Follows MyGOVEA principles: Minimal UI, accessibility, clear feedback, keyboard navigation, ARIA roles.
    - For use in dropdown menus (nav, user menu, actions, etc.)
    - Props: all standard <a> attributes; slot for link text.
    - Usage:
        <x-dropdown-link href="{{ route('profile') }}">{{ __('Profile') }}</x-dropdown-link>
        <x-dropdown-link href="{{ route('settings') }}" class="text-danger">{{ __('Settings') }}</x-dropdown-link>
--}}

<a
    {{ $attributes->merge([
        'class' => 'dropdown-item myds-dropdown-link d-flex align-items-center px-3 py-2 fw-medium text-body myds-transition',
        'role' => 'menuitem',
        'tabindex' => '0',
    ]) }}
>
    {{ $slot }}
</a>

{{--
    MYDS dropdown link anatomy:
    - px-3 py-2: MYDS spacing tokens (see MYDS-Develop-Overview.md)
    - fw-medium: MYDS recommended font weight for menu items
    - text-body: Uses MYDS color token for dropdown text
    - myds-transition: Ensures smooth hover/focus animation (see MYDS motion tokens)
    Accessibility:
    - role="menuitem": ARIA role for dropdown menu items
    - tabindex="0": Keyboard accessibility for focus/activation
    - All links should be focusable and respond to keyboard navigation
    MyGOVEA Principles referenced:
    - Minimalis dan Mudah, Seragam, Paparan/Menu Jelas, Kawalan Pengguna, Tipografi, Pencegahan Ralat, Panduan dan Dokumentasi
--}}
