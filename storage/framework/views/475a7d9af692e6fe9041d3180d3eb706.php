<?php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
?>

 

<?php $__env->startSection('title', __('Daftar Akaun Baru')); ?>

<?php $__env->startSection('page-style'); ?>
    <style>
        .register-card-footer a {
            text-decoration: none;
        }
        .register-card-footer a:hover {
            text-decoration: underline;
        }
        .app-brand-text {
            color: var(--bs-body-color) !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">

            
            <div class="app-brand justify-content-center mb-4">
              <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="<?php echo e(asset('assets/img/logo/motac-logo.svg')); ?>" alt="<?php echo e(__('Logo MOTAC IRMS')); ?>" style="height: 32px; width: auto;">
                </span>
                <span class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text"><?php echo e(__('motac-irms')); ?></span>
              </a>
            </div>

            <div class="motac-card shadow-sm">
                <div class="motac-card-header bg-primary text-white p-3">
                    <h4 class="mb-0 text-white d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        <?php echo e(__('Daftar Akaun Baru')); ?>

                    </h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="mb-1 fw-semibold text-center"><?php echo e(__('Sertai Sistem Kami')); ?></h5>
                    <p class="mb-3 text-center text-muted small"><?php echo e(__('Sila lengkapkan maklumat di bawah untuk mendaftar.')); ?></p>

                    <?php if (isset($component)) { $__componentOriginalb24df6adf99a77ed35057e476f61e153 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb24df6adf99a77ed35057e476f61e153 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.validation-errors','data' => ['class' => 'mb-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('validation-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb24df6adf99a77ed35057e476f61e153)): ?>
<?php $attributes = $__attributesOriginalb24df6adf99a77ed35057e476f61e153; ?>
<?php unset($__attributesOriginalb24df6adf99a77ed35057e476f61e153); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb24df6adf99a77ed35057e476f61e153)): ?>
<?php $component = $__componentOriginalb24df6adf99a77ed35057e476f61e153; ?>
<?php unset($__componentOriginalb24df6adf99a77ed35057e476f61e153); ?>
<?php endif; ?>

                    <form id="formAuthentication" class="mb-3" action="<?php echo e(route('register')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label"><?php echo e(__('Nama Penuh')); ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" placeholder="<?php echo e(__('Masukkan nama penuh anda')); ?>" autofocus value="<?php echo e(old('name')); ?>" required />
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium"><?php echo e($message); ?></span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo e(__('Alamat E-mel')); ?> <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="<?php echo e(__('cth: pengguna@example.com')); ?>" value="<?php echo e(old('email')); ?>" required/>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium"><?php echo e($message); ?></span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password"><?php echo e(__('Kata Laluan')); ?> <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <input type="password" id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="passwordHelpBlock" required autocomplete="new-password"/>
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="passwordHelpBlock" class="form-text small">
                                <?php echo e(__('Kata laluan mesti sekurang-kurangnya 8 aksara.')); ?>

                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium"><?php echo e($message); ?></span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation"><?php echo e(__('Sahkan Kata Laluan')); ?> <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation" required autocomplete="new-password"/>
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                        </div>

                        <?php if(Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature()): ?>
                            <div class="mb-3">
                                <div class="form-check <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <input class="form-check-input <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="checkbox" id="terms" name="terms" required />
                                    <label class="form-check-label small" for="terms">
                                        <?php echo e(__('Saya bersetuju dengan')); ?>

                                        <a href="<?php echo e(route('terms.show')); ?>" target="_blank" class="text-decoration-none"><?php echo e(__('terma perkhidmatan')); ?></a> &amp;
                                        <a href="<?php echo e(route('policy.show')); ?>" target="_blank" class="text-decoration-none"><?php echo e(__('dasar privasi')); ?></a>. <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block" role="alert"><span class="fw-medium"><?php echo e($message); ?></span></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endif; ?>

            <button type="submit" class="motac-btn-primary d-grid w-100">
                            <i class="bi bi-person-plus-fill me-1"></i><?php echo e(__('Daftar Akaun')); ?>

                        </button>
                    </form>
        </div>
        <div class="motac-card-footer text-center text-muted small p-3 register-card-footer">
                    <p class="mb-0">
                        <span><?php echo e(__('Sudah mempunyai akaun?')); ?></span>
                        <?php if(Route::has('login')): ?>
                        <a href="<?php echo e(route('login')); ?>" class="ms-1">
                            <span><?php echo e(__('Log masuk di sini')); ?></span>
                        </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-scripts'); ?>
<script>
    // Password visibility toggle for register form
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                } else {
                    input.type = "password";
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-blank', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/auth/register.blade.php ENDPATH**/ ?>