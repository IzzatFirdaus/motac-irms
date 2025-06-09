{{-- resources/views/livewire/resource-management/admin/equipment/partials/view-equipment-modal.blade.php --}}
<div wire:ignore.self class="modal fade" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content motac-modal-content">
            <div class="modal-header motac-modal-header">
                <h5 class="modal-title d-flex align-items-center" id="viewEquipmentModalLabel">
                    <i class="bi bi-display me-2"></i>
                    {{ __('Butiran Peralatan ICT') }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($viewingEquipment)
                    <dl class="row g-2 small">
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('No. Tag Aset') }}</dt>
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
            </div>
        </div>
    </div>
</div>
