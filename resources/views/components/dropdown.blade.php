{{--
    resources/views/components/dropdown.blade.php

    Bootstrap dropdown component for navigation menus.
    Provides flexible dropdown functionality with customizable trigger and content.

    Props:
    - $id: string - Dropdown ID (default: 'navbarDropdown')
    - $align: string - Dropdown alignment: 'start', 'end' (default: 'end')
    - $triggerClass: string - CSS classes for trigger button

    Slots:
    - $trigger: Dropdown trigger content
    - $content: Dropdown menu content

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

    Dependencies: Bootstrap 5
--}}
@props(['id' => 'navbarDropdown', 'align' => 'end', 'triggerClass' => 'nav-link dropdown-toggle'])

<li class="nav-item dropdown">
  {{-- Dropdown Trigger --}}
  <a id="{{ $id }}" {!! $attributes->merge(['class' => $triggerClass]) !!} href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    {{ $trigger }}
  </a>

  {{-- Dropdown Menu --}}
  <div class="dropdown-menu dropdown-menu-{{ $align }} {{ $attributes->get('menu-class', '') }}" aria-labelledby="{{ $id }}">
    {{ $content }}
  </div>
</li>
