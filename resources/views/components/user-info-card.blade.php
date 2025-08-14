{{--
    MYDS-compliant User Info Card Component
    resources/views/components/user-info-card.blade.php

    Displays a user's profile information using MYDS card, grid, and status badge.
    - Applies MYDS grid (12-8-4), color tokens, spacing, radius, and typography.
    - Accessible, responsive, and hierarchical per MYDS and MyGOVEA principles.
    - Shows avatar, name, email, department, position, grade, and status badge.
    - Optionally renders action buttons (edit, etc.).
    - All colours and typography follow MYDS variables.css.
    - Comments added to clarify anatomy and compliance.

    Props:
    - $user: User object containing profile information (required)
    - $showActions: Boolean to show action buttons (edit, etc.)

    Usage:
    <x-user-info-card :user="$user" />
    <x-user-info-card :user="$user" :showActions="true" />

    MYDS Principles: Citizen-centric, minimal, clear hierarchy, consistent, accessible, flexible, clear feedback.
--}}

@props(['user', 'showActions' => false])

@php
    // Blade's built-in asset() and route() helpers are only available in Blade views, not in compiled PHP files.
    // For documentation: If using pure PHP rendering, replace asset() and route() with correct URL generation logic.
    // In Blade, asset('...') generates a full URL for static assets.
    // In Blade, route('...') generates a URL for named routes.
    // We check if these helpers exist, and fallback to strings if not (for static analysis).

    $avatarUrl = isset($user->profile_photo_url) && $user->profile_photo_url
        ? $user->profile_photo_url
        : (function_exists('asset') ? asset('assets/img/avatars/default-avatar.png') : '/assets/img/avatars/default-avatar.png');

    $editUrl = function_exists('route')
        ? route('settings.users.edit', ['user' => $user->id])
        : "/settings/users/{$user->id}/edit";
@endphp

<div class="myds-container">
    <div class="card shadow-card mb-3 myds-row" style="border-radius: 12px; background: var(--myds-bg-white);">
        <div class="card-body d-flex align-items-center p-4">
            {{-- User Avatar: Fallback to default, full radius, proper sizing --}}
            <div class="me-4">
                <img src="{{ $avatarUrl }}"
                     alt="{{ $user->name }} avatar"
                     class="rounded-circle shadow"
                     style="width: 64px; height: 64px; object-fit: cover; border: 2px solid var(--myds-primary-200); background: var(--myds-bg-white);">
            </div>
            <div class="flex-grow-1">
                {{-- Name (MYDS H5), Email, Department, Position --}}
                <h5 class="mb-2 fw-semibold" style="font-family: 'Poppins', Arial, sans-serif; font-size: 1rem; color: var(--myds-primary-800);">
                    {{ $user->name }}
                </h5>
                <dl class="mb-0">
                    {{-- Email --}}
                    @if($user->email)
                        <div class="mb-1 d-flex align-items-center">
                            <i class="bi bi-envelope me-1 text-primary" aria-hidden="true"></i>
                            <span class="text-muted" style="font-family: 'Inter', Arial, sans-serif; font-size: 0.92rem;">{{ $user->email }}</span>
                        </div>
                    @endif
                    {{-- Department --}}
                    @if($user->department)
                        <div class="mb-1 d-flex align-items-center">
                            <i class="bi bi-building me-1 text-secondary" aria-hidden="true"></i>
                            <span style="font-family: 'Inter', Arial, sans-serif; font-size: 0.92rem;">{{ $user->department->name }}</span>
                        </div>
                    @endif
                    {{-- Position and Grade --}}
                    @if($user->position)
                        <div class="mb-1 d-flex align-items-center">
                            <i class="bi bi-person-badge me-1 text-info" aria-hidden="true"></i>
                            <span style="font-family: 'Inter', Arial, sans-serif; font-size: 0.92rem;">
                                {{ $user->position->name }}{{ $user->grade ? ' ('.$user->grade->name.')' : '' }}
                            </span>
                        </div>
                    @endif
                </dl>
                {{-- Status Badge (MYDS pill/tag) --}}
                <div class="mt-2">
                    <x-user-status-badge :status="$user->status" />
                </div>
            </div>
            @if($showActions)
                <div class="ms-4 d-flex flex-column align-items-end" style="min-width: 110px;">
                    {{-- Example edit button, MYDS secondary button --}}
                    <a href="{{ $editUrl }}"
                       class="btn btn-outline-secondary btn-sm mb-2 d-inline-flex align-items-center"
                       style="font-family: 'Inter', Arial, sans-serif; border-radius: 8px;">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                    </a>
                    {{-- Other actions can be added here following MYDS button anatomy --}}
                </div>
            @endif
        </div>
    </div>
</div>

{{--
    === MYDS Compliance Notes ===
    - Card uses MYDS shadow, border radius, and background tokens.
    - Avatar uses full radius and brand border for visual hierarchy.
    - Content uses MYDS typography: Poppins for name/title, Inter for details.
    - Grid is responsive using myds-container and myds-row (12-8-4).
    - All icons use Bootstrap Icons and have aria-hidden for accessibility.
    - Status badge uses <x-user-status-badge> for color and semantic status.
    - Action buttons use MYDS secondary button anatomy.
    - Layout and spacing follow MYDS spacing tokens (p-4, mb-2, me-4, etc.).
    - If user data is missing, fallback values are omitted for minimalism.
    - Follows MyGOVEA principles: citizen-centric, minimal, clear feedback, hierarchy, flexibility, accessibility.
--}}
