{{-- resources/views/livewire/resource-management/reports/loan-applications-report.blade.php --}}
<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-2 mb-md-0 d-flex align-items-center">
            <i class="bi bi-journal-text me-2"></i>
            {{ __('Laporan Permohonan Pinjaman Peralatan ICT') }}
        </h1>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i> {{ __('Penapisan & Carian') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Carian (ID, Tujuan)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari...') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Status Permohonan') }}</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Jabatan Pemohon') }}</label>
                    <select wire:model.live="filterDepartment" class="form-select">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Loan Applications Table --}}
    <div class="card motac-card">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-table me-2"></i> {{ __('Senarai Permohonan Pinjaman') }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('ID Permohonan') }}</th>
                        <th>{{ __('Pemohon') }}</th>
                        <th>{{ __('Jabatan') }}</th>
                        <th>{{ __('Tujuan') }}</th>
                        <th>{{ __('Tarikh Permohonan') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loanApplications as $loan)
                        <tr>
                            <td>#{{ $loan->id }}</td>
                            <td>{{ $loan->user?->name ?? '-' }}</td>
                            <td>{{ $loan->user?->department?->name ?? '-' }}</td>
                            <td style="max-width:220px;">{{ Str::limit($loan->purpose, 60) }}</td>
                            <td>{{ $loan->created_at->translatedFormat('d/m/Y H:i') }}</td>
                            <td><span class="badge {{ $loan->status_color_class }}">{{ $loan->status_translated }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">{{ __('Tiada permohonan pinjaman ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($loanApplications->hasPages())
            <div class="card-footer bg-light d-flex justify-content-center py-2">
                {{ $loanApplications->links() }}
            </div>
        @endif
    </div>
</div>
