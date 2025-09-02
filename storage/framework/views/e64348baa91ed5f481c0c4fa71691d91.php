<?php $__env->startSection('title', __('dashboard.it_admin_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><?php echo e(__('dashboard.it_admin_title')); ?></h1>
    </div>

    
    <div class="row">
        
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="motac-card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                <?php echo e(__('dashboard.pending_helpdesk_tickets')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($pending_helpdesk_tickets_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                    <?php if(Route::has('helpdesk.admin.index')): ?>
                    <a href="<?php echo e(route('helpdesk.admin.index')); ?>" class="stretched-link" title="<?php echo e(__('dashboard.manage_helpdesk_tickets')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="motac-card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                <?php echo e(__('dashboard.my_assigned_helpdesk_tickets')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($my_assigned_helpdesk_tickets_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                     <?php if(Route::has('helpdesk.admin.index')): ?>
                    <a href="<?php echo e(route('helpdesk.admin.index', ['assigned_to_me' => true])); ?>" class="stretched-link" title="<?php echo e(__('dashboard.view_my_assigned_tickets')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="motac-card shadow-sm mb-4">
                <div class="motac-card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i><?php echo e(__('dashboard.helpdesk_tickets_to_process_title')); ?>

                    </h6>
                    <?php if(Route::has('helpdesk.admin.index')): ?>
                        <a href="<?php echo e(route('helpdesk.admin.index')); ?>" class="motac-btn-outline btn-sm"><?php echo e(__('dashboard.view_all_helpdesk_tickets')); ?></a>
                    <?php endif; ?>
                </div>
                <div class="motac-card-body p-0">
                    
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('helpdesk.admin.ticket-management', ['displayLimit' => 5, 'defaultStatusFilter' => 'open']);

$__html = app('livewire')->mount($__name, $__params, 'lw-711756288-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/dashboard/itadmin-dashboard.blade.php ENDPATH**/ ?>