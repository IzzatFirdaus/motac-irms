{{--
    resources/views/components/action-section.blade.php

    A card-based section component styled according to MOTAC design language.
    Provides a consistent layout for action-based content with header and body.

    Props:
    - $title: string - The section title (required)
    - $description: string - Optional description text (optional)
    - $content: slot - The main content area (required)

    Usage:
    <x-action-section title="User Settings">
        <x-slot name="description">
            Configure your account preferences here.
        </x-slot>
        <x-slot name="content">
            <!-- Your form or content here -->
        </x-slot>
    </x-action-section>

    Dependencies: Bootstrap 5
--}}
<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
    </div>
    <div class="card-body">
        @if (isset($description))
            <p class="card-text text-muted small mb-3">{{ $description }}</p>
        @endif
        {{ $content }}
    </div>
</div>
