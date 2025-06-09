<div>
    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.bpm_title') }}</h1>
            <div>
                @if (Route::has('resource-management.equipment-admin.create'))<a href="{{ route('resource-management.equipment-admin.create') }}" class="btn btn-primary btn-sm shadow-sm me-2 d-inline-flex align-items-center"><i class="bi bi-plus-circle-fill me-1"></i>{{ __('dashboard.add_new_equipment') }}</a>@endif
                @if (Route::has('resource-management.equipment-admin.index'))<a href="{{ route('resource-management.equipment-admin.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm d-inline-flex align-items-center"><i class="bi bi-list-ul me-1"></i>{{ __('dashboard.view_full_inventory') }}</a>@endif
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 fw-bold text-primary d-flex align-items-center"><i class="bi bi-box-arrow-up-right me-2"></i>{{ __('dashboard.bpm_actions_title') }}</h6>
                        @if (Route::has('resource-management.bpm.outstanding-loans'))<a href="{{ route('resource-management.bpm.outstanding-loans') }}" class="btn btn-sm btn-outline-primary">{{ __('common.see_all') }}</a>@endif
                    </div>
                    <div class="card-body p-0">@livewire('resource-management.admin.bpm.outstanding-loans', ['itemsPerPage' => 10])</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 fw-bold text-primary d-flex align-items-center"><i class="bi bi-box-arrow-in-left me-2"></i>{{ __('dashboard.bpm_issued_title') }}</h6>
                        @if (Route::has('resource-management.bpm.issued-loans'))<a href="{{ route('resource-management.bpm.issued-loans') }}" class="btn btn-sm btn-outline-primary">{{ __('common.see_all') }}</a>@endif
                    </div>
                    <div class="card-body p-0">@livewire('resource-management.admin.bpm.issued-loans', ['itemsPerPage' => 10, 'highlightOverdue' => true])</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-archive-fill me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary">{{ __('dashboard.inventory_stock_summary') }}</h6></div>
                    <div class="card-body">
                        <p class="mb-2 d-flex align-items-center"><i class="bi bi-laptop-fill me-2 text-success"></i>{{ __('dashboard.laptops_available') }}<strong class="text-dark ms-1">{{ $availableLaptopsCount ?? __('common.not_available') }}</strong><span class="ms-1">{{ __('common.units') }}</span></p>
                        <p class="mb-2 d-flex align-items-center"><i class="bi bi-projector-fill me-2 text-success"></i>{{ __('dashboard.projectors_available') }}<strong class="text-dark ms-1">{{ $availableProjectorsCount ?? __('common.not_available') }}</strong><span class="ms-1">{{ __('common.units') }}</span></p>
                        <p class="mb-3 d-flex align-items-center"><i class="bi bi-printer-fill me-2 text-success"></i>{{ __('dashboard.printers_available') }}<strong class="text-dark ms-1">{{ $availablePrintersCount ?? __('common.not_available') }}</strong><span class="ms-1">{{ __('common.units') }}</span></p>
                        @if (Route::has('resource-management.equipment-admin.index'))<a href="{{ route('resource-management.equipment-admin.index') }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center"><i class="bi bi-search me-1"></i>{{ __('dashboard.view_detailed_inventory') }}</a>@endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-tools me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary">{{ __('dashboard.maintenance_equipment_title') }}</h6></div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 150px;"><p class="text-muted small text-center"><i class="bi bi-tools fs-2 d-block mb-2"></i>{{ __('dashboard.maintenance_equipment_text') }}</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
