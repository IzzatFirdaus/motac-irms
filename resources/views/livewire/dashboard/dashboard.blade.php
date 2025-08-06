{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    @push('page-style')
        <style>
            .quick-action-bs-icon {
                font-size: 2.5rem;
                display: block;
                margin-bottom: 0.75rem;
                line-height: 1;
            }
            .icon-stat {
                font-size: 1.5rem;
            }
            .table-hover tbody tr {
                cursor: pointer;
            }
            .card-title {
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- Show normal user dashboard if applicable --}}
        @if ($isNormalUser)
            @livewire('dashboard.user-dashboard', [
                'displayUserName' => $displayUserName,
                'pending_loans_count' => $pending_loans_count,
                'approved_loans_count' => $approved_loans_count,
                'rejected_loans_count' => $rejected_loans_count,
                'total_loans_count' => $total_loans_count,
                'recent_applications' => $recent_applications,
            ])
        @else
            {{-- For privileged users, defer to dashboard-wrapper for role-based dashboard --}}
            @include('livewire.dashboard.dashboard-wrapper', [
                'displayUserName' => $displayUserName
            ])
        @endif
    </div>

    @push('page-script')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function updateMotacDashboardClock() {
                    const now = new Date();
                    const pageLocale = @json(app()->getLocale());
                    const displayLocale = pageLocale === 'ms' ? 'ms-MY' : 'en-GB';
                    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };

                    const dateEl = document.getElementById('motacDashboardDate');
                    const timeEl = document.getElementById('motacDashboardTime');

                    if(dateEl) dateEl.textContent = now.toLocaleDateString(displayLocale, dateOptions);
                    if(timeEl) timeEl.textContent = now.toLocaleTimeString(displayLocale, timeOptions);
                }

                if (document.getElementById('motacDashboardDate') && document.getElementById('motacDashboardTime')) {
                    updateMotacDashboardClock();
                    setInterval(updateMotacDashboardClock, 1000);
                }
            });
        </script>
    @endpush
</div>
