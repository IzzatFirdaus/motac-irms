<x-app-layout>
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0">
                    {{ __('reports.user_activity.title') }}
                </h3>
                @if (Route::has('reports.index'))
                    <a href="{{ route('reports.index') }}"
                       class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1"></i>
                        {{ __('reports.back_to_list') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($users->count())
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase small">ID</th>
                                <th class="text-uppercase small">{{ __('Name') }}</th>
                                <th class="text-uppercase small">{{ __('Email') }}</th>
                                {{-- Removed Email Applications column --}}
                                <th class="text-uppercase small text-center">{{ __('reports.loan_applications.table.count') }}</th>
                                <th class="text-uppercase small text-center">{{ __('reports.approvals.table.count') }}</th>
                                <th class="text-uppercase small">{{ __('Created At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->full_name ?? $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    {{-- Removed Email Applications count --}}
                                    <td class="text-center">{{ $user->loan_applications_count ?? 0 }}</td>
                                    <td class="text-center">{{ $user->approvals_count ?? 0 }}</td>
                                    <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($users->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>{{ __('reports.loan_applications.no_results') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
