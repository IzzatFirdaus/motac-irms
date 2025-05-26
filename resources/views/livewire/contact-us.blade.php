<div>
    @section('title', __('Hubungi Kami'))

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('Hubungi Bahagian Pengurusan Maklumat (BPM), MOTAC') }}</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">{{ __('Untuk sebarang pertanyaan, bantuan teknikal, atau maklum balas berkaitan Sistem Pengurusan Pinjaman Peralatan ICT dan Permohonan Emel/ID Pengguna, sila hubungi kami melalui maklumat di bawah:') }}</p>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ __('Alamat') }}</h5>
                                <address>
                                    Bahagian Pengurusan Maklumat (BPM)<br>
                                    Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)<br>
                                    Aras 5<br> {{-- Example Address Detail --}}
                                    Presint 1, Pusat Pentadbiran Kerajaan Persekutuan<br>
                                    62505 Putrajaya<br>
                                    Malaysia
                                </address>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-phone-alt me-2 text-primary"></i>{{ __('Telefon') }}</h5>
                                <p>Unit Operasi Rangkaian dan Khidmat Pengguna (Helpdesk BPM):<br>
                                   +603-1234 5678 (Contoh) </p>


                                <h5><i class="fas fa-envelope me-2 text-primary"></i>{{ __('E-mel') }}</h5>
                                <p><a href="mailto:bpm.helpdesk@motac.gov.my">bpm.helpdesk@motac.gov.my</a> (Contoh)</p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="far fa-clock me-2 text-primary"></i>{{ __('Waktu Operasi Meja Bantuan') }}</h5>
                        <p class="mb-1"><strong>{{ __('Isnin - Jumaat:') }}</strong> 8:30 PG - 5:00 PTG</p>
                        <p><strong>{{ __('Tutup:') }}</strong> {{ __('Sabtu, Ahad & Cuti Umum') }}</p>

                        {{-- Optional: Simple Contact Form if logic is added to Livewire component --}}
                        {{--
                        <hr class="my-4">
                        <h5>{{ __('Hantar Mesej Pantas') }}</h5>
                        <form wire:submit.prevent="sendMessage">
                            <div class="mb-3">
                                <label for="contactName" class="form-label">{{ __('Nama Anda') }}</label>
                                <input type="text" wire:model.defer="contactName" class="form-control" id="contactName" required>
                                @error('contactName') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactEmail" class="form-label">{{ __('Emel Anda') }}</label>
                                <input type="email" wire:model.defer="contactEmail" class="form-control" id="contactEmail" required>
                                @error('contactEmail') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="contactMessage" class="form-label">{{ __('Mesej Anda') }}</label>
                                <textarea wire:model.defer="contactMessage" class="form-control" id="contactMessage" rows="4" required></textarea>
                                @error('contactMessage') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading wire:target="sendMessage" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{ __('Hantar Mesej') }}
                            </button>
                            @if(session()->has('contact_form_message'))
                                <div class="alert alert-success mt-3 py-2">{{ session('contact_form_message') }}</div>
                            @endif
                        </form>
                        --}}

                    </div>
                    <div class="card-footer text-muted small">
                       {{ __('Sila pastikan anda memberikan maklumat yang lengkap untuk memudahkan pihak kami memberi bantuan.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
