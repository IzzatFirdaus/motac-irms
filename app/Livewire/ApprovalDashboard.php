<?php

namespace App\Livewire;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Services\ApprovalService; // For processing decisions
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Make sure Rule is imported
use Illuminate\View\View; // Make sure this is imported
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On; // For listening to events
use Livewire\Component;
use Livewire\WithPagination;
use Throwable; // Import Throwable

#[Layout('layouts.app')] // Bootstrap main layout
class ApprovalDashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $filterType = 'all'; // 'all', EmailApplication::class, LoanApplication::class
    public string $searchTerm = '';
    public string $filterStatus = Approval::STATUS_PENDING; // Default to pending

    // Modal properties for taking action
    public bool $showApprovalActionModal = false;
    public ?Approval $selectedApproval = null;
    public string $decision = ''; // 'approved' or 'rejected'
    public string $comments = '';

    protected string $paginationTheme = 'bootstrap'; // <-- CONVERTED TO BOOTSTRAP

    protected ApprovalService $approvalService;

    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    #[Computed(persist: true, seconds: 300)]
    public function approvalTasks(): LengthAwarePaginator
    {
        /** @var \App\Models\User|null $officer */
        $officer = Auth::user();
        if (!$officer) {
            return new LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url(), 'query' => request()->query()]);
        }

        $query = Approval::query()
            ->where('officer_id', $officer->id)
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,department_id', 'user.department:id,name'],
                        LoanApplication::class => ['user:id,name,department_id', 'user.department:id,name', 'applicationItems'],
                    ]);
                }
            ])
            ->orderBy('created_at', 'desc');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType !== 'all') {
            $query->where('approvable_type', $this->filterType);
        }

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->whereHasMorph('approvable', [LoanApplication::class, EmailApplication::class], function ($qAppro, $type) {
                    $qAppro->whereHas('user', function ($qUser) {
                        $qUser->where('name', 'like', '%' . $this->searchTerm . '%')
                              ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                    });
                    if ($type === LoanApplication::class) {
                        $qAppro->orWhere('purpose', 'like', '%' . $this->searchTerm . '%'); //
                    } elseif ($type === EmailApplication::class) {
                        $qAppro->orWhere('proposed_email', 'like', '%' . $this->searchTerm . '%') //
                               ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%'); //
                    }
                })->orWhere('id', 'like', '%' . $this->searchTerm . '%'); //
            });
        }

        return $query->paginate(10);
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }
    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }


    public function openApprovalActionModal(int $approvalId): void
    {
        $this->selectedApproval = Approval::with('approvable.user')->find($approvalId);
        if (!$this->selectedApproval) {
            $this->dispatch('toastr', type: 'error', message: __('Rekod kelulusan tidak ditemui.'));
            return;
        }
        // $this->authorize('actOn', $this->selectedApproval); // Authorization check
        $this->decision = '';
        $this->comments = $this->selectedApproval->comments ?? '';
        $this->showApprovalActionModal = true;
        $this->dispatch('openApprovalActionModalEvent'); // For JS to hook into Bootstrap modal
    }

    public function submitDecision(): void
    {
        $this->validate([
            'decision' => ['required', Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])],
            'comments' => [Rule::requiredIf($this->decision === Approval::STATUS_REJECTED), 'nullable', 'string', 'min:5', 'max:1000'],
        ]);

        if (!$this->selectedApproval) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        try {
            $this->approvalService->recordDecision(
                $this->selectedApproval,
                $this->decision,
                $this->comments,
                $user
            );
            session()->flash('success', __('Keputusan kelulusan berjaya direkodkan.'));
            $this->dispatch('toastr', type: 'success', message: __('Keputusan kelulusan berjaya direkodkan.'));
            $this->closeModal();
            $this->refreshDataEvent(); // Corrected to call the existing method
        } catch (AuthorizationException $e) {
            Log::error('ApprovalDashboard: Authorization error on submitDecision.', ['message' => $e->getMessage(), 'user_id' => $user->id]);
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk tindakan ini.'));
        } catch (Throwable $e) {
            Log::error('ApprovalDashboard: Error submitting decision.', ['exception' => $e, 'user_id' => $user->id]);
            $this->dispatch('toastr', type: 'error', message: __('Gagal merekodkan keputusan: ') . $e->getMessage());
        }
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showApprovalActionModal = false;
        $this->selectedApproval = null;
        $this->decision = '';
        $this->comments = '';
        $this->resetErrorBag();
        $this->dispatch('closeApprovalActionModalEvent'); // For JS to hook into Bootstrap modal
    }

    #[On('refresh-approval-list')]
    public function refreshDataEvent(): void // Renamed to avoid conflict if a 'refreshData' property exists
    {
        unset($this->approvalTasks);
    }


    public function render(): View
    {
        return view('livewire.approval-dashboard', [
            // 'approvalTasks' property will be automatically available due to #[Computed]
        ])->title(__('Papan Pemuka Kelulusan'));
    }
}
