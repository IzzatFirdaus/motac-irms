
<div wire:ignore.self class="modal fade myds-modal" id="viewEquipmentModal" tabindex="-1" aria-labelledby="viewEquipmentModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content myds-modal-content">
            <div class="modal-header myds-modal-header">
                <h2 class="heading-small d-flex align-items-center mb-0" id="viewEquipmentModalLabel">
                    <i class="bi bi-display me-2"></i>
                    <?php echo e(__('Butiran Peralatan ICT')); ?>

                </h2>
                <button type="button" class="button variant-secondary size-small" wire:click="resetForm" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body myds-modal-body py-24 px-24">
                <?php ($equipmentInstance = $viewingEquipment ?? $editingEquipment ?? null); ?>
                <!--[if BLOCK]><![endif]--><?php if($equipmentInstance): ?>
                    <dl class="row g-2 heading-xsmall">
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('No. Tag Aset')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->tag_id ?? 'N/A'); ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Jenis Aset')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->asset_type_label ?? 'N/A'); ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Jenama & Model')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->brand ?? ''); ?> <?php echo e($equipmentInstance->model ?? ''); ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Status Operasi')); ?></dt>
                        <dd class="col-sm-8"><?php if (isset($component)) { $__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $attributes; } ?>
<?php $component = App\View\Components\EquipmentStatusBadge::resolve(['status' => $equipmentInstance->status] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('equipment-status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\EquipmentStatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55)): ?>
<?php $attributes = $__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55; ?>
<?php unset($__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55)): ?>
<?php $component = $__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55; ?>
<?php unset($__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55); ?>
<?php endif; ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Status Keadaan')); ?></dt>
                        <dd class="col-sm-8"><?php if (isset($component)) { $__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $attributes; } ?>
<?php $component = App\View\Components\EquipmentStatusBadge::resolve(['status' => $equipmentInstance->condition_status] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('equipment-status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\EquipmentStatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('condition')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55)): ?>
<?php $attributes = $__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55; ?>
<?php unset($__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55)): ?>
<?php $component = $__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55; ?>
<?php unset($__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55); ?>
<?php endif; ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Bahagian')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->department?->name ?? 'N/A'); ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Lokasi Simpanan')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->location?->name ?? 'N/A'); ?></dd>
                        <dt class="col-sm-4 fw-semibold text-muted"><?php echo e(__('Catatan')); ?></dt>
                        <dd class="col-sm-8"><?php echo e($equipmentInstance->notes); ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="heading-xsmall text-muted text-center"><?php echo e(__('Sila tunggu, memuatkan data...')); ?></p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="modal-footer myds-modal-footer">
                <button type="button" class="button variant-secondary size-medium" wire:click="resetForm"><?php echo e(__('Tutup')); ?></button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/resource-management/admin/equipment/partials/view-equipment-modal.blade.php ENDPATH**/ ?>