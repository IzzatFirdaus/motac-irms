{{-- Modal for confirming equipment deletion --}}
<div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content motac-modal-content">
            <div class="modal-header motac-modal-header">
                <h5 class="modal-title d-flex align-items-center" id="deleteConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                    {{ __('Sahkan Pemadaman Peralatan') }}
                </h5>
                <button type="button" class="btn-close" wire:click="resetForm" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($equipmentInstance)
                    <p>{{ __('Adakah anda pasti ingin memadam peralatan') }} <strong>#{{ $equipmentInstance->tag_id }}</strong>?</p>
                    <p class="text-danger fw-bold">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="resetForm">{{ __('Batal') }}</button>
                <button type="button" class="btn btn-danger" wire:click="deleteEquipment" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Ya, Padam') }}</span>
                    <span wire:loading>{{ __('Memadam...') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
