{{-- resources/views/livewire/resource-management/admin/equipment/partials/view-equipment-modal.blade.php --}}
@php use Illuminate\Support\Str; @endphp
<div wire:ignore.self class="modal fade" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content"> {{-- Ensure .modal-content is styled by MOTAC theme --}}
            <div class="modal-header"> {{-- Ensure .modal-header is styled by MOTAC theme --}}
                <h5 class="modal-title" id="viewEquipmentModalLabel">
                    <i class="bi bi-display me-2"></i> {{-- Added Bootstrap Icon for title --}}
                    {{ __('Butiran Peralatan ICT') }}
                    @if ($viewingEquipment)
                        : #{{ $viewingEquipment->tag_id ?? $viewingEquipment->id }}
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body"> {{-- Ensure .modal-body is styled by MOTAC theme --}}
                @if ($viewingEquipment)
                    <dl class="row g-2 small">
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('No. Tag Aset') }}</dt>
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
                            {{-- Ensure Helpers::getStatusColorClass provides MOTAC-themed badge classes (Design Language 2.1, 3.3) --}}
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
                            {{ $viewingEquipment->purchase_date ? ($viewingEquipment->purchase_date instanceof \Carbon\Carbon ? $viewingEquipment->purchase_date->translatedFormat(config('app.date_format_my', 'd M Y')) : $viewingEquipment->purchase_date) : __('N/A') }}
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Tarikh Tamat Waranti') }}</dt>
                        <dd class="col-sm-8">
                            {{ $viewingEquipment->warranty_expiry_date ? ($viewingEquipment->warranty_expiry_date instanceof \Carbon\Carbon ? $viewingEquipment->warranty_expiry_date->translatedFormat(config('app.date_format_my', 'd M Y')) : $viewingEquipment->warranty_expiry_date) : __('N/A') }}
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
                            {{ __('pada') }}
                            {{ $viewingEquipment->created_at?->translatedFormat(config('app.datetime_format_my', 'd M Y, H:i A')) }}
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Dikemaskini Terakhir Oleh') }}</dt>
                        <dd class="col-sm-8">{{ $viewingEquipment->updater->name ?? __('Sistem') }}
                            {{ __('pada') }}
                            {{ $viewingEquipment->updated_at?->translatedFormat(config('app.datetime_format_my', 'd M Y, H:i A')) }}
                        </dd>

                    </dl>
                @else
                    <p>{{ __('Tiada butiran peralatan untuk dipaparkan.') }}</p>
                @endif
            </div>
            <div class="modal-footer"> {{-- Ensure .modal-footer is styled by MOTAC theme --}}
                @if ($viewingEquipment)
                    @can('update', $viewingEquipment)
                        <a href="{{ route('resource-management.equipment-admin.edit', $viewingEquipment->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            {{-- Icon is already Bootstrap Icon --}}
                            <i class="bi bi-pencil-square me-1"></i> {{ __('Edit Halaman Penuh') }}
                        </a>
                    @endcan
                @endif
                <button type="button" class="btn btn-secondary" wire:click="closeModal"
                    data-bs-dismiss="modal">{{ __('Tutup') }}</button>
            </div>
        </div>
    </div>
</div>
