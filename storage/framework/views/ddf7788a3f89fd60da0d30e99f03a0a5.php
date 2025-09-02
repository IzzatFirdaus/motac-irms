
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-display me-2"></i><?php echo e(__('Sesi Pelayar Imbas Aktif')); ?>

        </h3>
    </div>
    <div class="card-body p-3 p-md-4">
        <p class="card-text text-muted small mb-3">
            <?php echo e(__('Urus dan log keluar sesi aktif anda pada pelayar imbas dan peranti lain. Jika perlu, anda boleh log keluar daripada semua sesi pelayar imbas anda yang lain merentas semua peranti anda. Jika anda merasakan akaun anda telah terjejas, anda juga patut mengemas kini kata laluan anda.')); ?>

        </p>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('status') && session('status_target') === $this->getId() . '.logoutOtherBrowserSessions'): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('status')); ?>

                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"
                    aria-label="<?php echo e(__('Tutup')); ?>"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if(count($this->sessions) > 0): ?>
            <div class="mt-3 list-group list-group-flush">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item px-0 py-2 d-flex align-items-center">
                        <div class="me-3">
                            <!--[if BLOCK]><![endif]--><?php if($session->agent->isDesktop()): ?>
                                <i class="bi bi-display fs-3 text-muted"></i>
                            <?php else: ?>
                                <i class="bi bi-phone-fill fs-3 text-muted"></i>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="flex-grow-1">
                            <div class="small">
                                <?php echo e($session->agent->platform() ? $session->agent->platform() : __('Tidak Diketahui')); ?>

                                -
                                <?php echo e($session->agent->browser() ? $session->agent->browser() : __('Tidak Diketahui')); ?>

                            </div>
                            <div>
                                <div class="small text-muted">
                                    <?php echo e($session->ip_address); ?>,
                                    <!--[if BLOCK]><![endif]--><?php if($session->is_current_device): ?>
                                        <span class="text-success fw-medium"><?php echo e(__('(Peranti ini)')); ?></span>
                                    <?php else: ?>
                                        <?php echo e(__('Aktif terakhir')); ?> <?php echo e($session->last_active); ?>

                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    <div class="card-footer bg-light text-end py-3 border-top">
        <button class="btn btn-primary motac-btn-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
            <i class="bi bi-box-arrow-right me-1"></i><?php echo e(__('Log Keluar Sesi Pelayar Imbas Lain')); ?>

        </button>
    </div>

    
    <div class="modal fade" id="confirmingLogoutModal-<?php echo e($this->getId()); ?>" tabindex="-1"
        aria-labelledby="confirmingLogoutModalLabel-<?php echo e($this->getId()); ?>" aria-hidden="true" wire:ignore.self
        x-data="{ show: <?php if ((object) ('confirmingLogout') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('confirmingLogout'->value()); ?>')<?php echo e('confirmingLogout'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('confirmingLogout'); ?>')<?php endif; ?>.defer }" x-show="show" @hidden.bs.modal="show = false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmingLogoutModalLabel-<?php echo e($this->getId()); ?>"><i
                            class="bi bi-shield-exclamation me-2"></i><?php echo e(__('Log Keluar Sesi Pelayar Lain')); ?></h5>
                    <button type="button" class="btn-close" @click="show = false"
                        aria-label="<?php echo e(__('Tutup')); ?>"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo e(__('Sila masukkan kata laluan anda untuk mengesahkan bahawa anda ingin log keluar daripada sesi pelayar imbas anda yang lain merentas semua peranti anda.')); ?>

                    </p>
                    <div class="mt-3" x-data="{}"
                        x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password_logout.focus(), 250)">
                        <input type="password" placeholder="<?php echo e(__('Kata Laluan')); ?>" x-ref="password_logout"
                            class="form-control form-control-sm <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary motac-btn-outline" @click="show = false"
                        wire:loading.attr="disabled">
                        <?php echo e(__('Batal')); ?>

                    </button>
                    <button class="btn btn-danger ms-2" wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                        <i class="bi bi-box-arrow-right me-1"></i><?php echo e(__('Log Keluar Sesi Lain')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="confirmingLogout" class="modal-backdrop fade show" id="backdropConfirmingLogout-<?php echo e($this->getId()); ?>"
        style="display: none;"></div>
    <script>
        // JavaScript to initialize modal and sync state with Livewire/Alpine.js
        document.addEventListener('livewire:init', () => {
            let modalElement = document.getElementById('confirmingLogoutModal-<?php echo e($this->getId()); ?>');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                Livewire.on('confirmingLogoutOtherBrowserSessions', () => {
                    modal.show();
                });
                Livewire.on('otherBrowserSessionsLoggedOut', () => {
                    modal.hide();
                });
                modalElement.addEventListener('hidden.bs.modal', function() {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('confirmingLogout', false);
                });
            }
        });
    </script>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/profile/logout-other-browser-sessions-form-profile.blade.php ENDPATH**/ ?>