{{-- resources/views/livewire/resource-management/reports/user-activity-report.blade.php --}}
<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-2 mb-md-0 d-flex align-items-center">
            <i class="bi bi-person-check-fill me-2"></i>
            {{ __('Laporan Aktiviti Pengguna') }}
        </h1>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i> {{ __('Penapisan & Carian') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Carian (Nama, Emel)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari pengguna...') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card motac-card">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-activity me-2"></i> {{ __('Rekod Aktiviti Pengguna') }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Nama') }}</th>
                        <th>{{ __('Emel') }}</th>
                        <th>{{ __('Jabatan') }}</th>
                        <th>{{ __('Jumlah Permohonan') }}</th>
                        <th>{{ __('Jumlah Kelulusan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department?->name ?? '-' }}</td>
                            <td>{{ $user->loan_applications_as_applicant_count }}</td>
                            <td>{{ $user->approvals_as_approver_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('Tiada data aktiviti pengguna ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-light d-flex justify-content-center py-2">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
