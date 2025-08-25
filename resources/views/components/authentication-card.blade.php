{{--
    resources/views/components/authentication-card.blade.php

    Main layout container for authentication pages (login, register, etc.).
    Provides responsive centering and consistent MOTAC branding.

    Slots:
    - $logo: Logo component to display above the card
    - $slot: Main content area (form, etc.)

    Usage:
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <!-- Login form content -->
    </x-authentication-card>

    Dependencies: Bootstrap 5
--}}
<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100 py-4">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
      {{-- Logo Section --}}
      <div class="text-center mb-4">
        {{ $logo }}
      </div>

      {{-- Main Authentication Card --}}
      <div class="card shadow-lg motac-auth-card">
        <div class="card-body p-4 p-sm-5">
            {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>
