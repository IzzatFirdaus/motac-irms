@props(['user', 'title' => __('MAKLUMAT PEMOHON')])

{{-- Assuming x-card provides a Bootstrap card structure --}}
<x-action-section :title="$title"> {{-- Using action-section for card structure if appropriate, or a more generic x-card --}}
    <x-slot name="content">
        @if ($user)
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('Nama Penuh:') }}</label>
                    <p class="form-control-plaintext ps-0">{{ $user->name ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('No. Pengenalan (NRIC):') }}</label>
                    <p class="form-control-plaintext ps-0">{{ $user->identification_number ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('Jawatan & Gred:') }}</label>
                    <p class="form-control-plaintext ps-0">
                        {{ optional($user->position)->name ?? __('N/A') }} ({{ optional($user->grade)->name ?? __('N/A') }})
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('Bahagian/Unit:') }}</label>
                    <p class="form-control-plaintext ps-0">{{ optional($user->department)->name ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('No. Telefon Bimbit:') }}</label>
                    <p class="form-control-plaintext ps-0">{{ $user->mobile_number ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">{{ __('E-mel Peribadi:') }}</label>
                    <p class="form-control-plaintext ps-0">{{ $user->email ?? __('N/A') }}</p> {{-- Assuming personal_email is user->email for login --}}
                </div>
            </div>
        @else
            {{-- Assuming x-alert is the refactored Bootstrap alert component --}}
            <x-alert type="warning" :message="__('Maklumat pengguna tidak dapat dimuatkan.')"/>
        @endif
    </x-slot>
</x-action-section>
