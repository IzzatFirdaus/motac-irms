{{-- resources/views/layouts/partials/navbar/dropdown-user-profile.blade.php --}}

{{-- This partial should only be included if the user is authenticated. --}}
@auth
    @php
        // It's good practice to get the authenticated user once to avoid multiple calls.
        $currentUser = Auth::user();
    @endphp

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            aria-label="{{ __('User Menu') }}" aria-expanded="false">
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
                                <img src="{{ $currentUser->profile_photo_url }}" alt="Avatar of {{ $currentUser->name }}"
                                    class="w-px-40 h-auto rounded-circle object-fit-cover">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $currentUser->name }}</span>
                            {{-- Using Str::title to ensure consistent capitalization of the role name --}}
                            <small class="text-muted">{{ Str::title($currentUser->getRoleNames()->first() ?? __('User')) }}</small>
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

            {{-- This link correctly uses a policy or permission check for visibility --}}
            @can('view-settings-admin')
                <li>
                    <a class="dropdown-item" href="{{ route('settings.users.index') }}">
                        <i class="bi bi-gear-fill me-2 fs-6"></i>
                        {{ __('Tetapan Sistem') }}
                    </a>
                </li>
            @endcan

            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                {{-- This form correctly and securely handles the logout process --}}
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                    @csrf
                </form>
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2 fs-6"></i>
                    {{ __('Log Keluar') }}
                </a>
            </li>
        </ul>
    </li>
@endauth
