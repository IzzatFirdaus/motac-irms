@props(['title', 'description', 'aside' => null])

<div {{ $attributes->merge(['class' => 'd-md-flex justify-content-between align-items-start mb-4']) }}> {{-- Changed to d-md-flex for responsiveness --}}
    <div class="mb-3 mb-md-0"> {{-- px-4 removed, handle padding on parent or via $attributes --}}
        <h3 class="h4">{{ $title }}</h3> {{-- Bootstrap heading class, adjust h1-h6 as needed --}}
        @if($description)
        <p class="text-muted mb-0">{{ $description }}</p> {{-- Bootstrap text-muted --}}
        @endif
    </div>

    @if ($aside)
    <div class="ms-md-3"> {{-- px-4 removed --}}
        {{ $aside }}
    </div>
    @endif
</div>
