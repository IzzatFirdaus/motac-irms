{{-- resources/views/livewire/resource-management/admin/equipment/index.blade.php --}}
@php
    use App\Helpers\Helpers;
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', __('Pengurusan Peralatan ICT'))

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">{{ __('Pengurusan Peralatan ICT') }}</h1>
            <div>
                @can('create', App\Models\Equipment::class)
                    {{-- Button to open Livewire modal within this component --}}
                    <button wire:click="openCreateModal" class="btn btn-primary d-inline-flex align-items-center me-2">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah (Modal)') }}
                    </button>
                    {{-- Link to a full-page Livewire form --}}
                    <a href="{{ route('resource-management.equipment-admin.create') }}" class="btn btn-outline-primary d-inline-flex align-items-center">
                        <i class="bi bi-node-plus-fill me-1"></i> {{ __('Tambah (Halaman Penuh)') }}
                    </a>
                @endcan
            </div>
        </div>

        {{-- Toastr success/error messages will be handled by dispatched events if you have a global listener --}}
        {{-- Example of how toastr might be initialized in your main layout:
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('toastr', event => {
                    toastr[event.type](event.message);
                });
            });
        </script>
        --}}

        {{-- Search and Filter UI --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                 <h3 class="h5 card-title fw-semibold mb-0">
                    {{ __('Carian & Tapis') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12 mb-2">
                        <label for="searchTerm" class="form-label visually-hidden">{{__('Carian Pantas')}}</label>
                        <input type="text" wire:model.live.debounce.300ms="searchTerm" id="searchTerm" class="form-control form-control-sm" placeholder="{{ __('Cari No. Tag, Siri, Jenama, Model...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterAssetType" class="form-label visually-hidden">{{__('Jenis Aset')}}</label>
                        <select wire:model.live="filterAssetType" id="filterAssetType" class="form-select form-select-sm">
                            <option value="">-- {{ __('Semua Jenis Aset') }} --</option>
                            @foreach($assetTypeOptions as $value => $label)
                                <option value="{{ $value }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                         <label for="filterStatus" class="form-label visually-hidden">{{__('Status Operasi')}}</label>
                        <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                            <option value="">-- {{ __('Semua Status Operasi') }} --</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterCondition" class="form-label visually-hidden">{{__('Status Kondisi')}}</label>
                        <select wire:model.live="filterCondition" id="filterCondition" class="form-select form-select-sm">
                            <option value="">-- {{ __('Semua Kondisi') }} --</option>
                            @foreach($conditionStatusOptions as $value => $label)
                                <option value="{{ $value }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterDepartmentId" class="form-label visually-hidden">{{__('Jabatan')}}</label>
                        <select wire:model.live="filterDepartmentId" id="filterDepartmentId" class="form-select form-select-sm">
                            <option value="">-- {{ __('Semua Jabatan') }} --</option>
                            @foreach($departmentOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div wire:loading.delay.long class="text-center my-4">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
            </div>
            <p class="mt-2 text-muted">{{ __('Sedang memuatkan senarai peralatan, sila tunggu...') }}</p>
        </div>

        {{-- Equipment Table --}}
        <div class="card shadow-sm" wire:loading.remove.delay.long>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Tag Aset') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis Aset') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenama') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Model') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Operasi') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Kondisi') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipmentList as $item)
                                <tr wire:key="equipment-{{ $item->id }}">
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="#" wire:click.prevent="openViewModal({{ $item->id }})" class="text-decoration-none text-primary-emphasis" title="{{__('Lihat Butiran Pantas')}}">
                                            {{ $item->tag_id ?? __('N/A') }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $item->asset_type_translated ?? ($item->asset_type ? __(Str::title(str_replace('_', ' ', $item->asset_type))) : __('N/A')) }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->brand ?? __('N/A') }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->model ?? __('N/A') }}</td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ Helpers::getStatusColorClass($item->status ?? '') }}">
                                            {{ $item->status_translated ?? ($item->status ? __(Str::title(str_replace('_', ' ', $item->status))) : __('N/A')) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ Helpers::getStatusColorClass($item->condition_status ?? '') }}">
                                            {{ $item->condition_status_translated ?? ($item->condition_status ? __(Str::title(str_replace('_', ' ', $item->condition_status))) : __('N/A')) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? __('N/A') }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($item->activeLoanTransaction?->loanApplication?->user)
                                            <a href="{{ route('settings.users.show', $item->activeLoanTransaction->loanApplication->user) }}" class="text-decoration-none" title="{{ __('Lihat Profil Pengguna') }}">
                                                {{ $item->activeLoanTransaction->loanApplication->user->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button wire:click="openViewModal({{ $item->id }})" type="button" class="btn btn-sm btn-outline-info border-0 p-1" title="{{ __('Lihat Butiran Pantas') }}"><i class="bi bi-display"></i></button>
                                            @can('update', $item)
                                                <button wire:click="openEditModal({{ $item->id }})" type="button" class="btn btn-sm btn-outline-primary border-0 p-1" title="{{ __('Kemaskini (Modal)') }}"><i class="bi bi-pencil-fill"></i></button>
                                            @endcan
                                            @can('delete', $item)
                                                <button wire:click="openDeleteModal({{ $item->id }})" type="button" class="btn btn-sm btn-outline-danger border-0 p-1" title="{{ __('Padam') }}"><i class="bi bi-trash3-fill"></i></button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-5 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted small">
                                            <i class="bi bi-info-circle-fill fs-1 text-secondary mb-2"></i>
                                            {{ __('Tiada rekod peralatan ICT ditemui yang sepadan dengan kriteria anda.') }}
                                            @if (!$searchTerm && !$filterAssetType && !$filterStatus && !$filterCondition && !$filterDepartmentId)
                                                @can('create', App\Models\Equipment::class)
                                                    <p class="mt-1">{{ __('Cuba tambah peralatan baru.') }}</p>
                                                @endcan
                                            @else
                                                 <p class="mt-1">{{ __('Sila laraskan tapisan carian anda.') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($equipmentList instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipmentList->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3 px-3">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Include Modals --}}
        {{-- The showCreateModal and showEditModal will control the same equipmentFormModal instance --}}
        @if($showCreateModal || $showEditModal)
            @include('livewire.resource-management.admin.equipment.partials.equipment-form-modal')
        @endif

        @if($showDeleteModal)
            @include('livewire.resource-management.admin.equipment.partials.delete-confirmation-modal')
        @endif

        @if($showViewModal && $viewingEquipment)
            @include('livewire.resource-management.admin.equipment.partials.view-equipment-modal')
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        // Ensure this script is loaded after Livewire and Bootstrap's JS
        document.addEventListener('livewire:init', () => {
            // Listener to open Bootstrap modals
            Livewire.on('open-modal', (event) => {
                let modalElement = document.getElementById(event.modalId);
                if (modalElement) {
                    let modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalInstance.show();
                }
            });

            // Listener to close Bootstrap modals
            Livewire.on('close-modal', (event) => {
                let modalElement = document.getElementById(event.modalId);
                if (modalElement) {
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });
    </script>
@endpush
