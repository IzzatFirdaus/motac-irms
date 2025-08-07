{{-- resources/views/layouts/sections/footer/footer-section.blade.php --}}
{{--
  Page Footer for MOTAC Integrated Resource Management System.
  Filename updated from footer.blade.php to footer-section.blade.php to follow descriptive, consistent naming conventions.
  Design Language References:
  - 1.1 Professionalism: Clean, uncluttered.
  - 1.2 User-Centricity: Clear, concise information in Bahasa Melayu.
  - 2.2 Typography: Noto Sans, appropriate small text size (via theme CSS or custom).
  - 2.1 Color Palette: Subtle background (Surface or light Background variant), text with good contrast.
--}}
<div>
    {{-- The 'bg-footer-theme' class should be styled in your theme's CSS
       to match MOTAC's desired footer background color and text color. --}}
    <footer class="content-footer footer bg-footer-theme" aria-label="{{ __('Pengaki Laman') }}">
        {{-- $containerClass is passed from the Footer.php Livewire component (e.g., 'container-fluid' or 'container-xxl') --}}
        <div class="{{ $containerClass ?? 'container-fluid' }}">
            <div
                class="footer-container d-flex align-items-center justify-content-md-between justify-content-center py-2 flex-md-row flex-column">
                <div class="text-center text-md-start mb-2 mb-md-0">
                    {{-- Design Language 1.4: Formal Tone. Using a more complete and formal copyright notice. --}}
                    <small class="text-muted">
                        {{ __('Hak Cipta Terpelihara') }} &copy; {{ date('Y') }} <a
                            href="{{ config('variables.creatorUrl', 'https://www.motac.gov.my') }}" target="_blank"
                            rel="noopener noreferrer"
                            class="footer-link fw-semibold">{{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)') }}</a>.
                        {{ __('Semua Hak Terpelihara.') }}
                        {{-- Version display - if needed --}}
                        {{-- | {{ __('Versi Sistem') }}: {{ config('app.version', '1.0.0') }} --}}
                    </small>
                </div>
                {{-- Optional: Links for documentation, support, or privacy policy --}}
                {{-- Ensure these links are relevant and maintained for MOTAC users. --}}
                <div class="d-none d-lg-inline-block">
                    {{-- Example:
          <a href="{{ url('/panduan-pengguna') }}" class="footer-link me-4" target="_blank">{{ __('Panduan Pengguna') }}</a>
          <a href="{{ url('/dasar-privasi') }}" class="footer-link me-3">{{ __('Dasar Privasi') }}</a>
          <a href="{{ url('/terma-syarat') }}" class="footer-link">{{ __('Terma & Syarat') }}</a>
          --}}
                    <small class="text-muted">
                        @if (config('app.version'))
                            {{ __('Versi Sistem') }}: {{ config('app.version', '1.0.0') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </footer>
</div>
