{{--
    resources/views/partials/sidebar.blade.php

    This file contains the HTML structure for the application's sidebar menu.
    It includes navigation links for both existing HRMS features and the new
    MOTAC Integrated Resource Management System features.
    Assumes integration with a Bootstrap-based admin theme like AdminLTE.
    It expects a $sidebarData array to be passed to it, containing:
    - boolean flags for @can checks (e.g., canViewAnyUsers)
    - counts for menu badges (e.g., pendingEmailApplicationsCount)
    - user roles/permissions if needed for specific display logic (e.g., userRoles)

    ****************************************************************************
    IMPORTANT: This partial should NOT perform database queries directly.
               All data needed for menu items (counts, relationship checks, etc.)
               must be fetched in the controller or Livewire component rendering
               the parent layout and passed to this partial via @include.
               Example: @include('partials.sidebar', ['sidebarData' => $sidebarData])
    ****************************************************************************
--}}

{{-- Safely access data passed from the parent view/layout --}}
@php
    // Access data passed from the parent view/layout
    // Provide empty array/default fallbacks for safety
    $canViewAnyEmailApplications = $sidebarData['canViewAnyEmailApplications'] ?? false;
    $canViewAnyLoanApplications = $sidebarData['canViewAnyLoanApplications'] ?? false;
    $canViewAnyUsers = $sidebarData['canViewAnyUsers'] ?? false;
    $canViewAnyEquipment = $sidebarData['canViewAnyEquipment'] ?? false;
    $canViewAnyGrades = $sidebarData['canViewAnyGrades'] ?? false;
    $canViewAnyLoanTransactions = $sidebarData['canViewAnyLoanTransactions'] ?? false;
    // Add boolean flags for other sections if needed
    $canViewApprovalDashboard = $sidebarData['canViewApprovalDashboard'] ?? false; // Assuming you add this flag
    $canViewApprovalHistory = $sidebarData['canViewApprovalHistory'] ?? false; // Assuming you add this flag

    $pendingEmailApplicationsCount = $sidebarData['pendingEmailApplicationsCount'] ?? 0;
    $pendingLoanApplicationsCount = $sidebarData['pendingLoanApplicationsCount'] ?? 0;
    $totalUserCount = $sidebarData['totalUserCount'] ?? 0;
    $totalEquipmentCount = $sidebarData['totalEquipmentCount'] ?? 0;
    $totalGradeCount = $sidebarData['totalGradeCount'] ?? 0; // Add fallback
    $totalLoanTransactionCount = $sidebarData['totalLoanTransactionCount'] ?? 0; // Add fallback

    $userRoles = $sidebarData['userRoles'] ?? collect(); // Provide fallback

    // Safely get the authenticated user (still needed for policies applied via Gate::allows or @can/@role directly if not using flags)
    // If you pass all permissions/roles as flags, you might not need Auth::user() here,
    // but it's often necessary for other layout elements or shared logic.
    $user = Auth::user();
@endphp


