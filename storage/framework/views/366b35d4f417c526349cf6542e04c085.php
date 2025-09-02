
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-person-lines-fill me-2"></i><?php echo e(__('Maklumat Profil')); ?>

        </h3>
    </div>
    <form wire:submit.prevent="updateProfileInformation">
        <div class="card-body p-3 p-md-4">
            
            <!--[if BLOCK]><![endif]--><?php if(session()->has('status') && session('status_target') === $this->getId().'.updateProfileInformation'): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('status')); ?>

                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="<?php echo e(__('Tutup')); ?>"></button>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <div wire:loading wire:target="updateProfileInformation" class="alert alert-info small py-2 mb-3">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <?php echo e(__('Menyimpan...')); ?>

            </div>

            <!--[if BLOCK]><![endif]--><?php if(Laravel\Jetstream\Jetstream::managesProfilePhotos()): ?>
                <div class="mb-4" x-data="{photoName: null, photoPreview: null}">
                    <label class="form-label fw-medium"><?php echo e(__('Foto Profil')); ?></label>
                    <div class="d-flex align-items-center">
                        <div x-show="!photoPreview" class="me-3">
                            <img src="<?php echo e($this->user->profile_photo_url); ?>" alt="<?php echo e($this->user->name); ?>" class="rounded-circle" style="height: 80px; width: 80px; object-fit: cover;">
                        </div>
                        <div x-show="photoPreview" style="display: none;" class="me-3">
                            <img x-bind:src="photoPreview" class="rounded-circle" style="height: 80px; width: 80px; object-fit: cover;" alt="<?php echo e(__('Pratonton Foto Baru')); ?>">
                        </div>
                        <div>
                            <input type="file" class="d-none" wire:model.live="photo" x-ref="photo" id="profilePhotoInput-<?php echo e($this->getId()); ?>"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   " />
                            <button type="button" class="btn btn-outline-secondary btn-sm me-2" x-on:click.prevent="$refs.photo.click()">
                                <i class="bi bi-upload me-1"></i><?php echo e(__('Pilih Foto Baru')); ?>

                            </button>

                            <!--[if BLOCK]><![endif]--><?php if($this->user->profile_photo_path): ?>
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="deleteProfilePhoto" title="<?php echo e(__('Buang Foto')); ?>" wire:loading.attr="disabled" wire:target="deleteProfilePhoto">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="d-block text-danger small mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="mb-3">
                <label for="name-<?php echo e($this->getId()); ?>" class="form-label fw-medium"><?php echo e(__('Nama Paparan Sistem')); ?> <span class="text-danger">*</span></label>
                <input id="name-<?php echo e($this->getId()); ?>" type="text" class="form-control form-control-sm <?php $__errorArgs = ['state.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       wire:model="state.name" required autocomplete="name" placeholder="<?php echo e(__('Nama yang akan dipaparkan dalam sistem')); ?>" />
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="mb-3">
                <label for="email-<?php echo e($this->getId()); ?>" class="form-label fw-medium"><?php echo e(__('Alamat E-mel (Login)')); ?> <span class="text-danger">*</span></label>
                <input id="email-<?php echo e($this->getId()); ?>" type="email" class="form-control form-control-sm <?php $__errorArgs = ['state.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       wire:model="state.email" required autocomplete="username" placeholder="<?php echo e(__('cth: pengguna@example.com')); ?>"/>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if(Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail()): ?>
                    <p class="small text-muted mt-2">
                        <?php echo e(__('Alamat e-mel anda belum disahkan.')); ?>

                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none align-baseline" wire:click.prevent="sendEmailVerification" wire:loading.attr="disabled">
                            <?php echo e(__('Klik di sini untuk menghantar semula e-mel pengesahan.')); ?>

                        </button>
                    </p>
                    <!--[if BLOCK]><![endif]--><?php if($this->verificationLinkSent): ?>
                        <p class="small text-success mt-2">
                            <i class="bi bi-check-circle-fill me-1"></i><?php echo e(__('Pautan pengesahan baharu telah dihantar ke alamat e-mel anda.')); ?>

                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            
        </div>
        <div class="card-footer bg-light text-end py-3 border-top">
            <button type="submit" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled" wire:target="updateProfileInformation">
                <span wire:loading.remove wire:target="updateProfileInformation">
                    <i class="bi bi-save-fill me-1"></i><?php echo e(__('Simpan Perubahan')); ?>

                </span>
                <span wire:loading wire:target="updateProfileInformation" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    <?php echo e(__('Menyimpan...')); ?>

                </span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/profile/update-profile-information-form-profile.blade.php ENDPATH**/ ?>