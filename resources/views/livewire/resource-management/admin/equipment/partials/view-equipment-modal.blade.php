{{-- resources/views/livewire/resource-management/admin/equipment/partials/view-equipment-modal.blade.php --}}
<<<<<<< HEAD
<div wire:ignore.self class="modal fade" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content motac-modal-content">
            <div class="modal-header motac-modal-header">
                <h5 class="modal-title d-flex align-items-center" id="viewEquipmentModalLabel">
                    <i class="bi bi-display me-2"></i>
                    {{ __('Butiran Peralatan ICT') }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
=======
@php use Illuminate\Support\Str; @endphp {{-- For Str::title --}}
<div wire:ignore.self class="modal fade" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEquipmentModalLabel">
                    {{ __('Butiran Peralatan ICT') }}
                    @if ($viewingEquipment)
                        : #{{ $viewingEquipment->tag_id ?? $viewingEquipment->id }}
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"></button>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
            </div>
            <div class="modal-body">
                @if ($viewingEquipment)
                    <dl class="row g-2 small">
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('No. Tag Aset') }}</dt>
<<<<<<< HEAD
                        <dd class="col-sm-8">{{ $viewingEquipment->tag_id ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenis Aset') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->asset_type_label ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenama & Model') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->brand ?? '' }} {{ $viewingEquipment->model ?? '' }}</dd>
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Operasi') }}</dt>
                        <dd class="col-sm-8"><x-equipment-status-badge :status="$viewingEquipment->status" /></dd>
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Keadaan') }}</dt>
                        <dd class="col-sm-8"><x-equipment-status-badge :status="$viewingEquipment->condition_status" :type="'condition'" /></dd>
                    </dl>
                @else
                    <p class="text-muted text-center">{{ __('Sila tunggu, memuatkan data...') }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Tutup') }}</button>
=======
                        <dd class="col-sm-8">{{ $viewingEquipment->tag_id ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenis Aset') }}</dt>
                        <dd class="col-sm-8">
                            {{ $viewingEquipment->asset_type_translated ?? ($viewingEquipment->asset_type ? __(Str::title(str_replace('_', ' ', $viewingEquipment->asset_type))) : __('N/A')) }}
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenama') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->brand ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Model') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->model ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Nombor Siri') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->serial_number ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Kod Item') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->item_code ?? __('N/A') }}</dd>

                        <hr class="my-2">

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Operasi') }}</dt>
                        <dd class="col-sm-8">
                            <span
                                class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($viewingEquipment->status ?? '') }}">
                                {{ $viewingEquipment->status_translated ?? ($viewingEquipment->status ? __(Str::title(str_replace('_', ' ', $viewingEquipment->status))) : __('N/A')) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Keadaan Fizikal') }}</dt>
                        <dd class="col-sm-8">
                            <span
                                class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($viewingEquipment->condition_status ?? '') }}">
                                {{ $viewingEquipment->condition_status_translated ?? ($viewingEquipment->condition_status ? __(Str::title(str_replace('_', ' ', $viewingEquipment->condition_status))) : __('N/A')) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Lokasi Semasa Fizikal') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->current_location ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Bahagian Pemilik/Penempatan') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->department->name ?? __('N/A') }}</dd>

                        <hr class="my-2">

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Tarikh Pembelian') }}</dt>
                        <dd class="col-sm-8">
                            {{ $viewingEquipment->purchase_date ? ($viewingEquipment->purchase_date instanceof \Carbon\Carbon ? $viewingEquipment->purchase_date->translatedFormat('d M Y') : $viewingEquipment->purchase_date) : __('N/A') }}
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Tarikh Tamat Waranti') }}</dt>
                        <dd class="col-sm-8">
                            {{ $viewingEquipment->warranty_expiry_date ? ($viewingEquipment->warranty_expiry_date instanceof \Carbon\Carbon ? $viewingEquipment->warranty_expiry_date->translatedFormat('d M Y') : $viewingEquipment->warranty_expiry_date) : __('N/A') }}
                        </dd>

                        <hr class="my-2">

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Keterangan Tambahan') }}</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">
                            {{ $viewingEquipment->description ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Nota Tambahan') }}</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $viewingEquipment->notes ?? __('N/A') }}
                        </dd>

                        <hr class="my-2">

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Dicipta Oleh') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->creator->name ?? __('Sistem') }}
                            {{ __('pada') }} {{ $viewingEquipment->created_at?->translatedFormat('d M Y, H:i A') }}
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Dikemaskini Terakhir Oleh') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->updater->name ?? __('Sistem') }}
                            {{ __('pada') }} {{ $viewingEquipment->updated_at?->translatedFormat('d M Y, H:i A') }}
                        </dd>

                    </dl>
                @else
                    <p>{{ __('Tiada butiran peralatan untuk dipaparkan.') }}</p>
                @endif
            </div>
            <div class="modal-footer">
                @if ($viewingEquipment)
                    @can('update', $viewingEquipment)
                        {{-- Link to full edit page --}}
                        <a href="{{ route('resource-management.equipment-admin.edit', $viewingEquipment->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil-square me-1"></i> {{ __('Edit Halaman Penuh') }}
                        </a>
                        {{-- Or button to open edit modal --}}
                        {{-- <button type="button" class="btn btn-primary btn-sm" wire:click="openEditModalFromView({{ $viewingEquipment->id }})" data-bs-dismiss="modal">
                            <i class="bi bi-pencil-fill me-1"></i> {{ __('Edit Dalam Modal') }}
                        </button> --}}
                    @endcan
                @endif
                <button type="button" class="btn btn-secondary" wire:click="closeModal"
                    data-bs-dismiss="modal">{{ __('Tutup') }}</button>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
            </div>
        </div>
    </div>
</div>
