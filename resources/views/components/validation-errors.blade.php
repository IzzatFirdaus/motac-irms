{{-- resources/views/components/validation-errors.blade.php --}}
@if ($errors->any())
    <div {!! $attributes->merge(['class' => 'alert alert-danger small my-3']) !!} role="alert"> {{-- Added my-3 for spacing --}}
        <div class="d-flex align-items-center mb-1">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{-- Bootstrap Icon --}}
            <span class="fw-bold">{{ __('Amaran! Sila perbetulkan ralat berikut:') }}</span>
        </div>
        <ul class="mb-0 ps-4"> {{-- Adjusted padding start --}}
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
