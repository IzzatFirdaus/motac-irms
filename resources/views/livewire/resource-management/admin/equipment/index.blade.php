@extends('layouts.app') {{-- Main application layout --}}

@section('title', __('Senarai Peralatan ICT'))

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 dark:text-gray-100">{{ __('Senarai Peralatan ICT') }}</h1>
        <div>
            @can('create', App\Models\Equipment::class)
                {{-- This route should load the page that includes the EquipmentForm Livewire component for creation --}}
                <a href="{{ route('resource-management.admin.equipment-admin.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> {{ __('Tambah Peralatan Baru') }}
                </a>
            @endcan
            {{-- Add other actions like Import/Export if needed --}}
        </div>
    </div>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    {{-- OPTION 1: If this page IS the main equipment admin index, load the Livewire component here: --}}
    {{-- @livewire('resource-management.admin.equipment.admin-equipment-index-lw') --}}

    {{-- OPTION 2: If this is a traditional Blade view with data from controller (as the code suggests): --}}
    <x-card>
        @if (!isset($equipment) || $equipment->isEmpty())
            <div class="p-4">
                 <x-alert type="info" message="{{__('Tiada peralatan ICT ditemui dalam inventori.')}}" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase">#</th>
                            <th class="small text-uppercase">{{__('Jenis Aset')}}</th>
                            <th class="small text-uppercase">{{__('Jenama & Model')}}</th>
                            <th class="small text-uppercase">{{__('Tag ID')}}</th>
                            <th class="small text-uppercase">{{__('No. Siri')}}</th>
                            <th class="small text-uppercase">{{__('Status Operasi')}}</th>
                            <th class="small text-uppercase">{{__('Status Keadaan')}}</th>
                            <th class="small text-uppercase">{{__('Lokasi Semasa')}}</th>
                            <th class="text-end small text-uppercase">{{__('Tindakan')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment as $item)
                            <tr>
                                <td class="small align-middle">{{ $loop->iteration + ($equipment->currentPage() - 1) * $equipment->perPage() }}</td>
                                <td class="small align-middle">{{ $item->asset_type_label ?? __('N/A') }}</td>
                                <td class="small align-middle">
                                    <div class="fw-medium">{{ $item->brand ?? __('N/A') }}</div>
                                    <div class="text-muted" style="font-size: 0.8em;">{{ $item->model ?? __('N/A') }}</div>
                                </td>
                                <td class="small align-middle">{{ $item->tag_id ?? __('N/A') }}</td>
                                <td class="small align-middle">{{ $item->serial_number ?? __('N/A') }}</td>
                                <td class="small align-middle">
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->status, 'bootstrap_badge') }} px-2 py-1">
                                        {{ $item->status_label ?? __(Str::title(str_replace('_',' ',$item->status))) }}
                                    </span>
                                </td>
                                <td class="small align-middle">
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status, 'bootstrap_badge_condition') }} px-2 py-1">
                                        {{ $item->condition_status_label ?? __(Str::title(str_replace('_',' ',$item->condition_status))) }}
                                    </span>
                                </td>
                                <td class="small align-middle">{{ $item->current_location ?? __('N/A') }}</td>
                                <td class="text-end align-middle">
                                    {{-- @can('view', $item) --}} {{-- Assuming general show route for equipment --}}
                                        {{-- <a href="{{ route('equipment.show', $item->id) }}" class="btn btn-sm btn-icon btn-outline-info border-0 me-1" title="{{__('Lihat')}}">
                                            <i class="ti ti-eye"></i>
                                        </a> --}}
                                    {{-- @endcan --}}
                                    @can('update', $item)
                                        {{-- This route should load the page that includes the EquipmentForm Livewire component for editing --}}
                                        <a href="{{ route('resource-management.admin.equipment-admin.edit', $item->id) }}" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1" title="{{__('Edit')}}">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $item)
                                        {{-- For deletion, it's best to use a Livewire action with confirmation --}}
                                        {{-- Example: <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{__('Padam')}}"><i class="ti ti-trash"></i></button> --}}
                                        <button onclick="confirm('Anda pasti ingin memadam peralatan ini: {{ $item->tag_id }}?') || event.stopImmediatePropagation()"
                                                wire:click="deleteEquipment({{ $item->id }})" {{-- Assuming a deleteEquipment method in a parent Livewire component if this view is part of one --}}
                                                class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{__('Padam')}}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($equipment->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-3">
                    {{ $equipment->links() }}
                </div>
            @endif
        @endif
    </x-card>
    {{-- End of OPTION 2 --}}
</div>
@endsection
