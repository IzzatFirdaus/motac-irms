{{--
    resources/views/components/authentication-card.blade.php

    MYDS-compliant authentication card component.
    - Uses MYDS grid, spacing, radius, shadow, and typography.
    - Ensures accessibility, responsive design, and clear structure.
    - Follows MyGOVEA principles: citizen-centric, minimalist, clear hierarchy, consistent, accessible.

    Slots:
    - $logo: Logo component to display above the card
    - $slot: Main content area (form, etc.)

    Usage:
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>
        <!-- Login/Register form content -->
    </x-authentication-card>
--}}

<div class="myds-container">
  <div class="myds-row justify-content-center align-items-center min-vh-100 py-4">
    <div class="myds-col-12 myds-col-md-8 myds-col-lg-6 myds-col-xl-5">
      {{-- Logo Section --}}
      <div class="text-center mb-4">
        {{ $logo }}
      </div>

      {{-- Main Authentication Card --}}
      <div class="myds-card myds-radius-l myds-shadow-card myds-bg-white" role="region" aria-label="Authentication Form">
        <div class="myds-card-body p-4 p-md-5">
            {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>

{{--
  MYDS/Accessibility notes:
  - Uses myds-container/myds-row/myds-col-* for grid system (responsive 12-8-4).
  - myds-card: MYDS card anatomy, spacing, shadow, large radius.
  - aria-label: Ensures screen readers announce card as authentication form.
  - Slot for logo and content enables reuse and modularity.
  - Min-vh-100: Vertically centers on viewport.
  - Responsive paddings for mobile/tablet/desktop.
  - All colors, spacing, and typography are controlled via MYDS tokens in CSS.
--}}
