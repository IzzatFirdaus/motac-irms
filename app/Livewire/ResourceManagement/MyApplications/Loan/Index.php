<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Access\AuthorizationException; // Corrected namespace

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';
    public string $filterStatus = '';
    protected string $paginationTheme = 'bootstrap';
    protected int $perPage = 10;

    public ?int $selectedApplicationId = null;
    public bool $showApprovalActionModal = false;
    public ?string $approvalActionType = null;
    public string $approvalComments = '';

    protected ApprovalService $approvalService;

    protected array $rules = [
        'approvalComments' => '',
    ];

    protected array $messages = [
        'approvalComments.required' => 'Sila masukkan sebab penolakan.',
    ];

    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
        $pageTitle = $this->generatePageTitle();
        $this->dispatch('update-page-title', title: $pageTitle);
    }

    public function generatePageTitle(): string
    {
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
        return __('Senarai Permohonan Pinjaman ICT Saya') . ' - ' . $appName;
    }

    /**
     * Computed property for loan applications.
     * The user's original file uses $this->loanApplications in render.
     * And this method name is getLoanApplicationsProperty which makes it $this->loanApplications.
     */
    public function getLoanApplicationsProperty()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            Log::warning('MyApplications\Loan\Index: Unauthenticated access attempt.');
            return LoanApplication::whereRaw('1 = 0')->paginate($this->perPage);
        }

        $query = LoanApplication::where('user_id', $user->id)
            ->with([
                'user:id,name',
                'responsibleOfficer:id,name',
                'currentApprovalOfficer:id,name',
                'approvals.officer:id,name'
            ])
            ->orderBy('updated_at', 'desc');

        if (!empty($this->searchTerm)) {
            $search = '%' . $this->searchTerm . '%';
            $query->where(function ($q) use ($search) {
                if (is_numeric(str_replace('%','',$search))) {
                     $q->orWhere('id', 'like', $search); // OrWhere because purpose might also match numeric IDs if purpose contains numbers
                }
                $q->orWhere('purpose', 'like', $search)
                  ->orWhere('location', 'like', $search);
            });
        }

        if (!empty($this->filterStatus) && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        $applications = $query->paginate($this->perPage);

        $applications->getCollection()->transform(function (LoanApplication $application) { // Type hint $application
            $application->can_act_on = $this->checkCanActOnApplication($application);
            return $application;
        });

        return $applications;
    }

    public function getStatusOptionsProperty(): array
    {
        $options = LoanApplication::getStatusOptions() ?? [];
        return ['' => __('Semua Status')] + $options;
    }

    protected function checkCanActOnApplication(LoanApplication $application): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->can('recordDecision', $application);
    }

    public function showApproveModal(int $applicationId): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        if (!$this->checkCanActOnApplication($application)) {
             session()->flash('error', __('Anda tidak dibenarkan untuk meluluskan permohonan ini.'));
             return;
        }

        $this->selectedApplicationId = $application->id;
        $this->approvalActionType = 'approve';
        $this->approvalComments = '';
        $this->resetValidation('approvalComments');
        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }

    public function showRejectModal(int $applicationId): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        if (!$this->checkCanActOnApplication($application)) {
             session()->flash('error', __('Anda tidak dibenarkan untuk menolak permohonan ini.'));
             return;
        }

        $this->selectedApplicationId = $application->id;
        $this->approvalActionType = 'reject';
        $this->approvalComments = '';
        $this->resetValidation('approvalComments');
        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'approvalComments' && $this->approvalActionType === 'reject') {
            $this->validateOnly($propertyName, [
                'approvalComments' => ['required', 'string', 'min:10', 'max:500'],
            ], $this->messages);
        }
    }

    public function submitApprovalAction(): void
    {
        $this->rules['approvalComments'] = ($this->approvalActionType === 'reject')
            ? ['required', 'string', 'min:10', 'max:500']
            : ['nullable', 'string', 'max:500'];
        $this->validate($this->rules, $this->messages);

        $application = LoanApplication::find($this->selectedApplicationId);
        if(!$application){
            session()->flash('error', __('Permohonan tidak ditemui.'));
            $this->closeApprovalActionModal();
            return;
        }

        /** @var User $user */
        $user = Auth::user();
        if (!$this->checkCanActOnApplication($application)) {
             session()->flash('error', __('Anda tidak lagi mempunyai kebenaran untuk tindakan ini.'));
             $this->closeApprovalActionModal();
             return;
        }

        $actionStatusForApprovalService = $this->approvalActionType === 'approve' ? Approval::STATUS_APPROVED : Approval::STATUS_REJECTED;

        try {
            $currentApprovalStageKey = $application->current_approval_stage ?? $application->status;

            $approvalTask = Approval::where('approvable_id', $application->id)
                ->where('approvable_type', $application->getMorphClass())
                ->where('stage', $currentApprovalStageKey)
                ->where('status', Approval::STATUS_PENDING)
                ->when(!$user->hasRole('Admin'), function ($query) use ($user) {
                    return $query->where('officer_id', $user->id);
                })
                ->first();

            if (!$approvalTask && $user->hasRole('Admin')) {
                // If admin is acting and no specific task, try to find ANY pending task for that stage
                $approvalTask = Approval::where('approvable_id', $application->id)
                    ->where('approvable_type', $application->getMorphClass())
                    ->where('stage', $currentApprovalStageKey)
                    ->where('status', Approval::STATUS_PENDING)
                    ->orderBy('created_at', 'desc') // Get the latest if multiple for stage
                    ->first();
            }


            if (!$approvalTask) {
                 session()->flash('error', __('Tiada tugasan kelulusan aktif yang sesuai ditemui untuk diproses. Semak status permohonan atau hubungi pentadbir.'));
                 Log::warning("No active approval task found for LoanApplication #{$application->id} at stage '{$currentApprovalStageKey}' for user #{$user->id} to act upon.");
                 $this->closeApprovalActionModal();
                 return;
            }

            $this->approvalService->processApprovalDecision(
                $approvalTask,
                $actionStatusForApprovalService,
                $user,
                $this->approvalComments
            );

            session()->flash('success', __('Tindakan kelulusan telah berjaya direkodkan.'));
        } catch (AuthorizationException $e) {
            Log::error("Authorization error during approval action for loan #{$application->id} by user #{$user->id}: " . $e->getMessage());
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error processing approval action for loan #{$application->id} by user #{$user->id}: " . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('Gagal memproses tindakan kelulusan. Ralat: ') . $e->getMessage());
        }

        $this->closeApprovalActionModal();
    }

    public function closeApprovalActionModal(): void
    {
        $this->showApprovalActionModal = false;
        $this->selectedApplicationId = null;
        $this->approvalActionType = null;
        $this->approvalComments = '';
        $this->resetValidation();
        $this->dispatch('closeModal', elementId: 'approvalActionModal');
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterStatus']);
        $this->resetPage();
    }

    public function render(): View
    {
        // The user's original file used 'applications' as the key for the view.
        return view('livewire.resource-management.my-applications.loan.index', [
            'applications' => $this->loanApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
