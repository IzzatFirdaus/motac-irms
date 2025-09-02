<?php if(isset($pageConfigs)): ?>
    <?php echo \App\Helpers\Helpers::updatePageConfig($pageConfigs); ?>

<?php endif; ?>

<?php
    $configData = \App\Helpers\Helpers::appClasses();
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);
?>



<?php $__env->startSection('layoutContent'); ?>
    
    <main class="authentication-wrapper authentication-basic px-4" id="main-content" role="main" aria-label="<?php echo e(__('Kandungan Utama')); ?>">
        <div class="authentication-inner py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.commonMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/layouts/layout-blank.blade.php ENDPATH**/ ?>