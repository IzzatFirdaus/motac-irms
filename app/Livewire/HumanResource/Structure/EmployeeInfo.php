<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Timeline;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * EmployeeInfo Livewire Component.
 *
 * Displays information about a specific employee, including their timeline and assets.
 * Allows toggling status and adding/updating timeline entries.
 */
class EmployeeInfo extends Component
{
    public $centers;

    public $departments;

    public $positions;

    // public $employee; // Type-hinted
    // public $timeline = null; // current timeline
    public $employeeTimelines;

    public array $employeeTimelineInfo = [];

    public $employeeAssets;

    public bool $isEdit = false;

    public ?int $confirmedId = null;

    public ?int $selectedCenter = null;

    public ?int $selectedDepartment = null;

    public ?int $selectedPosition = null;

    /**
     * Mounts the component and loads select options.
     */
    public function mount($id): void
    {
        // Load select options for departments and positions.
        $this->departments = Department::orderBy('name')->get();
        $this->positions   = Position::orderBy('name')->get();
        // Add similar logic for centers if required.
    }

    public function render()
    {
        // Load timelines and other data as needed.
        return view('livewire.human-resource.structure.employee-info');
    }

    /**
     * Toggle employee's active/inactive status and update their timeline.
     */
    public function toggleStatus(): void
    {
        $presentTimeline = $this->employee
            ->timelines()
            ->orderBy('start_date', 'desc')
            ->first();

        if ($this->employee->status === User::STATUS_ACTIVE) {
            $this->employee->status = User::STATUS_INACTIVE;
            if ($presentTimeline) {
                $presentTimeline->end_date = Carbon::now();
            }
        } else {
            $this->employee->status = User::STATUS_ACTIVE;
            if ($presentTimeline) {
                $presentTimeline->end_date = null;
            }
        }

        $this->employee->save();
        if ($presentTimeline) {
            $presentTimeline->save();
        }

        session()->flash('toastr', ['type' => 'success', 'message' => __('Status pekerja berjaya dikemaskini.')]);
    }

    /**
     * Submit (add or update) an employee timeline entry.
     */
    public function submitTimeline(): void
    {
        $this->employeeTimelineInfo['centerId']     = $this->selectedCenter;
        $this->employeeTimelineInfo['departmentId'] = $this->selectedDepartment;
        $this->employeeTimelineInfo['positionId']   = $this->selectedPosition;

        $this->validate([
            'selectedCenter'                  => 'required|exists:centers,id',
            'selectedDepartment'              => 'required|exists:departments,id',
            'selectedPosition'                => 'required|exists:positions,id',
            'employeeTimelineInfo.startDate'  => 'required|date',
            'employeeTimelineInfo.endDate'    => 'nullable|date|after_or_equal:employeeTimelineInfo.startDate',
            'employeeTimelineInfo.is_sequent' => 'required|boolean',
            'employeeTimelineInfo.notes'      => 'nullable|string|max:1000',
        ]);

        $this->isEdit ? $this->updateTimeline() : $this->storeTimeline();
    }

    /**
     * Show modal to add a new timeline entry.
     */
    public function showStoreTimelineModal(): void
    {
        $this->isEdit = false;
        $this->resetValidation();
        $this->reset(['timeline', 'selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo']);
        $this->employeeTimelineInfo = [
            'startDate'  => Carbon::now()->toDateString(),
            'endDate'    => null,
            'is_sequent' => false,
            'notes'      => null,
        ];
        $this->dispatch('clearSelect2Values');
        $this->dispatch('openModal', elementId: '#timelineModal');
    }

    /**
     * Store a new timeline entry.
     */
    public function storeTimeline(): void
    {
        DB::beginTransaction();
        try {
            // Logic to end previous timeline and add new entry would go here.
            // Uncomment and implement as needed.
            /*
            Timeline::create([
                'employee_id' => $this->employee->id,
                'center_id' => $this->selectedCenter,
                'department_id' => $this->selectedDepartment,
                'position_id' => $this->selectedPosition,
                'start_date' => $this->employeeTimelineInfo['startDate'],
                'end_date' => $this->employeeTimelineInfo['endDate'] ?? null,
                'is_sequent' => $this->employeeTimelineInfo['is_sequent'],
                'notes' => $this->employeeTimelineInfo['notes'] ?? null,
            ]);
            */
            $this->dispatch('closeModal', elementId: '#timelineModal');
            session()->flash('toastr', ['type' => 'success', 'message' => __('Rekod sejarah pekerjaan berjaya ditambah.')]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Error storing timeline: '.$exception->getMessage(), ['exception' => $exception]);
            session()->flash('toastr', ['type' => 'error', 'message' => __('Gagal menyimpan rekod sejarah pekerjaan.')]);
        }

        $this->reset(['selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo']);
    }

    /**
     * Update an existing timeline entry.
     */
    public function updateTimeline(): void
    {
        if (! $this->timeline) {
            session()->flash('toastr', ['type' => 'error', 'message' => __('Rekod sejarah tidak ditemui.')]);

            return;
        }

        $this->timeline->update([
            'center_id'     => $this->selectedCenter,
            'department_id' => $this->selectedDepartment,
            'position_id'   => $this->selectedPosition,
            'start_date'    => $this->employeeTimelineInfo['startDate'],
            'end_date'      => $this->employeeTimelineInfo['endDate'] ?? null,
            'is_sequent'    => $this->employeeTimelineInfo['is_sequent'],
            'notes'         => $this->employeeTimelineInfo['notes'] ?? null,
        ]);

        $this->dispatch('closeModal', elementId: '#timelineModal');
        session()->flash('toastr', ['type' => 'success', 'message' => __('Rekod sejarah pekerjaan berjaya dikemaskini.')]);
        $this->reset(['selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo', 'isEdit', 'timeline']);
    }

    /**
     * Confirm deletion of a timeline entry.
     */
    public function confirmDeleteTimeline(int $timelineId): void
    {
        $this->confirmedId = $timelineId;
    }

    // Implement deleteTimeline(Timeline $timeline) as needed.
}
