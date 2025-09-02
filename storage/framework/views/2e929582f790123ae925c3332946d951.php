<?php $__env->startSection('title', __('dashboard.admin_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><?php echo e(__('dashboard.admin_title')); ?></h1>
    </div>

    
    <div class="row">
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="motac-card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                <?php echo e(__('dashboard.total_users')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($users_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-muted" aria-hidden="true"></i>
                        </div>
                    </div>
                    <?php if(Route::has('settings.users.index')): ?>
                    <a href="<?php echo e(route('settings.users.index')); ?>" class="stretched-link" title="<?php echo e(__('dashboard.manage_users')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="motac-card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                <?php echo e(__('dashboard.pending_approvals')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($pending_approvals_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-2 text-muted" aria-hidden="true"></i>
                        </div>
                    </div>
                    <?php if(Route::has('approvals.dashboard')): ?>
                    <a href="<?php echo e(route('approvals.dashboard')); ?>" class="stretched-link" title="<?php echo e(__('dashboard.view_all_tasks')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="motac-card border-start border-info border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                <?php echo e(__('dashboard.available_equipment')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($equipment_available_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam-fill fs-2 text-muted" aria-hidden="true"></i>
                        </div>
                    </div>
                     <?php if(Route::has('resource-management.equipment-admin.index')): ?>
                        <a href="<?php echo e(route('resource-management.equipment-admin.index')); ?>" class="stretched-link" title="<?php echo e(__('dashboard.manage_inventory')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="motac-card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                <?php echo e(__('dashboard.loaned_equipment')); ?></div>
                            <div class="h5 mb-0 fw-bold text-dark"><?php echo e($equipment_on_loan_count ?? '0'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-truck fs-2 text-muted" aria-hidden="true"></i>
                        </div>
                    </div>
                    <?php if(Route::has('resource-management.bpm.issued-loans')): ?>
                        <a href="<?php echo e(route('resource-management.bpm.issued-loans')); ?>" class="stretched-link" title="<?php echo e(__('dashboard.view_active_loans')); ?>"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        
        <div class="col-lg-12 mb-4">
            <div class="motac-card shadow-sm h-100">
                <div class="motac-card-header py-3 d-flex align-items-center">
                    <i class="bi bi-laptop-fill me-2 text-primary" aria-hidden="true"></i>
                    <h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.loan_stats_title')); ?></h6>
                </div>
                <div class="motac-card-body">
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-truck me-2 text-info" aria-hidden="true"></i><?php echo e(__('common.on_loan')); ?></span> <strong class="text-dark"><?php echo e($loan_issued_count ?? 0); ?></strong></p>
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-patch-check-fill me-2 text-primary" aria-hidden="true"></i><?php echo e(__('common.approved_pending_issuance')); ?></span> <strong class="text-dark"><?php echo e($loan_approved_pending_issuance_count ?? 0); ?></strong></p>
                    <p class="mb-0 d-flex justify-content-between"><span><i class="bi bi-box-arrow-in-left me-2 text-success" aria-hidden="true"></i><?php echo e(__('common.returned')); ?></span> <strong class="text-dark"><?php echo e($loan_returned_count ?? 0); ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="motac-card shadow-sm mb-4">
                <div class="motac-card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2" aria-hidden="true"></i><?php echo e(__('dashboard.latest_tasks_title')); ?>

                    </h6>
                    <?php if(Route::has('approvals.dashboard')): ?>
                        <a href="<?php echo e(route('approvals.dashboard')); ?>" class="motac-btn-outline btn-sm"><?php echo e(__('dashboard.view_all_tasks')); ?></a>
                    <?php endif; ?>
                </div>
                <div class="motac-card-body p-0">
                    
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('resource-management.approval.approval-dashboard', ['displayLimit' => 5, 'showFilters' => false]);

$__html = app('livewire')->mount($__name, $__params, 'lw-4288683380-0', $__slots ?? [], get_defined_vars());

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/dashboard/admin-dashboard.blade.php ENDPATH**/ ?>