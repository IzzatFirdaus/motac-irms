
<div>
    <?php $__env->startSection('title', __('Pengurusan Peralatan ICT')); ?>

    <div class="container-fluid py-4">
        
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-24">
            <h1 class="heading-medium fw-semibold text-black-900 mb-0 d-flex align-items-center">
                <i class="bi bi-hdd-stack-fill me-2"></i>
                <?php echo e(__('Pengurusan Peralatan ICT')); ?>

            </h1>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Equipment::class)): ?>
                <button wire:click="createEquipment"
                    class="button variant-primary size-medium d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> <?php echo e(__('Tambah Peralatan Baharu')); ?>

                </button>
            <?php endif; ?>
        </div>

        <?php echo $__env->make('_partials._alerts.alert-general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
    <div class="card shadow-sm mb-24 motac-card">
            <div class="card-header bg-light-100 py-16 motac-card-header">
                <h5 class="heading-small fw-semibold text-black-900 mb-0 d-flex align-items-center">
                    <i class="bi bi-funnel-fill me-2"></i>
                    <?php echo e(__('Carian dan Saringan Peralatan')); ?>

                </h5>
            </div>
            <div class="card-body p-3 motac-card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="searchTerm"
                            class="form-label form-label-sm"><?php echo e(__('Carian (Tag, Jenama, Model, Siri)')); ?></label>
                        <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTerm"
                            class="form-control form-control-sm" placeholder="<?php echo e(__('Taip kata kunci...')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="filterAssetType" class="form-label form-label-sm"><?php echo e(__('Jenis Aset')); ?></label>
                        <select wire:model.live="filterAssetType" id="filterAssetType"
                            class="form-select form-select-sm">
                            <option value=""><?php echo e(__('Semua Jenis')); ?></option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assetTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e(__($label)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label form-label-sm"><?php echo e(__('Status Operasi')); ?></label>
                        <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                            <option value=""><?php echo e(__('Semua Status')); ?></option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e(__($label)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="resetForm"
                            class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline" type="button"
                            title="<?php echo e(__('Set Semula Carian & Saringan')); ?>">
                            <i class="bi bi-arrow-counterclockwise me-1"></i><?php echo e(__('Set Semula')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>

        
    <div class="card shadow-sm mb-24 motac-card">
            <div class="card-body p-0 motac-card-body">
                <div class="table-responsive">
                    <table class="table table-myds table-hover table-striped mb-0 align-middle">
                        <thead class="table-light-100">
                            <tr>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16" style="cursor:pointer;"
                                    wire:click="sortBy('tag_id')"><?php echo e(__('No. Tag Aset')); ?></th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">
                                    <?php echo e(__('Jenis & Model')); ?></th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16 text-center">
                                    <?php echo e(__('Status')); ?></th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16"><?php echo e(__('Lokasi')); ?>

                                </th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16 text-center">
                                    <?php echo e(__('Tindakan')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="5" class="p-0 border-0">
                                    <div wire:loading.flex class="progress bg-transparent rounded-0"
                                        style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary-500"
                                            role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $equipmentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr wire:key="equipment-item-<?php echo e($item->id); ?>">
                                    <td class="px-3 py-16 heading-xsmall fw-semibold text-black-900"><?php echo e($item->tag_id ?? __('N/A')); ?>

                                    </td>
                                    <td class="px-3 py-16 heading-xsmall">
                                        <span
                                            class="fw-semibold text-black-900"><?php echo e($item->asset_type_label ?? $item->asset_type); ?></span>
                                        <div class="text-muted"><?php echo e($item->brand ?? ''); ?> <?php echo e($item->model ?? ''); ?>

                                        </div>
                                    </td>
                                    <td class="px-3 py-16 heading-xsmall text-center">
                                        <?php if (isset($component)) { $__componentOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b4b20e7afdecc773b04cb2c4c349b55 = $attributes; } ?>
<?php $component = App\View\Components\EquipmentStatusBadge::resolve(['status' => $item->status] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
<?php endif; ?>
                                    </td>
                                    <td class="px-3 py-16 heading-xsmall text-muted"><?php echo e($item->department->name ?? __('Umum')); ?>

                                    </td>
                                    <td class="px-3 py-16 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $item)): ?>
                                                <button
                                                    wire:click="viewEquipment(<?php echo e($item->id); ?>)"
                                                    type="button"
                                                    class="button variant-info size-small border-0 p-1 motac-btn-icon"
                                                    title="<?php echo e(__('Lihat Butiran')); ?>"><i
                                                        class="bi bi-eye-fill"></i></button>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $item)): ?>
                                                <button
                                                    wire:click="editEquipment(<?php echo e($item->id); ?>)"
                                                    type="button"
                                                    class="button variant-primary size-small border-0 p-1 motac-btn-icon"
                                                    title="<?php echo e(__('Kemaskini')); ?>"><i
                                                        class="bi bi-pencil-fill"></i></button>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $item)): ?>
                                                <button
                                                    wire:click="confirmDeleteEquipment(<?php echo e($item->id); ?>)"
                                                    type="button"
                                                    class="button variant-danger size-small border-0 p-1 motac-btn-icon"
                                                    title="<?php echo e(__('Padam')); ?>"><i class="bi bi-trash3-fill"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-3 py-24 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted heading-xsmall">
                                            <i class="bi bi-hdd-stack-fill fs-1 text-secondary-500 mb-2"></i>
                                            <p><?php echo e(__('Tiada rekod peralatan ICT ditemui.')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($equipmentList->hasPages()): ?>
                    <div class="card-footer bg-light-100 border-top py-16 motac-card-footer d-flex justify-content-center">
                        <nav aria-label="MYDS Pagination">
                            <?php echo e($equipmentList->links('vendor.pagination.myds')); ?>

                        </nav>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('livewire.resource-management.admin.equipment.partials.equipment-form-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('livewire.resource-management.admin.equipment.partials.view-equipment-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('livewire.resource-management.admin.equipment.partials.delete-confirmation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Modal handling for Livewire events
        document.addEventListener('livewire:initialized', () => {
            const openModal = (modalId) => {
                let modalElement = document.getElementById(modalId);
                if (modalElement) {
                    // Ensure no lingering backdrops
                    const existingBackdrop = document.querySelector('.modal-backdrop.fade.show');
                    if (existingBackdrop) {
                        existingBackdrop.remove();
                    }
                    var myModal = new bootstrap.Modal(modalElement);
                    myModal.show();
                }
            };

            const closeModal = (modalId) => {
                let modalElement = document.getElementById(modalId);
                if (modalElement) {
                    var myModal = bootstrap.Modal.getInstance(modalElement);
                    if (myModal) {
                        myModal.hide();
                    }
                }
            };

            Livewire.on('open-modal', (event) => {
                let modalId = Array.isArray(event) ? event[0].modalId : event.modalId;
                if (modalId) openModal(modalId);
            });

            Livewire.on('close-modal', (event) => {
                let modalId = Array.isArray(event) ? event[0].modalId : event.modalId;
                if (modalId) closeModal(modalId);
            });

            Livewire.on('toastr', event => {
                let message = Array.isArray(event) ? event[0].message : event.message;
                let type = Array.isArray(event) ? event[0].type : event.type;
                if (window.toastr && message && type) {
                    window.toastr[type](message);
                } else {
                    console.warn('Toastr not available or event data missing. Message:', type, message);
                    alert(message); // Fallback
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/resource-management/admin/equipment/equipment-index.blade.php ENDPATH**/ ?>