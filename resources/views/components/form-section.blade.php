{{-- resources/views/components/form-section.blade.php --}}
@props(['submit'])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}> {{-- Consistent margin --}}
    @if (isset($title))
        <div class="card-header bg-light py-3"> {{-- Standard Bootstrap card header styling --}}
            <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
        </div>
    @endif

    <div class="card-body p-3 p-md-4"> {{-- Added consistent padding --}}
        <form wire:submit.prevent="{{ $submit }}">
            @if (isset($description))
                <p class="card-text text-muted small mb-3">{{ $description }}</p>
            @endif

            <div class="form-content"> {{-- Added a wrapper for the form slot for potential styling --}}
                {{ $form }}
            </div>

            @if (isset($actions))
                <div class="d-flex justify-content-end pt-3 mt-4 border-top"> {{-- Increased mt-4 for more space --}}
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
