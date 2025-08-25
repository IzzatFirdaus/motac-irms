{{-- resources/views/layouts/partials/navbar/navbar-user-profile.blade.php --}}
{{--
    User profile dropdown for the navigation bar (Alternative Design).
    Should only be included if the user is authenticated.
--}}

@auth
    @php
        $currentUser = Auth::user();
    @endphp

    <div class="navbar-action" tabindex="0">
        <a href="#" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('User Menu') }}">
            <img src="{{ $currentUser->profile_photo_url }}"
                alt="Avatar {{ $currentUser->name }}"
                class="navbar-avatar">
        </a>
        <div class="navbar-dropdown">
            <div style="padding: 16px 22px 10px 22px; border-bottom:1px solid #e6e6e6;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <img src="{{ $currentUser->profile_photo_url }}"
                        alt="Avatar {{ $currentUser->name }}"
                        class="navbar-avatar">
                    <div>
                        <strong>{{ $currentUser->name }}</strong><br>
                        <small class="text-muted" style="font-size: 0.97em;">
                            {{ Str::title($currentUser->getRoleNames()->first() ?? __('User')) }}
                        </small>
                    </div>
                </div>
            </div>
            <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> {{ __('Profil Saya') }}</a>
            @can('view-settings-admin')
                <a href="{{ route('settings.users.index') }}"><i class="bi bi-gear"></i> {{ __('Tetapan Sistem') }}</a>
            @endcan
            <a href="#"><i class="bi bi-question-circle"></i> {{ __('Bantuan') }}</a>
            <div style="border-top:1px solid #e6e6e6;"></div>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                @csrf
            </form>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> {{ __('Log Keluar') }}
            </a>
        </div>
    </div>
@endauth
