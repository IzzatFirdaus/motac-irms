{{--
    resources/views/components/user-status-badge.blade.php

    Badge to show user status (active/inactive/suspended/etc).
    Props:
    - $status: string representing user status, e.g. "active", "inactive", "suspended"
--}}

@props(['status'])

@php
    // Map status to Bootstrap badge classes and labels
    $statusMap = [
        'active' => ['class' => 'success', 'label' => __('Aktif')],
        'inactive' => ['class' => 'secondary', 'label' => __('Tidak Aktif')],
        'suspended' => ['class' => 'warning', 'label' => __('Digantung')],
        'pending' => ['class' => 'info', 'label' => __('Menunggu')],
    ];
    $badge = $statusMap[$status] ?? ['class' => 'dark', 'label' => ucfirst($status)];
@endphp

<span class="badge bg-{{ $badge['class'] }} px-3 py-1 rounded-pill align-middle">
    <i class="bi bi-circle-fill me-1" style="font-size: .8em;"></i>
    {{ $badge['label'] }}
</span>
