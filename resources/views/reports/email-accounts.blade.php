<x-app-layout>
    {{-- The outer container is managed by x-app-layout --}}

    <div class="card shadow-sm mb-4">
        {{-- Card Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mt-2">
                    <h3 class="h5 mb-0">
                        {{ __('Laporan Akaun Emel ICT') }}
                    </h3>
                </div>
                @if (Route::has('admin.reports.index'))
                    <div class="mt-2 flex-shrink-0">
                        <a href="{{ route('admin.reports.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Kembali ke Laporan') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">
            {{-- General alerts partial - ensure this partial is also Bootstrap styled --}}
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($emailApplications->isNotEmpty())
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small">{{ __('ID') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Pemohon') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Emel Dicadang') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Status') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Tarikh') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($emailApplications as $application)
                                <tr>
                                    <td class="fw-medium">{{ $application->id }}</td>
                                    <td>{{ $application->user->full_name ?? $application->user->name }}</td>
                                    <td>{{ $application->proposed_email }}</td>
                                    <td>
                                        @php
                                            // Assuming Helpers::getStatusColorClass() can be adapted
                                            // to return Bootstrap text/badge classes or you map them here.
                                            // Example: 'text-success', 'badge bg-warning text-dark'
                                            $statusClass = \App\Helpers\Helpers::getStatusColorClass(
                                                $application->status,
                                            );
                                            // If it returns Tailwind, map it:
                                            // $statusClass = match($statusClass) {
                                            //    'text-green-600' => 'text-success',
                                            //    'text-yellow-600' => 'text-warning',
                                            //     default => 'text-secondary',
                                            // };
                                        @endphp
                                        <span class="{{ $statusClass }} fw-semibold">
                                            {{ $application->status }} {{-- Consider using a more presentable status name --}}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($emailApplications->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $emailApplications->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Warning:">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                            {{ __('Tiada permohonan emel ditemui.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
