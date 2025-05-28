{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{-- Renders the application footer. --}}
{{-- Design Language: Clean & Uncluttered, Professional --}}
<div>
  <footer class="content-footer footer bg-footer-theme">
    @php
        // $configData is globally available via commonMaster.blade.php
        // $containerNav should default to 'container-fluid' for internal MOTAC system
        $containerClass = $containerNav ?? $configData['containerNav'] ?? 'container-fluid';
    @endphp
    <div class="{{ $containerClass }}">
      <div class="footer-container d-flex align-items-center justify-content-center py-2 flex-md-row flex-column">
        <div class="text-center">
          {{-- Design Language: Formal & Respectful Tone, Prominent MOTAC Branding (textual) --}}
          © <script>document.write(new Date().getFullYear())</script>
          {{ __('Copyright Bahagian Pengurusan Maklumat, MOTAC.') }}
          {{-- Using the key from my.json for copyright --}}
          {{-- This can be replaced by a more specific copyright string directly if preferred:
               e.g., __('Hak Cipta Terpelihara © :year Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia.', ['year' => date('Y')])
          --}}
        </div>
        {{-- Optional: Add other links here if necessary for internal systems, e.g., support contact or policy links --}}
        {{--
        <div class="ms-md-auto">
          <a href="{{ url('/hubungi-kami') }}" class="footer-link me-4">{{ __('Hubungi Kami') }}</a>
          <a href="{{ url('/dasar-privasi') }}" class="footer-link">{{ __('Dasar Privasi') }}</a>
        </div>
        --}}
      </div>
    </div>
  </footer>
</div>
