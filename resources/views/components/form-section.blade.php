{{--
    MYDS-compliant Form Section Card Component
    resources/views/components/form-section.blade.php

    Wraps forms in a card layout with Livewire integration.
    Ensures consistent MYDS design: grid, spacing, color, accessibility, and feedback.

    Props:
    - $submit: string - Livewire method name for form submission (required)

    Slots:
    - $title: Section title (optional)
    - $description: Section description (optional)
    - $form: Form fields and content (required)
    - $actions: Action buttons (optional)

    Usage Example:
    <x-form-section submit="saveUser">
        <x-slot name="title">{{ __('Maklumat Pengguna') }}</x-slot>
        <x-slot name="description">{{ __('Kemaskini profil pengguna.') }}</x-slot>
        <x-slot name="form">
            <!-- Form Fields Here -->
        </x-slot>
        <x-slot name="actions">
            <x-button>{{ __('Simpan') }}</x-button>
        </x-slot>
    </x-form-section>

    MYDS references: 12-8-4 grid, spacing, card anatomy, semantic color tokens, typography, accessibility (ARIA)
    MyGOVEA principles: Citizen-centric, minimal UI, error prevention, clear structure, accessibility

    Dependencies: Livewire, MYDS CSS/variables.css, MYDS spacing, MYDS typography, MYDS card anatomy
--}}

@php
    // Import the Str class for string operations
    use Illuminate\Support\Str;
@endphp

<div {{ $attributes->merge(['class' => 'myds-card myds-shadow-card myds-spacing-24 mb-4']) }} role="region" aria-labelledby="{{ isset($title) ? Str::slug($title, '-') . '-section' : null }}">
    {{-- NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime. --}}
    {{-- Card Header (if title provided) --}}
    @if (isset($title))
        <div class="myds-card-header myds-bg-washed py-3 px-4">
            <h2 id="{{ Str::slug($title, '-') . '-section' }}" class="myds-card-title h4 fw-semibold" style="font-family: 'Poppins', Arial, sans-serif;">
                {{-- NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime. --}}
                {{ $title }}
            </h2>
        </div>
    @endif

    <div class="myds-card-body p-4 p-md-5">
        {{-- Livewire Form --}}
        <form wire:submit.prevent="{{ $submit }}" autocomplete="off">
            {{-- Optional Description --}}
            @if (isset($description))
                <p class="myds-card-text myds-txt-black-500 small mb-3" style="font-family: 'Inter', Arial, sans-serif;">
                    {{ $description }}
                </p>
            @endif

            {{-- Form Content --}}
            <div class="form-content myds-spacing-12">
                {{ $form }}
            </div>

            {{-- Action Buttons --}}
            @if (isset($actions))
                <div class="d-flex flex-wrap gap-3 justify-content-end pt-3 mt-4 border-top myds-otl-divider">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>

{{--
    == MYDS Compliance Notes ==
    - Uses MYDS card anatomy: .myds-card, .myds-card-header, .myds-card-body, .myds-card-title
    - Responsive padding and spacing via MYDS spacing tokens
    - Typography: Poppins for title, Inter for body text
    - Semantic color tokens (bg-washed, txt-black-500, otl-divider) for background, text, borders
    - Accessibility: role="region", aria-labelledby for sections, clear heading hierarchy
    - Action buttons area uses gap and border-top for clear separation
    - Compatible with Livewire for reactive forms
--}}
