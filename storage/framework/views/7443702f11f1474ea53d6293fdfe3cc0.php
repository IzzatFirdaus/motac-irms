<?php $__env->startSection('title', __('dashboard.approver_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><?php echo e(__('dashboard.approver_tasks_title')); ?></h1>
        <?php if(Route::has('approvals.history')): ?>
            <a href="<?php echo e(route('approvals.history', ['status' => 'all'])); ?>" class="motac-btn-outline btn-sm d-inline-flex align-items-center"><i class="bi bi-clock-history me-1"></i><?php echo e(__('dashboard.view_my_approval_history')); ?></a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="motac-card shadow-sm mb-4">
                <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-card-checklist me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.apps_awaiting_your_action')); ?></h6></div>
                <div class="motac-card-body p-0">
                    
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('resource-management.approval.approval-dashboard', ['userId' => Auth::id(), 'defaultStatusFilter' => 'pending', 'showFilters' => true]);

$__html = app('livewire')->mount($__name, $__params, 'lw-4036200281-0', $__slots ?? [], get_defined_vars());

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

    <div class="row mt-2">
        <div class="col-lg-6 mb-4">
            <div class="motac-card shadow-sm h-100">
                <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-graph-up me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.personal_approval_stats')); ?></h6></div>
                <div class="motac-card-body">
                    <p class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill me-2 text-success"></i><?php echo e(__('dashboard.total_approved')); ?><strong class="text-dark ms-1"><?php echo e($approved_last_30_days ?? __('common.not_available')); ?></strong></p>
                    <p class="mb-0 d-flex align-items-center"><i class="bi bi-x-circle-fill me-2 text-danger"></i><?php echo e(__('dashboard.total_rejected')); ?><strong class="text-dark ms-1"><?php echo e($rejected_last_30_days ?? __('common.not_available')); ?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="motac-card shadow-sm h-100">
                <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-info-circle-fill me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary"><?php echo e(__('dashboard.approval_guidance_title')); ?></h6></div>
                <div class="motac-card-body">
                    <p class="small text-muted mb-3"><?php echo e(__('dashboard.approval_guidance_text')); ?></p>
                    <?php if(config('system_links.approval_guidelines_url')): ?>
                        <a href="<?php echo e(config('system_links.approval_guidelines_url')); ?>" target="_blank" rel="noopener noreferrer" class="motac-btn-outline btn-sm d-inline-flex align-items-center"><i class="bi bi-book-half me-1"></i><?php echo e(__('dashboard.read_full_guidelines')); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/dashboard/approver-dashboard.blade.php ENDPATH**/ ?>