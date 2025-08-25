<div>
    {{-- IT Admin Dashboard Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.it_admin_title') }}</h1>
    </div>

    {{-- Statistics Row --}}
    <div class="row">
        {{-- Pending Helpdesk Tickets Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                {{ __('dashboard.pending_helpdesk_tickets') }}
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_helpdesk_tickets_count ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- In Progress Helpdesk Tickets Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                {{ __('dashboard.in_progress_helpdesk_tickets') }}
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $in_progress_helpdesk_tickets_count ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tools fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity / Pending Helpdesk Tickets Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4 motac-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i>{{ __('dashboard.helpdesk_tickets_to_process_title') }}
                    </h6>
                    @if (Route::has('helpdesk.admin.index'))
                        <a href="{{route('helpdesk.admin.index')}}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{__('dashboard.view_all_helpdesk_tickets')}}</a>
                    @endif
                </div>
                <div class="card-body motac-card-body p-0">
                    @livewire('helpdesk.admin.ticket-management')
                </div>
            </div>
        </div>
    </div>
</div>
