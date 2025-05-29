{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{-- This is the Blade view for the Footer Livewire component. --}}
{{-- Design Language: Clean & Uncluttered, Professional, Bahasa Melayu. --}}

<div>
  <footer class="content-footer footer bg-footer-theme">
    {{-- $containerClass is passed from the Footer.php Livewire component, defaulting to container-fluid --}}
    <div class="{{ $containerClass ?? 'container-fluid' }}">
      <div class="footer-container d-flex align-items-center justify-content-center py-2 flex-md-row flex-column">
        <div class="text-center small"> {{-- Added 'small' for text size --}}
          {{ __('Hak Cipta Terpelihara') }} &copy; <script>document.write(new Date().getFullYear())</script>
          <a href="{{ url('/') }}" class="footer-link fw-semibold">{{ config('app.name', 'MOTAC') }}</a>.
          {{-- A more specific copyright:
          {{ __('Hak Cipta Terpelihara © :year Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia.', ['year' => date('Y')]) }}
          --}}
        </div>
        {{-- Example for internal links if needed, otherwise keep it clean --}}
        {{--
        <div class="d-none d-lg-inline-block ms-md-auto">
          <a href="{{ url('/panduan-pengguna') }}" class="footer-link me-4" target="_blank">{{ __('Panduan Pengguna') }}</a>
          <a href="{{ url('/hubungi-pentadbir') }}" class="footer-link">{{ __('Hubungi Pentadbir Sistem') }}</a>
        </div>
        --}}
      </div>
    </div>
  </footer>
</div>
