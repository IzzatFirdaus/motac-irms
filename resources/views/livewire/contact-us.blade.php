{{-- resources/views/livewire/contact-us.blade.php --}}
<div>
    @section('title', __('Hubungi Kami')) {{-- Set the page title --}}

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                {{-- Card styling will inherit from global MOTAC theme. Added shadow-sm for subtle depth. --}}
                <div class="card shadow-sm">
                    {{-- Card header using MOTAC primary blue. Padding adjusted for consistency. --}}
                    <div class="card-header bg-primary text-white p-3"> {{-- Using p-3 for padding similar to dashboard's motac-card-header --}}
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-chat-left-dots-fill me-2"></i>{{-- Bootstrap Icon --}}
                            {{ __('Hubungi Bahagian Pengurusan Maklumat (BPM), MOTAC') }}
                        </h4>
                    </div>
                    <div class="card-body p-4"> {{-- Using p-4 for padding similar to dashboard's motac-card-body --}}
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

                        {{-- If you decide to re-enable a direct contact form, ensure it aligns with the Helpdesk integration strategy. --}}
                        {{-- The original plan suggests a new Helpdesk system, so a direct form here might be redundant or require integration. --}}
                        {{--
                        <hr class="my-4">
                        <h5>{{ __('Hantar Mesej Terus') }}</h5>
                        <form wire:submit.prevent="sendMessage">
                            <div class="mb-3">
                                <label for="contactName" class="form-label">{{ __('Nama Anda') }}</label>
                                <input wire:model.defer="contactName" type="text" class="form-control @error('contactName') is-invalid @enderror" id="contactName" required>
                                @error('contactName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactEmail" class="form-label">{{ __('Emel Anda') }}</label>
                                <input wire:model.defer="contactEmail" type="email" class="form-control @error('contactEmail') is-invalid @enderror" id="contactEmail" required>
                                @error('contactEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactSubject" class="form-label">{{ __('Subjek') }}</label>
                                <input wire:model.defer="contactSubject" type="text" class="form-control @error('contactSubject') is-invalid @enderror" id="contactSubject" required>
                                @error('contactSubject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactMessage" class="form-label">{{ __('Mesej Anda') }}</label>
                                <textarea wire:model.defer="contactMessage" class="form-control @error('contactMessage') is-invalid @enderror" id="contactMessage" rows="4" required></textarea>
                                @error('contactMessage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading wire:target="sendMessage" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                {{ __('Hantar Mesej') }}
                            </button>
                            @if (session()->has('contact_form_message'))
                                <div class="alert alert-success mt-3 py-2">{{ session('contact_form_message') }}</div>
                            @endif
                        </form>
                        --}}
                    </div>
                    <div class="card-footer text-muted small p-3"> {{-- Added p-3 for padding --}}
                        {{ __('Sila pastikan anda memberikan maklumat yang lengkap untuk memudahkan pihak kami memberi bantuan.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
