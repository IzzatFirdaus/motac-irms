<<<<<<< HEAD
{{--
    resources/views/components/applicant-details-readonly.blade.php

    MYDS-compliant read-only display for applicant/user information.
    Applies MYDS grid, typography, spacing, and color tokens.
    Follows MyGOVEA Principles: citizen-centric, clear structure, minimalism, accessibility.

    Props:
    - $user: User model instance containing applicant data (required)
    - $title: string - Section title (default: 'MAKLUMAT PEMOHON')

    Usage:
    <x-applicant-details-readonly :user="$application->user" />
    <x-applicant-details-readonly :user="$user" :title="$customTitle" />

    Dependencies: x-action-section, x-alert components
--}}

@props(['user', 'title' => 'MAKLUMAT PEMOHON'])

<x-action-section :title="$title">
    <x-slot name="content">
        @if ($user)
            <div class="myds-row g-3 small">
                {{-- Full Name --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        Nama Penuh:
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ isset($user->name) ? $user->name : 'N/A' }}
                    </p>
                </div>

                {{-- Identification Number --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        No. Pengenalan (NRIC):
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ isset($user->identification_number) ? $user->identification_number : 'N/A' }}
                    </p>
                </div>

                {{-- Position and Grade --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        Jawatan & Gred:
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ (isset($user->position) && isset($user->position->name) ? $user->position->name : 'N/A') }}
                        (
                        {{ (isset($user->grade) && isset($user->grade->name) ? $user->grade->name : 'N/A') }}
                        )
                    </p>
                </div>

                {{-- Department/Unit --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        Bahagian/Unit:
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ (isset($user->department) && isset($user->department->name) ? $user->department->name : 'N/A') }}
                    </p>
                </div>

                {{-- Mobile Number --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        No. Telefon Bimbit:
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ isset($user->mobile_number) ? $user->mobile_number : 'N/A' }}
                    </p>
                </div>

                {{-- Email --}}
                <div class="myds-col-12 myds-col-md-6">
                    <label class="form-label fw-medium text-muted" style="font-family: 'Poppins', Arial, sans-serif;">
                        E-mel (Login):
                    </label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0"
                       style="font-family: 'Inter', Arial, sans-serif; color: var(--myds-txt-black-900);">
                        {{ isset($user->email) ? $user->email : 'N/A' }}
                    </p>
                </div>
            </div>
        @else
            {{-- User data not available warning --}}
            <x-alert type="warning" :message="'Maklumat pengguna tidak dapat dimuatkan.'" :icon="'bi-exclamation-triangle-fill'" />
        @endif
    </x-slot>
</x-action-section>

{{--
    MYDS Compliance:
    - Uses .myds-row, .myds-col-12, .myds-col-md-6 for 12-8-4 grid system (responsive).
    - Typography: Poppins for labels/headings, Inter for content, correct weights.
    - Spacing: Consistent padding/margin, section separation.
    - Colors: Uses MYDS color tokens, especially for text and muted labels.
    - Accessibility: Labels are explicit, content is readable, no excessive decoration.
    - Minimal, clear, and citizen-centric as per MyGOVEA.
=======
@props(['user', 'title' => __('MAKLUMAT PEMOHON')])

<x-card :title="$title">
    @if ($user)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Penuh:') }}</label>
                <p class="form-display-field">{{ $user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No. Pengenalan (NRIC):') }}</label>
                <p class="form-display-field">{{ $user->identification_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jawatan & Gred:') }}</label>
                <p class="form-display-field">
                    {{ optional($user->position)->name ?? 'N/A' }} ({{ optional($user->grade)->name ?? 'N/A' }})
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bahagian/Unit:') }}</label>
                <p class="form-display-field">{{ optional($user->department)->name ?? 'N/A' }}</p>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No. Telefon Bimbit:') }}</label>
                <p class="form-display-field">{{ $user->mobile_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('E-mel Peribadi:') }}</label>
                <p class="form-display-field">{{ $user->personal_email ?? 'N/A' }}</p>
            </div>
        </div>
    @else
        <x-alert type="warning" message="Maklumat pengguna tidak dapat dimuatkan."/>
    @endif
</x-card>

{{-- Styles (ensure these are in your global CSS or a relevant <style> block if not already present) --}}
{{--
<style>
    .form-display-field {
        margin-top: 0.25rem; /* mt-1 */
        font-size: 0.875rem; /* text-sm */
        color: #1f2937; /* text-gray-900 */
        /* dark:text-gray-100 */
        padding: 0.5rem 0.75rem; /* p-2 */
        border: 1px solid #e5e7eb; /* border-gray-200 */
        /* dark:border-gray-700 */
        border-radius: 0.375rem; /* rounded-md */
        background-color: #f9fafb; /* bg-gray-50 */
        /* dark:bg-gray-700/50 */
    }
    @media (prefers-color-scheme: dark) {
        .form-display-field {
            color: #f3f4f6; /* Replace with your dark:text-gray-100 equivalent */
            border-color: #4b5563; /* Replace with your dark:border-gray-700 equivalent */
            background-color: rgba(55, 65, 81, 0.5); /* Replace with your dark:bg-gray-700/50 equivalent */
        }
    }
</style>
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
--}}
