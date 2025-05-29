{{--
    NOTE: This is a structural conversion to Bootstrap 5.
    The CONTENT of this page is from Laravel Jetstream and is NOT specific to the MOTAC system.
    For the MOTAC application, this page should likely be replaced by custom dashboards
    or MOTAC-specific content as per the System Design Document (Revision 3).
--}}

<div class="container-fluid p-0"> {{-- Assuming this might be part of a larger layout that provides padding --}}

    <div class="p-4 p-lg-5 bg-light border-bottom"> {{-- Bootstrap bg-light and border --}}
        {{-- Assuming x-application-logo is Bootstrap compatible or an SVG --}}
        <x-application-logo style="height: 3rem; width: auto;" /> {{-- Adjusted styling --}}

        <h1 class="mt-4 h2"> {{-- Bootstrap heading --}}
            {{-- Content needs to be MOTAC specific --}}
            {{ __('Selamat Datang ke Aplikasi Jetstream Anda!') }}  {{-- Example of making string translatable --}}
        </h1>

        <p class="mt-3 text-muted lead">
            {{-- Content needs to be MOTAC specific --}}
            Laravel Jetstream menyediakan titik permulaan yang kemas dan mantap untuk aplikasi Laravel anda yang seterusnya. Laravel direka untuk membantu anda membina aplikasi anda menggunakan persekitaran pembangunan yang ringkas, berkuasa dan menyeronokkan. Kami percaya anda patut menyukai ekspresi kreativiti anda melalui pengaturcaraan, jadi kami telah meluangkan masa dengan teliti mencipta ekosistem Laravel agar menyegarkan. Kami harap anda menyukainya.
        </p>
    </div>

    <div class="bg-light py-4 px-lg-5">
        <div class="row g-4 g-lg-5"> {{-- Bootstrap grid --}}
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    {{-- SVG Icon for Documentation --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="text-secondary" style="width: 1.5rem; height: 1.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="ms-3 h5 mb-0">
                        <a href="https://laravel.com/docs" class="text-decoration-none text-dark fw-semibold">{{ __('Documentation') }}</a>
                    </h2>
                </div>

                <p class="mt-3 text-muted small">
                    {{-- Content needs to be MOTAC specific --}}
                    Laravel mempunyai dokumentasi hebat yang merangkumi setiap aspek kerangka kerja. Sama ada anda baru mengenali kerangka kerja atau mempunyai pengalaman sebelumnya, kami mengesyorkan membaca semua dokumentasi dari awal hingga akhir.
                </p>

                <p class="mt-3 small">
                    <a href="https://laravel.com/docs" class="d-inline-flex align-items-center fw-semibold text-primary text-decoration-none">
                        {{ __('Explore the documentation') }} [cite: 1]
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1" style="width: 1.25rem; height: 1.25rem; fill: currentColor;">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    {{-- SVG Icon for Laracasts --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="text-secondary" style="width: 1.5rem; height: 1.5rem;">
                        <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <h2 class="ms-3 h5 mb-0">
                        <a href="https://laracasts.com" class="text-decoration-none text-dark fw-semibold">{{ __('Laracasts') }}</a>
                    </h2>
                </div>

                <p class="mt-3 text-muted small">
                    {{-- Content needs to be MOTAC specific --}}
                    Laracasts menawarkan ribuan tutorial video mengenai pembangunan Laravel, PHP, dan JavaScript. Lihat sendiri, dan tingkatkan kemahiran pembangunan anda secara besar-besaran dalam proses tersebut.
                </p>

                <p class="mt-3 small">
                    <a href="https://laracasts.com" class="d-inline-flex align-items-center fw-semibold text-primary text-decoration-none">
                        {{ __('Start watching Laracasts') }} [cite: 1]
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1" style="width: 1.25rem; height: 1.25rem; fill: currentColor;">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            {{-- The "Tailwind" and "Authentication" sections from the original Jetstream welcome page are omitted here
                 as Tailwind is not the target framework, and Authentication details would be part of the MOTAC
                 system's specific user flows and documentation, not this generic welcome content. --}}

        </div>
    </div>
</div>
