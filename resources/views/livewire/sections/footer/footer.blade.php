{{-- resources/views/livewire/sections/footer/footer.blade.php --}}
{{--
    Application Footer
    Expects:
      - $containerNav (Bootstrap container class, e.g., 'container-fluid' or 'container-xxl')
--}}

<div>
  <footer class="content-footer footer motac-footer">
    @php
      // Use value passed from the Footer.php component for Bootstrap container class
      $containerClass = $containerNav;
    @endphp
    <div class="{{ $containerClass }}">
      {{-- Footer uses justify-content-between to space out the two main columns --}}
      <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">

        {{-- Column 1: Copyright and Page Links --}}
        <div class="text-center text-md-start mb-2 mb-md-0">
          <div class="text-muted small">
            {{-- Copyright text with current year and MOTAC link --}}
            &copy; <script>document.write(new Date().getFullYear())</script>
            <a href="https://www.motac.gov.my/" target="_blank" rel="noopener noreferrer" class="footer-link">
                {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}
            </a>
          </div>
          {{-- Important page links, separated by a vertical bar --}}
          <div class="pt-1">
            <a href="{{ route('terms') }}" class="footer-link me-2">{{ __('Terma Perkhidmatan') }}</a>
            <span class="text-muted">|</span>
            <a href="{{ route('contact-us') }}" class="footer-link ms-2">{{ __('Hubungi Kami') }}</a>
          </div>
        </div>

        {{-- Column 2: Social Media Links --}}
        {{-- Links to MOTAC's official social media accounts --}}
        <div class="d-flex justify-content-center">
            <a href="https://www.facebook.com/mymotac/" target="_blank" rel="noopener noreferrer" class="footer-link px-2" aria-label="MOTAC on Facebook">
                <i class="bi bi-facebook fs-5"></i>
            </a>
            <a href="https://www.instagram.com/mymotac/" target="_blank" rel="noopener noreferrer" class="footer-link px-2" aria-label="MOTAC on Instagram">
                <i class="bi bi-instagram fs-5"></i>
            </a>
            <a href="https://x.com/mymotac" target="_blank" rel="noopener noreferrer" class="footer-link px-2" aria-label="MOTAC on X">
                <i class="bi bi-twitter-x fs-5"></i>
            </a>
            <a href="https://www.youtube.com/user/mymotac" target="_blank" rel="noopener noreferrer" class="footer-link px-2" aria-label="MOTAC on YouTube">
                <i class="bi bi-youtube fs-5"></i>
            </a>
            <a href="https://www.tiktok.com/@mymotac" target="_blank" rel="noopener noreferrer" class="footer-link ps-2" aria-label="MOTAC on TikTok">
                <i class="bi bi-tiktok fs-5"></i>
            </a>
        </div>

      </div>
    </div>
  </footer>
</div>
