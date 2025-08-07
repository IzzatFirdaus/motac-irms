{{--
    resources/views/components/applicant-details-readonly.blade.php

    Displays applicant/user information in a read-only format using action-section layout.
    Commonly used in application reviews and user profile displays.

    Props:
    - $user: User model instance containing applicant data (required)
    - $title: string - Section title (default: 'MAKLUMAT PEMOHON')

    Usage:
    <x-applicant-details-readonly :user="$application->user" />
    <x-applicant-details-readonly :user="$user" :title="__('MAKLUMAT PENGGUNA')" />

    Dependencies: Bootstrap 5, x-action-section, x-alert components
--}}
@props(['user', 'title' => __('MAKLUMAT PEMOHON')])

<x-action-section :title="$title">
    <x-slot name="content">
        @if ($user)
            <div class="row g-3 small">
                {{-- Full Name --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Nama Penuh:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->name ?? __('N/A') }}</p>
                </div>

                {{-- Identification Number --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('No. Pengenalan (NRIC):') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->identification_number ?? __('N/A') }}</p>
                </div>

                {{-- Position and Grade --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Jawatan & Gred:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">
                        {{ optional($user->position)->name ?? __('N/A') }} ({{ optional($user->grade)->name ?? __('N/A') }})
                    </p>
                </div>

                {{-- Department/Unit --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Bahagian/Unit:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ optional($user->department)->name ?? __('N/A') }}</p>
                </div>

                {{-- Mobile Number --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('No. Telefon Bimbit:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->mobile_number ?? __('N/A') }}</p>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('E-mel (Login):') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->email ?? __('N/A') }}</p>
                </div>
            </div>
        @else
            {{-- User data not available warning --}}
            <x-alert type="warning" :message="__('Maklumat pengguna tidak dapat dimuatkan.')" :icon="'bi-exclamation-triangle-fill'" />
        @endif
    </x-slot>
</x-action-section>
