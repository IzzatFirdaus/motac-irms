{{-- Modal for create/edit equipment --}}
<div wire:ignore.self class="modal fade myds-modal" id="equipmentFormModal" tabindex="-1" aria-labelledby="equipmentFormModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="saveEquipment">
            <div class="modal-content myds-modal-content">
                <div class="modal-header myds-modal-header">
                    <h2 class="heading-small fw-semibold mb-0" id="equipmentFormModalLabel">
                        {{ ($isEditMode ?? false) ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru') }}
                    </h2>
                    <button type="button" class="button variant-secondary size-small" wire:click="resetForm" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body myds-modal-body py-24 px-24">
                    @include('livewire.resource-management.admin.equipment.equipment-form')
                </div>
            </div>
        </form>
    </div>
</div>
