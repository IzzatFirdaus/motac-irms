{{-- resources/views/partials/sidebar-partial.blade.php --}}
{{-- MOTAC AdminLTE-like Sidebar Navigation, with role-based and permission-based items --}}
@php
    $user = Auth::user();

    // Permissions flags & Counts from $sidebarData (ensure these are correctly passed from your layout's composer or controller)
    $canViewDashboard = $sidebarData['canViewDashboard'] ?? ($user ? true : false);

    $canViewMyLoanApplications = $sidebarData['canViewMyLoanApplications'] ?? ($user ? $user->can('viewAny', \App\Models\LoanApplication::class) : false);

    // Approvals
    $canViewApprovalDashboard = $sidebarData['canViewApprovalDashboard'] ?? ($user ? $user->can('view_approval_tasks') : false); // Custom permission
    $canViewApprovalHistory = $sidebarData['canViewApprovalHistory'] ?? ($user ? $user->can('view_approval_history') : false); // Custom permission
    $totalPendingApprovalTasks = $sidebarData['totalPendingApprovalTasks'] ?? 0;

    // Helpdesk (NEW)
    $canViewMyHelpdeskTickets = $sidebarData['canViewMyHelpdeskTickets'] ?? ($user ? $user->can('viewAny', \App\Models\HelpdeskTicket::class) : false);
    $canViewAdminHelpdeskTickets = $sidebarData['canViewAdminHelpdeskTickets'] ?? ($user ? $user->can('manage_helpdesk_tickets') : false);

    // Admin - Settings
    $canViewSettingsUsers = $sidebarData['canViewSettingsUsers'] ?? ($user ? $user->can('viewAny', \App\Models\User::class) : false);
    $canViewSettingsRoles = $sidebarData['canViewSettingsRoles'] ?? ($user ? $user->can('manage_roles') : false);
    $canViewSettingsPermissions = $sidebarData['canViewSettingsPermissions'] ?? ($user ? $user->can('manage_permissions') : false);
    $canViewSettingsGrades = $sidebarData['canViewSettingsGrades'] ?? ($user ? $user->can('viewAny', \App\Models\Grade::class) : false);
    $canViewSettingsDepartments = $sidebarData['canViewSettingsDepartments'] ?? ($user ? $user->can('viewAny', \App\Models\Department::class) : false);
    $canViewSettingsPositions = $sidebarData['canViewSettingsPositions'] ?? ($user ? $user->can('viewAny', \App\Models\Position::class) : false);

    // Admin - Resource Management Specific Modules
    $canViewAdminUsersIndex = $sidebarData['canViewAdminUsersIndex'] ?? ($user ? $user->can('viewAny', \App\Models\User::class) && $user->hasRole('Admin') : false);
    $canViewAdminEquipment = $sidebarData['canViewAdminEquipment'] ?? ($user ? $user->can('viewAny', \App\Models\Equipment::class) && $user->hasAnyRole(['Admin', 'BPM Staff']) : false);
    $canViewAdminLoanTransactions = $sidebarData['canViewAdminLoanTransactions'] ?? ($user ? $user->can('viewAny', \App\Models\LoanTransaction::class) && $user->hasAnyRole(['Admin', 'BPM Staff']) : false);

    // BPM Staff Specific Views
    $canViewBpmOutstandingLoans = $sidebarData['canViewBpmOutstandingLoans'] ?? ($user ? $user->hasAnyRole(['Admin', 'BPM Staff']) : false);
    $canViewBpmIssuedLoans = $sidebarData['canViewBpmIssuedLoans'] ?? ($user ? $user->hasAnyRole(['Admin', 'BPM Staff']) : false);

    // Reports
    $canViewReports = $sidebarData['canViewReports'] ?? ($user ? $user->hasAnyRole(['Admin', 'BPM Staff']) : false);

    $userRoles = $sidebarData['userRoles'] ?? ($user ? $user->getRoleNames() : collect());
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link text-decoration-none">
        {{-- Application logo --}}
        <x-application-mark class="brand-image img-circle elevation-3" style="opacity: .8; height: 33px; width: auto; color: #ffffff;" />
        <span class="brand-text fw-light">{{ config('app.name', __('Sistem MOTAC')) }}</span>
    </a>

    <div class="sidebar">
        @if ($user)
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="{{ $user->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}"
                     class="img-circle elevation-2" alt="{{ $user->name ?? __('Imej Pengguna') }}" style="width:34px; height:34px; object-fit:cover;">
            </div>
            <div class="info">
                <a href="{{ Auth::user() && Route::has('profile.show') ? route('profile.show') : '#' }}" class="d-block text-decoration-none">{{ $user->name ?? __('Pengguna') }}</a>
                <span class="d-block text-muted text-sm">{{ $userRoles->isNotEmpty() ? Str::title($userRoles->first()) : __('Pengguna Biasa') }}</span>
            </div>
        </div>
        @endif

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                {{-- Dashboard --}}
                @if($canViewDashboard)
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>{{ __('Papan Pemuka') }}</p>
                    </a>
                </li>
                @endif

                {{-- My Applications --}}
                @if ($canViewMyLoanApplications)
                    <li class="nav-header">{{ __('PERMOHONAN SAYA') }}</li>
                    <li class="nav-item">
                        <a href="{{ route('loan-applications.index') }}"
                           class="nav-link {{ request()->routeIs('loan-applications.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-laptop"></i>
                            <p>{{ __('Pinjaman Peralatan ICT') }}</p>
                        </a>
                    </li>
                @endif

                {{-- Helpdesk Section --}}
                @if($canViewMyHelpdeskTickets || $canViewAdminHelpdeskTickets)
                    <li class="nav-header">{{ __('SISTEM MEJA BANTUAN') }}</li>
                    @if ($canViewMyHelpdeskTickets)
                        <li class="nav-item">
                            <a href="{{ route('helpdesk.index') }}"
                               class="nav-link {{ request()->routeIs('helpdesk.index') || request()->routeIs('helpdesk.show') || request()->routeIs('helpdesk.create') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-life-preserver"></i>
                                <p>{{ __('Tiket Saya') }}</p>
                            </a>
                        </li>
                    @endif
                    @if ($canViewAdminHelpdeskTickets)
                        <li class="nav-item">
                            <a href="{{ route('helpdesk.admin.index') }}"
                               class="nav-link {{ request()->routeIs('helpdesk.admin.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-ticket-detailed-fill"></i>
                                <p>{{ __('Pengurusan Tiket') }}</p>
                            </a>
                        </li>
                    @endif
                @endif

                {{-- Approver Section --}}
                @if($canViewApprovalDashboard || $canViewApprovalHistory)
                    <li class="nav-header">{{ __('TUGASAN KELULUSAN') }}</li>
                    @if ($canViewApprovalDashboard)
                        <li class="nav-item">
                            <a href="{{ route('approvals.dashboard') }}"
                               class="nav-link {{ request()->routeIs('approvals.dashboard') || request()->routeIs('approvals.show') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-clipboard2-check-fill"></i>
                                <p>
                                    {{ __('Kelulusan Perlu Tindakan') }}
                                    @if ($totalPendingApprovalTasks > 0)
                                        <span class="badge bg-danger right">{{ $totalPendingApprovalTasks }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                    @endif
                    @if ($canViewApprovalHistory)
                         <li class="nav-item">
                            <a href="{{ route('approvals.history') }}"
                               class="nav-link {{ request()->routeIs('approvals.history') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-clock-history"></i>
                                <p>{{ __('Sejarah Kelulusan') }}</p>
                            </a>
                        </li>
                    @endif
                @endif

                {{-- BPM Staff & IT Admin Section --}}
                @if($user && ($user->hasAnyRole(['Admin', 'BPM Staff']) || $user->hasRole('IT Admin')))
                    <li class="nav-header">{{ __('PENGURUSAN SUMBER ICT') }}</li>
                    @if ($canViewAdminEquipment || $canViewBpmOutstandingLoans || $canViewBpmIssuedLoans || $canViewAdminLoanTransactions)
                        <li class="nav-item {{ request()->is('resource-management/equipment-admin*') || request()->is('resource-management/bpm*') || request()->is('admin/loan-transactions*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('resource-management/equipment-admin*') || request()->is('resource-management/bpm*') || request()->is('admin/loan-transactions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>{{ __('Peralatan & Pinjaman') }} <i class="right bi bi-chevron-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if($canViewAdminEquipment)
                                <li class="nav-item">
                                    <a href="{{ route('resource-management.equipment-admin.index') }}" class="nav-link {{ request()->routeIs('resource-management.equipment-admin.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Inventori Peralatan') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if($canViewBpmOutstandingLoans)
                                <li class="nav-item">
                                    <a href="{{ route('resource-management.bpm.outstanding-loans') }}" class="nav-link {{ request()->routeIs('resource-management.bpm.outstanding-loans') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Proses Pengeluaran') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if($canViewBpmIssuedLoans)
                                <li class="nav-item">
                                    <a href="{{ route('resource-management.bpm.issued-loans') }}" class="nav-link {{ request()->routeIs('resource-management.bpm.issued-loans') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Proses Pemulangan') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if($canViewAdminLoanTransactions && Route::has('admin.loan-transactions.index'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.loan-transactions.index') }}" class="nav-link {{ request()->routeIs('admin.loan-transactions.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Semua Transaksi') }}</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                {{-- System Settings (Admin Only) --}}
                @if ($canViewSettingsUsers || $canViewSettingsRoles || $canViewSettingsPermissions || $canViewSettingsGrades || $canViewSettingsDepartments || $canViewSettingsPositions)
                    <li class="nav-header">{{ __('TETAPAN SISTEM (ADMIN)') }}</li>
                    @if ($canViewSettingsUsers || $canViewSettingsRoles || $canViewSettingsPermissions)
                        <li class="nav-item {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permissions*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permissions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>{{ __('Pengguna & Capaian') }} <i class="right bi bi-chevron-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if ($canViewSettingsUsers)
                                <li class="nav-item">
                                    <a href="{{ route('settings.users.index') }}" class="nav-link {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Pengguna Sistem') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if ($canViewSettingsRoles)
                                <li class="nav-item">
                                    <a href="{{ route('settings.roles.index') }}" class="nav-link {{ request()->routeIs('settings.roles.*') ? 'active' : '' }}">
                                         <i class="nav-icon bi bi-dot"></i> <p>{{ __('Peranan') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if ($canViewSettingsPermissions)
                                <li class="nav-item">
                                    <a href="{{ route('settings.permissions.index') }}" class="nav-link {{ request()->routeIs('settings.permissions.*') ? 'active' : '' }}">
                                         <i class="nav-icon bi bi-dot"></i> <p>{{ __('Kebenaran') }}</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if ($canViewSettingsDepartments || $canViewSettingsPositions || $canViewSettingsGrades)
                         <li class="nav-item {{ request()->is('settings/departments*') || request()->is('settings/positions*') || request()->is('settings/grades*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('settings/departments*') || request()->is('settings/positions*') || request()->is('settings/grades*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-diagram-3-fill"></i>
                                <p>{{ __('Struktur Organisasi') }} <i class="right bi bi-chevron-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if($canViewSettingsDepartments)
                                <li class="nav-item">
                                    <a href="{{ route('settings.departments.index') }}" class="nav-link {{ request()->routeIs('settings.departments.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Jabatan/Unit') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if ($canViewSettingsPositions)
                                <li class="nav-item">
                                    <a href="{{ route('settings.positions.index') }}" class="nav-link {{ request()->routeIs('settings.positions.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Jawatan') }}</p>
                                    </a>
                                </li>
                                @endif
                                @if ($canViewSettingsGrades)
                                <li class="nav-item">
                                    <a href="{{ route('settings.grades.index') }}" class="nav-link {{ request()->routeIs('settings.grades.*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-dot"></i> <p>{{ __('Gred') }}</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                {{-- Reporting --}}
                @if($canViewReports)
                <li class="nav-header">{{ __('PELAPORAN') }}</li>
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-bar-graph-fill"></i>
                        <p>{{ __('Jana Laporan Sistem') }}</p>
                    </a>
                </li>
                @endif

                {{-- Logout --}}
                <li class="nav-item mt-auto">
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="nav-icon bi bi-box-arrow-left"></i>
                        <p>{{ __('Log Keluar') }}</p>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
