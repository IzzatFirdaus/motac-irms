{{-- resources/views/admin/profiles/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Edit Profil Saya'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <div
                    class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between mb-4 pb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold text-dark mb-1">{{ __('Profil Saya') }}</h1>
                        <p class="small text-muted mb-2 mb-sm-0">
                            {{ __('ID Pengguna') }}: {{ Auth::user()->id }} | {{ __('Dikemaskini terakhir') }}:
                            {{ Auth::user()->updated_at->translatedFormat('d M Y, H:i A') }}
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center mt-2 mt-sm-0">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Papan Pemuka') }}
                    </a>
                </div>

                {{-- Flash Messages --}}
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading fw-bold"><i
                                class="bi bi-x-octagon-fill me-2"></i>{{ __('Sila perbetulkan ralat berikut:') }}</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li><small>{{ $error }}</small></li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <form action="{{ route('resource-management.admin.profiles.update', Auth::user()) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-lg rounded-4 mb-4"> {{-- Slightly more rounded corners --}}
                        <div class="card-body p-4 p-lg-5">
                            <section class="mb-4">
                                <h2 class="h5 fw-semibold text-dark border-bottom pb-2 mb-3">
                                    <i class="bi bi-person-badge me-2"></i>{{ __('Maklumat Peribadi') }}
                                </h2>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-text', [
                                            'name' => 'name',
                                            'label' => __('Nama Paparan Pengguna'),
                                            'required' => true,
                                            'value' => Auth::user()->name,
                                            'placeholder' => __('Nama yang akan dipaparkan dalam sistem'),
                                        ])
                                    </div>

                                    <div class="col-md-6">
                                        {{-- Primary Login Email - Usually shown as readonly or with specific update flow --}}
                                        @include('admin.profiles.partials.input-email', [
                                            'name' => 'email',
                                            'label' => __('E-mel Utama (Login)'),
                                            'required' => true,
                                            'value' => Auth::user()->email,
                                            'placeholder' => __('E-mel untuk log masuk sistem'),
                                            // 'readonly' => true, // Consider making this readonly or having a separate process for changing primary email
                                        ])
                                    </div>

                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-text', [
                                            'name' => 'full_name', // Assuming you have a full_name attribute or an accessor
                                            'label' => __('Nama Penuh (seperti dalam IC/Passport)'),
                                            'value' =>
                                                Auth::user()->full_name ??
                                                (Auth::user()->title ? Auth::user()->title . ' ' : '') .
                                                    Auth::user()->name,
                                            'placeholder' => __('Cth: Encik Ahmad Bin Abu'),
                                        ])
                                    </div>


                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-email', [
                                            'name' => 'personal_email',
                                            'label' => __('E-mel Peribadi (Untuk Notifikasi Alternatif)'),
                                            'value' => Auth::user()->personal_email,
                                            'placeholder' => __('Cth: nama@example.com'),
                                        ])
                                    </div>

                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-text', [
                                            'name' => 'mobile_number', // Changed from phone_number to mobile_number for consistency with User model
                                            'label' => __('No. Telefon Bimbit'),
                                            'type' => 'tel',
                                            'value' => Auth::user()->mobile_number, // Directly use mobile_number from User model
                                            'placeholder' => __('Cth: 0123456789'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-text', [
                                            'name' => 'title',
                                            'label' => __('Gelaran (Cth: Encik, Puan, Dr.)'),
                                            'value' => Auth::user()->title,
                                            'placeholder' => __('Cth: Encik'),
                                        ])
                                    </div>
                                </div>
                            </section>

                            <hr class="my-4 opacity-50">

                            <section class="mb-2">
                                <h2 class="h5 fw-semibold text-dark border-bottom pb-2 mb-3">
                                    <i class="bi bi-shield-lock me-2"></i>{{ __('Tetapan Keselamatan') }}
                                </h2>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-password', [
                                            'name' => 'current_password',
                                            'label' => __('Kata Laluan Semasa'),
                                            'required' => false, // Only required if 'password' is filled
                                            'hint' => __('Hanya diperlukan jika anda ingin menukar kata laluan.'),
                                            'placeholder' => __('Masukkan kata laluan semasa anda'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        {{-- Spacer or other security settings can go here --}}
                                        {{-- Example: Two-Factor Authentication Toggle (if using Jetstream/Fortify features)
                                     @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Pengesahan Dua Faktor') }}</label>
                                            @if (Auth::user()->two_factor_secret)
                                                <p><span class="badge bg-success">{{ __('Aktif') }}</span></p>
                                                <button class="btn btn-sm btn-outline-danger" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                                                    {{ __('Nyahaktifkan 2FA') }}
                                                </button>
                                                Display recovery codes if needed
                                            @else
                                                <p><span class="badge bg-secondary">{{ __('Tidak Aktif') }}</span></p>
                                                <button class="btn btn-sm btn-outline-success" wire:click="enableTwoFactorAuthentication" wire:loading.attr="disabled">
                                                    {{ __('Aktifkan 2FA') }}
                                                </button>
                                            @endif
                                        </div>
                                     @endif
                                     --}}
                                    </div>


                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-password', [
                                            'name' => 'password',
                                            'label' => __(
                                                'Kata Laluan Baru (Biarkan kosong jika tidak mahu tukar)'),
                                            'placeholder' => __('Masukkan kata laluan baru'),
                                            'hint' => __(
                                                'Minimum 8 aksara. Lebih kuat dengan gabungan huruf, nombor & simbol.'),
                                        ])
                                    </div>

                                    <div class="col-md-6">
                                        @include('admin.profiles.partials.input-password', [
                                            'name' => 'password_confirmation',
                                            'label' => __('Sahkan Kata Laluan Baru'),
                                            'placeholder' => __('Taip semula kata laluan baru'),
                                        ])
                                    </div>
                                </div>
                            </section>

                            <div class="border-top pt-4 mt-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset"
                                        class="btn btn-outline-secondary d-inline-flex align-items-center">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Set Semula Borang') }}
                                    </button>
                                    <button type="submit" class="btn btn-success d-inline-flex align-items-center px-4">
                                        <i class="bi bi-save-fill me-1"></i> {{ __('Simpan Profil') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const inputId = this.dataset.target;
                    const input = document.getElementById(inputId);
                    const icon = this.querySelector('i');
                    if (input && icon) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('bi-eye-fill');
                            icon.classList.add('bi-eye-slash-fill');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('bi-eye-slash-fill');
                            icon.classList.add('bi-eye-fill');
                        }
                    }
                });
            });
        });
    </script>
@endpush
