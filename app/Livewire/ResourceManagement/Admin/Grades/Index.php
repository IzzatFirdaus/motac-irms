<?php

namespace App\Livewire\ResourceManagement\Admin\Grades;

use App\Models\Grade;
// use App\Models\User; // Not directly used for instantiation, but through relationships
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Validation\Rule as ValidationRule; // For unique rule, aliased

#[Layout('layouts.app')]
class Index extends Component
{
  use AuthorizesRequests, WithPagination;

  public string $searchTerm = '';
  public bool $showCreateModal = false;
  public bool $showEditModal = false;
  public bool $showDeleteModal = false;

  public ?Grade $editingGrade = null;
  public ?Grade $deletingGrade = null;

  // Form Fields - Livewire\Attributes\Rule not needed here if defined in rules() method
  public string $name = '';
  public ?int $level = null;
  public ?int $min_approval_grade_id = null;
  public bool $is_approver_grade = false;

  protected string $paginationTheme = 'bootstrap'; // Updated to Bootstrap

  public function mount(): void
  {
    $this->authorize('viewAny', Grade::class);
    $this->editingGrade = new Grade(); // For form binding on create
    $this->is_approver_grade = false; // Default for new grade
  }

  // Using a computed property for grades list
  public function getGradesProperty()
  {
    $query = Grade::with(['minApprovalGrade:id,name', 'creator:id,name', 'updater:id,name'])
      ->orderBy('level', 'desc')
      ->orderBy('name', 'asc');

    if (!empty($this->searchTerm)) {
      $query->where('name', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('level', 'like', '%' . $this->searchTerm . '%');
    }
    return $query->paginate(10);
  }

  // Using a computed property for available grades for min_approval_grade_id dropdown
  public function getAvailableGradesForDropdownProperty()
  {
    // Exclude the current editing grade from its own min_approval_grade_id list if applicable
    $query = Grade::orderBy('name');
    if ($this->editingGrade && $this->editingGrade->exists) {
      $query->where('id', '!=', $this->editingGrade->id);
    }
    return $query->pluck('name', 'id');
  }

  protected function rules(): array
  {
    $gradeIdToIgnore = $this->editingGrade && $this->editingGrade->exists ? $this->editingGrade->id : null;
    return [
      'name' => ['required', 'string', 'max:50', ValidationRule::unique('grades', 'name')->ignore($gradeIdToIgnore)],
      'level' => ['nullable', 'integer', 'min:1', 'max:100'],
      'min_approval_grade_id' => ['nullable', 'exists:grades,id'],
      'is_approver_grade' => ['boolean'],
    ];
  }

  public function openCreateModal(): void
  {
    $this->authorize('create', Grade::class);
    $this->resetForm();
    // Defaults are set in resetForm or mount
    $this->showCreateModal = true;
  }

  public function createGrade(): void
  {
    $this->authorize('create', Grade::class);
    $validated = $this->validate();
    Grade::create($validated);
    $this->dispatch('toastr', type: 'success', message: __('Gred berjaya ditambah.'));
    $this->closeModal();
  }

  public function openEditModal(Grade $grade): void
  {
    $this->authorize('update', $grade);
    $this->resetForm(); // Good practice to reset before populating
    $this->editingGrade = $grade;
    $this->name = $grade->name;
    $this->level = $grade->level;
    $this->min_approval_grade_id = $grade->min_approval_grade_id;
    $this->is_approver_grade = (bool)$grade->is_approver_grade;
    $this->showEditModal = true;
  }

  public function updateGrade(): void
  {
    if (!$this->editingGrade || !$this->editingGrade->exists) {
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

  public function openDeleteModal(Grade $grade): void
  {
    $this->authorize('delete', $grade);
    $this->deletingGrade = $grade;
    $this->showDeleteModal = true;
  }

  public function deleteGrade(): void
  {
    if (!$this->deletingGrade) return;
    $this->authorize('delete', $this->deletingGrade);
    try {
      // Check if the grade is being used as a min_approval_grade_id by other grades
      if (Grade::where('min_approval_grade_id', $this->deletingGrade->id)->exists()) {
        $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana ia ditetapkan sebagai Gred Kelulusan Minimum untuk gred lain.'));
      } elseif ($this->deletingGrade->positions()->exists() || $this->deletingGrade->users()->exists()) {
        $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana digunakan oleh rekod Pengguna atau Jawatan.'));
      } else {
        $this->deletingGrade->delete();
        $this->dispatch('toastr', type: 'success', message: __('Gred berjaya dipadam.'));
      }
    } catch (\Illuminate\Database\QueryException $e) {
      $errorCode = $e->errorInfo[1] ?? null;
      if ($errorCode == 1451 || str_contains(strtolower($e->getMessage()), 'foreign key constraint') || str_contains($e->getMessage(), 'Cannot delete or update a parent row')) { // MySQL error code for foreign key constraint
        $this->dispatch('toastr', type: 'error', message: __('Gred ini tidak boleh dipadam kerana digunakan oleh rekod lain (cth: Pengguna, Jawatan, atau sebagai Gred Kelulusan Minimum).'));
      } else {
        $this->dispatch('toastr', type: 'error', message: __('Gagal memadam gred: Sila hubungi pentadbir.'));
        \Illuminate\Support\Facades\Log::error("Error deleting grade: {$e->getMessage()}");
      }
    }
    $this->closeModal();
  }

  public function closeModal(): void
  {
    $this->showCreateModal = false;
    $this->showEditModal = false;
    $this->showDeleteModal = false;
    $this->resetForm();
    $this->resetErrorBag(); // Clear validation errors
  }

  private function resetForm(): void
  {
    $this->name = '';
    $this->level = null;
    $this->min_approval_grade_id = null;
    $this->is_approver_grade = false;
    $this->editingGrade = new Grade(); // Prepare a fresh model
    $this->deletingGrade = null;
  }

  public function updatingSearchTerm(): void
  {
    $this->resetPage();
  }

  public function render(): View
  {
    return view('livewire.resource-management.admin.grades.index', [
      'gradesList' => $this->grades, // Accesses the getGradesProperty()
      'availableGradesForDropdown' => $this->availableGradesForDropdown, // Accesses getAvailableGradesForDropdownProperty
    ])->title(__('Pengurusan Gred Jawatan'));
  }
}
