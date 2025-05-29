{{--
    NOTE: This is a Laravel Jetstream page that uses Jetstream components styled with Tailwind CSS.
    For the MOTAC system, this requires a UI refactor to Bootstrap 5 and
    replacement of Jetstream x-components with MOTAC's Bootstrap components.
    The layout extension should also point to your MOTAC system's main app layout.
    Adjustments below primarily make static text translatable.
--}}
@extends('layouts.app') {{-- Or your MOTAC system's main authenticated layout, e.g., layouts.contentNavbarLayout --}}

@php
// Example breadcrumbs if your layout supports it
// $breadcrumbs = [['link' => route('dashboard'), 'name' => __('Papan Pemuka')], ['name' => __('Profil')]];
@endphp

@section('title', __('Profil Pengguna'))


@section('content')
<div class="container-fluid"> {{-- Added a container for better spacing --}}
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
    <div class="mb-4">
        @livewire('profile.update-profile-information-form')
    </div>
    @endif

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="mb-4">
        @livewire('profile.update-password-form')
        </div>
    @endif

    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
    <div class="mb-4">
        @livewire('profile.two-factor-authentication-form')
    </div>
    @endif

    <div class="mb-4">
        @livewire('profile.logout-other-browser-sessions-form')
    </div>

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="mb-4"> {{-- Added margin bottom for consistency --}}
            @livewire('profile.delete-user-form')
        </div>
    @endif
</div>
@endsection