{{-- Assuming you are using AdminLTE or a similar Bootstrap-based theme --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4"> {{-- Main sidebar element with theme classes --}}
    {{-- Brand Logo - You will need to adjust the href and img src --}}
    <a href="{{ url('/') }}" class="brand-link"> {{-- Link to the application homepage --}}
        {{-- Replace with your actual logo path --}}
        {{-- Ensure you have a default logo path if profile_photo_path is null --}}
        <img src="{{ asset('path/to/your/logo.png') }}" alt="MOTAC Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8"> {{-- Logo image with styling --}}
        <span class="brand-text font-weight-light">Sistem MOTAC</span> {{-- Adjust brand name to Malay or your preference --}}
    </a>

    {{-- Sidebar content wrapper --}}
    <div class="sidebar">
        {{-- Sidebar user panel (optional) --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex"> {{-- User panel styling --}}
            <div class="image">
                {{-- User profile image --}}
                {{-- Use Auth::user()->profile_photo_url if using Jetstream/Fortify, otherwise use employee photo or default --}}
                @if ($user)
                    {{-- Use Auth::user() directly if you're not passing the user model in sidebarData --}}
                    {{-- Assuming getEmployeePhoto() accessor exists on the Employee model --}}
                    <img src="{{ $user->profile_photo_url ?? ($user->employee->getEmployeePhoto() ?? asset('dist/img/default-user.jpg')) }}"
                        class="img-circle elevation-2" alt="{{ $user->name ?? 'User' }}">
                @else
                    <img src="{{ asset('dist/img/default-user.jpg') }}" class="img-circle elevation-2" alt="Guest">
                @endif
            </div>
            <div class="info">
                {{-- Display user name and role --}}
                @if ($user)
                    <a href="{{ route('profile.show') ?? '#' }}" class="d-block">{{ $user->name ?? 'Pengguna' }}</a>
                    {{-- Link to profile, provide fallback --}}
                    {{-- Display first role from the passed $userRoles collection, provide fallback --}}
                    <span class="d-block text-muted text-sm">{{ $userRoles->first() ?? 'Pengguna Biasa' }}</span>
                @else
                    <span class="d-block text-muted">Tetamu</span> {{-- Guest display --}}
                @endif
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                {{-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library --}}

                {{-- Dashboard Link --}}
                <li class="nav-item">
                    {{-- Check if current route is 'dashboard' for active class --}}
                    {{-- Adjust 'active' class based on your AdminLTE/theme implementation --}}
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"> {{-- Use request()->routeIs for active check --}}
                        <i class="nav-icon fas fa-tachometer-alt"></i> {{-- Example icon (Font Awesome) --}}
                        <p>Dashboard</p> {{-- Localized label --}}
                    </a>
                </li>

                {{-- New MOTAC Integrated Resource Management Menu Items --}}
                {{-- Example: Wrap the entire RM section in a permission/role check if needed --}}
                {{-- @can('access-resource-management') --}} {{-- Assuming a general permission for RM --}}

                {{-- Resource Management Section Header --}}
                {{-- Show header only if user has access to any RM feature (using boolean flags) --}}
                @if (
                    $canViewAnyEmailApplications ||
                        $canViewAnyLoanApplications ||
                        $canViewAnyUsers ||
                        $canViewAnyEquipment ||
                        $canViewAnyGrades ||
                        $canViewAnyLoanTransactions)
                    <li class="nav-header">PENGURUSAN SUMBER BERSEPADU MOTAC</li> {{-- Localized Header --}}
                @endif


                {{-- Example: My Applications Parent Menu Item --}}
                {{-- Show this parent item if user can view any sub-item (using boolean flags) --}}
                @if ($canViewAnyEmailApplications || $canViewAnyLoanApplications)
                    {{-- Check active state using route wildcards --}}
                    <li class="nav-item {{ request()->routeIs('my-applications.*') ? 'menu-open' : '' }}">
                        {{-- AdminLTE menu-open for dropdown --}}
                        <a href="#"
                            class="nav-link {{ request()->routeIs('my-applications.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i> {{-- Example icon (Font Awesome) --}}
                            <p>
                                Permohonan Saya {{-- Localized label --}}
                                <i class="right fas fa-angle-left"></i> {{-- Angle icon for dropdown --}}
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Email Applications Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyEmailApplications)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('my-applications.email.index') ?? '#' }}" {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('my-applications.email.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Permohonan Emel</p> {{-- Localized label --}}
                                        {{-- TODO: Add badge if needed, using $pendingEmailApplicationsCount from $sidebarData --}}
                                        {{-- Example using the fetched count: --}}
                                        @if (($pendingEmailApplicationsCount ?? 0) > 0)
                                            <span
                                                class="badge badge-warning right">{{ $pendingEmailApplicationsCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Loan Applications Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyLoanApplications)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('my-applications.loan.index') ?? '#' }}" {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('my-applications.loan.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Permohonan Pinjaman Peralatan</p> {{-- Localized label --}}
                                        {{-- TODO: Add badge if needed, using $pendingLoanApplicationsCount from $sidebarData --}}
                                        @if (($pendingLoanApplicationsCount ?? 0) > 0)
                                            <span
                                                class="badge badge-warning right">{{ $pendingLoanApplicationsCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Add other application types here using $sidebarData flags --}}
                        </ul>
                    </li>
                @endif {{-- End My Applications parent item check --}}

                {{-- Example: Approval Dashboard Link --}}
                {{-- Check permission using a boolean flag passed in $sidebarData --}}
                @if ($canViewApprovalDashboard) {{-- Use boolean flag --}}
                    <li class="nav-item">
                        {{-- Check active state --}}
                        <a href="{{ route('approval-dashboard.index') ?? '#' }}" {{-- Use route helper, provide fallback --}}
                            class="nav-link {{ request()->routeIs('approval-dashboard.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check"></i> {{-- Example icon --}}
                            <p>
                                Dashboard Kelulusan {{-- Localized label --}}
                                {{-- Display total pending count if available in $sidebarData --}}
                                @php($totalPending = ($pendingEmailApplicationsCount ?? 0) + ($pendingLoanApplicationsCount ?? 0))
                                @if ($totalPending > 0)
                                    <span class="badge badge-danger right">{{ $totalPending }}</span>
                                    {{-- AdminLTE badge --}}
                                @endif
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Example: Approval History Link --}}
                {{-- Check permission using a boolean flag passed in $sidebarData --}}
                @if ($canViewApprovalHistory)
                    {{-- Use boolean flag --}}
                    <li class="nav-item">
                        {{-- Check active state --}}
                        <a href="{{ route('approvals.history') ?? '#' }}" {{-- Use route helper, provide fallback --}}
                            class="nav-link {{ request()->routeIs('approvals.history') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i> {{-- Example icon --}}
                            <p>Sejarah Kelulusan</p> {{-- Localized label --}}
                        </a>
                    </li>
                @endif


                {{-- Resource Management Administration Section --}}
                {{-- Show section if user has any admin permission (using combined flags) --}}
                @if ($canViewAnyUsers || $canViewAnyEquipment || $canViewAnyGrades || $canViewAnyLoanTransactions) {{-- Check combined flags --}}
                    <li class="nav-header">PENTADBIRAN PENGURUSAN SUMBER</li> {{-- Localized Header --}}

                    {{-- RM Administration Parent Menu Item --}}
                    {{-- Show parent item if user can view any sub-item --}}
                    <li class="nav-item {{ request()->routeIs('resource-management.admin.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('resource-management.admin.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i> {{-- Example icon --}}
                            <p>
                                Pentadbiran RM {{-- Localized label --}}
                                <i class="right fas fa-angle-left"></i> {{-- Angle icon for dropdown --}}
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Employees Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyUsers) {{-- Assuming viewAny users implies access to employee list --}}
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('resource-management.admin.employees.index') ?? '#' }}"
                                        {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('resource-management.admin.employees.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Pegawai/Kakitangan</p> {{-- Localized label --}}
                                        {{-- Display total user count if available in $sidebarData (can also be displayed on Users link) --}}
                                        @if (($totalUserCount ?? 0) > 0)
                                            <span class="badge badge-primary right">{{ $totalUserCount }}</span>
                                            {{-- AdminLTE badge --}}
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Users Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyUsers)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('resource-management.admin.users.index') ?? '#' }}"
                                        {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('resource-management.admin.users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Pengguna Sistem</p> {{-- Localized label --}}
                                        {{-- The total user count badge can be displayed on either Employees or Users link, choose one --}}
                                        {{-- @if (($totalUserCount ?? 0) > 0) --}}
                                        {{-- <span class="badge badge-primary right">{{ $totalUserCount }}</span> --}}
                                        {{-- @endif --}}
                                    </a>
                                </li>
                            @endif

                            {{-- Equipment Management Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyEquipment)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('resource-management.admin.equipment.index') ?? '#' }}"
                                        {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('resource-management.admin.equipment.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Pengurusan Peralatan ICT</p> {{-- Localized label --}}
                                        {{-- Display total equipment count if available in $sidebarData --}}
                                        @if (($totalEquipmentCount ?? 0) > 0)
                                            <span class="badge badge-info right">{{ $totalEquipmentCount }}</span>
                                            {{-- AdminLTE badge --}}
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Grade Management Submenu Item --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyGrades)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('resource-management.admin.grades.index') ?? '#' }}"
                                        {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('resource-management.admin.grades.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Pengurusan Gred</p> {{-- Localized label --}}
                                        {{-- Display total grade count if available in $sidebarData --}}
                                        @if (($totalGradeCount ?? 0) > 0)
                                            <span class="badge badge-secondary right">{{ $totalGradeCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Loan Transactions/History (Admin View) --}}
                            {{-- Use boolean flag from $sidebarData --}}
                            @if ($canViewAnyLoanTransactions)
                                <li class="nav-item">
                                    {{-- Check active state --}}
                                    <a href="{{ route('resource-management.admin.loan-transactions.index') ?? '#' }}"
                                        {{-- Use route helper, provide fallback --}}
                                        class="nav-link {{ request()->routeIs('resource-management.admin.loan-transactions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i> {{-- Example sub-item icon --}}
                                        <p>Sejarah Pinjaman Peralatan</p> {{-- Localized label --}}
                                        {{-- Display total loan transaction count if available in $sidebarData --}}
                                        @if (($totalLoanTransactionCount ?? 0) > 0)
                                            <span
                                                class="badge badge-primary right">{{ $totalLoanTransactionCount }}</span>
                                            {{-- AdminLTE badge --}}
                                        @endif
                                    </a>
                                </li>
                            @endif


                            {{-- Example of other admin sub-items using $sidebarData flags (commented out from original) --}}
                            {{-- @if ($canViewAnyCenters) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.centers.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.centers.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Centers') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}
                            {{-- @if ($canViewAnyDepartments) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.departments.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.departments.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Departments') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}
                            {{-- @if ($canViewAnyPositions) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.positions.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.positions.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Positions') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}
                            {{-- @if ($canViewAnyRoles) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.roles.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.roles.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Roles') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}
                            {{-- @if ($canViewAnyPermissions) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.permissions.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.permissions.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Permissions') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}
                            {{-- @if ($canViewAnyAuditLogs) --}}
                            {{--     <li class="nav-item"> --}}
                            {{--         <a href="{{ route('resource-management.admin.audit-logs.index') ?? '#' }}" class="nav-link {{ request()->routeIs('resource-management.admin.audit-logs.*') ? 'active' : '' }}"> --}}
                            {{--            <i class="far fa-circle nav-icon"></i> <p>{{ __('Audit Logs') }}</p> --}}
                            {{--        </a> --}}
                            {{--    </li> --}}
                            {{-- @endif --}}

                        </ul> {{-- End RM Administration submenu --}}
                    </li> {{-- End RM Administration parent menu item --}}
                @endif {{-- End combined admin flags check --}}

                {{-- @endcan --}}{{-- End general RM access check if it was opened --}}
                {{-- ☝️ End New MOTAC Integrated Resource Management Menu Items ☝️ --}}


                {{-- Logout Link --}}
                <li class="nav-item">
                    {{-- Ensure 'logout' route exists --}}
                    {{-- This link triggers a form submission to log the user out --}}
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i> {{-- Example icon (Font Awesome) --}}
                        <p>Log Keluar</p> {{-- Malay label --}}
                    </a>
                    {{-- Hidden form for the logout request --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf {{-- CSRF token --}}
                    </form>
                </li>

            </ul>
        </nav>
        {{-- /.sidebar-menu --}}\
    </div>
    {{-- /.sidebar --}}\
</aside>
