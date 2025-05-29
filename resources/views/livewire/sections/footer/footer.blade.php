{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{-- Renders the application footer. --}}
{{-- Design Language: Clean & Uncluttered, Professional --}}
<div>
  <footer class="content-footer footer bg-footer-theme">
    @php
        // $configData is globally available via commonMaster.blade.php
        // This ensures container class consistency with the main layout.
        $containerClass = $configData['containerNav'] ?? $containerNav ?? 'container-fluid';
    @endphp
    <div class="{{ $containerClass }}">
      <div class="footer-container d-flex align-items-center justify-content-center py-2 flex-md-row flex-column">
        <div class="text-center">
          {{-- Design Language: Formal & Respectful Tone, Prominent MOTAC Branding (textual) --}}
          {{ __('Hak Cipta Terpelihara') }} © <script>document.write(new Date().getFullYear())</script>
          <a href="{{ config('variables.creatorUrl', 'https://motac.gov.my') }}" target="_blank" class="footer-link fw-medium">{{ __(config('variables.creatorName', 'Bahagian Pengurusan Maklumat, MOTAC')) }}.</a>
          {{-- This provides flexibility via config/variables.php or defaults to MOTAC. --}}
        </div>
        {{-- Optional: Add other links here if necessary for internal systems --}}
        {{--
        <div class="ms-md-auto"> {{-- Only show if there are links --}}
            {{-- Example: <a href="{{ url('/bantuan') }}" class="footer-link me-4">{{ __('Bantuan Sistem') }}</a> --}}
        {{-- </div>
        --}}
      </div>
    </div>
  </footer>
</div>
