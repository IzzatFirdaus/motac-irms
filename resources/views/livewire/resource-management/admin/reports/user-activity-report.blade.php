<div>
    {{-- Card Header --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold text-dark">
                    {{ __('Laporan Aktiviti Pengguna') }}
                </h3>
                @if (Route::has('reports.index')) {{-- Corrected route name based on typical naming --}}
                    <a href="{{ route('reports.index') }}"
                       class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Kembali ke Senarai Laporan') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body p-3 p-md-4">
            <x-alert-bootstrap /> {{-- Assuming a global Bootstrap alert component --}}

            {{-- Add Filters here if needed for the Livewire component --}}
            {{-- Example:
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <input wire:model.live.debounce.300ms="searchTerm" type="search" class="form-control form-control-sm" placeholder="{{ __('Cari pengguna...') }}">
                </div>
            </div>
            --}}

            <div class="table-responsive">
                @if ($users->isNotEmpty())
                    <table class="table table-hover table-striped table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small">{{ __('ID') }}</th>
                                <th class="small">{{ __('Nama') }}</th>
                                <th class="small">{{ __('Emel') }}</th>
                                <th class="small text-center">{{ __('Permohonan Emel') }}</th>
                                <th class="small text-center">{{ __('Permohonan Pinjaman') }}</th>
                                <th class="small text-center">{{ __('Kelulusan Dibuat') }}</th>
                                <th class="small">{{ __('Tarikh Daftar') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50">
                                <td colspan="7" class="p-0">
                                     <div wire:loading.flex class="progress" style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($users as $user)
                                <tr wire:key="user-{{ $user->id }}">
                                    <td class="small align-middle">{{ $user->id }}</td>
                                    <td class="small align-middle">{{ $user->name }}</td>
                                    <td class="small align-middle">{{ $user->email }}</td>
                                    <td class="small text-center align-middle">
                                        <span class="badge text-bg-info">{{ $user->email_applications_count ?? 0 }}</span>
                                    </td>
                                    <td class="small text-center align-middle">
                                        <span class="badge text-bg-primary">{{ $user->loan_applications_count ?? 0 }}</span>
                                    </td>
                                    <td class="small text-center align-middle">
                                        <span class="badge text-bg-success">{{ $user->approvals_count ?? 0 }}</span>
                                    </td>
                                    <td class="small align-middle">{{ $user->created_at?->translatedFormat(config('motac.date_format_my', 'd/m/Y')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($users->hasPages())
                        <div class="card-footer bg-light border-top-0 d-flex justify-content-center pt-3 pb-2">
                            {{ $users->links() }} {{-- Ensure Bootstrap pagination views are configured --}}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('Tiada data aktiviti pengguna tersedia.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
