{{-- resources/views/layouts/partials/navbar/navbar-user.blade.php --}}
@php
    $containerNav = $containerNav ?? 'container-fluid';
@endphp

<nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bi bi-list fs-3"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            {{-- CORRECTED: Replaced duplicated HTML with the standardized partial. --}}
            @include('layouts.partials.navbar.dropdown-user-profile')
        </ul>
    </div>
</nav>
