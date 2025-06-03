{{-- resources/views/livewire/pages/coming-soon-page.blade.php or a similar Blade view --}}
<div>

    @section('title', __('Sedang Dibangunkan')) {{-- Design Language 1.2: Bahasa Melayu First --}}

    {{--
    Comments about Livewire component title attribute vs. @section are noted and correct.
    This @section('title') approach is standard for Blade views extending a layout that @yields('title').
  --}}

    {{--
    Inline style is acceptable for simple, one-off pages.
    Ensure Noto Sans font (Design Language 2.2) is applied globally via commonMaster.blade.php or main theme CSS.
  --}}
    <div style="text-align: center;">
        <div class="container-xxl container-p-y">
            <div class="misc-wrapper">
                {{-- Design Language 1.4: Formal Tone. Emoji removed. Text changed to formal BM. --}}
                <h2 class="mb-1 mx-2">{{ __('Fungsi Ini Sedang Dibangunkan') }}</h2>
                <p class="mb-4 mx-2">
                    {{-- Design Language 1.4: Formal Tone. Text changed to formal BM. --}}
                    {{ __('Kami sedang berusaha untuk menambah baik fungsi ini. Ia akan tersedia tidak lama lagi. Terima kasih atas kesabaran anda.') }}
                </p>
                <div class="mt-4">
                    <img src="{{ asset('assets/img/illustrations/page-misc-launching-soon.png') }}" width="140"
                        alt="{{ __('Ilustrasi - Sedang Dibangunkan') }}" class="img-fluid"> {{-- Improved alt text --}}
                </div>
            </div>
        </div>
    </div>
</div>
