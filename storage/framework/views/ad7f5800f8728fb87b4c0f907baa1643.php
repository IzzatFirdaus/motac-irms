<?php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
?>



<?php $__env->startSection('title', __('Log Masuk Sistem')); ?>

<?php $__env->startSection('page-style'); ?>
    <style>
        /* MYDS-compliant login page styling */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--myds-bg-secondary) 0%, var(--myds-bg-muted) 100%);
            padding: 20px;
        }

        .login-card {
            background: var(--myds-bg-primary);
            border: 1px solid var(--myds-gray-200);
            border-radius: var(--myds-radius-lg);
            box-shadow: var(--myds-shadow-lg);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            background: linear-gradient(135deg, var(--myds-primary) 0%, #1d4ed8 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .login-body {
            padding: 32px 24px;
        }

        .login-footer {
            background: var(--myds-bg-secondary);
            padding: 20px 24px;
            text-align: center;
            border-top: 1px solid var(--myds-gray-200);
        }

        .app-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-decoration: none;
        }

        .app-brand-logo {
            width: 48px;
            height: 48px;
            background: white;
            border-radius: var(--myds-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--myds-shadow-sm);
        }

        .app-brand-text {
            font-family: var(--myds-font-heading);
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--myds-txt-primary);
        }

        /* Dark mode adjustments */
        [data-bs-theme="dark"] .login-container {
            background: linear-gradient(135deg, var(--myds-gray-800) 0%, var(--myds-gray-700) 100%);
        }

        [data-bs-theme="dark"] .login-card {
            background: var(--myds-gray-800);
            border-color: var(--myds-gray-600);
        }

        [data-bs-theme="dark"] .login-footer {
            background: var(--myds-gray-700);
            border-color: var(--myds-gray-600);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-container">
        <div class="login-card">
            
            <header class="login-header" role="banner">
                <div class="app-brand">
                    <div class="app-brand-logo">
                        <img src="<?php echo e(asset('assets/img/logo/motac-logo.svg')); ?>"
                             alt="<?php echo e(__('Logo MOTAC')); ?>"
                             width="32"
                             height="32"
                             loading="eager">
                    </div>
                    <div class="app-brand-text text-white"><?php echo e(__('motac-irms')); ?></div>
                </div>

                <h1 class="h4 mb-2 text-white" style="font-family: var(--myds-font-heading);">
                    <i class="ti ti-login me-2" aria-hidden="true"></i>
                    <?php echo e(__('Log Masuk Sistem')); ?>

                </h1>
                <p class="mb-0 text-white-50" style="font-family: var(--myds-font-body);">
                    <?php echo e(__('Sistem Pengurusan Sumber Bersepadu MOTAC')); ?>

                </p>
            </header>

            
            <main class="login-body">
                <div class="text-center mb-4">
                    <h2 class="h5 fw-semibold mb-2" style="font-family: var(--myds-font-heading); color: var(--myds-txt-primary);">
                        <?php echo e(__('Selamat Datang!')); ?>

                    </h2>
                    <p class="text-muted mb-0" style="font-family: var(--myds-font-body);">
                        <?php echo e(__('Sila log masuk ke akaun anda untuk mengakses sistem.')); ?>

                    </p>
                </div>

                
                <?php if(session('status')): ?>
                    <div class="alert alert-success mb-3 d-flex align-items-center" role="alert">
                        <i class="ti ti-check me-2" aria-hidden="true"></i>
                        <span><?php echo e(session('status')); ?></span>
                    </div>
                <?php endif; ?>

                
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

                
                <form method="POST" action="<?php echo e(route('login')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    
                    <div class="mb-3">
                        <label for="login-email" class="myds-form-label">
                            <?php echo e(__('E-mel / ID Pengguna')); ?>

                            <span class="text-danger" aria-label="<?php echo e(__('Medan Wajib')); ?>">*</span>
                        </label>
                        <input type="text"
                               class="form-control myds-form-control mygovea-accessible <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="login-email"
                               name="email"
                               placeholder="<?php echo e(__('contoh: pengguna@motac.gov.my')); ?>"
                               value="<?php echo e(old('email')); ?>"
                               required
                               autofocus
                               autocomplete="username"
                               aria-describedby="email-help">
                        <div id="email-help" class="myds-form-text">
                            <?php echo e(__('Gunakan alamat e-mel rasmi MOTAC atau ID pengguna yang diberikan.')); ?>

                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="myds-form-error" role="alert">
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-3">
                        <label for="login-password" class="myds-form-label">
                            <?php echo e(__('Kata Laluan')); ?>

                            <span class="text-danger" aria-label="<?php echo e(__('Medan Wajib')); ?>">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control myds-form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="login-password"
                                   name="password"
                                   placeholder="••••••••••••"
                                   required
                                   autocomplete="current-password"
                                   aria-describedby="password-toggle password-help">
                            <button type="button"
                                    class="btn btn-outline-secondary mygovea-accessible password-toggle"
                                    id="password-toggle"
                                    aria-label="<?php echo e(__('Tunjukkan/Sembunyikan Kata Laluan')); ?>"
                                    tabindex="0">
                                <i class="ti ti-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-help" class="myds-form-text">
                            <?php echo e(__('Masukkan kata laluan yang diberikan oleh pentadbir sistem.')); ?>

                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="myds-form-error" role="alert">
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="remember-me"
                                   name="remember"
                                   <?php echo e(old('remember') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="remember-me">
                                <?php echo e(__('Ingat Saya')); ?>

                            </label>
                        </div>
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>"
                               class="text-decoration-none mygovea-accessible"
                               style="color: var(--myds-primary); font-size: 0.875rem;">
                                <?php echo e(__('Lupa Kata Laluan?')); ?>

                            </a>
                        <?php endif; ?>
                    </div>

                    
                    <button type="submit"
                            class="btn btn-myds-primary w-100 mygovea-accessible"
                            style="font-family: var(--myds-font-body);">
                        <i class="ti ti-login me-2" aria-hidden="true"></i>
                        <?php echo e(__('Log Masuk')); ?>

                    </button>
                </form>
            </main>

            
            <footer class="login-footer" role="contentinfo">
                <?php if(Route::has('register')): ?>
                    <p class="mb-2" style="font-family: var(--myds-font-body);">
                        <span class="text-muted"><?php echo e(__('Pengguna baru?')); ?></span>
                        <a href="<?php echo e(route('register')); ?>"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-primary); font-weight: 500;">
                            <?php echo e(__('Cipta akaun di sini')); ?>

                        </a>
                    </p>
                <?php endif; ?>

                <div class="d-flex justify-content-center flex-wrap gap-3">
                    <?php if(Route::has('policy.show')): ?>
                        <a href="<?php echo e(route('policy.show')); ?>"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-txt-muted); font-size: 0.875rem;">
                            <?php echo e(__('Dasar Privasi')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if(Route::has('terms.show')): ?>
                        <a href="<?php echo e(route('terms.show')); ?>"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-txt-muted); font-size: 0.875rem;">
                            <?php echo e(__('Terma Perkhidmatan')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </footer>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-scripts'); ?>
    <script>
        // MYDS-compliant password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('login-password');
            const passwordToggle = document.getElementById('password-toggle');
            const toggleIcon = passwordToggle.querySelector('i');

            if (passwordToggle && passwordInput && toggleIcon) {
                passwordToggle.addEventListener('click', function() {
                    const isPassword = passwordInput.type === 'password';

                    // Toggle input type
                    passwordInput.type = isPassword ? 'text' : 'password';

                    // Update icon
                    toggleIcon.className = isPassword ? 'ti ti-eye-off' : 'ti ti-eye';

                    // Update ARIA label
                    passwordToggle.setAttribute('aria-label',
                        isPassword ? '<?php echo e(__('Sembunyikan Kata Laluan')); ?>' : '<?php echo e(__('Tunjukkan Kata Laluan')); ?>'
                    );
                });

                // Keyboard accessibility
                passwordToggle.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-blank', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/auth/login.blade.php ENDPATH**/ ?>