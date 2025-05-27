@props(['type' => 'info', 'message' => null, 'title' => null])

@php
    $baseClass = 'alert p-4 mb-4 border rounded-md text-sm';
    $typeClasses = [
        'success' => 'bg-green-100 dark:bg-green-800 border-green-300 dark:border-green-600 text-green-700 dark:text-green-200',
        'danger' => 'bg-red-100 dark:bg-red-800 border-red-300 dark:border-red-600 text-red-700 dark:text-red-200',
        'warning' => 'bg-yellow-100 dark:bg-yellow-800 border-yellow-300 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200',
        'info' => 'bg-blue-100 dark:bg-blue-800 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-200',
    ];
    $iconClasses = [
        'success' => 'ti ti-circle-check text-green-500 dark:text-green-400', // Tabler Icon example
        'danger' => 'ti ti-alert-circle text-red-500 dark:text-red-400',
        'warning' => 'ti ti-alert-triangle text-yellow-500 dark:text-yellow-400',
        'info' => 'ti ti-info-circle text-blue-500 dark:text-blue-400',
    ];
    $containerClass = $baseClass . ' ' . ($typeClasses[$type] ?? $typeClasses['info']);
    $currentIconClass = $iconClasses[$type] ?? $iconClasses['info'];

    $alertTitle = $title ?? ucfirst($type) . '!';
@endphp

@if ($message || !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => $containerClass]) }} role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="{{ $currentIconClass }} h-5 w-5"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium">{{ $alertTitle }}</h3>
                <div class="mt-2 text-sm">
                    @if ($message)
                        <p>{{ $message }}</p>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
@endif
