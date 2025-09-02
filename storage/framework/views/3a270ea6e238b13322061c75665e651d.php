
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Submit a Helpdesk Ticket')); ?>

    </h2>

    <form wire:submit.prevent="createTicket" enctype="multipart/form-data" class="py-4">
        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="alert alert-success mb-3"><?php echo e(session('message')); ?></div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger mb-3"><?php echo e(session('error')); ?></div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="mb-3">
            <label class="form-label"><?php echo e(__('Title')); ?></label>
            <input type="text" class="form-control" wire:model.defer="title">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo e(__('Description')); ?></label>
            <textarea class="form-control" wire:model.defer="description" rows="4"></textarea>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php echo e(__('Category')); ?></label>
                <select class="form-select" wire:model.defer="category_id">
                    <option value=""><?php echo e(__('Choose...')); ?></option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php echo e(__('Priority')); ?></label>
                <select class="form-select" wire:model.defer="priority_id">
                    <option value=""><?php echo e(__('Choose...')); ?></option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pri->id); ?>"><?php echo e($pri->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo e(__('Attachments (optional)')); ?></label>
            <input type="file" class="form-control" wire:model="attachments" multiple>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <button class="btn btn-primary" type="submit"><?php echo e(__('Submit Ticket')); ?></button>
    </form>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/helpdesk/ticket-form.blade.php ENDPATH**/ ?>