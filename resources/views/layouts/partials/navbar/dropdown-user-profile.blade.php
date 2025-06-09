{{-- resources/views/layouts/partials/navbar/dropdown-user-profile.blade.php --}}
{{-- This partial contains the standardized user profile dropdown menu. --}}
@auth
    @php
        $currentUser = Auth::user();
    @endphp
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            aria-label="{{ __('Menu Pengguna') }}">
            <div class="avatar avatar-online">
                <img src="{{ $currentUser->profile_photo_url }}"
                    alt="{{ __('Avatar :name', ['name' => $currentUser->name]) }}"
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
                                    alt="{{ __('Avatar :name', ['name' => $currentUser->name]) }}"
                                    class="w-px-40 h-auto rounded-circle object-fit-cover">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $currentUser->name }}</span>
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
            {{-- The 'view_equipment' permission can act as a proxy for admin-level access --}}
            @can('view_equipment')
                <li>
                    <a class="dropdown-item" href="{{ route('resource-management.equipment-admin.index') }}">
                        <i class="bi bi-gear-fill me-2 fs-6"></i>
                        {{ __('Pentadbiran Sistem') }}
                    </a>
                </li>
            @endcan
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
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
@else
    {{-- Optional: Show login button if user is not authenticated --}}
    <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">
            <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
            <span class="align-middle">{{ __('Log Masuk') }}</span>
        </a>
    </li>
@endauth
