<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-start border-{{ $color ?? 'primary' }} border-4 shadow-sm h-100 py-2 motac-card">
        <div class="card-body">
            <div class="row g-0 align-items-center">
                <div class="col">
                    <div class="text-xs fw-bold text-{{ $color ?? 'primary' }} text-uppercase mb-1">
                        {{ $label ?? 'Stat' }}
                    </div>
                    <div class="h5 mb-0 fw-bold text-dark">
                        {{ $value ?? 0 }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="{{ $icon ?? 'bi bi-bar-chart' }} fs-2 text-gray-300"></i>
                </div>
            </div>
            @if (!empty($link))
                <a href="{{ $link }}" class="stretched-link" title="{{ $label }}"></a>
            @endif
        </div>
    </div>
</div>
