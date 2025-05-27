@props(['user', 'title' => __('MAKLUMAT PEMOHON')])

<x-card :title="$title">
    @if ($user)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Penuh:') }}</label>
                <p class="form-display-field">{{ $user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->passport_number ? __('No. Pasport:') : __('No. Pengenalan (NRIC):') }}</label>
                <p class="form-display-field">{{ $user->identification_number ?? $user->passport_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jawatan & Gred:') }}</label>
                <p class="form-display-field">
                    {{ optional($user->position)->name ?? 'N/A' }} ({{ optional($user->grade)->name ?? 'N/A' }})
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bahagian/Unit:') }}</label>
                <p class="form-display-field">{{ optional($user->department)->name ?? 'N/A' }}</p>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No. Telefon Bimbit:') }}</label>
                <p class="form-display-field">{{ $user->mobile_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('E-mel Peribadi:') }}</label>
                <p class="form-display-field">{{ $user->personal_email ?? 'N/A' }}</p>
            </div>
        </div>
    @else
        <x-alert type="warning" message="Maklumat pengguna tidak dapat dimuatkan."/>
    @endif
</x-card>

{{-- Add this style to your global CSS or within a <style> tag in your main layout for .form-display-field --}}
{{--
<style>
    .form-display-field {
        margin-top: 0.25rem; /* mt-1 */
        font-size: 0.875rem; /* text-sm */
        color: #1f2937; /* text-gray-900 */
        /* dark:text-gray-100 */
        padding: 0.5rem 0.75rem; /* p-2 */
        border: 1px solid #e5e7eb; /* border-gray-200 */
        /* dark:border-gray-700 */
        border-radius: 0.375rem; /* rounded-md */
        background-color: #f9fafb; /* bg-gray-50 */
        /* dark:bg-gray-700/50 */
    }
    @media (prefers-color-scheme: dark) {
        .form-display-field {
            color: #f3f4f6; /* Replace with your dark:text-gray-100 equivalent */
            border-color: #4b5563; /* Replace with your dark:border-gray-700 equivalent */
            background-color: rgba(55, 65, 81, 0.5); /* Replace with your dark:bg-gray-700/50 equivalent */
        }
    }
</style>
--}}
