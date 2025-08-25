{{--
    resources/views/components/form-section.blade.php

    Form section component that wraps forms in a card layout with Livewire integration.
    Provides consistent styling and structure for form-based interfaces.

    Props:
    - $submit: string - Livewire method name for form submission (required)

    Slots:
    - $title: Section title (optional)
    - $description: Section description (optional)
    - $form: Form fields and content (required)
    - $actions: Action buttons (optional)

    Usage:
    <x-form-section submit="saveUser">
        <x-slot name="title">
            {{ __('User Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Update user profile information.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Form fields here -->
        </x-slot>

        <x-slot name="actions">
            <x-button>{{ __('Save') }}</x-button>
        </x-slot>
    </x-form-section>

    Dependencies: Bootstrap 5, Livewire
--}}
@props(['submit'])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    {{-- Card Header (if title provided) --}}
    @if (isset($title))
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
        </div>
    @endif

    <div class="card-body p-3 p-md-4">
        {{-- Livewire Form --}}
        <form wire:submit.prevent="{{ $submit }}">
            {{-- Optional Description --}}
            @if (isset($description))
                <p class="card-text text-muted small mb-3">{{ $description }}</p>
            @endif

            {{-- Form Content --}}
            <div class="form-content">
                {{ $form }}
            </div>

            {{-- Action Buttons --}}
            @if (isset($actions))
                <div class="d-flex justify-content-end pt-3 mt-4 border-top">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
