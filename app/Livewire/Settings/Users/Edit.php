<?php

namespace App\Livewire\Settings\Users;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout; // Keep Title attribute
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
// #[Title(method: 'getPageTitle')] // Old line causing the error
#[Title('Kemaskini Pengguna')] // - Fixed: Use a static string or concatenate directly
class Edit extends Component
{
    public User $user;

    // Form fields
    public string $title = '';

    public string $name = '';

    public string $identification_number = '';

    public ?string $passport_number = null;

    public ?int $position_id = null;

    public ?int $grade_id = null;

    public ?int $department_id = null;

    public ?string $level = null; // Aras

    public string $mobile_number = '';

    public string $personal_email = ''; // Bound to 'email' field for login

    public ?string $motac_email = null;

    public string $service_status = '';

    public string $appointment_type = '';

    public ?string $previous_department_name = null;

    public ?string $previous_department_email = null;

    public string $status = '';

    public array $selectedRoles = [];

    // Data for dropdowns
    public array $departmentOptions = [];

    public array $positionOptions = [];

    public array $gradeOptions = [];

    public array $serviceStatusOptions = [];

    public array $appointmentTypeOptions = [];

    public array $levelOptions = [];

    public array $titleOptions = [];

    public array $allRoles = [];

    // Optional: Inject UserService
    // protected UserService $userService;

    // public function boot(UserService $userService)
    // {
    //     $this->userService = $userService;
    // }

    // Removed getPageTitle method as it's no longer used for the #[Title] attribute directly
    // If you need dynamic title, you'll set it in the render method or a similar lifecycle hook
    // public function getPageTitle(): string
    // {
    //     return __('Kemaskini Pengguna') . ' - ' . $this->user->name;
    // }

    public function mount(User $user): void
    {
        // Use authorization gate directly as abort_unless is usually for controllers or outside mount
        if (! Auth::user()->can('update', $user)) {
            abort(403, __('Tindakan tidak dibenarkan.'));
        }

        $this->user = $user;

        // Consider moving option loading to a dedicated method or service
        $this->loadDropdownOptions();
        $this->populateFormFields(); // Populate after options are loaded for defaults

        $this->selectedRoles = $this->user->roles->pluck('id')->map(fn ($id): string => (string) $id)->toArray();

        // Dynamically set page title here if using older Livewire 3 versions
        // and you want a dynamic title based on $this->user->name.
        // This is often done in the layout or by passing data to the view.
        // For Livewire component, the #[Title] attribute is for the <title> tag.
        // If you need the dynamic user name in the <title> tag, you might need to adjust your layout.
        // Or, if your layout accepts a 'title' variable, you can pass it:
        // $this->dispatch('setPageTitle', __('Kemaskini Pengguna') . ' - ' . $this->user->name);
        // (This would require a listener in your layout file or a parent component)
    }

    protected function loadDropdownOptions(): void
    {
        $this->departmentOptions = Department::orderBy('name')->pluck('name', 'id')->all();
        $this->positionOptions = Position::orderBy('name')->pluck('name', 'id')->all();
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->serviceStatusOptions = User::getServiceStatusOptions();
        $this->appointmentTypeOptions = User::getAppointmentTypeOptions();
        $this->levelOptions = User::getLevelOptions();
        $this->titleOptions = User::getTitleOptions();
        $this->allRoles = Role::orderBy('name')->pluck('name', 'id')->all();
    }

