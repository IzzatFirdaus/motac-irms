{{-- resources/views/components/report/card.blade.php --}}
<div class="col">
    <div class="card h-100 shadow-sm motac-card">
        <div class="card-body d-flex flex-column p-4">
            <div class="mb-2">
                <i class="bi {{ $icon }} fs-2 text-primary"></i>
            </div>
            <h3 class="h5 fw-semibold mb-2">{{ $title }}</h3>
            <p class="card-text small text-muted mb-4">{{ $description }}</p>
            <a href="{{ $route }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
            </a>
        </div>
    </div>
</div>
