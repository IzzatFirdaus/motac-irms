{{--
    resources/views/components/switchable-team.blade.php

    MYDS-compliant: Team switch dropdown component (currently disabled).
    Updated for MYDS grid, accessibility, and MyGOVEA 18 principles.

    Principles applied:
    - Berpaksikan Rakyat: Clear status, action feedback
    - Minimalis & Mudah: Simple, clear UI
    - Seragam: MYDS classes for consistent styling
    - Struktur Hierarki: Clear layout and grouping
    - Komponen UI/UX: Modular, accessible dropdown
    - Tipografi: MYDS fonts and sizes
    - Kawalan Pengguna: Obvious disabled state, clear messaging
    - Pencegahan Ralat: Disabled control and warning message
    - Panduan & Dokumentasi: Inline comments for maintainers

    Usage:
    <x-switchable-team :team="$team" />
--}}

@props(['team', 'component' => 'dropdown-link'])

@php
    // Import Auth facade for authentication checks
    use Illuminate\Support\Facades\Auth; // NOTE: PHP0413 'unknown class: Illuminate\Support\Facades\Auth' is a static analyzer limitation; this works in Laravel Blade at runtime.

    // Helper method to determine if the current user is a member of this team
    $isActiveTeam = false;
    if (Auth::check() && method_exists(Auth::user(), 'current_team_id')) {
        // Compare user's current_team_id to this team id (standard Laravel Jetstream approach)
        $isActiveTeam = Auth::user()->current_team_id == $team->id; // NOTE: PHP0416 'undefined property: User::$current_team_id' is a static analyzer limitation; this works if property exists in your User model.
    }
@endphp

<form method="POST" action="{{-- route('current-team.update') --}}" id="switch-team-form-{{ $team->id }}">
    @method('PUT')
    @csrf

    <input type="hidden" name="team_id" value="{{ $team->id }}">

    {{-- Dropdown item styled for MYDS --}}
    <x-dynamic-component
        :component="$component"
        href="#"
        onclick="alert('Fungsi penukaran pasukan tidak aktif pada masa ini. Sila hubungi pentadbir sistem.'); return false;"
        title="{{ __('Fungsi penukaran pasukan tidak aktif') }}"
        class="myds-dropdown-item d-flex align-items-center px-3 py-2"
        aria-disabled="true"
        tabindex="-1"
    >
        <div class="d-flex align-items-center w-100">
            @if ($isActiveTeam)
                {{-- Active team indicator: MYDS icon + color --}}
                <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 1.1rem;" aria-label="{{ __('Pasukan aktif') }}"></i>
            @else
                {{-- Spacer for non-active team (preserves alignment) --}}
                <span style="width: calc(1.1rem + 0.5rem); display: inline-block;"></span>
            @endif

            {{-- Team name, truncated for overflow, with MYDS typography --}}
            <span class="text-truncate myds-font-inter myds-font-md" style="letter-spacing: 0.01em;">
                {{ $team->name }}
            </span>
        </div>
    </x-dynamic-component>
</form>

{{--
    Documentation notes:
    - Uses MYDS typography and spacing for dropdown items.
    - Disabled state uses aria-disabled and tabindex="-1" for accessibility.
    - Icon provides status clarity and aligns with MYDS status badge conventions.
    - Truncation ensures data is readable and fits grid.
    - Alert prevents accidental action and provides clear feedback (error prevention).
    - Modular structure for easy future enabling of team switching.
    - Auth::user()->current_team_id is the recommended way to check active team in Jetstream/Laravel.
--}}
