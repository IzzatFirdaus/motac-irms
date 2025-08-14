{{--
    resources/views/components/validation-errors.blade.php

    MYDS-compliant component to display validation errors in a standardized, accessible way.
    - Uses MYDS alert anatomy, status color tokens, semantic heading, and WCAG accessibility.
    - Follows MyGOVEA principles: error prevention, clear feedback, accessibility, minimal UI.

    Props:
    - $errors: (optional) Illuminate\Support\MessageBag or array of errors. Defaults to $errors global.

    Usage:
    <x-validation-errors />
    <x-validation-errors :errors="$errors" />

    References:
    - MYDS-Design-Overview.md
    - MYDS-Colour-Reference.md
    - prinsip-reka-bentuk-mygovea.md
--}}

@props(['errors' => $errors])

@if ($errors->any())
    <div
        class="myds-alert myds-alert-danger myds-shadow-card myds-radius-l mb-4"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        tabindex="-1"
        style="
            background: var(--myds-danger-50, #FEF2F2);
            color: var(--myds-danger-700, #B91C1C);
            border: 1px solid var(--myds-danger-200, #FECACA);
            box-shadow: 0 2px 6px rgba(220,38,38,0.08), 0 6px 24px rgba(220,38,38,0.08);
            border-radius: 12px;
            padding: 16px 20px;
        "
    >
        <div class="d-flex align-items-start">
            {{-- Leading MYDS error icon --}}
            <div class="flex-shrink-0 me-2" aria-hidden="true">
                <i class="bi bi-exclamation-triangle-fill"
                   style="font-size: 1.6rem; color: var(--myds-danger-700, #B91C1C);"></i>
            </div>

            <div class="flex-grow-1">
                {{-- Semantic error heading --}}
                <h3 class="h5 fw-semibold mb-1" style="font-family:'Poppins',Arial,sans-serif;">
                    {{ __('Ralat Pengesahan') }}
                </h3>
                {{-- List of validation errors --}}
                <ul class="myds-list myds-list-danger mb-0 ps-3" style="font-size: 1rem; font-family:'Inter',Arial,sans-serif;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            {{-- Dismiss button (if you want manual close, can be commented out) --}}
            <button type="button"
                    class="btn myds-btn myds-btn-tertiary ms-3"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('Tutup') }}"
                    style="background: none; border: none; color: var(--myds-danger-600, #DC2626); font-size: 1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
@endif

{{--
    === MYDS & MyGOVEA Principles Applied ===
    - Error block uses semantic color tokens for danger status (MYDS).
    - Typography reflects MYDS heading/body standards.
    - Accessible: ARIA roles, assertive feedback, high contrast.
    - Minimal UI: Only shows when there are errors, avoids clutter.
    - Structure/hierarchy: Heading, icon, list of errors, optional close.
    - Error prevention: Prompt feedback to help users resolve form problems.
    - Citizen-centric: Clear, readable, and actionable feedback.
--}}
