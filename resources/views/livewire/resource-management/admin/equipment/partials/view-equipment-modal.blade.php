{{-- Modal to view equipment details --}}
<div wire:ignore.self class="modal fade myds-modal" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content myds-modal-content">
            <div class="modal-header myds-modal-header">
                <h2 class="heading-small d-flex align-items-center mb-0" id="viewEquipmentModalLabel">
                    <i class="bi bi-display me-2"></i>
                    {{ __('Butiran Peralatan ICT') }}
                </h2>
                <button type="button" class="button variant-secondary size-small" wire:click="resetForm" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body myds-modal-body py-24 px-24">
                @if ($equipmentInstance)
                    <dl class="row g-2 heading-xsmall">
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('No. Tag Aset') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->tag_id ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Jenis Aset') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->asset_type_label ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Jenama & Model') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->brand ?? '' }} {{ $equipmentInstance->model ?? '' }}</dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Status Operasi') }}</dt>
                        <dd class="col-sm-8"><x-equipment-status-badge :status="$equipmentInstance->status" /></dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Status Keadaan') }}</dt>
                        <dd class="col-sm-8"><x-equipment-status-badge :status="$equipmentInstance->condition_status" :type="'condition'" /></dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Bahagian') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->department?->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Lokasi Simpanan') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->location?->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4 fw-semibold text-muted">{{ __('Catatan') }}</dt>
                        <dd class="col-sm-8">{{ $equipmentInstance->notes }}</dd>
                    </dl>
                @else
                    <p class="heading-xsmall text-muted text-center">{{ __('Sila tunggu, memuatkan data...') }}</p>
                @endif
            </div>
            <div class="modal-footer myds-modal-footer">
                <button type="button" class="button variant-secondary size-medium" wire:click="resetForm">{{ __('Tutup') }}</button>
            </div>
        </div>
    </div>
</div>
