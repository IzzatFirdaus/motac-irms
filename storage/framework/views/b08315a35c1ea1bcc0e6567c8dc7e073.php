<?php $__env->startSection('title', __('Helpdesk Tickets')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="h4 mb-3"><?php echo e(__('Helpdesk Tickets')); ?></h1>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('helpdesk.my-tickets-index');

$__html = app('livewire')->mount($__name, $__params, 'lw-2392392102-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php if(session('success')): ?>
        <div class="alert alert-success mt-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/helpdesk/index.blade.php ENDPATH**/ ?>