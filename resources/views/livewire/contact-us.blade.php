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
                            {{ __('Untuk sebarang pertanyaan, bantuan teknikal, atau maklum balas berkaitan Sistem Pengurusan Sumber Bersepadu MOTAC (Emel/ID Pengguna & Pinjaman Peralatan ICT), sila hubungi kami melalui maklumat di bawah:') }}
                        </p>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5>
                                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i>{{-- Bootstrap Icon --}}
                                    {{ __('Alamat') }}
                                </h5>
                                <address class="mb-0" style="line-height: 1.6;"> {{-- Improved line-height for address block --}}
                                    Bahagian Pengurusan Maklumat (BPM)<br>
                                    Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)<br>
                                    Aras 5, Blok D4, Kompleks D,<br>
                                    Pusat Pentadbiran Kerajaan Persekutuan,<br>
                                    62505 Putrajaya,<br>
                                    Wilayah Persekutuan Putrajaya,<br>
                                    Malaysia.
                                </address>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5>
                                    <i class="bi bi-telephone-fill me-2 text-primary"></i>{{-- Bootstrap Icon --}}
                                    {{ __('Telefon') }}
                                </h5>
                                <p class="mb-2">Unit Operasi Rangkaian dan Khidmat Pengguna (Helpdesk BPM):<br>
                                    +603-8000 8000 (samb. xxxx) </p>

                                <h5 class="mt-3"> {{-- Added margin-top for better separation --}}
                                    <i class="bi bi-envelope-fill me-2 text-primary"></i>{{-- Bootstrap Icon --}}
                                    {{ __('E-mel') }}
                                </h5>
                                <p class="mb-0"><a href="mailto:bpm.helpdesk@motac.gov.my" class="text-primary">bpm.helpdesk@motac.gov.my</a></p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">
                            <i class="bi bi-clock-history me-2 text-primary"></i>{{-- Bootstrap Icon --}}
                            {{ __('Waktu Operasi Meja Bantuan') }}
                        </h5>
                        <p class="mb-1"><strong>{{ __('Isnin - Jumaat:') }}</strong> 8:30 PG - 5:00 PTG</p>
                        <p class="mb-0"><strong>{{ __('Tutup:') }}</strong>
                            {{ __('Sabtu, Ahad & Cuti Umum Malaysia') }}</p>

                        {{-- Optional: Simple Contact Form - Remains commented. --}}
                        {{-- If re-enabled, it uses Bootstrap classes and should align well. --}}
                        {{--
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-chat-right-text me-2 text-primary"></i>{{ __('Hantar Mesej Pantas') }}</h5>
                        <form wire:submit.prevent="sendMessage">
                            <div class="mb-3">
                                <label for="contactName" class="form-label">{{ __('Nama Anda') }}</label>
                                <input type="text" wire:model.defer="contactName" class="form-control @error('contactName') is-invalid @enderror" id="contactName" required>
                                @error('contactName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactEmail" class="form-label">{{ __('Emel Anda') }}</label>
                                <input type="email" wire:model.defer="contactEmail" class="form-control @error('contactEmail') is-invalid @enderror" id="contactEmail" required>
                                @error('contactEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
