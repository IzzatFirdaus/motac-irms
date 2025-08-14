{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
    {{-- Log at the start of the vertical menu blade --}}
    @php
        \Illuminate\Support\Facades\Log::info('Rendering vertical-menu.blade.php');
        \Illuminate\Support\Facades\Log::debug('menuData available in vertical-menu: ' . (isset($menuData) ? 'true' : 'false'));
        \Illuminate\Support\Facades\Log::debug('configData available in vertical-menu: ' . (isset($configData) ? 'true' : 'false'));
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">

        <div class="app-brand demo px-3 py-2 border-bottom">
            <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo-icon.svg') }}"
                        alt="{{ __('Logo Aplikasi') }}" height="32">
                </span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __($configData['templateName'] ?? config('app.name')) }}</span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" aria-label="{{ __('Tutup/Buka Menu Sisi') }}">
                <i class="bi bi-list d-block fs-4 align-middle"></i>
            </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" role="menu">
            @if (isset($menuData) && !empty($menuData->menu))
                {{-- Log before including the submenu partial --}}
                @php \Illuminate\Support\Facades\Log::info('Including submenu-partial.blade.php from vertical-menu.'); @endphp
                @include('layouts.sections.menu.submenu-partial', ['menuItems' => $menuData->menu])
            @else
                <li class="menu-item" role="none">
                    <a href="javascript:void(0);" class="menu-link" role="menuitem">
                        <i class="menu-icon bi bi-exclamation-circle-fill"></i>
                        <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
                    </a>
                </li>
                {{-- Log if no menu data is available --}}
                @php \Illuminate\Support\Facades\Log::warning('No menu data available in vertical-menu.blade.php to render.'); @endphp
            @endif
        </ul>
    </aside>
</div>
