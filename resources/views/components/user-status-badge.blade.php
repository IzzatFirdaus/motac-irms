{{--
    resources/views/components/user-status-badge.blade.php

    MYDS-compliant user status badge component.
    Shows user status (active/inactive/suspended/etc) as a badge using MYDS color tokens,
    accessible icon, and semantic label.

    - MYDS: Uses color tokens (success, secondary, warning, info, danger, dark)
    - Accessibility: Semantic text, role="status", high contrast, icon
    - Typography: Inter for text, consistent sizing
    - Radius: Pill for rounded edge per MYDS
    - Principles: Citizen-centric, minimal, clear feedback, accessibility, MyGOVEA #1, #5, #7, #14, #16, #17

    Props:
    - $status: string representing user status, e.g. "active", "inactive", "suspended"

    Usage:
    <x-user-status-badge status="active" />
    <x-user-status-badge :status="$user->status" />
--}}

@props(['status'])

@php
    // Map status to MYDS color tokens and labels
    $statusMap = [
        'active'    => ['class' => 'myds-badge myds-bg-success-500 myds-txt-white',         'label' => __('Aktif'),         'icon' => 'bi-check-circle-fill'],
        'inactive'  => ['class' => 'myds-badge myds-bg-gray-400 myds-txt-white',            'label' => __('Tidak Aktif'),   'icon' => 'bi-slash-circle-fill'],
        'suspended' => ['class' => 'myds-badge myds-bg-warning-400 myds-txt-black-900',     'label' => __('Digantung'),     'icon' => 'bi-pause-circle-fill'],
        'pending'   => ['class' => 'myds-badge myds-bg-info-500 myds-txt-white',            'label' => __('Menunggu'),      'icon' => 'bi-clock-history'],
        'locked'    => ['class' => 'myds-badge myds-bg-danger-500 myds-txt-white',          'label' => __('Terkunci'),      'icon' => 'bi-lock-fill'],
        'deleted'   => ['class' => 'myds-badge myds-bg-dark myds-txt-white',                'label' => __('Dihapus'),       'icon' => 'bi-x-circle-fill'],
        // Default fallback: dark badge, show status string
    ];
    $badge = $statusMap[$status] ?? [
        'class' => 'myds-badge myds-bg-gray-700 myds-txt-white',
        'label' => __(ucfirst($status)),
        'icon'  => 'bi-circle-fill'
    ];
@endphp

<span
    class="{{ $badge['class'] }} px-3 py-1 rounded-pill align-middle d-inline-flex align-items-center gap-1"
    role="status"
    aria-label="{{ $badge['label'] }}"
    tabindex="0"
>
    {{-- Status icon: MYDS icon system, accessible --}}
    <i class="bi {{ $badge['icon'] }}" style="font-size: .8em;" aria-hidden="true"></i>
    {{ $badge['label'] }}
</span>

{{--
    === MYDS Component Documentation ===
    - Color tokens use MYDS colour reference (see variables.css, MYDS-Colour-Reference.md)
    - Accessible: role="status", aria-label for screen readers, keyboard focusable (tabindex)
    - Typography: Inter, semibold
    - Spacing: px-3 py-1, gap-1 (MYDS spacing tokens)
    - Radius: rounded-pill (MYDS pill mode)
    - Icon: uses Bootstrap Icons, map to MYDS icon anatomy
    - For additional statuses, update $statusMap with MYDS-compliant colors/icons/labels
    - Principles: Citizen-centric, minimal, clear, accessible, consistent, error prevention
--}}
