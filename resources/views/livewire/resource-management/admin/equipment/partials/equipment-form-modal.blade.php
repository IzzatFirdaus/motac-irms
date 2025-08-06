{{-- Modal for create/edit equipment --}}
<div wire:ignore.self class="modal fade" id="equipmentFormModal" tabindex="-1" aria-labelledby="equipmentFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="saveEquipment">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title" id="equipmentFormModalLabel">
                        {{ ($isEditMode ?? false) ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="resetForm" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('livewire.resource-management.admin.equipment.equipment-form')
                </div>
            </div>
        </form>
    </div>
</div>
