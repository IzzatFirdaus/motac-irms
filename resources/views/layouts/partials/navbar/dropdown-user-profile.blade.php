{{-- resources/views/layouts/partials/navbar/dropdown-user-profile.blade.php --}}
@auth
    @php
        $currentUser = Auth::user();
    @endphp
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            aria-label="{{ __('Menu Pengguna') }}" aria-expanded="false">
            <div class="avatar avatar-online">
                <img src="{{ $currentUser->profile_photo_url }}" alt="Avatar of {{ $currentUser->name }}"
                    class="w-px-40 h-auto rounded-circle object-fit-cover">
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('profile.show') }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                                <img src="{{ $currentUser->profile_photo_url }}"
                                    alt="Avatar of {{ $currentUser->name }}"
                                    class="w-px-40 h-auto rounded-circle object-fit-cover">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $currentUser->name }}</span>
                            {{-- REVISED: The user's primary role is displayed here, as per the design document. --}}
                            <small
                                class="text-muted">{{ Str::title($currentUser->getRoleNames()->first() ?? __('Pengguna')) }}</small>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('profile.show') }}">
                    <i class="bi bi-person-circle me-2 fs-6"></i>
                    {{ __('Profil Saya') }}
                </a>
            </li>
            {{-- REVISED: 'view_equipment' permission is used as a proxy for admin-level access. --}}
            {{-- This link now correctly points to the admin equipment index route. --}}
            @can('view_equipment')
                <li>
                    <a class="dropdown-item" href="{{ route('admin.equipment.index') }}">
                        <i class="bi bi-gear-fill me-2 fs-6"></i>
                        {{ __('Pentadbiran Sistem') }}
                    </a>
                </li>
            @endcan
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                {{-- REVISED: This uses a form to safely log out via a POST request. --}}
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2 fs-6"></i>
                    {{ __('Log Keluar') }}
                </a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </li>
@endauth
