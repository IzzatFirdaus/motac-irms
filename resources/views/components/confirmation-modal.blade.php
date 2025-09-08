{{--
    resources/views/components/confirmation-modal.blade.php

    MYDS-compliant Confirmation Dialog Modal.
    - Follows MYDS modal anatomy: header (icon + title), content, footer (actions).
    - Uses MYDS color tokens, typography, radius, spacing, and accessibility (ARIA roles).
    - Applies Principle 5 (Minimalis), Principle 7 (Paparan Jelas), Principle 13 (UI/UX), Principle 14 (Tipografi), Principle 17 (Pencegahan Ralat) from MyGOVEA.
    - Accessible for keyboard/screen reader users.

    Props:
    - $id: string - Modal ID (optional)
    - $maxWidth: string - Modal size: 'sm', 'lg', 'xl', 'fullscreen' (optional)
    - $title: string - Modal title
    - $content: mixed - Modal main content
    - $footer: mixed - Modal footer (buttons)

    Usage:
    <x-confirmation-modal>
        <x-slot name="title">{{ __('Confirm Deletion') }}</x-slot>
        <x-slot name="content">{{ __('Are you sure you want to delete this item?') }}</x-slot>
        <x-slot name="footer">
            <x-secondary-button>{{ __('Cancel') }}</x-secondary-button>
            <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
--}}

@props(['id' => null, 'maxWidth' => null, 'title' => null, 'content' => null, 'footer' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <x-slot name="title">
        <div class="d-flex align-items-center gap-2">
            {{-- MYDS: Leading warning icon, color token --}}
            <span aria-hidden="true" class="myds-modal-icon">
                <i class="bi bi-exclamation-triangle-fill"
                   style="color: var(--myds-warning-500); font-size: 2rem;"></i>
            </span>
            {{-- MYDS: Modal Title, semibold, Poppins --}}
            <span class="myds-modal-title h4 fw-semibold" style="font-family: 'Poppins', Arial, sans-serif;">
                {{ $title }}
            </span>
        </div>
    </x-slot>

    <x-slot name="content">
        {{-- MYDS: Modal content, body text, Inter --}}
        <div class="myds-modal-content text-body" style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900); font-size: 1rem;">
            {{ $content }}
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex flex-row-reverse gap-2">
            {{-- MYDS: Action Buttons, Primary (danger), Secondary, with default order for accessibility --}}
            {{ $footer }}
        </div>
    </x-slot>
</x-modal>

{{--
    MYDS Features:
    - Modal uses 8px radius, shadow, color tokens for background and border.
    - Focus management and ARIA roles provided via x-modal.
    - Action buttons placed using MYDS button anatomy.
    - Minimalist layout, clear hierarchy, responsive at all breakpoints.
    - Clear feedback and error prevention (confirmation required).
--}}
