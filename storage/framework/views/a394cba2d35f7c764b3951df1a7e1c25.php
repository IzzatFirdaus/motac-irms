<?php
    // The $illustrationStyleSuffix is used for dark/light mode illustrations.
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
?>

 

<?php $__env->startSection('title', __('403 - Akses Dihalang')); ?>

<?php $__env->startSection('page-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-misc.css')); ?>">
    <style>
        .misc-wrapper .display-5 {
            color: var(--bs-danger);
        }
        .motac-error-illustration {
            max-width: 250px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder">403</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-hand-thumbs-down-fill me-2"></i><?php echo e(__('Akses Dihalang!')); ?>

            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                <?php echo e(__('Anda tidak mempunyai kebenaran yang mencukupi untuk mengakses sumber atau halaman ini. Sila hubungi pentadbir sistem jika anda memerlukan akses.')); ?>

            </p>
            <a href="<?php echo e(url('/')); ?>" class="motac-btn-primary d-inline-flex align-items-center" aria-label="<?php echo e(__('Kembali ke Laman Utama')); ?>">
                <i class="bi bi-house-door-fill me-2" aria-hidden="true"></i><?php echo e(__('Kembali ke Laman Utama')); ?>

            </a>
            <div class="mt-4">
                
                
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-blank', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/errors/403.blade.php ENDPATH**/ ?>