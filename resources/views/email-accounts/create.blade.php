@php
    // The $message variable is automatically available within @error blocks in Laravel Blade.
    // Linter warnings about unassigned $message (PHP1412) within @error blocks are false positives.
@endphp
@extends('layouts.app')

@section('title', __('Daftar Akaun E-mel / ID Pengguna Secara Terus'))

@pushOnce('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endPushOnce

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-lg rounded-3 border-0">
                <div class="card-header bg-primary text-white">
                    <h1 class="fs-4 fw-bold mb-0 text-center d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-fill-add me-2"></i>
                        {{ __('Daftar Akaun E-mel / ID Pengguna (Pentadbir IT)') }}
                    </h1>
                </div>
                <div class="card-body p-4 p-sm-5">
                    <p class="text-muted text-center mb-4">{{__('Gunakan borang ini untuk mendaftar akaun e-mel rasmi MOTAC atau ID Pengguna secara terus untuk pengguna sedia ada.')}}</p>

                    @include('partials.validation-errors-alt') {{-- Assumes standard partial for validation errors --}}
                    @include('partials.alert-messages') {{-- Assumes standard partial for session messages --}}

                    {{-- Ensure this route 'admin.email-accounts.store-direct' exists and points to a controller method --}}
                    <form action="{{ route('admin.email-accounts.store-direct') }}" method="POST" class="vstack gap-3">
                        @csrf

                        <div class="mb-3">
                            <label for="select2UserId" class="form-label fw-semibold">{{ __('Pilih Pengguna') }} <span class="text-danger">*</span></label>
                            <div wire:ignore> {{-- Add wire:ignore if this select2 is within a Livewire parent, otherwise not needed --}}
                                <select name="user_id" id="select2UserId" class="form-select select2-user-selection @error('user_id') is-invalid @enderror" required data-placeholder="{{ __('-- Pilih Pengguna Berdaftar --') }}">
                                    <option value="">{{ __('-- Pilih Pengguna Berdaftar --') }}</option>
                                    {{-- $usersForSelection should be passed from the controller --}}
                                    @foreach ($usersForSelection ?? [] as $user)
                                        @if(is_object($user))
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ e($user->name) }} ({{ e($user->email) }}) - {{ e(optional($user->department)->name ?? 'Jabatan T/D') }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @error('user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="motac_email" class="form-label fw-semibold">{{ __('E-mel Rasmi MOTAC yang akan Didaftarkan') }} <span class="text-danger">*</span></label>
                            <input type="email" name="motac_email" id="motac_email" class="form-control @error('motac_email') is-invalid @enderror" value="{{ old('motac_email') }}" placeholder="cth: nama.pengguna@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}" required>
                            <div class="form-text small">{{__('Pastikan format adalah betul dan unik.')}}</div>
                            @error('motac_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user_id_assigned" class="form-label fw-semibold">{{ __('ID Pengguna / Network ID (Jika Ada)') }}</label>
                            <input type="text" name="user_id_assigned" id="user_id_assigned" class="form-control @error('user_id_assigned') is-invalid @enderror" value="{{ old('user_id_assigned') }}" placeholder="{{__('Cth: MOTAC/NAMA/123 atau nama.pengguna')}}">
                            <div class="form-text small">{{__('Jika berbeza dari prefix e-mel atau untuk akses sistem lain.')}}</div>
                            @error('user_id_assigned') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="initial_password" class="form-label fw-semibold">{{ __('Kata Laluan Awal (Pilihan)') }}</label>
                            <input type="password" name="initial_password" id="initial_password" class="form-control @error('initial_password') is-invalid @enderror" autocomplete="new-password">
                            <div class="form-text small">{{__('Biarkan kosong untuk kata laluan rawak (akan dimaklumkan kepada pengguna). Jika diisi, pastikan mematuhi polisi keselamatan.')}}</div>
                            @error('initial_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="mb-3">
                            <label for="initial_password_confirmation" class="form-label fw-semibold">{{ __('Sahkan Kata Laluan Awal') }}</label>
                            <input type="password" name="initial_password_confirmation" id="initial_password_confirmation" class="form-control">
                        </div>


                        <div class="mb-3">
                            <label for="admin_notes" class="form-label fw-semibold">{{ __('Nota / Justifikasi Pentadbir') }}</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror" rows="3" placeholder="{{__('Nyatakan sebab pendaftaran terus atau maklumat tambahan untuk rujukan.')}}">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="notify_user" id="notify_user" value="1" class="form-check-input" checked>
                            <label class="form-check-label" for="notify_user">
                                {{ __('Maklumkan kepada pengguna selepas akaun berjaya didaftarkan.') }}
                            </label>
                        </div>

                        <div class="d-flex justify-content-center mt-4 pt-2 border-top">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2 d-inline-flex align-items-center"> {{-- Adjust route as needed --}}
                                <i class="bi bi-x-circle me-1"></i>{{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-person-check-fill me-2"></i>
                                {{ __('Daftar Akaun Ini') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@pushOnce('custom-scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{asset('assets/vendor/libs/select2/i18n/ms.js')}}"></script> {{-- Assuming Malay translation for Select2 --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
            const selectUserEl = $('#select2UserId');
            if (selectUserEl.length) {
                selectUserEl.select2({
                    placeholder: "{{ __('Pilih Pengguna Berdaftar') }}",
                    dropdownParent: selectUserEl.parent(), // Adjust if modal or other complex parent
                    allowClear: true,
                    language: "ms"
                });
            }
        } else {
            console.error("jQuery or Select2 is not loaded for Email Account Create page.");
        }
    });
</script>
@endPushOnce
