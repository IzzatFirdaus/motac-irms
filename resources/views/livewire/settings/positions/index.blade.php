<div>
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Jawatan') }}</h2>
            </div>
            <div class="col-md-6">
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari jawatan (nama, gred)...') }}">
            </div>
        </div>

        {{-- Example: <a href="{{ route('settings.positions.create') }}" class="btn btn-primary mb-3">{{ __('Tambah Jawatan Baru') }}</a> --}}

        <div class="card shadow-sm">
            <div class="card-body">
                @if($positions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    {{ __('Nama Jawatan') }}
                                    @if($sortField === 'name')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('grade_id')" style="cursor: pointer;">
                                    {{ __('Gred') }} {{-- Or sort by related table column if possible with a join --}}
                                    @if($sortField === 'grade_id')
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
                            @foreach ($positions as $position)
                            <tr>
                                <td>{{ $position->name }}</td>
                                <td>{{ $position->grade->name ?? '-' }}</td> {{-- Assumes 'grade' relationship exists and grade has a 'name' attribute --}}
                                <td>
                                    @if($position->is_active)
                                        <span class="badge bg-success">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Tidak Aktif') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Add action buttons (e.g., Edit, View) here --}}
                                    {{-- Example: <a href="{{ route('settings.positions.edit', $position) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a> --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $positions->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    {{ __('Tiada jawatan ditemui.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
