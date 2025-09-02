


<?php
    $configData = \App\Helpers\Helpers::appClasses();
    $rtlSupport = $configData['rtlSupport'] ?? '';
    $currentStyle = $configData['style'] ?? 'light';
    $currentTheme = $configData['theme'] ?? 'theme-motac';
    $hasCustomizer = $configData['hasCustomizer'] ?? false;
    $styleSuffix = $currentStyle !== 'light' ? '-' . $currentStyle : '';
?>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<?php if(($configData['textDirection'] ?? 'ltr') === 'rtl' && ($configData['myRTLSupport'] ?? false)): ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
<?php endif; ?>


<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/fontawesome.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/tabler-icons.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/flag-icons.css')); ?>" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css' . $rtlSupport . '/core' . $styleSuffix . '.css')); ?>"
    class="<?php echo e($hasCustomizer ? 'template-customizer-core-css' : ''); ?>" />


<link rel="stylesheet"
    href="<?php echo e(asset('assets/vendor/css' . $rtlSupport . '/' . $currentTheme . $styleSuffix . '.css')); ?>"
    class="<?php echo e($hasCustomizer ? 'template-customizer-theme-css' : ''); ?>" />


<link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />


<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/node-waves/node-waves.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/typeahead-js/typeahead.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/toastr/toastr.css')); ?>" />



<link rel="stylesheet" href="<?php echo e(asset('assets/css/variables.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/myds-components.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" />

<?php echo $__env->yieldContent('vendor-style'); ?>
<?php echo $__env->yieldContent('page-style'); ?>
<?php echo $__env->yieldPushContent('custom-css'); ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>



<?php /**PATH C:\laragon\www\motac-irms\resources\views/layouts/sections/layout-styles.blade.php ENDPATH**/ ?>