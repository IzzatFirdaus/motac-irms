{{--
    MYDS-compliant dropdown component for navigation menus.
    Applies:
    - MYDS dropdown anatomy and ARIA accessibility
    - Consistent spacing, color, focus ring, and minimal/multi-level menu variants
    - Keyboard navigation and WCAG compliance (Principles 1, 5, 7, 13, 16, 17)
    - Responsive alignment and semantic roles

    Props:
    - $id: string - Dropdown ID (default: 'navbarDropdown')
    - $align: string - Dropdown alignment: 'start', 'end', 'center' (default: 'end')
    - $triggerClass: string - CSS classes for trigger button

    Slots:
    - trigger: Dropdown trigger content
    - content: Dropdown menu content

    Usage:
    <x-dropdown>
        <x-slot name="trigger">
            {{ Auth::user()->name }}
        </x-slot>
        <x-slot name="content">
            <x-dropdown-link href="{{ route('profile') }}">Profile</x-dropdown-link>
            <x-dropdown-link href="{{ route('logout') }}">Logout</x-dropdown-link>
        </x-slot>
    </x-dropdown>

    Dependencies: Bootstrap 5, MYDS CSS, ARIA attributes
--}}
@props([
    'id' => 'navbarDropdown',
    'align' => 'end', // 'start', 'end', 'center'
    'triggerClass' => 'myds-dropdown-trigger nav-link dropdown-toggle'
])

<li class="nav-item myds-dropdown dropdown" role="presentation">
  {{-- Dropdown Trigger (button or link, always with aria-haspopup/aria-expanded) --}}
  <a
    id="{{ $id }}"
    {!! $attributes->merge([
        'class' => $triggerClass,
        'href' => '#',
        'role' => 'button',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
        'tabindex' => '0'
    ]) !!}
    data-bs-toggle="dropdown"
    @keydown.enter.prevent="this.click()" {{-- Keyboard accessibility: open on Enter key --}}
  >
    {{ $trigger }}
    {{-- Add dropdown chevron icon for clarity --}}
    <i class="bi bi-chevron-down ms-1" aria-hidden="true"></i>
  </a>

  {{-- Dropdown Menu --}}
  <div class="myds-dropdown-menu dropdown-menu dropdown-menu-{{ $align }} {{ $attributes->get('menu-class', '') }}"
       aria-labelledby="{{ $id }}"
       role="menu"
       tabindex="-1"
  >
    {{ $content }}
  </div>
</li>

{{--
    MYDS Notes:
    - Uses .myds-dropdown and .myds-dropdown-menu for MYDS styling, spacing, border radius, shadow, and color tokens.
    - Trigger is accessible (role="button", aria-haspopup, aria-expanded, tabindex).
    - Chevron icon visually indicates menu state.
    - Dropdown menu is keyboard accessible and ARIA labeled.
    - Responsive alignment: use 'start', 'end', 'center' for menu placement.
    - Follows MyGOVEA principles: Citizen-centric (clarity, accessibility), Minimalism, Consistency, Clear feedback, Hierarchy, UI/UX, Error prevention.
--}}
