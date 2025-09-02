<?php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
?>



<?php $__env->startSection('title', __('Tetapkan Semula Kata Laluan')); ?>

<?php $__env->startSection('page-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus, .form-check-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); }
        .auth-cover-bg-color { background-color: #eef3f7; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="<?php echo e(asset('assets/img/illustrations/motac-auth-professional-light.png')); ?>"
                        alt="<?php echo e(__('Ilustrasi Tetapan Semula Kata Laluan MOTAC')); ?>" class="img-fluid my-5 auth-illustration">
                    <img src="<?php echo e(asset('assets/img/illustrations/bg-shape-image-light.png')); ?>"
                        alt="<?php echo e(__('Corak Latar Belakang Hiasan')); ?>" class="platform-bg">
                </div>
            </div>
            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <?php echo $__env->make('_partials.macros', ['height' => 32, 'withbg' => 'fill: var(--bs-primary);'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </span>
                            <span class="app-brand-text demo text-body fw-bold fs-4 ms-1"><?php echo e(__(config('app.name', 'MOTAC'))); ?></span>
                        </a>
                    </div>
                    <h3 class="mb-1 fw-semibold text-center"><?php echo e(__('Tetapkan Semula Kata Laluan Anda')); ?> <i class="bi bi-shield-lock-fill ms-1"></i></h3>
                    <p class="mb-4 text-center text-muted small"><?php echo e(__('Sila masukkan kata laluan baharu anda di bawah.')); ?></p>
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
                    <form id="formAuthentication" class="mb-3" action="<?php echo e(route('password.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="token" value="<?php echo e($request->route('token')); ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo e(__('Alamat E-mel')); ?></label>
                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email"
                                name="email" placeholder="<?php echo e(__('cth: pengguna@motac.gov.my')); ?>"
                                value="<?php echo e($request->email ?? old('email')); ?>" readonly />
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
                            <label class="form-label" for="password"><?php echo e(__('Kata Laluan Baru')); ?> <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <input type="password" id="password"
                                    class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="passwordHelpBlockReset" required autofocus autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                             <div id="passwordHelpBlockReset" class="form-text small">
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
                            <label class="form-label" for="password_confirmation"><?php echo e(__('Sahkan Kata Laluan Baru')); ?> <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password_confirmation" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                        </div>
                        <button type="submit" class="motac-btn-primary d-grid w-100 mb-3">
                            <i class="bi bi-key-fill me-1"></i><?php echo e(__('Tetapkan Kata Laluan Baru')); ?>

                        </button>
                        <div class="text-center">
                            <?php if(Route::has('login')): ?>
                                <a href="<?php echo e(route('login')); ?>" class="d-flex align-items-center justify-content-center text-decoration-none small">
                                    <i class="bi bi-chevron-left me-1"></i><?php echo e(__('Kembali ke Log Masuk')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-scripts'); ?>
    <script>
        // Toggle password visibility for reset password form
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
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

<?php echo $__env->make('layouts.layout-blank', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>