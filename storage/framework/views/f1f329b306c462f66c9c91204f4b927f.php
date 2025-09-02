
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('My Helpdesk Tickets')); ?>

    </h2>

    
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <input type="text" class="form-control"
                wire:model.debounce.300ms="search"
                placeholder="<?php echo e(__('Search your tickets...')); ?>">
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="status">
                <option value=""><?php echo e(__('All Statuses')); ?></option>
                <option value="open"><?php echo e(__('Open')); ?></option>
                <option value="in_progress"><?php echo e(__('In Progress')); ?></option>
                <option value="resolved"><?php echo e(__('Resolved')); ?></option>
                <option value="closed"><?php echo e(__('Closed')); ?></option>
                <option value="pending_user_feedback"><?php echo e(__('Pending User Feedback')); ?></option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="priority">
                <option value=""><?php echo e(__('All Priorities')); ?></option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($priority->id); ?>"><?php echo e($priority->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="category">
                <option value=""><?php echo e(__('All Categories')); ?></option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>
    </div>

    
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr class="table-light">
                    <th><?php echo e(__('ID')); ?></th>
                    <th><?php echo e(__('Title')); ?></th>
                    <th><?php echo e(__('Status')); ?></th>
                    <th><?php echo e(__('Priority')); ?></th>
                    <th><?php echo e(__('Assigned To')); ?></th>
                    <th><?php echo e(__('Created')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($ticket->id); ?></td>
                        <td>
                            <a href="<?php echo e(route('helpdesk.view', $ticket->id)); ?>" class="text-primary"><?php echo e($ticket->title); ?></a>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?></span>
                        </td>
                        <td>
                            <span class="badge bg-info"><?php echo e($ticket->priority->name ?? '-'); ?></span>
                        </td>
                        <td><?php echo e($ticket->assignedTo->name ?? __('Unassigned')); ?></td>
                        <td><?php echo e($ticket->created_at->format('d M Y')); ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('helpdesk.view', $ticket->id)); ?>"><?php echo e(__('View')); ?></a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted"><?php echo e(__('No tickets found.')); ?></td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <?php echo e($tickets->links()); ?>

    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/helpdesk/my-tickets-index.blade.php ENDPATH**/ ?>