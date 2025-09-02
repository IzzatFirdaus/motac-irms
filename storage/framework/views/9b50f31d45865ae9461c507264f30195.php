<?php
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout variables
    $container = $configData['container'] ?? 'container-fluid';
    $containerNav = $configData['containerNav'] ?? 'container-fluid';
    $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';
?>



<?php $__env->startSection('layoutContent'); ?>
    <div class="layout-wrapper layout-content-navbar" role="document">
        <div class="layout-container">

            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.menu.vertical-menu');

$__html = app('livewire')->mount($__name, $__params, 'lw-3216271225-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> 

            <div class="layout-page">
                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.navbar.navbar', [
                    'containerNav' => $containerNav,
                    'navbarDetachedClass' => $navbarDetached,
                ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3216271225-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

                
                <div class="content-wrapper">
                    <main id="main-content" class="<?php echo e($container); ?> flex-grow-1 container-p-y" role="main" aria-label="<?php echo e(__('Kandungan Utama')); ?>">
                        
                        <?php if(isset($slot)): ?>
                            <?php echo e($slot); ?>

                        <?php else: ?>
                            <?php echo $__env->yieldContent('content'); ?>
                        <?php endif; ?>
                    </main>

                    
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.footer.footer');

$__html = app('livewire')->mount($__name, $__params, 'lw-3216271225-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        
    <div class="layout-overlay layout-menu-toggle" aria-hidden="true"></div>
        <div class="drag-target"></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.commonMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/layouts/app.blade.php ENDPATH**/ ?>