<?php

namespace App\Livewire\Settings;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role; // For role assignment

#[Layout('layouts.app')]
class CreateUser extends Component
{
    // User model fields
    public string $title = '';
    public string $name = '';
    public string $identification_number = '';
    public ?string $passport_number = null;
    // public $profile_photo_path = ''; // Requires Livewire\WithFileUploads and more setup
    public ?int $position_id = null;
    public ?int $grade_id = null;
    public ?int $department_id = null;
    public ?string $level = null; // Aras
    public string $mobile_number = '';
    public string $personal_email = '';
    public ?string $motac_email = null;
    public string $service_status = '';
    public string $appointment_type = '';
    public ?string $previous_department_name = null;
    public ?string $previous_department_email = null;
    public string $password = '';
    public string $password_confirmation = '';
    public string $status = 'active'; // Default status

    // For role selection
    public array $selectedRoles = [];

    // Data for dropdowns
    public $departmentOptions = [];
    public $positionOptions = [];
    public $gradeOptions = [];
    public $serviceStatusOptions = [];
    public $appointmentTypeOptions = [];
    public $levelOptions = []; // For 'Aras'
    public $titleOptions = [];
    public $allRoles = [];

    public function mount(): void
    {
        // Assuming User model has static methods/arrays for these options
        // or you fetch them from a dedicated source/config
        $this->departmentOptions = Department::orderBy('name')->pluck('name', 'id')->all();
        $this->positionOptions = Position::orderBy('name')->pluck('name', 'id')->all(); // Filter by grade if necessary in view
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();

        // These should match the enum values in your users table migration/model
        $this->serviceStatusOptions = User::getServiceStatusOptions(); // Example: ['tetap' => 'Tetap', ...]
        $this->appointmentTypeOptions = User::getAppointmentTypeOptions(); // Example: ['baharu' => 'Baharu', ...]
        $this->levelOptions = User::getLevelOptions(); // Example: ['1' => '1', '2' => '2', ...]
        $this->titleOptions = User::getTitleOptions(); // Example: ['Encik' => 'Encik', 'Puan' => 'Puan', ...]
        $this->allRoles = Role::orderBy('name')->pluck('name', 'id')->all();

        if (empty($this->service_status) && !empty($this->serviceStatusOptions)) {
            $this->service_status = array_key_first($this->serviceStatusOptions);
        }
        if (empty($this->appointment_type) && !empty($this->appointmentTypeOptions)) {
            $this->appointment_type = array_key_first($this->appointmentTypeOptions);
        }
        if (empty($this->title) && !empty($this->titleOptions)) {
            $this->title = array_key_first($this->titleOptions);
        }
    }

    public function saveUser(): void
    {
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
            'personal_email' => $validatedData['personal_email'],
            'motac_email' => $validatedData['motac_email'],
            'service_status' => $validatedData['service_status'],
            'appointment_type' => $validatedData['appointment_type'],
            'previous_department_name' => $validatedData['previous_department_name'],
            'previous_department_email' => $validatedData['previous_department_email'],
            'password' => Hash::make($validatedData['password']),
            'status' => $validatedData['status'],
            // 'user_id_assigned' // This might be assigned by a separate process after email provisioning
        ];

        $user = User::create($userData);

        if (!empty($validatedData['selectedRoles'])) {
            $user->roles()->sync($validatedData['selectedRoles']);
        }

        session()->flash('message', __('Pengguna berjaya dicipta.'));
        $this->resetForm();

        // Optionally redirect
        // return redirect()->route('settings.users.index');
    }

    public function render()
    {
        // Data for dropdowns is already loaded in mount/properties
        return view('livewire.settings.create-user');
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys($this->titleOptions))],
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:20|unique:users,identification_number',
            'passport_number' => 'nullable|string|max:20|unique:users,passport_number',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'grade_id' => 'required|exists:grades,id',
            'level' => ['nullable', 'string', \Illuminate\Validation\Rule::in(array_keys($this->levelOptions))],
            'mobile_number' => 'required|string|max:20',
            'personal_email' => 'required|email|max:255|unique:users,personal_email',
            'motac_email' => 'nullable|email|max:255|unique:users,motac_email',
            'service_status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys($this->serviceStatusOptions))],
            'appointment_type' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys($this->appointmentTypeOptions))],
            'previous_department_name' => 'nullable|string|max:255',
            'previous_department_email' => 'nullable|email|max:255',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in(['active', 'inactive'])],
            'selectedRoles' => 'nullable|array',
            'selectedRoles.*' => 'exists:roles,id', // Validate that roles exist by ID
        ];
    }

    private function resetForm(): void
    {
        $this->resetExcept('departmentOptions', 'positionOptions', 'gradeOptions', 'serviceStatusOptions', 'appointmentTypeOptions', 'levelOptions', 'titleOptions', 'allRoles');
        $this->status = 'active'; // Reset to default
        if (!empty($this->serviceStatusOptions)) {
            $this->service_status = array_key_first($this->serviceStatusOptions);
        }
        if (!empty($this->appointmentTypeOptions)) {
            $this->appointment_type = array_key_first($this->appointmentTypeOptions);
        }
        if (!empty($this->titleOptions)) {
            $this->title = array_key_first($this->titleOptions);
        }
    }
}
