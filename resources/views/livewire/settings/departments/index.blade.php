<div>
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Jabatan') }}</h2>
            </div>
            <div class="col-md-6">
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari jabatan (nama, kod)...') }}">
            </div>
        </div>

        {{-- You can add a button here to create a new department if needed --}}
        {{-- Example: <a href="{{ route('settings.departments.create') }}" class="btn btn-primary mb-3">{{ __('Tambah Jabatan Baru') }}</a> --}}

        <div class="card shadow-sm">
            <div class="card-body">
                @if($departments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    {{ __('Nama Jabatan') }}
                                    @if($sortField === 'name')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('code')" style="cursor: pointer;">
                                    {{ __('Kod') }}
                                    @if($sortField === 'code')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('branch_type')" style="cursor: pointer;">
                                    {{ __('Jenis Cawangan') }}
                                    @if($sortField === 'branch_type')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                    {{ __('Status') }}
                                    @if($sortField === 'is_active')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->code ?? '-' }}</td>
                                <td>{{ $department->branch_type_label ?? $department->branch_type }}</td> {{-- Assuming you have a branch_type_label accessor or use constants --}}
                                <td>
                                    @if($department->is_active)
                                        <span class="badge bg-success">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Tidak Aktif') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Add action buttons (e.g., Edit, View) here --}}
                                    {{-- Example: <a href="{{ route('settings.departments.edit', $department) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a> --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $departments->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    {{ __('Tiada jabatan ditemui.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
