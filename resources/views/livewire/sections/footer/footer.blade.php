{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{-- Renders the application footer. --}}
{{-- Design Language Alignment:
     - Principle: Clean & Uncluttered, Professional, Formal & Respectful Tone.
     - Colors (2.1): Footer background likely var(--motac-surface) or var(--bs-tertiary-bg), text var(--motac-text-muted) or var(--motac-text).
     - Typography (2.2): Noto Sans, legible size (likely small or default body).
     - Branding: Prominent MOTAC Branding (textual).
--}}
<div>
  {{-- The 'motac-footer' class should be defined in your main MOTAC theme CSS
       to apply appropriate background, border-top, text colors, and padding
       according to the Design Language Documentation.
       The 'content-footer' class can be kept if it provides useful base styling from your theme. --}}
  <footer class="content-footer footer motac-footer">
    @php
        // $containerNav is now correctly determined and passed from the Footer.php Livewire component.
        // $configData is globally available via commonMaster.blade.php but not directly needed here for containerClass.
        $containerClass = $containerNav; // Directly use the value passed from the component
    @endphp
    <div class="{{ $containerClass }}">
      {{--
        The footer-container div centers content and handles responsive flex direction.
        Text within should inherit Noto Sans from the global theme.
        The 'footer-link' class needs MOTAC theming for color and hover states.
      --}}
      <div class="footer-container d-flex align-items-center justify-content-center py-2 flex-md-row flex-column">
        <div class="text-center small"> {{-- Added 'small' class for slightly smaller footer text if desired --}}
          {{-- Design Language: Formal & Respectful Tone, Prominent MOTAC Branding (textual) --}}
          {{ __('Hak Cipta Terpelihara') }} &copy; <script>document.write(new Date().getFullYear())</script>
          <a href="{{ config('variables.creatorUrl', 'https://motac.gov.my') }}" target="_blank" rel="noopener noreferrer" class="footer-link fw-medium">{{-- Ensure .footer-link is MOTAC themed --}}
            {{ __(config('variables.creatorName', 'Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia')) }}.
          </a>
          {{ __('Semua Hak Terpelihara.') }} {{-- Added for completeness --}}
        </div>
        {{-- Optional: Add other links here if necessary for internal systems --}}
        {{--
        <div class="ms-md-auto"> {{-- Only show if there are links --}}
            {{-- Example: <a href="{{ url('/bantuan') }}" class="footer-link me-4">{{ __('Bantuan Sistem') }}</a> --}}
            {{-- Example: <a href="{{ url('/dasar-privasi') }}" class="footer-link">{{ __('Dasar Privasi') }}</a> --}}
        {{-- </div>
        --}}
      </div>
    </div>
  </footer>
</div>
