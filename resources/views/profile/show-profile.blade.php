{{-- resources/views/profile/show-profile.blade.php --}}
{{-- Main Profile Page for the logged-in user (renamed for naming convention) --}}
@extends('layouts.app') {{-- Your MOTAC-themed Bootstrap 5 main layout --}}

@php
// Breadcrumbs for navigation (optional)
$breadcrumbs = [
    ['link' => route('dashboard'), 'name' => __('Papan Pemuka')],
    ['name' => __('Profil Saya')]
];
@endphp

@section('title', __('Profil Saya'))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-1 mb-sm-0 d-flex align-items-center">
                    <i class="bi bi-person-circle me-2"></i>{{ __('Profil Saya') }}
                </h1>
                {{-- Optional: Add a link back to the main dashboard or other relevant page --}}
            </div>

            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="mb-4">
                    @livewire('profile.update-profile-information-form-profile')
                </div>
                <hr class="my-4">
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mb-4">
                    @livewire('profile.update-password-form-profile')
                </div>
                <hr class="my-4">
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mb-4">
                    @livewire('profile.two-factor-authentication-form-profile')
                </div>
                <hr class="my-4">
            @endif

            <div class="mb-4">
                @livewire('profile.logout-other-browser-sessions-form-profile')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <hr class="my-4">
                <div class="mb-4">
                    @livewire('profile.delete-user-form-profile')
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
