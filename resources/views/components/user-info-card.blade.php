{{-- resources/views/components/user-info-card.blade.php --}}
@props([
    'user',
    'title' => '',
    'cardClass' => 'card shadow-sm motac-card mb-4', // Default card classes
    'headerClass' => 'card-header bg-light py-3 motac-card-header',
    'bodyClass' => 'card-body p-3 p-md-4 motac-card-body',
])

@if ($user)
    <div class="{{ $cardClass }}">
        @if (!empty($title))
            <div class="{{ $headerClass }}">
                <h4 class="h5 card-title mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-lines-fill me-2 fs-5"></i>{{ $title }}
                </h4>
            </div>
        @endif
        <div class="{{ $bodyClass }}">
            <div class="row g-3">
                {{-- Column 1 --}}
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-person-badge me-1"></i>{{ __('Nama Penuh') }}:</label>
                        <span
                            class="fw-semibold">{{ ($user->title ? $user->title . ' ' : '') . ($user->full_name ?? $user->name) }}</span>
                    </div>

                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-card-heading me-1"></i>{{ __('No. Kad Pengenalan') }}:</label>
                        <span>{{ $user->identification_number ?? __('N/A') }}</span>
                    </div>

                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-person-workspace me-1"></i>{{ __('Jawatan') }}:</label>
                        <span>{{ optional($user->position)->name ?? __('N/A') }}</span>
                    </div>

                    <div class="mb-0"> {{-- Removed mb-2 for last item in col --}}
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-bar-chart-steps me-1"></i>{{ __('Gred') }}:</label>
                        <span>{{ optional($user->grade)->name ?? __('N/A') }}</span>
                    </div>
                </div>

                {{-- Column 2 --}}
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-building me-1"></i>{{ __('Bahagian / Unit') }}:</label>
                        <span>{{ optional($user->department)->name ?? __('N/A') }}</span>
                    </div>

                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-envelope-at-fill me-1"></i>{{ __('E-mel Rasmi (MOTAC)') }}:</label>
                        <span class="text-primary">{{ $user->motac_email ?? __('N/A') }}</span>
                    </div>

                    <div class="mb-2">
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-envelope-fill me-1"></i>{{ __('E-mel Peribadi (Login)') }}:</label>
                        <span>{{ $user->email ?? __('N/A') }}</span>
                    </div>

                    <div class="mb-0"> {{-- Removed mb-2 for last item in col --}}
                        <label class="fw-medium text-muted d-block small"><i
                                class="bi bi-telephone-fill me-1"></i>{{ __('No. Telefon Bimbit') }}:</label>
                        <span>{{ $user->mobile_number ?? __('N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <x-alert type="warning" :message="__('Maklumat pengguna tidak tersedia.')" icon="bi-exclamation-triangle-fill" />
@endif
