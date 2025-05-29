<x-app-layout>
    {{-- The outer container is managed by x-app-layout --}}

    <div class="card shadow-sm mb-4">
        {{-- Card Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mt-2">
                    <h3 class="h5 mb-0">
                        {{ __('User Activity Report') }}
                    </h3>
                </div>
                @if (Route::has('admin.reports.index'))
                    <div class="mt-2 flex-shrink-0">
                        <a href="{{ route('admin.reports.index') }}"
                           class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Back to Reports') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">
            {{-- Ensure this partial is also Bootstrap styled --}}
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($users->count() > 0)
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small">
                                    {{ __('ID') }}
                                </th>
                                <th scope="col" class="text-uppercase small">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="text-uppercase small">
                                    {{ __('Email') }}
                                </th>
                                <th scope="col" class="text-center text-uppercase small">
                                    {{ __('Email Apps') }}
                                </th>
                                <th scope="col" class="text-center text-uppercase small">
                                    {{ __('Loan Apps') }}
                                </th>
                                <th scope="col" class="text-center text-uppercase small">
                                    {{ __('Approvals') }}
                                </th>
                                <th scope="col" class="text-uppercase small">
                                    {{ __('Registered') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="fw-medium">
                                        {{ $user->id }}
                                    </td>
                                    <td>
                                        {{ $user->full_name ?? $user->name }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td class="text-center">
                                        {{ $user->email_applications_count ?? 0 }}
                                    </td>
                                    <td class="text-center">
                                        {{ $user->loan_applications_count ?? 0 }}
                                    </td>
                                    <td class="text-center">
                                        {{ $user->approvals_count ?? 0 }}
                                    </td>
                                    <td>
                                        {{ $user->created_at?->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($users->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{-- Ensure Laravel pagination views are configured for Bootstrap --}}
                            {{-- e.g., Paginator::useBootstrapFive(); in AppServiceProvider --}}
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        {{-- Define SVG for Bootstrap Icons if not globally available --}}
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </symbol>
                        </svg> --}}
                        <div>
                            {{ __('No user activity data available.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
