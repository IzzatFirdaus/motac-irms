{{-- Path: resources/views/livewire/settings/users/index-page.blade.php (or similar, this is the page view) --}}
{{-- This Blade file loads the main Livewire component for user listing and management. --}}
<div>
    @php
        // $configData = \App\Helpers\Helpers::appClasses(); // Usually not needed directly in this wrapper
    @endphp

    {{-- Page title is typically set using #[Title('...')] in the Livewire component class --}}
    {{-- @section('title', __('Pengurusan Pengguna Sistem')) --}}

    {{--
        The system design (Section 9.1) indicates that user CRUD operations are
        managed by Livewire components such as 'SettingsUsersLW' or 'AdminUsersIndexLW'.
        This Blade view acts as the entry point to load that primary Livewire component.
    --}}
    @livewire('settings.users.index-lw') {{-- Replace with the actual name of your user listing Livewire component --}}

    {{--
        The Livewire component ('settings.users.index-lw' or equivalent) will have its own Blade view
        (e.g., resources/views/livewire/settings/users/index.blade.php) containing:
        - Page Header with "Tambah Pengguna Baru" button (linking to a create user page/modal).
        - Search and filtering inputs.
        - Table listing users with relevant columns (Name, Email, MOTAC Email, NRIC, Department, Roles, Status).
        - Action buttons (View, Edit, Delete) for each user.
        - Pagination.
        - Potentially modals for delete confirmation if not handled by separate pages.
    --}}
</div>
