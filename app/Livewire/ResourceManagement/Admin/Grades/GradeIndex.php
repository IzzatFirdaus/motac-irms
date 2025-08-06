<?php

namespace App\Livewire\ResourceManagement\Admin\Grades;

use App\Models\Grade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * GradeIndex Livewire component.
 * Handles listing, searching, creating, editing, and deleting of job grades (gred jawatan).
 */
#[Layout('layouts.app')]
class GradeIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?Grade $editingGrade = null;
    public ?Grade $deletingGrade = null;

    // Form Fields
    public string $name = '';
    public ?int $level = null;
    public ?int $min_approval_grade_id = null;
    public bool $is_approver_grade = false;

    protected string $paginationTheme = 'bootstrap';

    /**
     * Component mount logic: authorize and set default state.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', Grade::class);
        $this->editingGrade = new Grade; // For form binding on create
        $this->is_approver_grade = false; // Default for new grade
    }

    /**
     * Computed property to get filtered and paginated grades.
     */
    public function getGradesProperty()
    {
        $query = Grade::with(['minApprovalGrade:id,name', 'creator:id,name', 'updater:id,name'])
            ->orderBy('level', 'desc')
            ->orderBy('name', 'asc');

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('level', 'like', '%'.$this->searchTerm.'%');
            });
        }

        return $query->paginate(10);
    }

    /**
     * Computed property for grades that can be selected as min_approval_grade_id.
     */
    public function getAvailableGradesForDropdownProperty()
    {
        $query = Grade::orderBy('name');
        if ($this->editingGrade instanceof Grade && $this->editingGrade->exists) {
            $query->where('id', '!=', $this->editingGrade->id);
        }

        return $query->pluck('name', 'id');
    }

    /**
     * Open the create modal and reset the form.
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', Grade::class);
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Validate and create a new grade record.
     */
    public function createGrade(): void
    {
        $this->authorize('create', Grade::class);
        $validated = $this->validate();
        Grade::create($validated);
        $this->dispatch('toastr', type: 'success', message: __('Gred berjaya ditambah.'));
        $this->closeModal();
    }

    /**
     * Open the edit modal and populate the form with the selected grade.
     */
    public function openEditModal(Grade $grade): void
    {
        $this->authorize('update', $grade);
        $this->resetForm();
        $this->editingGrade = $grade;
        $this->name = $grade->name;
        $this->level = $grade->level;
        $this->min_approval_grade_id = $grade->min_approval_grade_id;
        $this->is_approver_grade = (bool) $grade->is_approver_grade;
        $this->showEditModal = true;
    }

    /**
     * Validate and update the selected grade record.
     */
    public function updateGrade(): void
    {
        if (! $this->editingGrade instanceof Grade || ! $this->editingGrade->exists) {
            $this->dispatch('toastr', type: 'error', message: __('Ralat: Tiada gred dipilih untuk dikemaskini.'));
            return;
        }

        $this->authorize('update', $this->editingGrade);
        $validated = $this->validate();

        // Prevent a grade from selecting itself as min_approval_grade_id
        if ($this->editingGrade->id == $validated['min_approval_grade_id']) {
            $this->addError('min_approval_grade_id', 'Gred kelulusan minimum tidak boleh sama dengan gred semasa.');
            return;
        }

        $this->editingGrade->update($validated);
        $this->dispatch('toastr', type: 'success', message: __('Maklumat gred berjaya dikemaskini.'));
        $this->closeModal();
    }

    /**
     * Open the delete confirmation modal for a grade.
     */
    public function openDeleteModal(Grade $grade): void
    {
        $this->authorize('delete', $grade);
        $this->deletingGrade = $grade;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the selected grade, if not used as min_approval or by positions/users.
     */
    public function deleteGrade(): void
    {
        if (! $this->deletingGrade instanceof Grade) {
            return;
        }

        $this->authorize('delete', $this->deletingGrade);
        try {
            // Check if this grade is used as a min_approval_grade_id by others
            if (Grade::where('min_approval_grade_id', $this->deletingGrade->id)->exists()) {
                $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana ia ditetapkan sebagai Gred Kelulusan Minimum untuk gred lain.'));
            } elseif ($this->deletingGrade->positions()->exists() || $this->deletingGrade->users()->exists()) {
                $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana digunakan oleh rekod Pengguna atau Jawatan.'));
            } else {
                $this->deletingGrade->delete();
                $this->dispatch('toastr', type: 'success', message: __('Gred berjaya dipadam.'));
            }
        } catch (\Illuminate\Database\QueryException $queryException) {
            $errorCode = $queryException->errorInfo[1] ?? null;
            if ($errorCode == 1451 || str_contains(strtolower($queryException->getMessage()), 'foreign key constraint') || str_contains($queryException->getMessage(), 'Cannot delete or update a parent row')) {
                $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana digunakan oleh rekod lain (cth: Pengguna, Jawatan, atau sebagai Gred Kelulusan Minimum).'));
            } else {
                $this->dispatch('toastr', type: 'error', message: __('Gagal memadam gred: Sila hubungi pentadbir.'));
                \Illuminate\Support\Facades\Log::error('Error deleting grade: '.$queryException->getMessage());
            }
        }

        $this->closeModal();
    }

    /**
     * Close all modals and reset form state.
     */
    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    /**
     * Reset pagination on search.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Render the grade index Blade view.
     */
    public function render(): View
    {
        return view('livewire.resource-management.admin.grades.grade-index', [
            'gradesList' => $this->grades,
            'availableGradesForDropdown' => $this->availableGradesForDropdown,
        ]);
    }

    /**
     * Validation rules for grade form.
     */
    protected function rules(): array
    {
        $gradeIdToIgnore = $this->editingGrade instanceof Grade && $this->editingGrade->exists ? $this->editingGrade->id : null;

        return [
            'name' => ['required', 'string', 'max:50', ValidationRule::unique('grades', 'name')->ignore($gradeIdToIgnore)],
            'level' => ['nullable', 'integer', 'min:1', 'max:100'],
            'min_approval_grade_id' => ['nullable', 'exists:grades,id'],
            'is_approver_grade' => ['boolean'],
        ];
    }

    /**
     * Reset the form fields to default.
     */
    private function resetForm(): void
    {
        $this->name = '';
        $this->level = null;
        $this->min_approval_grade_id = null;
        $this->is_approver_grade = false;
        $this->editingGrade = new Grade;
        $this->deletingGrade = null;
    }
}
