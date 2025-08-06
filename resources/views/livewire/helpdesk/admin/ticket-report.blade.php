{{-- resources/views/livewire/helpdesk/admin/ticket-report.blade.php --}}
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Helpdesk Reports') }}
    </h2>

    <div class="py-6">
        <div class="container mx-auto px-4">
            {{-- Alerts --}}
            @if (session()->has('message'))
                <div class="alert alert-success mb-3">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form wire:submit.prevent="generateReport" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="reportType" class="form-label">{{ __('Report Type') }}</label>
                    <select wire:model="reportType" id="reportType" class="form-select">
                        <option value="volume">{{ __('Ticket Volume (Monthly)') }}</option>
                        <option value="resolution_time">{{ __('Average Resolution Time (by Category)') }}</option>
                        <option value="status_distribution">{{ __('Ticket Status Distribution') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="startDate" class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" id="startDate" class="form-control" wire:model="startDate" />
                </div>
                <div class="col-md-3">
                    <label for="endDate" class="form-label">{{ __('End Date') }}</label>
                    <input type="date" id="endDate" class="form-control" wire:model="endDate" />
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" type="submit">{{ __('Generate') }}</button>
                </div>
            </form>

            {{-- Report Results --}}
            <div>
                @if ($reportType === 'volume')
                    <h5>{{ __('Ticket Volume by Month') }}</h5>
                    @if($reportData->isNotEmpty())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Total Tickets') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $data)
                                    <tr>
                                        <td>{{ $data->month }}</td>
                                        <td>{{ $data->total_tickets }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">{{ __('No data available for this period.') }}</div>
                    @endif
                @elseif ($reportType === 'resolution_time')
                    <h5>{{ __('Average Resolution Time by Category') }}</h5>
                    @if($reportData->isNotEmpty())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Avg. Hours to Resolve') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $data)
                                    <tr>
                                        <td>{{ $data->category->name ?? 'N/A' }}</td>
                                        <td>{{ round($data->avg_hours_to_resolve, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">{{ __('No data available for this period.') }}</div>
                    @endif
                @elseif ($reportType === 'status_distribution')
                    <h5>{{ __('Ticket Status Distribution') }}</h5>
                    @if($reportData->isNotEmpty())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $data)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $data->status)) }}</td>
                                        <td>{{ $data->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">{{ __('No data available for this period.') }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
