
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-key-fill me-2"></i><?php echo e(__('Kemaskini Kata Laluan')); ?>

        </h3>
    </div>
    <form wire:submit.prevent="updatePassword">
        <div class="card-body p-3 p-md-4">
            <p class="card-text text-muted small mb-3">
                <?php echo e(__('Pastikan akaun anda menggunakan kata laluan yang panjang dan rawak untuk kekal selamat.')); ?>

            </p>

            <div wire:loading wire:target="updatePassword" class="alert alert-info small py-2 mb-3">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <?php echo e(__('Menyimpan...')); ?>

            </div>
            <!--[if BLOCK]><![endif]--><?php if(session()->has('status') && session('status_target') === $this->getId() . '.updatePassword'): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('status')); ?>

                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"
                        aria-label="<?php echo e(__('Tutup')); ?>"></button>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="mb-3">
                <label for="current_password-<?php echo e($this->getId()); ?>"
                    class="form-label fw-medium"><?php echo e(__('Kata Laluan Semasa')); ?> <span
                        class="text-danger">*</span></label>
                <input id="current_password-<?php echo e($this->getId()); ?>" type="password"
                    class="form-control form-control-sm <?php $__errorArgs = ['state.current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    wire:model="state.current_password" autocomplete="current-password" required />
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="mb-3">
                <label for="password-<?php echo e($this->getId()); ?>" class="form-label fw-medium"><?php echo e(__('Kata Laluan Baru')); ?>

                    <span class="text-danger">*</span></label>
                <input id="password-<?php echo e($this->getId()); ?>" type="password"
                    class="form-control form-control-sm <?php $__errorArgs = ['state.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    wire:model="state.password" autocomplete="new-password" required />
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                <div class="form-text small">
                    <?php echo e(__('Gunakan sekurang-kurangnya 8 aksara. Gabungan huruf besar, huruf kecil, nombor dan simbol adalah digalakkan.')); ?>

                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation-<?php echo e($this->getId()); ?>"
                    class="form-label fw-medium"><?php echo e(__('Sahkan Kata Laluan Baru')); ?> <span
                        class="text-danger">*</span></label>
                <input id="password_confirmation-<?php echo e($this->getId()); ?>" type="password"
                    class="form-control form-control-sm <?php $__errorArgs = ['state.password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    wire:model="state.password_confirmation" autocomplete="new-password" required />
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        <div class="card-footer bg-light text-end py-3 border-top">
            <button type="submit" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled"
                wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">
                    <i class="bi bi-save-fill me-1"></i><?php echo e(__('Simpan Kata Laluan')); ?>

                </span>
                <span wire:loading wire:target="updatePassword" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    <?php echo e(__('Menyimpan...')); ?>

                </span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/profile/update-password-form-profile.blade.php ENDPATH**/ ?>