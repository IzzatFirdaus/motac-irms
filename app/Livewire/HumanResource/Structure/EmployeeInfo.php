<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Center; // Assuming from HRMS base
use App\Models\Department; // MOTAC Design
use App\Models\Employee; // Assuming from HRMS base, maps conceptually to User
use App\Models\Position; // MOTAC Design
use App\Models\Timeline; // Assuming from HRMS base
use App\Models\User; // For status constants
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component; // Added for logging

class EmployeeInfo extends Component
{
    public $centers;

    public $departments;

    public $positions;

    // public Employee $employee; // Type-hinted
    // public ?Timeline $timeline = null; // Current timeline being edited/viewed
    public $employeeTimelines;

    public array $employeeTimelineInfo = []; // For form binding

    public $employeeAssets;

    public bool $isEdit = false;

    public ?int $confirmedId = null; // For delete confirmations

    public ?int $selectedCenter = null; // Ensure these are nullable if they can be empty

    public ?int $selectedDepartment = null;

    public ?int $selectedPosition = null;

    public function mount($id): void
    {
        // $this->employee = Employee::findOrFail($id); // Use findOrFail for better error handling
        // $this->employeeAssets = $this->employee
        // ->transitions() // Assuming 'transitions' is a valid relationship on Employee for assets
        // ->with('asset')
        // ->orderBy('handed_date', 'desc')
        // ->get();

        // $this->centers = Center::all(); // Consider pluck for dropdowns if large
        $this->departments = Department::orderBy('name')->get(); // Fetch all or pluck relevant fields
        $this->positions = Position::orderBy('name')->get(); // Fetch all or pluck relevant fields
    }

    public function render()
    {
        // $this->employeeTimelines = Timeline::with(['center', 'department', 'position'])
        // ->where('employee_id', $this->employee->id)
        // ->orderBy('start_date', 'desc') // Often more logical to sort timelines by date
        // ->get();

        return view('livewire.human-resource.structure.employee-info');
    }

    // Align with User model's status ('active'/'inactive')
    public function toggleStatus(): void
    {
        $presentTimeline = $this->employee
            ->timelines()
            ->orderBy('start_date', 'desc') // Consistently order by start_date
            ->first();

        // Assuming Employee model has a 'status' attribute like the User model
        if ($this->employee->status === User::STATUS_ACTIVE) { // Use constants from User model
            $this->employee->status = User::STATUS_INACTIVE;
            if ($presentTimeline) {
                $presentTimeline->end_date = Carbon::now();
            }
        } else {
            $this->employee->status = User::STATUS_ACTIVE;
            if ($presentTimeline) {
                // Only set end_date to null if this action implies reactivating the current role
                // This logic might need refinement based on how "present timeline" is defined
                // If reactivating, a new timeline entry might be more appropriate than nullifying end_date of the last one.
                // For now, keeping original logic of nullifying end_date for simplicity.
                $presentTimeline->end_date = null;
            }
        }

        $this->employee->save();
        if ($presentTimeline) {
            $presentTimeline->save();
        }

        session()->flash('toastr', ['type' => 'success', 'message' => __('Status pekerja berjaya dikemaskini.')]);
    }

    public function submitTimeline(): void
    {
        // Values are already bound via selectedCenter, selectedDepartment, selectedPosition
        $this->employeeTimelineInfo['centerId'] = $this->selectedCenter;
        $this->employeeTimelineInfo['departmentId'] = $this->selectedDepartment;
        $this->employeeTimelineInfo['positionId'] = $this->selectedPosition;

        // Ensure 'is_sequent' and other fields are correctly bound to $this->employeeTimelineInfo
        // before this validation runs if they come from form inputs directly into employeeTimelineInfo array.

        $this->validate([
            'selectedCenter' => 'required|exists:centers,id', // Validate the selected properties
            'selectedDepartment' => 'required|exists:departments,id',
            'selectedPosition' => 'required|exists:positions,id',
            'employeeTimelineInfo.startDate' => 'required|date',
            'employeeTimelineInfo.endDate' => 'nullable|date|after_or_equal:employeeTimelineInfo.startDate',
            'employeeTimelineInfo.is_sequent' => 'required|boolean', // Assuming 'is_sequent' in form
            'employeeTimelineInfo.notes' => 'nullable|string|max:1000',
        ]);

        $this->isEdit ? $this->updateTimeline() : $this->storeTimeline();
    }

    public function showStoreTimelineModal(): void
    {
        $this->isEdit = false;
        $this->resetValidation(); // Clear validation errors
        $this->reset(['timeline', 'selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo']);
        $this->employeeTimelineInfo = [ // Initialize with defaults
            'startDate' => Carbon::now()->toDateString(),
            'endDate' => null,
            'is_sequent' => false, // Or true, depending on default expectation
            'notes' => null,
        ];
        $this->dispatch('clearSelect2Values'); // Assuming this JS event handles Select2 if used
        $this->dispatch('openModal', elementId: '#timelineModal');
    }

