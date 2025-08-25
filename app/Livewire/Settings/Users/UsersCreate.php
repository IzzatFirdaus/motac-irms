<?php

namespace App\Livewire\Settings\Users;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

/**
 * UsersCreate Livewire Component
 * Handles the creation of new users.
 */
#[Layout('layouts.app')]
#[Title('Tambah Pengguna Baru')]
class UsersCreate extends Component
{
    // User model fields
    public string $title = '';
    public string $name = '';
    public string $identification_number = '';
    public ?string $passport_number = null;
    public ?int $position_id = null;
    public ?int $grade_id = null;
    public ?int $department_id = null;
    public ?string $level = null;
    public string $mobile_number = '';
    public string $personal_email = '';
    public ?string $motac_email = null;
    public string $service_status = '';
    public string $appointment_type = '';
    public ?string $previous_department_name = null;
    public ?string $previous_department_email = null;
    public string $password = '';
    public string $password_confirmation = '';
    public string $status = User::STATUS_ACTIVE;
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

    /**
     * On mount, authorize and load dropdown options.
     */
    public function mount(): void
    {
        abort_unless(Auth::user()->can('create', User::class), 403, __('Tindakan tidak dibenarkan.'));
        $this->loadDropdownOptions();

        // Set default selections
        if (($this->title === '' || $this->title === '0') && $this->titleOptions !== []) {
            $this->title = array_key_first($this->titleOptions);
        }
        if (($this->service_status === '' || $this->service_status === '0') && $this->serviceStatusOptions !== []) {
            $this->service_status = array_key_first($this->serviceStatusOptions);
        }
        if (($this->appointment_type === '' || $this->appointment_type === '0') && $this->appointmentTypeOptions !== []) {
            $this->appointment_type = array_key_first($this->appointmentTypeOptions);
        }
    }

    /**
     * Load all dropdown options for form fields.
     */
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

    /**
     * Save the new user.
     */
    public function saveUser(): void
    {
        abort_unless(Auth::user()->can('create', User::class), 403, __('Tindakan tidak dibenarkan.'));

        $validatedData = $this->validate();

        $userData = [
            'title' => $validatedData['title'],
            'name' => $validatedData['name'],
            'identification_number' => $validatedData['identification_number'],
            'passport_number' => $validatedData['passport_number'],
            'department_id' => $validatedData['department_id'],
            'position_id' => $validatedData['position_id'],
            'grade_id' => $validatedData['grade_id'],
            'level' => $validatedData['level'],
            'mobile_number' => $validatedData['mobile_number'],
            'email' => $validatedData['personal_email'],
            'personal_email' => $validatedData['personal_email'],
            'motac_email' => $validatedData['motac_email'],
            'service_status' => $validatedData['service_status'],
            'appointment_type' => $validatedData['appointment_type'],
            'previous_department_name' => $validatedData['previous_department_name'],
            'previous_department_email' => $validatedData['previous_department_email'],
            'password' => Hash::make($validatedData['password']),
            'status' => $validatedData['status'],
            'email_verified_at' => now(),
        ];

        $user = User::create($userData);

        if (! empty($validatedData['selectedRoles'])) {
            $user->roles()->sync($validatedData['selectedRoles']);
        }

        session()->flash('message', __('Pengguna :name berjaya dicipta.', ['name' => $user->name]));
        $this->resetForm();
    }

    /**
     * Reset the form to the initial state.
     */
    public function resetForm(): void
    {
        $this->resetValidation();
        $this->resetExcept([
            'departmentOptions',
            'positionOptions',
            'gradeOptions',
            'serviceStatusOptions',
            'appointmentTypeOptions',
            'levelOptions',
            'titleOptions',
            'allRoles',
        ]);
        $this->status = User::STATUS_ACTIVE;
        if ($this->titleOptions !== []) {
            $this->title = array_key_first($this->titleOptions);
        }
        if ($this->serviceStatusOptions !== []) {
            $this->service_status = array_key_first($this->serviceStatusOptions);
        }
        if ($this->appointmentTypeOptions !== []) {
            $this->appointment_type = array_key_first($this->appointmentTypeOptions);
        }
        $this->selectedRoles = [];
    }

    /**
     * Render the create user view.
     */
    public function render()
    {
        return view('livewire.settings.users.users-create');
    }

    /**
     * Validation rules for user creation.
     */
    protected function rules(): array
    {
        $passwordRules = ['required', 'string', PasswordRule::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'];
        return [
            'title' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(User::getTitleOptions()))],
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:20|unique:users,identification_number,NULL,id,deleted_at,NULL',
            'passport_number' => 'nullable|string|max:20|unique:users,passport_number,NULL,id,deleted_at,NULL',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'grade_id' => 'required|exists:grades,id',
            'level' => ['nullable', 'string', \Illuminate\Validation\Rule::in(array_keys(User::getLevelOptions()))],
            'mobile_number' => 'required|string|max:20',
            'personal_email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'motac_email' => 'nullable|email|max:255|unique:users,motac_email,NULL,id,deleted_at,NULL',
            'service_status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(User::getServiceStatusOptions()))],
            'appointment_type' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(User::getAppointmentTypeOptions()))],
            'previous_department_name' => 'nullable|string|max:255',
            'previous_department_email' => 'nullable|email|max:255',
            'password' => $passwordRules,
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(User::getStatusOptions()))],
            'selectedRoles' => 'nullable|array',
            'selectedRoles.*' => 'exists:roles,id',
        ];
    }
}
