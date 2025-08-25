{{-- Modal for confirming equipment deletion --}}
<div wire:ignore.self class="modal fade myds-modal" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content myds-modal-content">
            <div class="modal-header myds-modal-header">
                <h2 class="heading-small d-flex align-items-center mb-0" id="deleteConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger-500 me-2"></i>
                    {{ __('Sahkan Pemadaman Peralatan') }}
                </h2>
                <button type="button" class="button variant-secondary size-small" wire:click="resetForm" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body myds-modal-body py-24 px-24">
                @if ($equipmentInstance)
                    <p class="heading-xsmall">{{ __('Adakah anda pasti ingin memadam peralatan') }} <strong>#{{ $equipmentInstance->tag_id }}</strong>?</p>
                    <p class="text-danger-500 fw-semibold heading-xsmall">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                @endif
            </div>
            <div class="modal-footer myds-modal-footer">
                <button type="button" class="button variant-secondary size-medium" wire:click="resetForm">{{ __('Batal') }}</button>
                <button type="button" class="button variant-danger size-medium" wire:click="deleteEquipment" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Ya, Padam') }}</span>
                    <span wire:loading>{{ __('Memadam...') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
