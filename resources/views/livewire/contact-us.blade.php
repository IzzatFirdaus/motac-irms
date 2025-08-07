{{-- resources/views/livewire/contact-us.blade.php --}}
<div>
    @section('title', __('Hubungi Kami')) {{-- Set the page title --}}

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-chat-left-dots-fill me-2"></i>
                            {{ __('Hubungi Bahagian Pengurusan Maklumat (BPM), MOTAC') }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="lead">
                            {{ __('Untuk sebarang pertanyaan, bantuan teknikal berkaitan Pinjaman Peralatan ICT, atau maklum balas mengenai Sistem Pengurusan Sumber Bersepadu MOTAC, sila hubungi kami melalui maklumat di bawah atau layari Sistem Meja Bantuan kami.') }}
                        </p>
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h5><i class="bi bi-telephone-fill me-2 text-primary"></i>{{ __('Telefon') }}</h5>
                                <p class="mb-1">{{ __('Talian Utama:') }} <a href="tel:+60380008000" class="text-decoration-none">+603-8000 8000</a></p>
                                <p class="mb-0">{{ __('Faks:') }} <a href="tel:+60388888624" class="text-decoration-none">+603-8888 8624</a></p>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h5><i class="bi bi-envelope-fill me-2 text-primary"></i>{{ __('Emel') }}</h5>
                                <p class="mb-0"><a href="mailto:bpm@motac.gov.my" class="text-decoration-none">bpm@motac.gov.my</a></p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5><i class="bi bi-geo-alt-fill me-2 text-primary"></i>{{ __('Alamat') }}</h5>
                            <address>
                                {{ __('Bahagian Pengurusan Maklumat') }}<br>
                                {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)') }}<br>
                                {{ __('No. 2, Menara 1, Jalan P5/6, Presint 5') }}<br>
                                {{ __('62200 PUTRAJAYA') }}<br>
                                {{ __('MALAYSIA') }}
                            </address>
                        </div>
                    </div>
                    <div class="card-footer text-muted small p-3">
                        {{ __('Sila pastikan anda memberikan maklumat yang lengkap untuk memudahkan pihak kami memberi bantuan.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
