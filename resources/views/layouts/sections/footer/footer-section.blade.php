{{--
    MYDS-compliant Footer Section Layout
    resources/views/layouts/sections/footer/footer-section.blade.php

    Displays government branding, navigation links, and legal info in the footer.
    - Uses MYDS grid, color tokens, spacing, typography, and responsive layout.
    - Follows MyGOVEA principles: citizen-centric, minimal, clear, accessible, consistent, documented.
    - All icons and images use asset() helper for path resolution (fallback logic for static analysis).
    - All routes use route() helper for URL generation (fallback logic for static analysis).
    - Uses config() for dynamic government name (fallback logic for static analysis).
--}}

@php
    // Helper: Use asset() for logo, fallback to static path if not available
    $logoUrl = function_exists('asset')
        ? asset('assets/img/logos/motac-logo-footer.svg')
        : '/assets/img/logos/motac-logo-footer.svg';

    // Helper: Use route() for navigation links, fallback to static paths if not available
    $homeUrl = function_exists('route') ? route('home') : '/';
    $privacyUrl = function_exists('route') ? route('privacy') : '/privacy';
    $disclaimerUrl = function_exists('route') ? route('disclaimer') : '/disclaimer';
    $contactUrl = function_exists('route') ? route('contact') : '/contact';

    // Helper: Use config() for agency name, fallback to raw string
    $agencyName = function_exists('config')
        ? config('app.government_name', 'Kementerian Pelancongan, Seni dan Budaya Malaysia')
        : 'Kementerian Pelancongan, Seni dan Budaya Malaysia';
@endphp

<footer class="myds-footer myds-bg-washed py-4 border-top" role="contentinfo" aria-label="Footer Section">
    <div class="container">
        <div class="row align-items-center">
            {{-- Footer Logo & Branding --}}
            <div class="col-12 col-md-4 mb-3 mb-md-0 text-center text-md-start">
                <a href="{{ $homeUrl }}" aria-label="Halaman Utama">
                    <img src="{{ $logoUrl }}" alt="Logo MOTAC" style="height:40px;max-width:160px;">
                </a>
                <div class="fw-semibold mt-2" style="font-family:'Poppins',Arial,sans-serif; font-size:1rem; color:var(--myds-primary-700);">
                    {{ $agencyName }}
                </div>
            </div>
            {{-- Footer Navigation Links --}}
            <div class="col-12 col-md-5 mb-3 mb-md-0 text-center text-md-start d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                <a href="{{ $privacyUrl }}" class="myds-footer-link small text-muted" style="text-decoration:none;">
                    Privasi
                </a>
                <span class="mx-1 text-muted">|</span>
                <a href="{{ $disclaimerUrl }}" class="myds-footer-link small text-muted" style="text-decoration:none;">
                    Penafian
                </a>
                <span class="mx-1 text-muted">|</span>
                <a href="{{ $contactUrl }}" class="myds-footer-link small text-muted" style="text-decoration:none;">
                    Hubungi Kami
                </a>
            </div>
            {{-- Footer Legal Info & Last Updated --}}
            <div class="col-12 col-md-3 text-center text-md-end mt-2 mt-md-0">
                <span class="small text-muted" style="font-family:'Inter',Arial,sans-serif;">
                    &copy; {{ date('Y') }} {{ $agencyName }}. Semua Hak Cipta Terpelihara.
                </span>
                <br>
                <span class="small text-muted" style="font-size: 0.9em;">
                    Dikemaskini: {{ date('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>
</footer>

{{--
    === MYDS & MyGOVEA Compliance Notes ===
    - Footer uses MYDS bg-washed, border-top, spacing, and grid system.
    - Logo and agency name follow branding guidelines (left for desktop, top for mobile).
    - Navigation links use semantic markup and are keyboard accessible.
    - Legal info uses Inter font for clarity and compliance.
    - All links and assets use fallback logic for static analysis or non-Blade rendering.
    - Responsive for mobile/tablet/desktop via grid classes.
    - Accessible: role="contentinfo", aria-label, alt text for logo.
    - Documentation: Inline comments explain usage, compliance, and helper fallbacks.
--}}
