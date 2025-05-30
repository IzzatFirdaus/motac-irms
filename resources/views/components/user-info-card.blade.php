{{-- resources/views/components/user-info-card.blade.php --}}
@props([
    'user', // Expects an App\Models\User object
    'title' => '', // Optional title for the card, default is empty
])

@if ($user)
    @if (!empty($title))
        <h4 class="mb-3">{{ $title }}</h4>
    @endif
    <div class="row gx-md-5 gy-3">
        {{-- Column 1 --}}
        <div class="col-md-6">
            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('Nama Penuh') }}:</span>
                <span class="fw-semibold">{{ ($user->title ? $user->title . ' ' : '') . ($user->full_name ?? $user->name) }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('No. Kad Pengenalan') }}:</span>
                <span>{{ $user->identification_number ?? __('N/A') }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('Jawatan') }}:</span>
                <span>{{ optional($user->position)->name ?? __('N/A') }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('Gred') }}:</span>
                <span>{{ optional($user->grade)->name ?? __('N/A') }}</span>
            </div>
        </div>

        {{-- Column 2 --}}
        <div class="col-md-6">
            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('Bahagian / Unit') }}:</span>
                <span>{{ optional($user->department)->name ?? __('N/A') }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('E-mel Rasmi (MOTAC)') }}:</span>
                <span class="text-primary">{{ $user->motac_email ?? __('N/A') }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('E-mel Peribadi (untuk Login)') }}:</span>
                <span>{{ $user->email ?? __('N/A') }}</span>
            </div>

            <div class="mb-2">
                <span class="fw-medium text-muted d-block small">{{ __('No. Telefon Bimbit') }}:</span>
                <span>{{ $user->mobile_number ?? __('N/A') }}</span>
            </div>
        </div>

        {{-- Optional: User ID Assigned (Network ID) could be added if relevant for the card's context --}}
        {{--
        <div class="col-12 mt-2">
            <span class="fw-medium text-muted d-block small">{{ __('ID Pengguna (Rangkaian)') }}:</span>
            <span>{{ $user->user_id_assigned ?? __('N/A') }}</span>
        </div>
        --}}
    </div>
@else
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ __('Maklumat pengguna tidak tersedia.') }}
    </div>
@endif
