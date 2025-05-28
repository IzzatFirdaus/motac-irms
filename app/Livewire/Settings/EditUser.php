<?php

namespace App\Livewire\Settings;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role; // For role assignment

#[Layout('layouts.app')]
class EditUser extends Component
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
    public ?string $level = null;
    public string $mobile_number = '';
    public string $personal_email = '';
    public ?string $motac_email = null;
    public string $service_status = '';
    public string $appointment_type = '';
    public ?string $previous_department_name = null;
    public ?string $previous_department_email = null;
    public string $status = '';

    // For role selection
    public array $selectedRoles = [];

    // Data for dropdowns
    public $departmentOptions = [];
    public $positionOptions = [];
    public $gradeOptions = [];
    public $serviceStatusOptions = [];
    public $appointmentTypeOptions = [];
    public $levelOptions = [];
    public $titleOptions = [];
    public $allRoles = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->populateFormFields();

        $this->departmentOptions = Department::orderBy('name')->pluck('name', 'id')->all();
        $this->positionOptions = Position::orderBy('name')->pluck('name', 'id')->all();
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->serviceStatusOptions = User::getServiceStatusOptions();
        $this->appointmentTypeOptions = User::getAppointmentTypeOptions();
        $this->levelOptions = User::getLevelOptions();
        $this->titleOptions = User::getTitleOptions();
        $this->allRoles = Role::orderBy('name')->pluck('name', 'id')->all();

        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
    }

    public function saveUser(): void
    {
        $validatedData = $this->validate();

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
          'personal_email' => $validatedData['personal_email'],
          'motac_email' => $validatedData['motac_email'],
          'service_status' => $validatedData['service_status'],
          'appointment_type' => $validatedData['appointment_type'],
          'previous_department_name' => $validatedData['previous_department_name'],
          'previous_department_email' => $validatedData['previous_department_email'],
          'status' => $validatedData['status'],
        ]);

        if (isset($validatedData['selectedRoles'])) {
            $this->user->roles()->sync($validatedData['selectedRoles']);
        }

        session()->flash('message', __('Maklumat pengguna berjaya dikemaskini.'));
        // return redirect()->route('settings.users.show', $this->user);
    }

    public function render()
    {
        return view('livewire.settings.edit-user');
    }

    protected function rules(): array
    {
        return [
          'title' => ['required', 'string', ValidationRule::in(array_keys($this->titleOptions))],
          'name' => 'required|string|max:255',
          'identification_number' => ['required', 'string', 'max:20', ValidationRule::unique('users', 'identification_number')->ignore($this->user->id)],
          'passport_number' => ['nullable', 'string', 'max:20', ValidationRule::unique('users', 'passport_number')->ignore($this->user->id)],
          'department_id' => 'required|exists:departments,id',
          'position_id' => 'required|exists:positions,id',
          'grade_id' => 'required|exists:grades,id',
          'level' => ['nullable', 'string', ValidationRule::in(array_keys($this->levelOptions))],
          'mobile_number' => 'required|string|max:20',
          'personal_email' => ['required', 'email', 'max:255', ValidationRule::unique('users', 'personal_email')->ignore($this->user->id)],
          'motac_email' => ['nullable', 'email', 'max:255', ValidationRule::unique('users', 'motac_email')->ignore($this->user->id)],
          'service_status' => ['required', 'string', ValidationRule::in(array_keys($this->serviceStatusOptions))],
          'appointment_type' => ['required', 'string', ValidationRule::in(array_keys($this->appointmentTypeOptions))],
          'previous_department_name' => 'nullable|string|max:255',
          'previous_department_email' => 'nullable|email|max:255',
          'status' => ['required', 'string', ValidationRule::in(['active', 'inactive'])],
          'selectedRoles' => 'nullable|array',
          'selectedRoles.*' => 'exists:roles,id',
        ];
    }

    private function populateFormFields(): void
    {
        $this->title = $this->user->title;
        $this->name = $this->user->name;
        $this->identification_number = $this->user->identification_number;
        $this->passport_number = $this->user->passport_number;
        $this->department_id = $this->user->department_id;
        $this->position_id = $this->user->position_id;
        $this->grade_id = $this->user->grade_id;
        $this->level = $this->user->level;
        $this->mobile_number = $this->user->mobile_number;
        $this->personal_email = $this->user->personal_email;
        $this->motac_email = $this->user->motac_email;
        $this->service_status = $this->user->service_status;
        $this->appointment_type = $this->user->appointment_type;
        $this->previous_department_name = $this->user->previous_department_name;
        $this->previous_department_email = $this->user->previous_department_email;
        $this->status = $this->user->status;
    }
}
