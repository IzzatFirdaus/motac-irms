@extends('layouts.app')

@section('title', 'Senarai Peralatan ICT')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 dark:text-gray-100">{{ __('Senarai Peralatan ICT') }}</h1>
        <div>
            @can('create', App\Models\Equipment::class)
                {{-- Link to the route that loads the Livewire EquipmentForm for creation --}}
                <a href="{{ route('resource-management.admin.equipment-admin.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> {{ __('Tambah Peralatan Baru') }}
                </a>
            @endcan
        </div>
    </div>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    {{-- Livewire component for equipment listing and filtering is recommended here --}}
    {{-- For now, assuming a standard Blade list is still in use for this specific path 'equipment.index' --}}
    {{-- If AdminEquipmentIndexLW is the primary index, this Blade view might be vestigial or for a different role. --}}

    <x-card> {{-- Using the card component --}}
        @if ($equipment->isEmpty())
            <x-alert type="info" message="Tiada peralatan ICT ditemui dalam inventori." />
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm"> {{-- Added table-sm for compactness --}}
                    <thead class="table-light">
                        <tr>
                            <th class="small">ID</th>
                            <th class="small">Jenis Aset</th>
                            <th class="small">Jenama & Model</th>
                            <th class="small">Tag ID</th>
                            <th class="small">No. Siri</th>
                            <th class="small">Status Operasi</th>
                            <th class="small">Status Keadaan</th>
                            <th class="small">Lokasi</th>
                            <th class="text-end small">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment as $item)
                            <tr>
                                <td class="small">{{ $item->id }}</td>
                                <td class="small">{{ $item->asset_type_label }}</td>
                                <td class="small">
                                    <div class="fw-medium">{{ $item->brand ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 0.8em;">{{ $item->model ?? 'N/A' }}</div>
                                </td>
                                <td class="small">{{ $item->tag_id ?? 'N/A' }}</td>
                                <td class="small">{{ $item->serial_number ?? 'N/A' }}</td>
                                <td class="small">
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->status, 'bootstrap_badge') }} px-2 py-1">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="small">
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status, 'bootstrap_badge_condition') }} px-2 py-1">
                                        {{ $item->condition_status_label }}
                                    </span>
                                </td>
                                <td class="small">{{ $item->current_location ?? 'N/A' }}</td>
                                <td class="text-end">
                                    @can('view', $item)
                                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-info border-0 p-1" title="Lihat">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update', $item)
                                        {{-- Link to the route that loads the Livewire EquipmentForm for editing --}}
                                        <a href="{{ route('resource-management.admin.equipment-admin.edit', $item) }}" class="btn btn-sm btn-outline-primary border-0 p-1 ms-1" title="Edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $item)
                                        {{-- Implement delete as a Livewire action or a modal confirmation for safety --}}
                                        {{-- Example basic form:
                                        <form action="{{ route('equipment.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti ingin memadam peralatan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1" title="Padam">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        --}}
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($equipment->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                    {{ $equipment->links() }}
                </div>
            @endif
        @endif
    </x-card>
</div>
@endsection