    private function populateFormFields(): void
    {
        $this->title = $this->user->title ?? ($this->titleOptions === [] ? '' : array_key_first($this->titleOptions));
        $this->name = $this->user->name;
        $this->identification_number = $this->user->identification_number ?? '';
        $this->passport_number = $this->user->passport_number;
        $this->department_id = $this->user->department_id;
        $this->position_id = $this->user->position_id;
        $this->grade_id = $this->user->grade_id;
        $this->level = $this->user->level;
        $this->mobile_number = $this->user->mobile_number ?? '';
        $this->personal_email = $this->user->email; // Main login email
        $this->motac_email = $this->user->motac_email;
        $this->service_status = $this->user->service_status ?? ($this->serviceStatusOptions === [] ? '' : array_key_first($this->serviceStatusOptions));
        $this->appointment_type = $this->user->appointment_type ?? ($this->appointmentTypeOptions === [] ? '' : array_key_first($this->appointmentTypeOptions));
        $this->previous_department_name = $this->user->previous_department_name;
        $this->previous_department_email = $this->user->previous_department_email;
        $this->status = $this->user->status;
    }

    public function saveUser(): void
    {
        // Use authorization gate directly as abort_unless is usually for controllers or outside mount
        if (! Auth::user()->can('update', $this->user)) {
            abort(403, __('Tindakan tidak dibenarkan.'));
        }

        $validatedData = $this->validate();

        // Consider moving user update logic to a UserService
        $this->user->update([
            'title' => $validatedData['title'],
            'name' => $validatedData['name'],
            'identification_number' => $validatedData['identification_number'],
            'passport_number' => $validatedData['passport_number'],
            'department_id' => $validatedData['department_id'],
            'position_id' => $validatedData['position_id'],
            'grade_id' => $validatedData['grade_id'],
            'level' => $validatedData['level'],
            'mobile_number' => $validatedData['mobile_number'],
            'email' => $validatedData['personal_email'], // Update main login email
            'personal_email' => $validatedData['personal_email'], // Also update personal_email if distinct column
            'motac_email' => $validatedData['motac_email'],
            'service_status' => $validatedData['service_status'],
            'appointment_type' => $validatedData['appointment_type'],
            'previous_department_name' => $validatedData['previous_department_name'],
            'previous_department_email' => $validatedData['previous_department_email'],
            'status' => $validatedData['status'],
        ]);

        if (isset($validatedData['selectedRoles'])) {
            $this->user->roles()->sync($validatedData['selectedRoles']);
        } else {
            $this->user->roles()->sync([]); // If selectedRoles is not set or empty, remove all roles
        }

        session()->flash('message', __('Maklumat pengguna :name berjaya dikemaskini.', ['name' => $this->user->name]));
    }

    public function render()
    {
        // View path assumes your Blade file is at resources/views/livewire/settings/users/edit.blade.php
        return view('livewire.settings.users.edit');
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', ValidationRule::in(array_keys(User::getTitleOptions()))],
            'name' => 'required|string|max:255',
            'identification_number' => ['required', 'string', 'max:20', ValidationRule::unique('users', 'identification_number')->ignore($this->user->id)->whereNull('deleted_at')],
            'passport_number' => ['nullable', 'string', 'max:20', ValidationRule::unique('users', 'passport_number')->ignore($this->user->id)->whereNull('deleted_at')],
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'grade_id' => 'required|exists:grades,id',
            'level' => ['nullable', 'string', ValidationRule::in(array_keys(User::getLevelOptions()))], // Aras
            'mobile_number' => 'required|string|max:20',
            'personal_email' => ['required', 'email', 'max:255', ValidationRule::unique('users', 'email')->ignore($this->user->id)->whereNull('deleted_at')], // Main login email
            'motac_email' => ['nullable', 'email', 'max:255', ValidationRule::unique('users', 'motac_email')->ignore($this->user->id)->whereNull('deleted_at')],
            'service_status' => ['required', 'string', ValidationRule::in(array_keys(User::getServiceStatusOptions()))],
            'appointment_type' => ['required', 'string', ValidationRule::in(array_keys(User::getAppointmentTypeOptions()))],
            'previous_department_name' => 'nullable|string|max:255',
            'previous_department_email' => 'nullable|email|max:255',
            'status' => ['required', 'string', ValidationRule::in(array_keys(User::getStatusOptions()))],
            'selectedRoles' => 'nullable|array',
            'selectedRoles.*' => 'exists:roles,id',
        ];
    }
}
