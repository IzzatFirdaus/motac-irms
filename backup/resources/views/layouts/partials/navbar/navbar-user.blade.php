{{-- resources/views/layouts/partials/navbar/navbar-user.blade.php --}}
{{--
    Main user navigation bar component.
    Updated to use the new standardized navbar-user-profile partial.
    Filename unchanged, but includes updated partial name for user profile dropdown.
--}}

@php
    $containerNav = $containerNav ?? 'container-fluid';
@endphp

<nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    {{-- Sidebar toggle for mobile views --}}
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bi bi-list fs-3"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            {{-- User profile dropdown (now points to renamed partial) --}}
            @include('layouts.partials.navbar.navbar-user-profile')
        </ul>
    </div>
</nav>
