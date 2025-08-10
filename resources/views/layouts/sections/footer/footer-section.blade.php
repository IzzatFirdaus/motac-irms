{{-- resources/views/livewire/sections/footer/footer-section.blade.php --}}
{{--
  Page Footer for MOTAC Integrated Resource Management System.
  Updated with a modern, accessible, and government-compliant design.
  - Uses dark theme for strong contrast and clear separation
  - Includes logo, system name, ministry, legal, important links, version, and last update date
--}}
<div>
    <footer class="content-footer bg-footer-alt" aria-label="{{ __('Pengaki Laman') }}">
        <div class="{{ $containerClass ?? 'container-fluid' }} footer-alt-container">
            {{-- Left: Logo and Ministry Info --}}
            <div>
                <div class="footer-brand">
                    <span class="footer-logo">
                        <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC') }}" />
                    </span>
                    <span class="footer-brand-text">motac-irms</span>
                </div>
                <div class="footer-ministry">
                    {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)') }}
                </div>
                <div class="footer-legal">
                    {{ __('Hak Cipta') }} &copy; {{ date('Y') }}. {{ __('Semua Hak Terpelihara.') }}
                </div>
                <div class="footer-social">
                    <a href="https://www.facebook.com/mymotac/" target="_blank" aria-label="Facebook" class="footer-link"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/mymotac/" target="_blank" aria-label="Instagram" class="footer-link"><i class="bi bi-instagram"></i></a>
                    <a href="https://x.com/mymotac" target="_blank" aria-label="X" class="footer-link"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.youtube.com/user/mymotac" target="_blank" aria-label="YouTube" class="footer-link"><i class="bi bi-youtube"></i></a>
                    <a href="https://www.tiktok.com/@mymotac" target="_blank" aria-label="TikTok" class="footer-link"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            {{-- Center: Useful Links --}}
            <nav class="footer-links" aria-label="{{ __('Pautan Penting') }}">
                <a href="{{ route('terms') }}" class="footer-link">{{ __('Terma Perkhidmatan') }}</a>
                <a href="{{ route('policy') }}" class="footer-link">{{ __('Dasar Privasi') }}</a>
                <a href="{{ route('contact-us') }}" class="footer-link">{{ __('Hubungi ICT') }}</a>
                {{-- Add more links as necessary --}}
            </nav>

            {{-- Right: System Info --}}
            <div class="footer-meta">
                <span class="footer-version">{{ __('Versi Sistem') }}: {{ config('app.version', '1.0.0') }}</span>
                <span class="footer-version">{{ __('Kemaskini Terakhir') }}: {{ date('d/m/Y') }}</span>
            </div>
        </div>
    </footer>

    {{-- Inline CSS for footer customization --}}
    <style>
        .bg-footer-alt {
            background: #263238;
            color: #fafafa;
            border-top: 4px solid #1976d2;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
        }
        .footer-alt-container {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px 32px;
            padding: 24px 16px 16px 16px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .footer-logo {
            width: 40px;
            height: 40px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 4px rgba(25, 118, 210, 0.20);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .footer-logo img {
            width: 32px;
            height: auto;
            display: block;
        }
        .footer-brand-text {
            font-weight: 700;
            color: #fafafa;
            font-size: 1.2em;
            letter-spacing: 0.04em;
        }
        .footer-ministry {
            color: #b3e5fc;
            font-size: 1.03em;
            margin-bottom: 6px;
        }
        .footer-legal {
            color: #b0bec5;
            font-size: 0.97em;
            margin-bottom: 8px;
        }
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.5em;
            margin-bottom: 10px;
        }
        .footer-link {
            color: #90caf9;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            display: inline-block;
            padding: 2px 0;
        }
        .footer-link:hover, .footer-link:focus {
            color: #fff;
            text-decoration: underline;
            outline: none;
        }
        .footer-meta {
            text-align: right;
            font-size: 0.97em;
            min-width: 170px;
        }
        .footer-meta .footer-version {
            color: #b0bec5;
            margin-bottom: 3px;
            display: block;
        }
        .footer-social {
            margin-top: 12px;
            display: flex;
            gap: 10px;
        }
        .footer-social a {
            color: #b3e5fc;
            font-size: 1.25em;
            transition: color 0.2s;
        }
        .footer-social a:hover { color: #fff; }
        @media (max-width: 900px) {
            .footer-alt-container {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                gap: 16px 0;
            }
            .footer-meta {
                text-align: left;
            }
        }
        @media (max-width: 600px) {
            .footer-alt-container {
                padding: 20px 8px 10px 8px;
            }
            .footer-brand-text {
                font-size: 1.05em;
            }
            .footer-ministry {
                font-size: 0.98em;
            }
            .footer-meta {
                min-width: 0;
            }
        }
    </style>
    {{-- Bootstrap Icons CDN (for preview/production, should be loaded globally) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</div>
