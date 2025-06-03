{{-- resources/views/profile/update-profile-information-form.blade.php (MOTAC Bootstrap 5 Version) --}}
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-person-lines-fill me-2"></i>{{ __('Maklumat Profil') }}
        </h3>
    </div>
    <form wire:submit.prevent="updateProfileInformation">
        <div class="card-body p-3 p-md-4">
            {{-- Action Message: Assumes you have a Bootstrap-styled alert component or handle session messages globally --}}
            @if (session()->has('status') && session('status_target') === $this->getId().'.updateProfileInformation')
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
                </div>
            @endif
             <div wire:loading wire:target="updateProfileInformation" class="alert alert-info small py-2 mb-3">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                {{ __('Menyimpan...') }}
            </div>


            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="mb-4" x-data="{photoName: null, photoPreview: null}">
                    <label class="form-label fw-medium">{{ __('Foto Profil') }}</label>
                    <div class="d-flex align-items-center">
                        <div x-show="!photoPreview" class="me-3">
                            <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-circle" style="height: 80px; width: 80px; object-fit: cover;">
                        </div>
                        <div x-show="photoPreview" style="display: none;" class="me-3">
                            <img x-bind:src="photoPreview" class="rounded-circle" style="height: 80px; width: 80px; object-fit: cover;" alt="{{__('Pratonton Foto Baru')}}">
                        </div>
                        <div>
                            <input type="file" class="d-none" wire:model.live="photo" x-ref="photo" id="profilePhotoInput-{{ $this->getId() }}"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   " />
                            <button type="button" class="btn btn-outline-secondary btn-sm me-2" x-on:click.prevent="$refs.photo.click()">
                                <i class="bi bi-upload me-1"></i>{{ __('Pilih Foto Baru') }}
                            </button>

                            @if ($this->user->profile_photo_path)
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="deleteProfilePhoto" title="{{__('Buang Foto')}}" wire:loading.attr="disabled" wire:target="deleteProfilePhoto">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    @error('photo') <span class="d-block text-danger small mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="mb-3">
                <label for="name-{{ $this->getId() }}" class="form-label fw-medium">{{ __('Nama Paparan Sistem') }} <span class="text-danger">*</span></label>
                <input id="name-{{ $this->getId() }}" type="text" class="form-control form-control-sm @error('state.name') is-invalid @enderror"
                       wire:model="state.name" required autocomplete="name" placeholder="{{__('Nama yang akan dipaparkan dalam sistem')}}" />
                @error('state.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email-{{ $this->getId() }}" class="form-label fw-medium">{{ __('Alamat E-mel (Login)') }} <span class="text-danger">*</span></label>
                <input id="email-{{ $this->getId() }}" type="email" class="form-control form-control-sm @error('state.email') is-invalid @enderror"
                       wire:model="state.email" required autocomplete="username" placeholder="{{__('cth: pengguna@example.com')}}"/>
                @error('state.email') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                    <p class="small text-muted mt-2">
                        {{ __('Alamat e-mel anda belum disahkan.') }}
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none align-baseline" wire:click.prevent="sendEmailVerification" wire:loading.attr="disabled">
                            {{ __('Klik di sini untuk menghantar semula e-mel pengesahan.') }}
                        </button>
                    </p>
                    @if ($this->verificationLinkSent)
                        <p class="small text-success mt-2">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('Pautan pengesahan baharu telah dihantar ke alamat e-mel anda.') }}
                        </p>
                    @endif
                @endif
            </div>
            {{-- Add other MOTAC-specific profile fields here (title, full_name, identification_number etc.)
                 using the same Bootstrap 5 structure as above.
                 Example:
            <div class="mb-3">
                <label for="full_name-{{ $this->getId() }}" class="form-label fw-medium">{{ __('Nama Penuh Rasmi') }}</label>
                <input id="full_name-{{ $this->getId() }}" type="text" class="form-control form-control-sm @error('state.full_name') is-invalid @enderror"
                       wire:model="state.full_name" autocomplete="name" placeholder="{{__('Seperti dalam Kad Pengenalan/Pasport')}}" />
                @error('state.full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            --}}
        </div>
        <div class="card-footer bg-light text-end py-3 border-top">
            <button type="submit" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled" wire:target="updateProfileInformation">
                <span wire:loading.remove wire:target="updateProfileInformation">
                    <i class="bi bi-save-fill me-1"></i>{{ __('Simpan Perubahan') }}
                </span>
                <span wire:loading wire:target="updateProfileInformation" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    {{ __('Menyimpan...') }}
                </span>
            </button>
        </div>
    </form>
</div>
