{{--
    This Blade view is used by Livewire\Misc\ComingSoon component.
    It provides a formal "Coming Soon" message in Malay, as per MOTAC Design Language guidelines.
--}}

<div>
    {{-- Set the page title for layouts that @yield('title') --}}
    @section('title', __('Sedang Dibangunkan'))

    <div style="text-align: center;">
        <div class="container-xxl container-p-y">
            <div class="misc-wrapper">
                {{-- Main heading: formal, in Bahasa Melayu --}}
                <h2 class="mb-1 mx-2">{{ __('Fungsi Ini Sedang Dibangunkan') }}</h2>
                <p class="mb-4 mx-2">
                    {{ __('Kami sedang berusaha untuk menambah baik fungsi ini. Ia akan tersedia tidak lama lagi. Terima kasih atas kesabaran anda.') }}
                </p>
                <div class="mt-4">
                    {{-- The illustration image expresses "Coming Soon" visually --}}
                    <img src="{{ asset('assets/img/illustrations/page-misc-launching-soon.png') }}"
                         width="140"
                         alt="{{ __('Ilustrasi - Sedang Dibangunkan') }}"
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