    public function storeTimeline(): void
    {
        DB::beginTransaction();
        try {
            $currentLatestTimeline = $this->employee
                ->timelines()
                ->orderBy('start_date', 'desc')
                ->first();

            // If there's an existing latest timeline and it doesn't have an end date,
            // and the new timeline starts after or on the same day, set its end date.
            if ($currentLatestTimeline && is_null($currentLatestTimeline->end_date)) {
                $newStartDate = Carbon::parse($this->employeeTimelineInfo['startDate']);
                if (! $currentLatestTimeline->start_date || $newStartDate->gte(Carbon::parse($currentLatestTimeline->start_date))) {
                    $currentLatestTimeline->end_date = $newStartDate->copy()->subDay(); // End previous day
                    $currentLatestTimeline->save();
                }
            }

            // Timeline::create([
            // 'employee_id' => $this->employee->id,
            // 'center_id' => $this->selectedCenter,
            // 'department_id' => $this->selectedDepartment,
            // 'position_id' => $this->selectedPosition,
            // 'start_date' => $this->employeeTimelineInfo['startDate'],
            // 'end_date' => $this->employeeTimelineInfo['endDate'] ?? null,
            // 'is_sequent' => $this->employeeTimelineInfo['is_sequent'], // Ensure this comes from form correctly
            // 'notes' => $this->employeeTimelineInfo['notes'] ?? null,
            // ]);

            $this->dispatch('closeModal', elementId: '#timelineModal');
            session()->flash('toastr', ['type' => 'success', 'message' => __('Rekod sejarah pekerjaan berjaya ditambah.')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing timeline: '.$e->getMessage(), ['exception' => $e]);
            session()->flash('toastr', ['type' => 'error', 'message' => __('Gagal menyimpan rekod sejarah pekerjaan.')]);
            // throw $e; // Optionally rethrow or handle more gracefully
        }
        $this->reset(['selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo']);
    }

    // public function showUpdateTimelineModal(Timeline $timeline): void
    // {
    // $this->isEdit = true;
    // $this->resetValidation();
    // $this->timeline = $timeline;

    // $this->selectedCenter = $timeline->center_id;
    // $this->selectedDepartment = $timeline->department_id;
    // $this->selectedPosition = $timeline->position_id;

    // $this->employeeTimelineInfo = [
    // 'startDate' => Carbon::parse($timeline->start_date)->toDateString(),
    // 'endDate' => $timeline->end_date ? Carbon::parse($timeline->end_date)->toDateString() : null,
    // 'is_sequent' => (bool) $timeline->is_sequent,
    // 'notes' => $timeline->notes,
    // ];

    // $this->dispatch(
    // 'setSelect2Values', // Assuming this JS event handles Select2 if used
    // centerId: $timeline->center_id,
    // departmentId: $timeline->department_id,
    // positionId: $timeline->position_id
    // );
    // $this->dispatch('openModal', elementId: '#timelineModal');
    // }

    public function updateTimeline(): void
    {
        if (! $this->timeline) {
            session()->flash('toastr', ['type' => 'error', 'message' => __('Rekod sejarah tidak ditemui.')]);

            return;
        }

        $this->timeline->update([
            'center_id' => $this->selectedCenter,
            'department_id' => $this->selectedDepartment,
            'position_id' => $this->selectedPosition,
            'start_date' => $this->employeeTimelineInfo['startDate'],
            'end_date' => $this->employeeTimelineInfo['endDate'] ?? null,
            'is_sequent' => $this->employeeTimelineInfo['is_sequent'], // Ensure this comes from form
            'notes' => $this->employeeTimelineInfo['notes'] ?? null,
        ]);

        $this->dispatch('closeModal', elementId: '#timelineModal');
        session()->flash('toastr', ['type' => 'success', 'message' => __('Rekod sejarah pekerjaan berjaya dikemaskini.')]);
        $this->reset(['selectedCenter', 'selectedDepartment', 'selectedPosition', 'employeeTimelineInfo', 'isEdit', 'timeline']);
    }

    public function confirmDeleteTimeline(int $timelineId): void // Accept ID for safety
    {
        $this->confirmedId = $timelineId;
    }

    // public function deleteTimeline(Timeline $timeline): void // Use route-model binding or findOrFail
    // {
    // if ($this->confirmedId !== $timeline->id) { // Extra check
    // session()->flash('toastr', ['type' => 'error', 'message' => __('Pemadaman tidak sah.')]);
    // return;
    // }
    // $timeline->delete();
    // session()->flash('toastr', ['type' => 'success', 'message' => __('Rekod sejarah pekerjaan berjaya dipadam.')]);
    // $this->confirmedId = null;
    // }

    // This method might need more context on what "present timeline" means.
    // Is it the one with a null end_date? Or the most recent start_date?
    // public function setPresentTimeline(Timeline $timeline): void
    // {
    // First, ensure no other timeline is marked as "present" (null end_date) for this employee
    // Timeline::where('employee_id', $this->employee->id)
    // ->whereNull('end_date')
    // ->where('id', '!=', $timeline->id)
    // ->update(['end_date' => Carbon::now()->subDay()]); // Or a specific logic to close them

    // $timeline->end_date = null;
    // $timeline->save();

    // session()->flash('toastr', ['type' => 'success', 'message' => __('Jawatan semasa berjaya ditetapkan.')]);
    // }
}
