<?php $__env->startSection('title', __('Create Helpdesk Ticket')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="h4 mb-3"><?php echo e(__('Create Helpdesk Ticket')); ?></h1>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('helpdesk.create-ticket-form');

$__html = app('livewire')->mount($__name, $__params, 'lw-1060600599-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/helpdesk/create.blade.php ENDPATH**/ ?>