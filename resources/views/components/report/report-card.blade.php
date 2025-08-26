{{-- resources/views/components/report/report-card.blade.php --}}
{{--
    MOTAC - Reusable Report Card Component
    This Blade component renders a Bootstrap card for dashboard/report tiles with icon, title, description, and a view button.
    Usage: <x-report.report-card :icon="'bi-bar-chart'" :title="'Some Title'" :description="'Desc...'" :route="route('something')" />

    Props:
    - $icon: Bootstrap icon class (e.g. 'bi-bar-chart')
    - $title: Card title
    - $description: Card description
    - $route: Route or URL for the "View Report" button
--}}

<div class="col">
    <div class="motac-card h-100 shadow-sm">
        <div class="card-body d-flex flex-column p-4">
            {{-- Icon at the top --}}
            <div class="mb-2">
                <i class="bi {{ $icon }} fs-2 text-primary"></i>
            </div>
            {{-- Title --}}
            <h3 class="h5 fw-semibold mb-2">{{ $title }}</h3>
            {{-- Description --}}
            <p class="card-text small text-muted mb-4">{{ $description }}</p>
            {{-- "View Report" Button at the bottom --}}
            <a href="{{ $route }}" class="motac-btn-primary btn-sm mt-auto d-inline-flex align-items-center" role="button" aria-label="{{ __('Lihat Laporan: :title', ['title' => $title]) }}">
                <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
            </a>
        </div>
    </div>
</div>
