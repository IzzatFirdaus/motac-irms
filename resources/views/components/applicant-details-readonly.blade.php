{{-- resources/views/components/applicant-details-readonly.blade.php --}}
@props(['user', 'title' => __('MAKLUMAT PEMOHON')])

{{-- Assuming x-action-section provides a Bootstrap card structure styled by MOTAC theme.
     Alternatively, wrap this in a standard <div class="card motac-card"> with <card-header> and <card-body>. --}}
<x-action-section :title="$title">
    <x-slot name="content">
        @if ($user)
            <div class="row g-3 small"> {{-- Added small class for text size consistency --}}
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Nama Penuh:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->name ?? __('N/A') }}</p> {{-- Example with border for visual separation --}}
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('No. Pengenalan (NRIC):') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->identification_number ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Jawatan & Gred:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">
                        {{ optional($user->position)->name ?? __('N/A') }} ({{ optional($user->grade)->name ?? __('N/A') }})
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('Bahagian/Unit:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ optional($user->department)->name ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('No. Telefon Bimbit:') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->mobile_number ?? __('N/A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">{{ __('E-mel (Login):') }}</label>
                    <p class="form-control-plaintext ps-0 border-bottom pb-1 mb-0">{{ $user->email ?? __('N/A') }}</p>
                </div>
            </div>
        @else
            {{-- Assuming x-alert is the refactored Bootstrap alert component --}}
            <x-alert type="warning" :message="__('Maklumat pengguna tidak dapat dimuatkan.')" :icon="'bi-exclamation-triangle-fill'" />
        @endif
    </x-slot>
</x-action-section>
