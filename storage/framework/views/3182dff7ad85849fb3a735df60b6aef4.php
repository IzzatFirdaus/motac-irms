
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Ticket Details')); ?> #<?php echo e($ticket->id); ?>

    </h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5><?php echo e($ticket->title); ?></h5>
            <p><strong><?php echo e(__('Status:')); ?></strong> <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?></p>
            <p><strong><?php echo e(__('Category:')); ?></strong> <?php echo e($ticket->category->name ?? '-'); ?></p>
            <p><strong><?php echo e(__('Priority:')); ?></strong> <?php echo e($ticket->priority->name ?? '-'); ?></p>
            <p><strong><?php echo e(__('Description:')); ?></strong> <?php echo e($ticket->description); ?></p>
            <p><strong><?php echo e(__('Submitted by:')); ?></strong> <?php echo e($ticket->user->name ?? '-'); ?></p>
            <p><strong><?php echo e(__('Created at:')); ?></strong> <?php echo e($ticket->created_at->format('d M Y H:i')); ?></p>
        </div>
    </div>

    
    <div class="mb-3">
        <h5><?php echo e(__('Comments')); ?></h5>
        <ul class="list-group">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $ticket->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="list-group-item">
                    <strong><?php echo e($comment->user->name ?? 'N/A'); ?></strong>:
                    <?php echo e($comment->content); ?>

                    <span class="text-muted small float-end"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="list-group-item text-muted"><?php echo e(__('No comments yet.')); ?></li>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </ul>
    </div>

    
    <form wire:submit.prevent="addComment" class="mb-4">
        <div class="mb-3">
            <label class="form-label"><?php echo e(__('Add Comment')); ?></label>
            <textarea class="form-control" wire:model.defer="newComment" rows="3"></textarea>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo e(__('Attachments (optional)')); ?></label>
            <input type="file" class="form-control" wire:model="commentAttachments" multiple>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['commentAttachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <!--[if BLOCK]><![endif]--><?php if(auth()->user()->hasRole('IT Admin')): ?>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" wire:model.defer="isInternalComment" id="isInternalComment">
                <label class="form-check-label" for="isInternalComment">
                    <?php echo e(__('Internal Note (Visible to IT Admin Only)')); ?>

                </label>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <button class="btn btn-primary" type="submit"><?php echo e(__('Submit Comment')); ?></button>
    </form>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/helpdesk/ticket-detail.blade.php ENDPATH**/ ?>