{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{--
    MYDS & MyGOVEA Compliant Application Footer
    Expects:
      - $containerNav (Bootstrap container class, e.g., 'container-fluid' or 'container-xxl')
    Features:
      - MYDS design system compliant
      - WCAG 2.1 accessibility standards
      - MyGOVEA citizen-centric principles
      - Semantic HTML structure
      - Ministry logo and name, system name, copyright
      - Important links: terms, privacy, contact
      - Social media icons (official MOTAC accounts)
      - System version and last update
--}}

<footer class="content-footer footer bg-footer-theme" role="contentinfo" aria-label="{{ __('Pengaki Laman') }}">
    @php
        // Use value passed from the Footer.php component for Bootstrap container class
        $containerClass = $containerNav ?? 'container-fluid';
    @endphp

    <div class="{{ $containerClass }} d-flex flex-wrap justify-content-between py-4 flex-md-row flex-column">
        {{-- Left Section: Ministry and System Info --}}
        <div class="mb-2 mb-md-0">
            <div class="d-flex align-items-center mb-2">
                <span class="footer-logo me-3">
                    <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                         alt="{{ __('Logo MOTAC') }}"
                         width="32"
                         height="32"
                         loading="lazy" />
                </span>
                <div>
                    <div class="fw-semibold">{{ __('app.system_name') }}</div>
                    <small class="text-muted">{{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}</small>
                </div>
            </div>

            <div class="footer-copyright">
                {{ __('Hak Cipta') }} &copy;
                <script>document.write(new Date().getFullYear())</script>
                {{ __('Kerajaan Malaysia') }}. {{ __('Semua Hak Terpelihara') }}.
            </div>
        </div>

        {{-- Center Section: Important Links --}}
        <nav class="d-flex flex-column flex-sm-row gap-2 mb-2 mb-md-0"
             aria-label="{{ __('Pautan Penting') }}"
             role="navigation">
            <a href="{{ route('terms') }}"
               class="footer-link mygovea-accessible"
               aria-label="{{ __('Terma dan Syarat Perkhidmatan') }}">
                {{ __('Terma Perkhidmatan') }}
            </a>
            <span class="d-none d-sm-inline text-muted">|</span>
            <a href="{{ route('policy') }}"
               class="footer-link mygovea-accessible"
               aria-label="{{ __('Dasar Privasi dan Perlindungan Data') }}">
                {{ __('Dasar Privasi') }}
            </a>
            <span class="d-none d-sm-inline text-muted">|</span>
            <a href="{{ route('contact-us') }}"
               class="footer-link mygovea-accessible"
               aria-label="{{ __('Hubungi Pasukan ICT') }}">
                {{ __('Hubungi ICT') }}
            </a>
        </nav>

        {{-- Right Section: Social Media & System Info --}}
        <div class="text-md-end">
            {{-- Social Media Links --}}
            <div class="social-links mb-2">
                <span class="small text-muted me-2">{{ __('Ikuti MOTAC:') }}</span>
                <a href="https://www.facebook.com/mymotac/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-social-link mygovea-accessible"
                   aria-label="{{ __('Facebook MOTAC') }}"
                   title="{{ __('Facebook MOTAC') }}">
                    <i class="ti ti-brand-facebook" aria-hidden="true"></i>
                </a>
                <a href="https://www.instagram.com/mymotac/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-social-link mygovea-accessible"
                   aria-label="{{ __('Instagram MOTAC') }}"
                   title="{{ __('Instagram MOTAC') }}">
                    <i class="ti ti-brand-instagram" aria-hidden="true"></i>
                </a>
                <a href="https://x.com/mymotac"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-social-link mygovea-accessible"
                   aria-label="{{ __('X (Twitter) MOTAC') }}"
                   title="{{ __('X (Twitter) MOTAC') }}">
                    <i class="ti ti-brand-x" aria-hidden="true"></i>
                </a>
                <a href="https://www.youtube.com/user/mymotac"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-social-link mygovea-accessible"
                   aria-label="{{ __('YouTube MOTAC') }}"
                   title="{{ __('YouTube MOTAC') }}">
                    <i class="ti ti-brand-youtube" aria-hidden="true"></i>
                </a>
            </div>

            {{-- System Version Info --}}
            <div class="footer-meta small text-muted">
                <div>{{ __('Versi Sistem') }}: {{ config('app.version', '4.0.0') }}</div>
                <div>{{ __('Kemaskini') }}: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- MYDS-compliant footer styles --}}
    <style>
        .footer-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--myds-bg-primary);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.1);
        }

        .footer-link {
            color: var(--myds-txt-secondary);
            text-decoration: none;
            transition: color 0.2s ease;
            font-weight: 500;
        }

        .footer-link:hover,
        .footer-link:focus {
            color: var(--myds-primary);
            text-decoration: underline;
        }

        .footer-social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin: 0 4px;
            color: var(--myds-txt-muted);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .footer-social-link:hover,
        .footer-social-link:focus {
            color: var(--myds-primary);
            background-color: var(--myds-bg-muted);
            transform: translateY(-1px);
        }

        .footer-copyright {
            font-size: 0.875rem;
            color: var(--myds-txt-muted);
            margin-bottom: 0;
        }

        .footer-meta {
            line-height: 1.4;
        }

        .social-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        /* Dark mode adjustments */
        [data-bs-theme="dark"] .footer-logo {
            background: var(--myds-gray-800);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Mobile responsiveness */
        @media (max-width: 767px) {
            .social-links {
                justify-content: flex-start;
                margin-bottom: 12px;
            }

            .footer-meta {
                text-align: left !important;
            }
        }
    </style>
</footer>
