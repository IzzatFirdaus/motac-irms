<?php $__env->startSection('title', __('dashboard.bpm_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><?php echo e(__('dashboard.bpm_title')); ?></h1>
        <div>
            <?php if(Route::has('resource-management.equipment-admin.create')): ?><a href="<?php echo e(route('resource-management.equipment-admin.create')); ?>" class="motac-btn-primary btn-sm shadow-sm me-2 d-inline-flex align-items-center"><i class="bi bi-plus-circle-fill me-1"></i><?php echo e(__('dashboard.add_new_equipment')); ?></a><?php endif; ?>
            <?php if(Route::has('resource-management.equipment-admin.index')): ?><a href="<?php echo e(route('resource-management.equipment-admin.index')); ?>" class="motac-btn-outline btn-sm shadow-sm d-inline-flex align-items-center"><i class="bi bi-list-ul me-1"></i><?php echo e(__('dashboard.view_full_inventory')); ?></a><?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="motac-card shadow-sm">
                <div class="motac-card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 fw-bold text-primary d-flex align-items-center"><i class="bi bi-box-arrow-up-right me-2"></i><?php echo e(__('dashboard.bpm_actions_title')); ?></h6>
                    <?php if(Route::has('resource-management.bpm.outstanding-loans')): ?><a href="<?php echo e(route('resource-management.bpm.outstanding-loans')); ?>" class="motac-btn-outline btn-sm"><?php echo e(__('common.see_all')); ?></a><?php endif; ?>
                </div>
                <div class="motac-card-body p-0"><?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('resource-management.admin.bpm.outstanding-loans', ['itemsPerPage' => 10]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3425419839-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="motac-card shadow-sm">
                <div class="motac-card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 fw-bold text-primary d-flex align-items-center"><i class="bi bi-box-arrow-in-left me-2"></i><?php echo e(__('dashboard.bpm_issued_title')); ?></h6>
                    <?php if(Route::has('resource-management.bpm.issued-loans')): ?><a href="<?php echo e(route('resource-management.bpm.issued-loans')); ?>" class="motac-btn-outline btn-sm"><?php echo e(__('common.see_all')); ?></a><?php endif; ?>
                </div>
                <div class="motac-card-body p-0"><?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('resource-management.admin.bpm.issued-loans', ['itemsPerPage' => 10, 'highlightOverdue' => true]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3425419839-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?></div>
            </div>
        </div>
    </div>

    <div class="row">
            <div class="col-lg-6 mb-4">
            <div class="motac-card shadow-sm h-100">
                <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-archive-fill me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.inventory_stock_summary')); ?></h6></div>
                <div class="motac-card-body">
                    <p class="mb-2 d-flex align-items-center"><i class="bi bi-laptop-fill me-2 text-success"></i><?php echo e(__('dashboard.laptops_available')); ?><strong class="text-dark ms-1"><?php echo e($availableLaptopsCount ?? __('common.not_available')); ?></strong><span class="ms-1"><?php echo e(__('common.units')); ?></span></p>
                    <p class="mb-2 d-flex align-items-center"><i class="bi bi-projector-fill me-2 text-success"></i><?php echo e(__('dashboard.projectors_available')); ?><strong class="text-dark ms-1"><?php echo e($availableProjectorsCount ?? __('common.not_available')); ?></strong><span class="ms-1"><?php echo e(__('common.units')); ?></span></p>
                    <p class="mb-3 d-flex align-items-center"><i class="bi bi-printer-fill me-2 text-success"></i><?php echo e(__('dashboard.printers_available')); ?><strong class="text-dark ms-1"><?php echo e($availablePrintersCount ?? __('common.not_available')); ?></strong><span class="ms-1"><?php echo e(__('common.units')); ?></span></p>
                    <?php if(Route::has('resource-management.equipment-admin.index')): ?><a href="<?php echo e(route('resource-management.equipment-admin.index')); ?>" class="motac-btn-outline btn-sm d-inline-flex align-items-center"><i class="bi bi-search me-1"></i><?php echo e(__('dashboard.view_detailed_inventory')); ?></a><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="motac-card shadow-sm h-100">
                <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-tools me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.maintenance_equipment_title')); ?></h6></div>
                <div class="motac-card-body d-flex align-items-center justify-content-center" style="min-height: 150px;"><p class="text-muted small text-center"><i class="bi bi-tools fs-2 d-block mb-2"></i><?php echo e(__('dashboard.maintenance_equipment_text')); ?></p></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/dashboard/bpm-dashboard.blade.php ENDPATH**/ ?>