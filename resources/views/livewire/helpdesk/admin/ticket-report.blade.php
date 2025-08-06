<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Helpdesk Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">{{ __('Generate Report') }}</h3>
                    <form wire:submit.prevent="generateReport" class="flex items-end space-x-4">
                        <div class="flex-grow">
                            <x-label for="reportType" value="{{ __('Report Type') }}" />
                            <select wire:model.live="reportType" id="reportType" class="form-select rounded-md shadow-sm border-gray-300 w-full">
                                <option value="volume">{{ __('Ticket Volume (Monthly)') }}</option>
                                <option value="resolution_time">{{ __('Average Resolution Time (by Category)') }}</option>
                                <option value="status_distribution">{{ __('Ticket Status Distribution') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="startDate" value="{{ __('Start Date') }}" />
                            <x-input type="date" wire:model.live="startDate" id="startDate" class="w-full" />
                            <x-input-error for="startDate" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="endDate" value="{{ __('End Date') }}" />
                            <x-input type="date" wire:model.live="endDate" id="endDate" class="w-full" />
                            <x-input-error for="endDate" class="mt-2" />
                        </div>
                        <x-button type="submit" wire:loading.attr="disabled">
                            {{ __('Generate') }}
                        </x-button>
                    </form>
                </div>

                <hr class="my-6">

                <div>
                    <h3 class="text-lg font-semibold mb-3">{{ __('Report Results') }}</h3>
                    @if ($reportType === 'volume')
                        <h4 class="text-md font-medium mb-2">{{ __('Ticket Volume by Month') }}</h4>
                        @if($reportData->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Month') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Tickets') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->month }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->total_tickets }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">{{ __('No data available for this report type and period.') }}</p>
                        @endif
                    @elseif ($reportType === 'resolution_time')
                        <h4 class="text-md font-medium mb-2">{{ __('Average Resolution Time by Category') }}</h4>
                        @if($reportData->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Hours to Resolve') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->category->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ round($data->avg_hours_to_resolve, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">{{ __('No data available for this report type and period.') }}</p>
                        @endif
                    @elseif ($reportType === 'status_distribution')
                        <h4 class="text-md font-medium mb-2">{{ __('Ticket Status Distribution') }}</h4>
                        @if($reportData->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Count') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $data->status)) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">{{ __('No data available for this report type and period.') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
