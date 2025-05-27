@props(['title' => null, 'titleClass' => 'text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3', 'bodyClass' => ''])

<div {{ $attributes->merge(['class' => 'card border border-gray-300 dark:border-gray-700 rounded-lg p-6 mb-6 bg-white dark:bg-gray-800 shadow-md']) }}>
    @if ($title)
        <h3 class="{{ $titleClass }}">
            {{ $title }}
        </h3>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
