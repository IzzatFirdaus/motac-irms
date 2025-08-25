{{-- resources/views/livewire/resource-management/admin/equipment/partials/delete-confirmation-modal.blade.php --}}
<<<<<<< HEAD
<div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content motac-modal-content">
            <div class="modal-header motac-modal-header">
                <h5 class="modal-title d-flex align-items-center" id="deleteConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                    {{ __('Sahkan Pemadaman Peralatan') }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($deletingEquipment)
                    <p>{{ __('Adakah anda pasti ingin memadam peralatan') }} <strong>#{{ $deletingEquipment->tag_id }}</strong>?</p>
                    <p class="text-danger fw-bold">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                <button type="button" class="btn btn-danger" wire:click="deleteEquipment" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Ya, Padam') }}</span>
                    <span wire:loading>{{ __('Memadam...') }}</span>
=======
<div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1"
    aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> {{-- Ensure .modal-content is styled by MOTAC theme --}}
            <div class="modal-header"> {{-- Ensure .modal-header is styled by MOTAC theme --}}
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    {{-- Iconography: Design Language 2.4 --}}
                    <i class="bi bi-trash3-fill me-2 text-danger"></i>
                    {{ __('Sahkan Pemadaman Peralatan') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body"> {{-- Ensure .modal-body is styled by MOTAC theme --}}
                @if ($deletingEquipment)
                    <p>{{ __('Adakah anda pasti ingin memadam peralatan berikut?') }}</p>
                    <ul class="list-unstyled">
                        <li><strong>{{ __('Tag ID') }}:</strong> {{ $deletingEquipment->tag_id }}</li>
                        <li><strong>{{ __('Jenis Aset') }}:</strong>
                            {{ $deletingEquipment->asset_type_translated ?? $deletingEquipment->asset_type }}</li>
                        <li><strong>{{ __('Jenama') }}:</strong> {{ $deletingEquipment->brand ?? __('N/A') }}</li>
                        <li><strong>{{ __('Model') }}:</strong> {{ $deletingEquipment->model ?? __('N/A') }}</li>
                    </ul>
                    {{-- Icons are already Bootstrap Icons --}}
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
            <div class="modal-footer"> {{-- Ensure .modal-footer is styled by MOTAC theme --}}
                <button type="button" class="btn btn-outline-secondary" wire:click="closeModal"
                    data-bs-dismiss="modal">{{ __('Batal') }}</button>
                <button type="button" class="btn btn-danger" wire:click="deleteEquipment" wire:loading.attr="disabled">
                    {{-- Ensure .btn-danger uses MOTAC danger color --}}
                    <span wire:loading wire:target="deleteEquipment" class="spinner-border spinner-border-sm me-2"
                        role="status" aria-hidden="true"></span>
                    {{-- Icon is already Bootstrap Icon --}}
                    <i wire:loading.remove class="bi bi-trash3-fill me-1"></i>
                    {{ __('Ya, Padam') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                </button>
            </div>
        </div>
    </div>
</div>
