{{-- resources/views/livewire/resource-management/admin/equipment/partials/delete-confirmation-modal.blade.php --}}
<div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1"
    aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">{{ __('Sahkan Pemadaman Peralatan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($deletingEquipment)
                    <p>{{ __('Adakah anda pasti ingin memadam peralatan berikut?') }}</p>
                    <ul class="list-unstyled">
                        <li><strong>{{ __('Tag ID') }}:</strong> {{ $deletingEquipment->tag_id }}</li>
                        <li><strong>{{ __('Jenis Aset') }}:</strong>
                            {{ $deletingEquipment->asset_type_translated ?? $deletingEquipment->asset_type }}</li>
                        <li><strong>{{ __('Jenama') }}:</strong> {{ $deletingEquipment->brand ?? __('N/A') }}</li>
                        <li><strong>{{ __('Model') }}:</strong> {{ $deletingEquipment->model ?? __('N/A') }}</li>
                    </ul>
                    <p class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('Tindakan ini tidak boleh diundur.') }}</p>
                    @if ($deletingEquipment->loanApplicationItems()->exists() || $deletingEquipment->loanTransactionItems()->exists())
                        <p class="text-warning fw-bold mt-2">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ __('Amaran: Peralatan ini mempunyai rekod pinjaman yang aktif atau sejarah pinjaman. Memadam peralatan ini mungkin tidak dibenarkan atau akan menyebabkan data tidak konsisten.') }}
                        </p>
                    @endif
                @else
                    <p>{{ __('Peralatan tidak ditemui untuk dipadam.') }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="closeModal"
                    data-bs-dismiss="modal">{{ __('Batal') }}</button>
                <button type="button" class="btn btn-danger" wire:click="deleteEquipment" wire:loading.attr="disabled">
                    <span wire:loading wire:target="deleteEquipment" class="spinner-border spinner-border-sm me-2"
                        role="status" aria-hidden="true"></span>
                    <i wire:loading.remove class="bi bi-trash3-fill me-1"></i>
                    {{ __('Ya, Padam') }}
                </button>
            </div>
        </div>
    </div>
</div>
